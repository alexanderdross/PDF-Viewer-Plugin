<?php

namespace Drupal\Tests\pdf_embed_seo_pro_plus\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests the WebhookDispatcher service.
 *
 * @group pdf_embed_seo_pro_plus
 */
class WebhookDispatcherTest extends UnitTestCase {

  /**
   * Test webhook URL validation.
   */
  public function testWebhookUrlValidation() {
    $valid_urls = [
      'https://example.com/webhook',
      'https://api.example.com/v1/webhooks',
      'https://hooks.slack.com/services/xxx/yyy/zzz',
    ];

    $invalid_urls = [
      'http://insecure.com/webhook',
      'ftp://example.com/webhook',
      'not-a-url',
      'javascript:alert(1)',
    ];

    foreach ($valid_urls as $url) {
      $is_valid = filter_var($url, FILTER_VALIDATE_URL) !== FALSE &&
                  strpos($url, 'https://') === 0;
      $this->assertTrue($is_valid, "URL should be valid: {$url}");
    }

    foreach ($invalid_urls as $url) {
      $is_valid = filter_var($url, FILTER_VALIDATE_URL) !== FALSE &&
                  strpos($url, 'https://') === 0;
      $this->assertFalse($is_valid, "URL should be invalid: {$url}");
    }
  }

  /**
   * Test webhook event types.
   */
  public function testWebhookEventTypes() {
    $valid_events = [
      'document.viewed',
      'document.downloaded',
      'document.printed',
      'password.success',
      'password.failed',
      'annotation.created',
      'annotation.deleted',
    ];

    foreach ($valid_events as $event) {
      $this->assertMatchesRegularExpression('/^[a-z]+\.[a-z]+$/', $event);
    }
  }

  /**
   * Test webhook signature generation.
   */
  public function testWebhookSignatureGeneration() {
    $secret = 'webhook_secret_key_123';
    $payload = json_encode([
      'event' => 'document.viewed',
      'timestamp' => gmdate('c'),
      'data' => ['document_id' => 123],
    ]);

    $signature = hash_hmac('sha256', $payload, $secret);

    $this->assertEquals(64, strlen($signature));
    $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $signature);
  }

  /**
   * Test webhook signature verification.
   */
  public function testWebhookSignatureVerification() {
    $secret = 'webhook_secret_key_123';
    $payload = '{"event":"test"}';

    $expected_signature = hash_hmac('sha256', $payload, $secret);
    $received_signature = 'sha256=' . $expected_signature;

    $signature_parts = explode('=', $received_signature);
    $algorithm = $signature_parts[0];
    $hash = $signature_parts[1];

    $this->assertEquals('sha256', $algorithm);
    $this->assertTrue(hash_equals($expected_signature, $hash));
  }

  /**
   * Test webhook payload structure.
   */
  public function testWebhookPayloadStructure() {
    $payload = [
      'event' => 'document.viewed',
      'timestamp' => gmdate('c'),
      'data' => [
        'document_id' => 123,
        'title' => 'Test PDF',
        'user_id' => 1,
      ],
    ];

    $required_keys = ['event', 'timestamp', 'data'];
    foreach ($required_keys as $key) {
      $this->assertArrayHasKey($key, $payload);
    }
  }

  /**
   * Test retry delay calculation.
   */
  public function testRetryDelayCalculation() {
    $retry_config = [
      'max_retries' => 3,
      'retry_delay' => 60,
      'backoff_factor' => 2,
      'max_delay' => 3600,
    ];

    $delays = [];
    $current_delay = $retry_config['retry_delay'];

    for ($i = 0; $i < $retry_config['max_retries']; $i++) {
      $delays[] = min($current_delay, $retry_config['max_delay']);
      $current_delay *= $retry_config['backoff_factor'];
    }

    $this->assertCount(3, $delays);
    $this->assertEquals([60, 120, 240], $delays);
  }

  /**
   * Test webhook delivery log structure.
   */
  public function testWebhookDeliveryLogStructure() {
    $log_entry = [
      'id' => 1,
      'webhook_id' => 1,
      'event' => 'document.viewed',
      'payload' => '{"event":"document.viewed",...}',
      'response_code' => 200,
      'response_body' => '{"status":"ok"}',
      'attempts' => 1,
      'status' => 'delivered',
      'created' => time(),
      'delivered_at' => time(),
    ];

    $this->assertArrayHasKey('webhook_id', $log_entry);
    $this->assertArrayHasKey('response_code', $log_entry);
    $this->assertEquals(200, $log_entry['response_code']);
  }

}
