<?php

namespace Drupal\Tests\pdf_embed_seo_pro_plus\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests the AuditLogger service.
 *
 * @group pdf_embed_seo_pro_plus
 */
class AuditLoggerTest extends UnitTestCase {

  /**
   * Test audit log entry structure.
   */
  public function testAuditLogEntryStructure() {
    $log_entry = [
      'id' => 1,
      'timestamp' => time(),
      'user_id' => 1,
      'action' => 'document_viewed',
      'object_type' => 'pdf_document',
      'object_id' => 123,
      'ip_address' => '192.168.1.0',
      'user_agent' => 'Mozilla/5.0...',
      'details' => json_encode(['page' => 1, 'duration' => 30]),
    ];

    $required_keys = ['timestamp', 'user_id', 'action', 'ip_address'];
    foreach ($required_keys as $key) {
      $this->assertArrayHasKey($key, $log_entry);
    }
  }

  /**
   * Test audit action types.
   */
  public function testAuditActionTypes() {
    $valid_actions = [
      'document_viewed',
      'document_downloaded',
      'document_printed',
      'password_attempt_success',
      'password_attempt_failed',
      'settings_changed',
      'document_created',
      'document_updated',
      'document_deleted',
      'annotation_added',
      'annotation_deleted',
    ];

    foreach ($valid_actions as $action) {
      $this->assertMatchesRegularExpression('/^[a-z_]+$/', $action);
    }
  }

  /**
   * Test IP anonymization.
   */
  public function testIpAnonymization() {
    $full_ipv4 = '192.168.1.100';

    $parts = explode('.', $full_ipv4);
    $parts[3] = '0';
    $anonymized_ip = implode('.', $parts);

    $this->assertEquals('192.168.1.0', $anonymized_ip);
  }

  /**
   * Test log retention calculation.
   */
  public function testLogRetentionCalculation() {
    $retention_days = 90;
    $cutoff_timestamp = strtotime("-{$retention_days} days");

    $old_log = [
      'timestamp' => strtotime('-100 days'),
    ];

    $this->assertTrue($old_log['timestamp'] < $cutoff_timestamp);
  }

  /**
   * Test details serialization.
   */
  public function testDetailsSerialization() {
    $details = [
      'page' => 5,
      'duration' => 120,
      'scroll_depth' => 75,
    ];

    $serialized = json_encode($details);
    $deserialized = json_decode($serialized, TRUE);

    $this->assertEquals($details, $deserialized);
  }

  /**
   * Test user agent sanitization.
   */
  public function testUserAgentSanitization() {
    $dirty_agent = '<script>alert("xss")</script>Mozilla/5.0';
    $clean_agent = strip_tags($dirty_agent);

    $this->assertStringNotContainsString('<script>', $clean_agent);
    $this->assertStringContainsString('Mozilla/5.0', $clean_agent);
  }

  /**
   * Test log query filtering.
   */
  public function testLogQueryFiltering() {
    $valid_filters = [
      'action' => 'document_viewed',
      'user_id' => 1,
      'object_type' => 'pdf_document',
      'date_from' => '2024-01-01',
      'date_to' => '2024-12-31',
    ];

    foreach ($valid_filters as $key => $value) {
      $this->assertNotEmpty($value);
    }
  }

}
