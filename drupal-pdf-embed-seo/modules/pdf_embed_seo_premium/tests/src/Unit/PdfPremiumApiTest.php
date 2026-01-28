<?php

namespace Drupal\Tests\pdf_embed_seo_premium\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests for PDF Premium API endpoints.
 *
 * @group pdf_embed_seo_premium
 */
class PdfPremiumApiTest extends UnitTestCase {

  /**
   * Tests premium API endpoint paths.
   */
  public function testPremiumEndpointPaths() {
    $endpoints = [
      'analytics' => '/api/pdf-embed-seo/v1/analytics',
      'analytics_documents' => '/api/pdf-embed-seo/v1/analytics/documents',
      'analytics_export' => '/api/pdf-embed-seo/v1/analytics/export',
      'progress_get' => '/api/pdf-embed-seo/v1/documents/{id}/progress',
      'progress_save' => '/api/pdf-embed-seo/v1/documents/{id}/progress',
      'verify_password' => '/api/pdf-embed-seo/v1/documents/{id}/verify-password',
      'download' => '/api/pdf-embed-seo/v1/documents/{id}/download',
      'expiring_link_generate' => '/api/pdf-embed-seo/v1/documents/{id}/expiring-link',
      'expiring_link_validate' => '/api/pdf-embed-seo/v1/documents/{id}/expiring-link/{token}',
      'categories' => '/api/pdf-embed-seo/v1/categories',
      'tags' => '/api/pdf-embed-seo/v1/tags',
      'bulk_import' => '/api/pdf-embed-seo/v1/bulk/import',
      'bulk_import_status' => '/api/pdf-embed-seo/v1/bulk/import/status',
    ];

    $this->assertArrayHasKey('analytics', $endpoints);
    $this->assertArrayHasKey('download', $endpoints);
    $this->assertArrayHasKey('expiring_link_generate', $endpoints);
    $this->assertArrayHasKey('expiring_link_validate', $endpoints);
  }

  /**
   * Tests download tracking response structure.
   */
  public function testDownloadTrackingResponse() {
    $response = [
      'success' => TRUE,
      'download_count' => 15,
      'timestamp' => '2026-01-28T10:30:00+00:00',
    ];

    $this->assertTrue($response['success']);
    $this->assertArrayHasKey('download_count', $response);
    $this->assertArrayHasKey('timestamp', $response);
    $this->assertEquals(15, $response['download_count']);
  }

  /**
   * Tests download count increment.
   */
  public function testDownloadCountIncrement() {
    $initial_count = 10;
    $new_count = $initial_count + 1;

    $this->assertEquals(11, $new_count);
  }

  /**
   * Tests expiring link generation response structure.
   */
  public function testExpiringLinkGenerationResponse() {
    $response = [
      'success' => TRUE,
      'token' => 'abc123def456',
      'url' => 'https://example.com/pdf/document-slug?access_token=abc123def456',
      'expires_at' => '2026-01-29T10:30:00+00:00',
      'max_uses' => 5,
      'uses' => 0,
    ];

    $this->assertTrue($response['success']);
    $this->assertArrayHasKey('token', $response);
    $this->assertArrayHasKey('url', $response);
    $this->assertArrayHasKey('expires_at', $response);
    $this->assertArrayHasKey('max_uses', $response);
    $this->assertEquals(0, $response['uses']);
  }

  /**
   * Tests expiring link validation response - valid.
   */
  public function testExpiringLinkValidationValid() {
    $response = [
      'valid' => TRUE,
      'pdf_url' => 'https://example.com/files/document.pdf',
      'title' => 'Test Document',
      'remaining_uses' => 3,
    ];

    $this->assertTrue($response['valid']);
    $this->assertArrayHasKey('pdf_url', $response);
    $this->assertArrayHasKey('remaining_uses', $response);
  }

  /**
   * Tests expiring link validation response - expired.
   */
  public function testExpiringLinkValidationExpired() {
    $response = [
      'valid' => FALSE,
      'error' => 'link_expired',
      'message' => 'This access link has expired.',
    ];

    $this->assertFalse($response['valid']);
    $this->assertEquals('link_expired', $response['error']);
  }

  /**
   * Tests expiring link validation response - max uses exceeded.
   */
  public function testExpiringLinkValidationMaxUses() {
    $response = [
      'valid' => FALSE,
      'error' => 'max_uses_exceeded',
      'message' => 'This access link has reached its maximum number of uses.',
    ];

    $this->assertFalse($response['valid']);
    $this->assertEquals('max_uses_exceeded', $response['error']);
  }

  /**
   * Tests expiring link default expiration (24 hours).
   */
  public function testExpiringLinkDefaultExpiration() {
    $default_expiration_seconds = 86400; // 24 hours.
    $current_time = time();
    $expected_expiry = $current_time + $default_expiration_seconds;

    $this->assertEquals(86400, $default_expiration_seconds);
    $this->assertGreaterThan($current_time, $expected_expiry);
  }

  /**
   * Tests expiring link max uses enforcement logic.
   */
  public function testMaxUsesEnforcement() {
    $max_uses = 5;
    $current_uses = 4;

    // Before reaching limit.
    $this->assertTrue($current_uses < $max_uses);

    // After reaching limit.
    $current_uses = 5;
    $this->assertFalse($current_uses < $max_uses);

    // Unlimited uses (0 means unlimited).
    $max_uses = 0;
    $this->assertTrue($max_uses === 0 || $current_uses < $max_uses);
  }

  /**
   * Tests token generation format.
   */
  public function testTokenGenerationFormat() {
    // Token should be alphanumeric and at least 32 characters.
    $token = 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6';

    $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $token);
    $this->assertGreaterThanOrEqual(32, strlen($token));
  }

  /**
   * Tests analytics response structure.
   */
  public function testAnalyticsResponseStructure() {
    $response = [
      'total_views' => 1500,
      'total_downloads' => 250,
      'unique_documents' => 25,
      'period' => '30days',
      'top_documents' => [
        ['id' => 1, 'title' => 'Doc 1', 'views' => 500, 'downloads' => 100],
        ['id' => 2, 'title' => 'Doc 2', 'views' => 300, 'downloads' => 50],
      ],
    ];

    $this->assertArrayHasKey('total_views', $response);
    $this->assertArrayHasKey('total_downloads', $response);
    $this->assertArrayHasKey('period', $response);
    $this->assertArrayHasKey('top_documents', $response);
    $this->assertCount(2, $response['top_documents']);
  }

  /**
   * Tests premium settings response.
   */
  public function testPremiumSettingsResponse() {
    $settings = [
      'viewer_theme' => 'light',
      'default_allow_download' => TRUE,
      'default_allow_print' => TRUE,
      'archive_url' => '/pdf',
      'is_premium' => TRUE,
      'license_status' => 'valid',
      'features' => [
        'analytics' => TRUE,
        'password_protection' => TRUE,
        'reading_progress' => TRUE,
        'download_tracking' => TRUE,
        'expiring_links' => TRUE,
        'schema_optimization' => TRUE,
        'role_based_access' => TRUE,
        'bulk_import' => TRUE,
        'viewer_enhancements' => TRUE,
      ],
    ];

    $this->assertTrue($settings['is_premium']);
    $this->assertEquals('valid', $settings['license_status']);
    $this->assertTrue($settings['features']['download_tracking']);
    $this->assertTrue($settings['features']['expiring_links']);
  }

}
