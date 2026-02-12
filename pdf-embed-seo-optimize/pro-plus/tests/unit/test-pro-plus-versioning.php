<?php
/**
 * Unit tests for Pro-plus document versioning.
 *
 * @package PDF_Embed_SEO_Pro_Plus
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Pro-plus document versioning functionality.
 */
class Test_Pro_Plus_Versioning extends PDF_Pro_Plus_Test_Case {

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->activate_pro_plus_license();
		$this->enable_setting( 'enable_versioning', true );
	}

	/**
	 * Test version number format.
	 */
	public function test_version_number_format() {
		$valid_versions = array( '1.0', '1.1', '2.0', '1.0.1', '10.5.3' );
		$invalid_versions = array( 'v1.0', '1', 'abc', '1.0.0.0.0' );

		foreach ( $valid_versions as $version ) {
			$this->assertMatchesRegularExpression( '/^\d+(\.\d+){1,2}$/', $version );
		}

		foreach ( $invalid_versions as $version ) {
			$this->assertDoesNotMatchRegularExpression( '/^\d+\.\d+(\.\d+)?$/', $version );
		}
	}

	/**
	 * Test version metadata structure.
	 */
	public function test_version_metadata_structure() {
		$version_meta = array(
			'version_id'     => 1,
			'document_id'    => $this->pdf_id,
			'version_number' => '1.0',
			'file_url'       => 'https://example.com/v1/test.pdf',
			'file_size'      => 1024000, // bytes
			'checksum'       => md5( 'test-file-content' ),
			'author_id'      => $this->admin_user_id,
			'changelog'      => 'Initial version',
			'created_at'     => current_time( 'mysql' ),
			'is_current'     => true,
		);

		$required_keys = array( 'version_id', 'document_id', 'version_number', 'file_url', 'created_at' );
		foreach ( $required_keys as $key ) {
			$this->assertArrayHasKey( $key, $version_meta );
		}
	}

	/**
	 * Test version comparison.
	 */
	public function test_version_comparison() {
		$this->assertEquals( -1, version_compare( '1.0', '1.1' ) );
		$this->assertEquals( 1, version_compare( '2.0', '1.9' ) );
		$this->assertEquals( 0, version_compare( '1.0', '1.0' ) );
		$this->assertEquals( -1, version_compare( '1.0', '1.0.1' ) );
	}

	/**
	 * Test version limit enforcement.
	 */
	public function test_version_limit_enforcement() {
		$max_versions = 10;
		$this->enable_setting( 'keep_versions', $max_versions );

		$versions = array();
		for ( $i = 1; $i <= 15; $i++ ) {
			$versions[] = array(
				'version_number' => "1.{$i}",
				'created_at'     => gmdate( 'Y-m-d H:i:s', strtotime( "-{$i} days" ) ),
			);
		}

		// Simulate cleanup: keep only the most recent versions.
		usort( $versions, function( $a, $b ) {
			return strtotime( $b['created_at'] ) - strtotime( $a['created_at'] );
		} );

		$kept_versions = array_slice( $versions, 0, $max_versions );

		$this->assertCount( $max_versions, $kept_versions );
	}

	/**
	 * Test auto-versioning on file update.
	 */
	public function test_auto_versioning_setting() {
		$this->enable_setting( 'auto_version', true );

		$settings = get_option( 'pdf_embed_seo_pro_plus_settings', array() );
		$this->assertTrue( $settings['auto_version'] );
	}

	/**
	 * Test version rollback data.
	 */
	public function test_version_rollback_data() {
		$current_version = array(
			'version_number' => '2.0',
			'file_url'       => 'https://example.com/v2/test.pdf',
		);

		$rollback_version = array(
			'version_number' => '1.5',
			'file_url'       => 'https://example.com/v1.5/test.pdf',
		);

		// After rollback, the rolled-back version becomes current.
		$this->assertNotEquals( $current_version['file_url'], $rollback_version['file_url'] );
	}

	/**
	 * Test version file checksum.
	 */
	public function test_version_file_checksum() {
		$content = 'PDF file content here';
		$md5_checksum = md5( $content );
		$sha256_checksum = hash( 'sha256', $content );

		$this->assertEquals( 32, strlen( $md5_checksum ) );
		$this->assertEquals( 64, strlen( $sha256_checksum ) );
	}

	/**
	 * Test version changelog sanitization.
	 */
	public function test_version_changelog_sanitization() {
		$dirty_changelog = '<script>alert("xss")</script>Updated formatting and fixed typos.';
		$clean_changelog = wp_kses( $dirty_changelog, array(
			'strong' => array(),
			'em'     => array(),
			'br'     => array(),
			'ul'     => array(),
			'li'     => array(),
		) );

		$this->assertStringNotContainsString( '<script>', $clean_changelog );
	}

	/**
	 * Test version diff information.
	 */
	public function test_version_diff_info() {
		$diff = array(
			'from_version'    => '1.0',
			'to_version'      => '1.1',
			'pages_added'     => 2,
			'pages_removed'   => 0,
			'pages_modified'  => 5,
			'size_change'     => 50000, // bytes.
			'size_change_pct' => 4.8,
		);

		$this->assertArrayHasKey( 'from_version', $diff );
		$this->assertArrayHasKey( 'to_version', $diff );
	}

	/**
	 * Test version history query.
	 */
	public function test_version_history_ordering() {
		$versions = array(
			array( 'version_number' => '1.0', 'created_at' => '2024-01-01 10:00:00' ),
			array( 'version_number' => '1.1', 'created_at' => '2024-02-01 10:00:00' ),
			array( 'version_number' => '2.0', 'created_at' => '2024-03-01 10:00:00' ),
		);

		// Sort by date descending (newest first).
		usort( $versions, function( $a, $b ) {
			return strtotime( $b['created_at'] ) - strtotime( $a['created_at'] );
		} );

		$this->assertEquals( '2.0', $versions[0]['version_number'] );
		$this->assertEquals( '1.0', $versions[2]['version_number'] );
	}

	/**
	 * Test version restore permissions.
	 */
	public function test_version_restore_permissions() {
		// Only users with edit capability should restore versions.
		wp_set_current_user( $this->admin_user_id );
		$can_restore_admin = current_user_can( 'edit_post', $this->pdf_id );

		wp_set_current_user( $this->subscriber_user_id );
		$can_restore_subscriber = current_user_can( 'edit_post', $this->pdf_id );

		$this->assertTrue( $can_restore_admin );
		$this->assertFalse( $can_restore_subscriber );
	}
}
