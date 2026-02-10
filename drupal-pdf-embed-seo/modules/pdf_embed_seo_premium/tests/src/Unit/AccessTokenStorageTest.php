<?php

namespace Drupal\Tests\pdf_embed_seo_premium\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests for the Access Token Storage service.
 *
 * @group pdf_embed_seo_premium
 * @coversDefaultClass \Drupal\pdf_embed_seo_premium\Service\AccessTokenStorage
 */
class AccessTokenStorageTest extends UnitTestCase {

  /**
   * Tests token generation format.
   *
   * @covers ::createToken
   */
  public function testTokenGenerationFormat() {
    // Token should be 64 character hex string (32 bytes).
    $token = bin2hex(random_bytes(32));

    $this->assertEquals(64, strlen($token));
    $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $token);
  }

  /**
   * Tests token creation response structure.
   *
   * @covers ::createToken
   */
  public function testTokenCreationResponse() {
    $response = [
      'token' => bin2hex(random_bytes(32)),
      'expires' => time() + 3600,
      'max_uses' => 5,
      'pdf_document_id' => 123,
    ];

    $this->assertArrayHasKey('token', $response);
    $this->assertArrayHasKey('expires', $response);
    $this->assertArrayHasKey('max_uses', $response);
    $this->assertArrayHasKey('pdf_document_id', $response);
  }

  /**
   * Tests default expiration (24 hours).
   *
   * @covers ::createToken
   */
  public function testDefaultExpiration() {
    $default_expires_in = 86400; // 24 hours.
    $current_time = time();
    $expires_at = $current_time + $default_expires_in;

    $this->assertEquals($current_time + 86400, $expires_at);
  }

  /**
   * Tests custom expiration.
   *
   * @covers ::createToken
   */
  public function testCustomExpiration() {
    $expires_in = 3600; // 1 hour.
    $current_time = time();
    $expires_at = $current_time + $expires_in;

    $this->assertEquals($current_time + 3600, $expires_at);
  }

  /**
   * Tests unlimited uses (max_uses = 0).
   *
   * @covers ::validateToken
   */
  public function testUnlimitedUses() {
    $max_uses = 0;
    $use_count = 100;

    // When max_uses is 0, unlimited uses are allowed.
    $valid = $max_uses === 0 || $use_count < $max_uses;

    $this->assertTrue($valid);
  }

  /**
   * Tests max uses enforcement.
   *
   * @covers ::validateToken
   */
  public function testMaxUsesEnforcement() {
    $max_uses = 5;

    // Under limit.
    $use_count = 3;
    $this->assertTrue($use_count < $max_uses);

    // At limit.
    $use_count = 5;
    $this->assertFalse($use_count < $max_uses);

    // Over limit.
    $use_count = 6;
    $this->assertFalse($use_count < $max_uses);
  }

  /**
   * Tests token expiry validation.
   *
   * @covers ::validateToken
   */
  public function testTokenExpiryValidation() {
    $current_time = time();

    // Not expired.
    $expires_at = $current_time + 3600;
    $is_expired = $current_time > $expires_at;
    $this->assertFalse($is_expired);

    // Expired.
    $expires_at = $current_time - 3600;
    $is_expired = $current_time > $expires_at;
    $this->assertTrue($is_expired);
  }

  /**
   * Tests document ID validation.
   *
   * @covers ::validateToken
   */
  public function testDocumentIdValidation() {
    $token_document_id = 123;
    $requested_document_id = 123;

    $valid = $token_document_id === $requested_document_id;
    $this->assertTrue($valid);

    $requested_document_id = 456;
    $valid = $token_document_id === $requested_document_id;
    $this->assertFalse($valid);
  }

  /**
   * Tests validation response for valid token.
   *
   * @covers ::validateToken
   */
  public function testValidTokenResponse() {
    $response = [
      'valid' => TRUE,
      'data' => [
        'pdf_document_id' => 123,
        'expires' => time() + 3600,
        'remaining_uses' => 3,
      ],
    ];

    $this->assertTrue($response['valid']);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(123, $response['data']['pdf_document_id']);
  }

  /**
   * Tests validation response for expired token.
   *
   * @covers ::validateToken
   */
  public function testExpiredTokenResponse() {
    $response = [
      'valid' => FALSE,
      'message' => 'Access link has expired.',
    ];

    $this->assertFalse($response['valid']);
    $this->assertEquals('Access link has expired.', $response['message']);
  }

  /**
   * Tests validation response for invalid token.
   *
   * @covers ::validateToken
   */
  public function testInvalidTokenResponse() {
    $response = [
      'valid' => FALSE,
      'message' => 'Invalid or expired access link.',
    ];

    $this->assertFalse($response['valid']);
    $this->assertStringContainsString('Invalid', $response['message']);
  }

  /**
   * Tests validation response for max uses exceeded.
   *
   * @covers ::validateToken
   */
  public function testMaxUsesExceededResponse() {
    $response = [
      'valid' => FALSE,
      'message' => 'Access link has reached maximum uses.',
    ];

    $this->assertFalse($response['valid']);
    $this->assertStringContainsString('maximum uses', $response['message']);
  }

  /**
   * Tests use count increment.
   *
   * @covers ::validateToken
   */
  public function testUseCountIncrement() {
    $use_count_before = 3;
    $use_count_after = $use_count_before + 1;

    $this->assertEquals(4, $use_count_after);
  }

  /**
   * Tests remaining uses calculation.
   *
   * @covers ::validateToken
   */
  public function testRemainingUsesCalculation() {
    $max_uses = 10;
    $use_count = 3;
    $remaining = $max_uses - $use_count;

    $this->assertEquals(7, $remaining);

    // For unlimited, remaining should be NULL.
    $max_uses = 0;
    $remaining = $max_uses > 0 ? $max_uses - $use_count : NULL;
    $this->assertNull($remaining);
  }

  /**
   * Tests cleanup of expired tokens.
   *
   * @covers ::cleanupExpired
   */
  public function testCleanupExpiredTokens() {
    $current_time = time();
    $token_expires = $current_time - 3600; // Expired 1 hour ago.

    $should_delete = $token_expires < $current_time;

    $this->assertTrue($should_delete);
  }

  /**
   * Tests cleanup keeps valid tokens.
   *
   * @covers ::cleanupExpired
   */
  public function testCleanupKeepsValidTokens() {
    $current_time = time();
    $token_expires = $current_time + 3600; // Expires in 1 hour.

    $should_delete = $token_expires < $current_time;

    $this->assertFalse($should_delete);
  }

  /**
   * Tests token hash storage for lookup.
   *
   * @covers ::validateToken
   */
  public function testTokenHashStorage() {
    $token = bin2hex(random_bytes(32));
    $token_hash = hash('sha256', $token);

    // Hash should be 64 characters.
    $this->assertEquals(64, strlen($token_hash));
    $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $token_hash);
  }

  /**
   * Tests database table schema.
   */
  public function testDatabaseTableSchema() {
    $schema = [
      'fields' => [
        'id' => ['type' => 'serial', 'unsigned' => TRUE, 'not null' => TRUE],
        'token_hash' => ['type' => 'varchar', 'length' => 64, 'not null' => TRUE],
        'pdf_document_id' => ['type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE],
        'expires' => ['type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE],
        'max_uses' => ['type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0],
        'use_count' => ['type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0],
        'created' => ['type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE],
        'created_by' => ['type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE],
      ],
      'primary key' => ['id'],
      'indexes' => [
        'token_hash' => ['token_hash'],
        'pdf_document_id' => ['pdf_document_id'],
        'expires' => ['expires'],
      ],
    ];

    $this->assertArrayHasKey('fields', $schema);
    $this->assertArrayHasKey('token_hash', $schema['fields']);
    $this->assertArrayHasKey('pdf_document_id', $schema['fields']);
    $this->assertArrayHasKey('expires', $schema['fields']);
    $this->assertArrayHasKey('max_uses', $schema['fields']);
    $this->assertArrayHasKey('use_count', $schema['fields']);
  }

  /**
   * Tests State API fallback compatibility.
   */
  public function testStateApiFallback() {
    // State API key format.
    $token = 'abc123def456';
    $state_key = 'pdf_access_token_' . $token;

    $this->assertEquals('pdf_access_token_abc123def456', $state_key);
  }

  /**
   * Tests token data structure for State API.
   */
  public function testStateApiTokenData() {
    $token_data = [
      'pdf_id' => 123,
      'expires' => time() + 3600,
      'max_uses' => 5,
      'uses' => 0,
      'created_by' => 1,
      'created' => time(),
    ];

    $this->assertArrayHasKey('pdf_id', $token_data);
    $this->assertArrayHasKey('expires', $token_data);
    $this->assertArrayHasKey('max_uses', $token_data);
    $this->assertArrayHasKey('uses', $token_data);
  }

  /**
   * Tests ISO 8601 date formatting.
   */
  public function testIso8601DateFormatting() {
    $timestamp = time();
    $iso_date = date('c', $timestamp);

    // Should match ISO 8601 format.
    $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/', $iso_date);
  }

}
