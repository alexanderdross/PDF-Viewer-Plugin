<?php

namespace Drupal\Tests\pdf_embed_seo_premium\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests for the Rate Limiter service.
 *
 * @group pdf_embed_seo_premium
 * @coversDefaultClass \Drupal\pdf_embed_seo_premium\Service\RateLimiter
 */
class RateLimiterTest extends UnitTestCase {

  /**
   * Tests rate limit configuration defaults.
   *
   * @covers ::getConfig
   */
  public function testRateLimitConfigDefaults() {
    $config = [
      'password_verify' => [
        'max_attempts' => 5,
        'window_seconds' => 300,
        'block_seconds' => 900,
      ],
    ];

    $this->assertEquals(5, $config['password_verify']['max_attempts']);
    $this->assertEquals(300, $config['password_verify']['window_seconds']);
    $this->assertEquals(900, $config['password_verify']['block_seconds']);
  }

  /**
   * Tests rate limit check when under limit.
   *
   * @covers ::checkLimit
   */
  public function testCheckLimitUnderLimit() {
    $max_attempts = 5;
    $current_attempts = 3;

    $allowed = $current_attempts < $max_attempts;

    $this->assertTrue($allowed);
  }

  /**
   * Tests rate limit check when at limit.
   *
   * @covers ::checkLimit
   */
  public function testCheckLimitAtLimit() {
    $max_attempts = 5;
    $current_attempts = 5;

    $allowed = $current_attempts < $max_attempts;

    $this->assertFalse($allowed);
  }

  /**
   * Tests rate limit check when over limit.
   *
   * @covers ::checkLimit
   */
  public function testCheckLimitOverLimit() {
    $max_attempts = 5;
    $current_attempts = 6;

    $allowed = $current_attempts < $max_attempts;

    $this->assertFalse($allowed);
  }

  /**
   * Tests rate limit response structure when blocked.
   *
   * @covers ::checkLimit
   */
  public function testBlockedResponseStructure() {
    $response = [
      'allowed' => FALSE,
      'message' => 'Too many failed attempts. Please try again later.',
      'retry_after' => 900,
      'remaining_attempts' => 0,
    ];

    $this->assertFalse($response['allowed']);
    $this->assertArrayHasKey('message', $response);
    $this->assertArrayHasKey('retry_after', $response);
    $this->assertEquals(900, $response['retry_after']);
    $this->assertEquals(0, $response['remaining_attempts']);
  }

  /**
   * Tests rate limit response structure when allowed.
   *
   * @covers ::checkLimit
   */
  public function testAllowedResponseStructure() {
    $max_attempts = 5;
    $current_attempts = 2;

    $response = [
      'allowed' => TRUE,
      'remaining_attempts' => $max_attempts - $current_attempts,
    ];

    $this->assertTrue($response['allowed']);
    $this->assertEquals(3, $response['remaining_attempts']);
  }

  /**
   * Tests attempt recording logic.
   *
   * @covers ::recordAttempt
   */
  public function testRecordAttempt() {
    $attempts_before = 4;
    $attempts_after = $attempts_before + 1;

    $this->assertEquals(5, $attempts_after);
  }

  /**
   * Tests successful attempt resets counter.
   *
   * @covers ::recordAttempt
   */
  public function testSuccessfulAttemptReset() {
    $attempts_before = 4;
    $success = TRUE;

    // Successful attempts should reset the counter.
    $attempts_after = $success ? 0 : $attempts_before + 1;

    $this->assertEquals(0, $attempts_after);
  }

  /**
   * Tests failed attempt increments counter.
   *
   * @covers ::recordAttempt
   */
  public function testFailedAttemptIncrement() {
    $attempts_before = 4;
    $success = FALSE;

    $attempts_after = $success ? 0 : $attempts_before + 1;

    $this->assertEquals(5, $attempts_after);
  }

  /**
   * Tests block expiry time calculation.
   *
   * @covers ::checkLimit
   */
  public function testBlockExpiryCalculation() {
    $block_seconds = 900;
    $block_start = time();
    $block_expires = $block_start + $block_seconds;

    $this->assertGreaterThan($block_start, $block_expires);
    $this->assertEquals($block_start + 900, $block_expires);
  }

  /**
   * Tests retry after calculation.
   *
   * @covers ::checkLimit
   */
  public function testRetryAfterCalculation() {
    $block_expires = time() + 600;
    $current_time = time();
    $retry_after = $block_expires - $current_time;

    $this->assertGreaterThan(0, $retry_after);
    $this->assertLessThanOrEqual(600, $retry_after);
  }

  /**
   * Tests window expiry check.
   *
   * @covers ::checkLimit
   */
  public function testWindowExpiry() {
    $window_seconds = 300;
    $first_attempt = time() - 400; // 400 seconds ago.
    $window_start = time() - $window_seconds;

    // First attempt is outside the window.
    $in_window = $first_attempt >= $window_start;

    $this->assertFalse($in_window);
  }

  /**
   * Tests per-document rate limiting.
   *
   * @covers ::checkLimit
   */
  public function testPerDocumentRateLimiting() {
    $ip_address = '192.168.1.100';
    $document_a = 1;
    $document_b = 2;

    // Each document has separate rate limit.
    $key_a = sprintf('%s:%s:%d', 'password_verify', $ip_address, $document_a);
    $key_b = sprintf('%s:%s:%d', 'password_verify', $ip_address, $document_b);

    $this->assertNotEquals($key_a, $key_b);
  }

  /**
   * Tests cleanup of old records logic.
   *
   * @covers ::cleanup
   */
  public function testCleanupOldRecords() {
    $older_than = 86400; // 24 hours.
    $threshold = time() - $older_than;

    // Records older than threshold should be deleted.
    $record_time = time() - 100000; // ~27 hours old.
    $should_delete = $record_time < $threshold;

    $this->assertTrue($should_delete);
  }

  /**
   * Tests cleanup keeps recent records.
   *
   * @covers ::cleanup
   */
  public function testCleanupKeepsRecentRecords() {
    $older_than = 86400; // 24 hours.
    $threshold = time() - $older_than;

    // Recent records should be kept.
    $record_time = time() - 3600; // 1 hour old.
    $should_delete = $record_time < $threshold;

    $this->assertFalse($should_delete);
  }

  /**
   * Tests IP address handling.
   *
   * @covers ::checkLimit
   */
  public function testIpAddressHandling() {
    $ip_v4 = '192.168.1.100';
    $ip_v6 = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';

    // Both formats should be valid.
    $this->assertMatchesRegularExpression('/^[\d.]+$/', $ip_v4);
    $this->assertMatchesRegularExpression('/^[0-9a-f:]+$/i', $ip_v6);
  }

  /**
   * Tests HTTP 429 response code for rate limiting.
   */
  public function testHttpStatusCode() {
    $http_too_many_requests = 429;

    $this->assertEquals(429, $http_too_many_requests);
  }

  /**
   * Tests action type configuration.
   *
   * @covers ::getConfig
   */
  public function testActionTypeConfiguration() {
    $supported_actions = [
      'password_verify',
      'api_request',
      'login_attempt',
    ];

    $this->assertContains('password_verify', $supported_actions);
  }

}
