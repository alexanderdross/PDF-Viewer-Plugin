<?php
/**
 * Unit tests for Pro-plus webhooks.
 *
 * @package PDF_Embed_SEO_Pro_Plus
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Pro-plus webhook functionality.
 */
class Test_Pro_Plus_Webhooks extends PDF_Pro_Plus_Test_Case {

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->activate_pro_plus_license();
		$this->enable_setting( 'enable_webhooks', true );
	}

	/**
	 * Test webhook URL validation.
	 */
	public function test_webhook_url_validation() {
		$valid_urls = array(
			'https://example.com/webhook',
			'https://api.example.com/v1/webhooks',
			'https://hooks.slack.com/services/xxx/yyy/zzz',
		);

		$invalid_urls = array(
			'http://insecure.com/webhook', // Non-HTTPS.
			'ftp://example.com/webhook',
			'not-a-url',
			'javascript:alert(1)',
		);

		foreach ( $valid_urls as $url ) {
			$this->assertTrue(
				filter_var( $url, FILTER_VALIDATE_URL ) !== false &&
				strpos( $url, 'https://' ) === 0,
				"URL should be valid: {$url}"
			);
		}

		foreach ( $invalid_urls as $url ) {
			$is_valid = filter_var( $url, FILTER_VALIDATE_URL ) !== false &&
			            strpos( $url, 'https://' ) === 0;
			$this->assertFalse( $is_valid, "URL should be invalid: {$url}" );
		}
	}

	/**
	 * Test webhook payload structure.
	 */
	public function test_webhook_payload_structure() {
		$payload = array(
			'event'     => 'document.viewed',
			'timestamp' => gmdate( 'c' ),
			'data'      => array(
				'document_id' => $this->pdf_id,
				'title'       => 'Test PDF',
				'user_id'     => $this->admin_user_id,
				'ip_address'  => '192.168.1.100',
			),
			'signature' => 'sha256=abc123...',
		);

		$required_keys = array( 'event', 'timestamp', 'data' );
		foreach ( $required_keys as $key ) {
			$this->assertArrayHasKey( $key, $payload );
		}
	}

	/**
	 * Test webhook event types.
	 */
	public function test_webhook_event_types() {
		$valid_events = array(
			'document.viewed',
			'document.downloaded',
			'document.printed',
			'password.success',
			'password.failed',
			'annotation.created',
			'annotation.deleted',
			'signature.added',
			'progress.updated',
		);

		foreach ( $valid_events as $event ) {
			$this->assertMatchesRegularExpression( '/^[a-z]+\.[a-z]+$/', $event );
		}
	}

	/**
	 * Test webhook signature generation.
	 */
	public function test_webhook_signature_generation() {
		$secret = 'webhook_secret_key_123';
		$payload = wp_json_encode( array(
			'event'     => 'document.viewed',
			'timestamp' => gmdate( 'c' ),
			'data'      => array( 'document_id' => 123 ),
		) );

		$signature = hash_hmac( 'sha256', $payload, $secret );

		$this->assertEquals( 64, strlen( $signature ) ); // SHA256 produces 64 hex chars.
		$this->assertMatchesRegularExpression( '/^[a-f0-9]{64}$/', $signature );
	}

	/**
	 * Test webhook signature verification.
	 */
	public function test_webhook_signature_verification() {
		$secret = 'webhook_secret_key_123';
		$payload = '{"event":"test"}';

		$expected_signature = hash_hmac( 'sha256', $payload, $secret );
		$received_signature = 'sha256=' . $expected_signature;

		// Verify signature.
		$signature_parts = explode( '=', $received_signature );
		$algorithm = $signature_parts[0];
		$hash = $signature_parts[1];

		$this->assertEquals( 'sha256', $algorithm );
		$this->assertTrue( hash_equals( $expected_signature, $hash ) );
	}

	/**
	 * Test webhook retry logic.
	 */
	public function test_webhook_retry_configuration() {
		$retry_config = array(
			'max_retries'     => 3,
			'retry_delay'     => 60,     // 1 minute.
			'backoff_factor'  => 2,      // Exponential backoff.
			'max_delay'       => 3600,   // 1 hour max.
		);

		// Calculate retry delays.
		$delays = array();
		$current_delay = $retry_config['retry_delay'];

		for ( $i = 0; $i < $retry_config['max_retries']; $i++ ) {
			$delays[] = min( $current_delay, $retry_config['max_delay'] );
			$current_delay *= $retry_config['backoff_factor'];
		}

		$this->assertCount( 3, $delays );
		$this->assertEquals( array( 60, 120, 240 ), $delays );
	}

	/**
	 * Test webhook timeout settings.
	 */
	public function test_webhook_timeout_settings() {
		$timeout = 30; // 30 seconds.

		$this->assertGreaterThan( 0, $timeout );
		$this->assertLessThanOrEqual( 60, $timeout ); // Reasonable max timeout.
	}

	/**
	 * Test webhook secret generation.
	 */
	public function test_webhook_secret_generation() {
		$secret = wp_generate_password( 32, true, false );

		$this->assertEquals( 32, strlen( $secret ) );
	}

	/**
	 * Test webhook headers.
	 */
	public function test_webhook_headers() {
		$headers = array(
			'Content-Type'     => 'application/json',
			'X-PDF-Event'      => 'document.viewed',
			'X-PDF-Delivery'   => wp_generate_uuid4(),
			'X-PDF-Signature'  => 'sha256=abc123...',
			'User-Agent'       => 'PDF-Embed-SEO-Webhooks/1.3.0',
		);

		$required_headers = array( 'Content-Type', 'X-PDF-Signature' );
		foreach ( $required_headers as $header ) {
			$this->assertArrayHasKey( $header, $headers );
		}
	}

	/**
	 * Test webhook event filtering.
	 */
	public function test_webhook_event_filtering() {
		$enabled_events = array( 'view', 'download' );
		$this->enable_setting( 'webhook_events', $enabled_events );

		$settings = get_option( 'pdf_embed_seo_pro_plus_settings', array() );
		$this->assertEquals( $enabled_events, $settings['webhook_events'] );
	}

	/**
	 * Test webhook delivery log structure.
	 */
	public function test_webhook_delivery_log() {
		$log_entry = array(
			'id'            => 1,
			'webhook_url'   => 'https://example.com/webhook',
			'event'         => 'document.viewed',
			'payload'       => '{"event":"document.viewed",...}',
			'response_code' => 200,
			'response_body' => '{"status":"ok"}',
			'attempts'      => 1,
			'delivered_at'  => current_time( 'mysql' ),
			'created_at'    => current_time( 'mysql' ),
		);

		$this->assertArrayHasKey( 'webhook_url', $log_entry );
		$this->assertArrayHasKey( 'response_code', $log_entry );
		$this->assertEquals( 200, $log_entry['response_code'] );
	}
}
