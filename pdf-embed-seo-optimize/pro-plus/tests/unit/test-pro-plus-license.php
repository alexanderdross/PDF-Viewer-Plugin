<?php
/**
 * Unit tests for Pro-plus license validation.
 *
 * @package PDF_Embed_SEO_Pro_Plus
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Pro-plus license functionality.
 */
class Test_Pro_Plus_License extends PDF_Pro_Plus_Test_Case {

	/**
	 * Test valid Pro-plus license key format.
	 */
	public function test_valid_license_key_format() {
		$valid_key = 'PDF$PRO+#ABCD-EFGH@IJKL-MNOP!QRST';
		update_option( 'pdf_embed_seo_pro_plus_license_key', $valid_key );

		// License should match Pro+ format.
		$this->assertTrue(
			(bool) preg_match( '/^PDF\$PRO\+#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}-[A-Z0-9]{4}![A-Z0-9]{4}$/i', $valid_key )
		);
	}

	/**
	 * Test invalid license key format.
	 */
	public function test_invalid_license_key_format() {
		$invalid_keys = array(
			'invalid-key',
			'PDF$PRO#ABCD-EFGH@IJKL-MNOP!QRST', // Missing + for Pro+
			'PDFPRO+ABCDEFGH@IJKLMNOP!QRST',    // Missing $ and #
			'short',
		);

		foreach ( $invalid_keys as $key ) {
			$this->assertFalse(
				(bool) preg_match( '/^PDF\$PRO\+#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}-[A-Z0-9]{4}![A-Z0-9]{4}$/i', $key ),
				"Key should be invalid: {$key}"
			);
		}
	}

	/**
	 * Test unlimited license key format.
	 */
	public function test_unlimited_license_key_format() {
		$unlimited_key = 'PDF$UNLIMITED#ABCD@EFGH!IJKL';

		$this->assertTrue(
			(bool) preg_match( '/^PDF\$UNLIMITED#[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i', $unlimited_key )
		);
	}

	/**
	 * Test development license key format.
	 */
	public function test_dev_license_key_format() {
		$dev_key = 'PDF$DEV#ABCD-EFGH@IJKL!MNOP';

		$this->assertTrue(
			(bool) preg_match( '/^PDF\$DEV#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i', $dev_key )
		);
	}

	/**
	 * Test license status options.
	 */
	public function test_license_status_values() {
		$valid_statuses = array( 'valid', 'invalid', 'expired', 'inactive', 'grace_period' );

		foreach ( $valid_statuses as $status ) {
			update_option( 'pdf_embed_seo_pro_plus_license_status', $status );
			$stored = get_option( 'pdf_embed_seo_pro_plus_license_status' );
			$this->assertEquals( $status, $stored );
		}
	}

	/**
	 * Test license expiration date storage.
	 */
	public function test_license_expiration_storage() {
		$expires = gmdate( 'Y-m-d', strtotime( '+1 year' ) );
		update_option( 'pdf_embed_seo_pro_plus_license_expires', $expires );

		$stored = get_option( 'pdf_embed_seo_pro_plus_license_expires' );
		$this->assertEquals( $expires, $stored );
	}

	/**
	 * Test grace period detection.
	 */
	public function test_grace_period_detection() {
		// Set license as expired 7 days ago (within 14-day grace period).
		$expired_date = gmdate( 'Y-m-d', strtotime( '-7 days' ) );
		update_option( 'pdf_embed_seo_pro_plus_license_expires', $expired_date );
		update_option( 'pdf_embed_seo_pro_plus_license_status', 'valid' );

		// Grace period is within 14 days after expiration.
		$expires_timestamp = strtotime( $expired_date );
		$grace_end = $expires_timestamp + ( 14 * DAY_IN_SECONDS );

		$this->assertTrue( time() < $grace_end );
	}

	/**
	 * Test grace period exceeded.
	 */
	public function test_grace_period_exceeded() {
		// Set license as expired 30 days ago (beyond 14-day grace period).
		$expired_date = gmdate( 'Y-m-d', strtotime( '-30 days' ) );
		update_option( 'pdf_embed_seo_pro_plus_license_expires', $expired_date );

		$expires_timestamp = strtotime( $expired_date );
		$grace_end = $expires_timestamp + ( 14 * DAY_IN_SECONDS );

		$this->assertTrue( time() > $grace_end );
	}

	/**
	 * Test license key sanitization.
	 */
	public function test_license_key_sanitization() {
		$dirty_key = '<script>PDF$PRO+#ABCD-EFGH@IJKL-MNOP!QRST</script>';
		$clean_key = sanitize_text_field( $dirty_key );

		$this->assertStringNotContainsString( '<script>', $clean_key );
	}

	/**
	 * Test Premium version requirement.
	 */
	public function test_premium_version_requirement() {
		// Pro+ requires Premium version 1.2.0 or higher.
		$min_version = '1.2.0';

		// This should pass with current Premium version.
		if ( defined( 'PDF_EMBED_SEO_PREMIUM_VERSION' ) ) {
			$this->assertTrue(
				version_compare( PDF_EMBED_SEO_PREMIUM_VERSION, $min_version, '>=' )
			);
		} else {
			$this->markTestSkipped( 'Premium version constant not defined.' );
		}
	}

	/**
	 * Test license status defaults.
	 */
	public function test_license_status_default() {
		delete_option( 'pdf_embed_seo_pro_plus_license_status' );
		delete_option( 'pdf_embed_seo_pro_plus_license_key' );

		$status = get_option( 'pdf_embed_seo_pro_plus_license_status', 'inactive' );
		$this->assertEquals( 'inactive', $status );
	}
}
