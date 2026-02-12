<?php
/**
 * Base test case class for Pro-plus tests.
 *
 * @package PDF_Embed_SEO_Pro_Plus
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base test case class with common utilities for Pro-plus testing.
 */
class PDF_Pro_Plus_Test_Case extends WP_UnitTestCase {

	/**
	 * Test PDF document ID.
	 *
	 * @var int
	 */
	protected $pdf_id;

	/**
	 * Test user ID (admin).
	 *
	 * @var int
	 */
	protected $admin_user_id;

	/**
	 * Test user ID (subscriber).
	 *
	 * @var int
	 */
	protected $subscriber_user_id;

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();

		// Create admin user.
		$this->admin_user_id = $this->factory->user->create( array(
			'role' => 'administrator',
		) );

		// Create subscriber user.
		$this->subscriber_user_id = $this->factory->user->create( array(
			'role' => 'subscriber',
		) );

		// Create test PDF document.
		$this->pdf_id = $this->factory->post->create( array(
			'post_type'    => 'pdf_document',
			'post_title'   => 'Test PDF Document',
			'post_content' => 'Test description',
			'post_status'  => 'publish',
			'post_author'  => $this->admin_user_id,
		) );

		// Set PDF file meta.
		update_post_meta( $this->pdf_id, '_pdf_file_url', 'https://example.com/test.pdf' );
		update_post_meta( $this->pdf_id, '_pdf_allow_download', true );
		update_post_meta( $this->pdf_id, '_pdf_allow_print', true );
	}

	/**
	 * Tear down test fixtures.
	 */
	public function tearDown(): void {
		parent::tearDown();

		// Clean up.
		wp_delete_post( $this->pdf_id, true );
		wp_delete_user( $this->admin_user_id );
		wp_delete_user( $this->subscriber_user_id );
	}

	/**
	 * Set Pro-plus license as valid.
	 */
	protected function activate_pro_plus_license() {
		update_option( 'pdf_embed_seo_pro_plus_license_key', 'PDF$PRO+#TEST-TEST@TEST-TEST!TEST' );
		update_option( 'pdf_embed_seo_pro_plus_license_status', 'valid' );
	}

	/**
	 * Set Pro-plus license as expired.
	 */
	protected function expire_pro_plus_license() {
		update_option( 'pdf_embed_seo_pro_plus_license_status', 'expired' );
		update_option( 'pdf_embed_seo_pro_plus_license_expires', gmdate( 'Y-m-d', strtotime( '-30 days' ) ) );
	}

	/**
	 * Enable a specific Pro-plus setting.
	 *
	 * @param string $setting Setting key.
	 * @param mixed  $value   Setting value.
	 */
	protected function enable_setting( $setting, $value = true ) {
		$settings = get_option( 'pdf_embed_seo_pro_plus_settings', array() );
		$settings[ $setting ] = $value;
		update_option( 'pdf_embed_seo_pro_plus_settings', $settings );
	}

	/**
	 * Create test webhook URL.
	 *
	 * @return string
	 */
	protected function get_test_webhook_url() {
		return 'https://example.com/webhook-receiver';
	}

	/**
	 * Create mock annotation data.
	 *
	 * @return array
	 */
	protected function get_mock_annotation() {
		return array(
			'type'      => 'highlight',
			'page'      => 1,
			'x'         => 100,
			'y'         => 200,
			'width'     => 200,
			'height'    => 50,
			'color'     => '#ffff00',
			'content'   => 'Test annotation',
			'author_id' => $this->admin_user_id,
			'created'   => current_time( 'mysql' ),
		);
	}
}
