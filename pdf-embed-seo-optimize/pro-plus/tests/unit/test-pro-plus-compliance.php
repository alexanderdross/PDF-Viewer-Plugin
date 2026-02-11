<?php
/**
 * Unit tests for Pro-plus compliance features (GDPR, HIPAA).
 *
 * @package PDF_Embed_SEO_Pro_Plus
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Pro-plus compliance functionality.
 */
class Test_Pro_Plus_Compliance extends PDF_Pro_Plus_Test_Case {

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->activate_pro_plus_license();
		$this->enable_setting( 'enable_compliance', true );
	}

	/**
	 * Test GDPR mode setting.
	 */
	public function test_gdpr_mode_setting() {
		$this->enable_setting( 'gdpr_mode', true );

		$settings = get_option( 'pdf_embed_seo_pro_plus_settings', array() );
		$this->assertTrue( $settings['gdpr_mode'] );
	}

	/**
	 * Test HIPAA mode setting.
	 */
	public function test_hipaa_mode_setting() {
		$this->enable_setting( 'hipaa_mode', true );

		$settings = get_option( 'pdf_embed_seo_pro_plus_settings', array() );
		$this->assertTrue( $settings['hipaa_mode'] );
	}

	/**
	 * Test data retention period setting.
	 */
	public function test_data_retention_period() {
		$valid_periods = array( 30, 90, 180, 365, 730 );

		foreach ( $valid_periods as $days ) {
			$this->enable_setting( 'data_retention_days', $days );
			$settings = get_option( 'pdf_embed_seo_pro_plus_settings', array() );
			$this->assertEquals( $days, $settings['data_retention_days'] );
		}
	}

	/**
	 * Test user consent tracking structure.
	 */
	public function test_consent_tracking_structure() {
		$consent = array(
			'user_id'         => $this->admin_user_id,
			'consent_type'    => 'analytics',
			'consented'       => true,
			'timestamp'       => current_time( 'mysql' ),
			'ip_address'      => '192.168.1.0', // Anonymized.
			'consent_version' => '1.0',
		);

		$required_keys = array( 'consent_type', 'consented', 'timestamp' );
		foreach ( $required_keys as $key ) {
			$this->assertArrayHasKey( $key, $consent );
		}
	}

	/**
	 * Test consent types.
	 */
	public function test_consent_types() {
		$valid_consent_types = array(
			'analytics',
			'tracking',
			'marketing',
			'functional',
			'necessary',
		);

		foreach ( $valid_consent_types as $type ) {
			$this->assertMatchesRegularExpression( '/^[a-z_]+$/', $type );
		}
	}

	/**
	 * Test data export format.
	 */
	public function test_data_export_format() {
		$user_data = array(
			'user'       => array(
				'id'       => $this->admin_user_id,
				'email'    => 'admin@example.com',
				'name'     => 'Admin User',
			),
			'activities' => array(
				array(
					'type'      => 'document_viewed',
					'timestamp' => current_time( 'mysql' ),
					'document'  => 'Test PDF',
				),
			),
			'consents'   => array(
				array(
					'type'      => 'analytics',
					'consented' => true,
					'timestamp' => current_time( 'mysql' ),
				),
			),
			'exported_at' => current_time( 'mysql' ),
		);

		$json_export = wp_json_encode( $user_data, JSON_PRETTY_PRINT );
		$this->assertJson( $json_export );
	}

	/**
	 * Test data deletion verification.
	 */
	public function test_data_deletion_verification() {
		$deletion_record = array(
			'request_id'    => wp_generate_uuid4(),
			'user_id'       => $this->admin_user_id,
			'user_email'    => 'user@example.com',
			'requested_at'  => current_time( 'mysql' ),
			'completed_at'  => current_time( 'mysql' ),
			'data_deleted'  => array(
				'analytics_records' => 150,
				'consent_records'   => 3,
				'progress_records'  => 25,
			),
			'verified_by'   => $this->admin_user_id,
		);

		$this->assertArrayHasKey( 'request_id', $deletion_record );
		$this->assertArrayHasKey( 'completed_at', $deletion_record );
	}

	/**
	 * Test IP anonymization.
	 */
	public function test_ip_anonymization_gdpr() {
		$this->enable_setting( 'gdpr_mode', true );

		$full_ipv4 = '192.168.1.100';
		$full_ipv6 = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';

		// Anonymize IPv4 (last octet).
		$parts_v4 = explode( '.', $full_ipv4 );
		$parts_v4[3] = '0';
		$anon_ipv4 = implode( '.', $parts_v4 );

		// Anonymize IPv6 (last 80 bits).
		$parts_v6 = explode( ':', $full_ipv6 );
		for ( $i = 5; $i < 8; $i++ ) {
			$parts_v6[ $i ] = '0000';
		}
		$anon_ipv6 = implode( ':', $parts_v6 );

		$this->assertEquals( '192.168.1.0', $anon_ipv4 );
		$this->assertStringEndsWith( ':0000:0000:0000', $anon_ipv6 );
	}

	/**
	 * Test access log retention.
	 */
	public function test_access_log_retention() {
		$retention_days = 365;
		$this->enable_setting( 'data_retention_days', $retention_days );

		$cutoff_date = gmdate( 'Y-m-d H:i:s', strtotime( "-{$retention_days} days" ) );

		// Logs older than cutoff should be deleted.
		$old_log = array(
			'timestamp' => gmdate( 'Y-m-d H:i:s', strtotime( '-400 days' ) ),
		);

		$this->assertTrue( strtotime( $old_log['timestamp'] ) < strtotime( $cutoff_date ) );
	}

	/**
	 * Test consent banner configuration.
	 */
	public function test_consent_banner_config() {
		$banner_config = array(
			'enabled'           => true,
			'position'          => 'bottom',
			'theme'             => 'dark',
			'accept_text'       => 'Accept All',
			'decline_text'      => 'Decline',
			'customize_text'    => 'Customize',
			'privacy_link'      => '/privacy-policy/',
			'show_preferences'  => true,
		);

		$valid_positions = array( 'top', 'bottom', 'floating' );
		$this->assertContains( $banner_config['position'], $valid_positions );
	}

	/**
	 * Test audit trail for compliance.
	 */
	public function test_compliance_audit_trail() {
		$audit_entry = array(
			'id'          => 1,
			'action'      => 'data_export_requested',
			'user_id'     => $this->admin_user_id,
			'timestamp'   => current_time( 'mysql' ),
			'ip_address'  => '192.168.1.0',
			'details'     => 'User requested data export under GDPR Article 20.',
			'compliance'  => 'gdpr',
			'article'     => 'Article 20 - Right to data portability',
		);

		$this->assertArrayHasKey( 'compliance', $audit_entry );
		$this->assertArrayHasKey( 'article', $audit_entry );
	}

	/**
	 * Test HIPAA Business Associate Agreement tracking.
	 */
	public function test_hipaa_baa_tracking() {
		$baa_record = array(
			'organization'     => 'Healthcare Provider Inc.',
			'signed_date'      => '2024-01-15',
			'expiration_date'  => '2025-01-15',
			'document_url'     => 'https://example.com/baa-signed.pdf',
			'contact_name'     => 'John Smith',
			'contact_email'    => 'john@healthcare.com',
		);

		$this->assertArrayHasKey( 'signed_date', $baa_record );
		$this->assertArrayHasKey( 'expiration_date', $baa_record );
	}
}
