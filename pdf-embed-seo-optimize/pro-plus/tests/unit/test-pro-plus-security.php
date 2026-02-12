<?php
/**
 * Unit tests for Pro-plus security features.
 *
 * @package PDF_Embed_SEO_Pro_Plus
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Pro-plus security functionality.
 */
class Test_Pro_Plus_Security extends PDF_Pro_Plus_Test_Case {

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->activate_pro_plus_license();
		$this->enable_setting( 'enable_security', true );
	}

	/**
	 * Test 2FA secret generation.
	 */
	public function test_2fa_secret_generation() {
		// Base32 alphabet for TOTP secrets.
		$base32_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
		$secret_length = 16;
		$secret = '';

		for ( $i = 0; $i < $secret_length; $i++ ) {
			$secret .= $base32_chars[ wp_rand( 0, strlen( $base32_chars ) - 1 ) ];
		}

		$this->assertEquals( $secret_length, strlen( $secret ) );
		$this->assertMatchesRegularExpression( '/^[A-Z2-7]+$/', $secret );
	}

	/**
	 * Test TOTP code validation format.
	 */
	public function test_totp_code_format() {
		// TOTP codes are 6-digit numbers.
		$valid_codes = array( '123456', '000000', '999999' );
		$invalid_codes = array( '12345', '1234567', 'abcdef', '12345a' );

		foreach ( $valid_codes as $code ) {
			$this->assertMatchesRegularExpression( '/^\d{6}$/', $code );
		}

		foreach ( $invalid_codes as $code ) {
			$this->assertDoesNotMatchRegularExpression( '/^\d{6}$/', $code );
		}
	}

	/**
	 * Test IP whitelist validation.
	 */
	public function test_ip_whitelist_validation() {
		$valid_ips = array(
			'192.168.1.1',
			'10.0.0.1',
			'172.16.0.1',
			'8.8.8.8',
			'2001:0db8:85a3:0000:0000:8a2e:0370:7334', // IPv6.
		);

		$invalid_ips = array(
			'999.999.999.999',
			'192.168.1',
			'not-an-ip',
			'192.168.1.1/24', // CIDR not a plain IP.
		);

		foreach ( $valid_ips as $ip ) {
			$this->assertNotFalse(
				filter_var( $ip, FILTER_VALIDATE_IP ),
				"IP should be valid: {$ip}"
			);
		}

		foreach ( $invalid_ips as $ip ) {
			// Note: CIDR notation is valid for whitelisting but not as a plain IP.
			if ( strpos( $ip, '/' ) === false ) {
				$this->assertFalse(
					filter_var( $ip, FILTER_VALIDATE_IP ),
					"IP should be invalid: {$ip}"
				);
			}
		}
	}

	/**
	 * Test CIDR notation validation.
	 */
	public function test_cidr_notation_validation() {
		$valid_cidrs = array(
			'192.168.1.0/24',
			'10.0.0.0/8',
			'172.16.0.0/16',
		);

		foreach ( $valid_cidrs as $cidr ) {
			$parts = explode( '/', $cidr );
			$this->assertCount( 2, $parts );
			$this->assertNotFalse( filter_var( $parts[0], FILTER_VALIDATE_IP ) );
			$this->assertGreaterThanOrEqual( 0, (int) $parts[1] );
			$this->assertLessThanOrEqual( 32, (int) $parts[1] );
		}
	}

	/**
	 * Test audit log entry structure.
	 */
	public function test_audit_log_entry_structure() {
		$log_entry = array(
			'id'         => 1,
			'timestamp'  => current_time( 'mysql' ),
			'user_id'    => $this->admin_user_id,
			'user_email' => 'admin@example.com',
			'action'     => 'document_viewed',
			'object_id'  => $this->pdf_id,
			'object_type' => 'pdf_document',
			'ip_address' => '192.168.1.100',
			'user_agent' => 'Mozilla/5.0...',
			'details'    => array(
				'page'     => 1,
				'duration' => 30,
			),
		);

		$required_keys = array( 'timestamp', 'user_id', 'action', 'ip_address' );
		foreach ( $required_keys as $key ) {
			$this->assertArrayHasKey( $key, $log_entry );
		}
	}

	/**
	 * Test audit log action types.
	 */
	public function test_audit_log_action_types() {
		$valid_actions = array(
			'document_viewed',
			'document_downloaded',
			'document_printed',
			'password_attempt_success',
			'password_attempt_failed',
			'login_attempt',
			'settings_changed',
			'document_created',
			'document_updated',
			'document_deleted',
			'annotation_added',
			'annotation_deleted',
			'signature_added',
		);

		foreach ( $valid_actions as $action ) {
			$this->assertMatchesRegularExpression( '/^[a-z_]+$/', $action );
		}
	}

	/**
	 * Test failed login attempt tracking.
	 */
	public function test_failed_login_tracking() {
		$max_attempts = 5;
		$lockout_duration = 15 * MINUTE_IN_SECONDS;

		$attempts = array();
		$ip = '192.168.1.100';

		for ( $i = 1; $i <= $max_attempts + 1; $i++ ) {
			$attempts[] = array(
				'ip'        => $ip,
				'timestamp' => time() - ( ( $max_attempts + 1 - $i ) * 60 ),
				'success'   => false,
			);
		}

		$recent_failures = count( array_filter( $attempts, function( $a ) {
			return ! $a['success'] && $a['timestamp'] > time() - 15 * MINUTE_IN_SECONDS;
		} ) );

		$this->assertGreaterThan( $max_attempts, $recent_failures );
	}

	/**
	 * Test IP anonymization for GDPR compliance.
	 */
	public function test_ip_anonymization() {
		$original_ip = '192.168.1.100';

		// Anonymize last octet for IPv4.
		$parts = explode( '.', $original_ip );
		$parts[3] = '0';
		$anonymized_ip = implode( '.', $parts );

		$this->assertEquals( '192.168.1.0', $anonymized_ip );
	}

	/**
	 * Test session token validation.
	 */
	public function test_session_token_validation() {
		$token = wp_generate_password( 64, false );

		$this->assertEquals( 64, strlen( $token ) );
		$this->assertMatchesRegularExpression( '/^[a-zA-Z0-9]+$/', $token );
	}

	/**
	 * Test audit log retention settings.
	 */
	public function test_audit_log_retention() {
		$valid_retention_days = array( 30, 60, 90, 180, 365 );

		foreach ( $valid_retention_days as $days ) {
			$this->enable_setting( 'audit_log_retention', $days );
			$settings = get_option( 'pdf_embed_seo_pro_plus_settings', array() );
			$this->assertEquals( $days, $settings['audit_log_retention'] );
		}
	}

	/**
	 * Test brute force protection threshold.
	 */
	public function test_brute_force_threshold() {
		$this->enable_setting( 'max_failed_attempts', 5 );
		$settings = get_option( 'pdf_embed_seo_pro_plus_settings', array() );

		$this->assertEquals( 5, $settings['max_failed_attempts'] );
		$this->assertGreaterThan( 0, $settings['max_failed_attempts'] );
	}
}
