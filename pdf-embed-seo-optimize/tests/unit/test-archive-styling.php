<?php
/**
 * Unit tests for Archive Styling Settings (v1.2.7).
 *
 * Tests for custom heading, alignment, colors, and REST API viewer loading.
 *
 * @package PDF_Embed_SEO
 * @subpackage Tests
 * @since 1.2.7
 */

/**
 * Test Archive Styling Settings functionality.
 */
class Test_Archive_Styling extends WP_UnitTestCase {

	/**
	 * Test PDF document ID.
	 *
	 * @var int
	 */
	protected $pdf_id;

	/**
	 * Original settings.
	 *
	 * @var array
	 */
	protected $original_settings;

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();

		// Store original settings.
		$this->original_settings = get_option( 'pdf_embed_seo_settings', array() );

		// Create a test PDF document.
		$this->pdf_id = $this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Test PDF for Styling',
			'post_status' => 'publish',
			'post_name'   => 'test-styling-pdf',
		) );
	}

	/**
	 * Tear down test fixtures.
	 */
	public function tearDown(): void {
		// Restore original settings.
		update_option( 'pdf_embed_seo_settings', $this->original_settings );
		parent::tearDown();
	}

	/**
	 * Test default archive heading value.
	 */
	public function test_default_archive_heading() {
		$settings = PDF_Embed_SEO::get_setting();

		// When archive_heading is empty, default should be used.
		$heading = isset( $settings['archive_heading'] ) && ! empty( $settings['archive_heading'] )
			? $settings['archive_heading']
			: 'PDF Documents';

		$this->assertEquals( 'PDF Documents', $heading );
	}

	/**
	 * Test custom archive heading is saved and retrieved.
	 */
	public function test_custom_archive_heading_saved() {
		$settings = array(
			'archive_heading' => 'My Custom PDF Library',
		);
		update_option( 'pdf_embed_seo_settings', $settings );

		$saved_settings = get_option( 'pdf_embed_seo_settings' );
		$this->assertEquals( 'My Custom PDF Library', $saved_settings['archive_heading'] );
	}

	/**
	 * Test archive heading sanitization strips HTML.
	 */
	public function test_archive_heading_sanitization() {
		$admin = new PDF_Embed_SEO_Admin();

		$input = array(
			'archive_heading' => '<script>alert("xss")</script>My PDFs',
		);

		$sanitized = $admin->sanitize_settings( $input );

		// sanitize_text_field should strip script tags.
		$this->assertStringNotContainsString( '<script>', $sanitized['archive_heading'] );
		$this->assertStringContainsString( 'My PDFs', $sanitized['archive_heading'] );
	}

	/**
	 * Test default heading alignment is center.
	 */
	public function test_default_heading_alignment_is_center() {
		$admin = new PDF_Embed_SEO_Admin();

		$input = array(); // Empty input.
		$sanitized = $admin->sanitize_settings( $input );

		$this->assertEquals( 'center', $sanitized['archive_heading_alignment'] );
	}

	/**
	 * Test heading alignment accepts valid values.
	 */
	public function test_heading_alignment_valid_values() {
		$admin = new PDF_Embed_SEO_Admin();

		$valid_alignments = array( 'left', 'center', 'right' );

		foreach ( $valid_alignments as $alignment ) {
			$input = array( 'archive_heading_alignment' => $alignment );
			$sanitized = $admin->sanitize_settings( $input );
			$this->assertEquals( $alignment, $sanitized['archive_heading_alignment'] );
		}
	}

	/**
	 * Test heading alignment rejects invalid values.
	 */
	public function test_heading_alignment_invalid_value() {
		$admin = new PDF_Embed_SEO_Admin();

		$input = array( 'archive_heading_alignment' => 'invalid_alignment' );
		$sanitized = $admin->sanitize_settings( $input );

		// Should fall back to default 'center'.
		$this->assertEquals( 'center', $sanitized['archive_heading_alignment'] );
	}

	/**
	 * Test font color sanitization.
	 */
	public function test_font_color_sanitization() {
		$admin = new PDF_Embed_SEO_Admin();

		// Valid hex color.
		$input = array( 'archive_font_color' => '#ff0000' );
		$sanitized = $admin->sanitize_settings( $input );
		$this->assertEquals( '#ff0000', $sanitized['archive_font_color'] );

		// Invalid color should be sanitized.
		$input = array( 'archive_font_color' => 'not-a-color' );
		$sanitized = $admin->sanitize_settings( $input );
		$this->assertEmpty( $sanitized['archive_font_color'] );
	}

	/**
	 * Test background color sanitization.
	 */
	public function test_background_color_sanitization() {
		$admin = new PDF_Embed_SEO_Admin();

		// Valid hex color.
		$input = array( 'archive_background_color' => '#f0f0f0' );
		$sanitized = $admin->sanitize_settings( $input );
		$this->assertEquals( '#f0f0f0', $sanitized['archive_background_color'] );

		// Invalid color should be sanitized.
		$input = array( 'archive_background_color' => 'invalid' );
		$sanitized = $admin->sanitize_settings( $input );
		$this->assertEmpty( $sanitized['archive_background_color'] );
	}

	/**
	 * Test empty color uses theme default.
	 */
	public function test_empty_color_uses_default() {
		$admin = new PDF_Embed_SEO_Admin();

		$input = array(
			'archive_font_color' => '',
			'archive_background_color' => '',
		);
		$sanitized = $admin->sanitize_settings( $input );

		$this->assertEmpty( $sanitized['archive_font_color'] );
		$this->assertEmpty( $sanitized['archive_background_color'] );
	}

	/**
	 * Test archive heading filter works.
	 */
	public function test_archive_heading_filter() {
		$settings = array(
			'archive_heading' => 'Custom Heading',
		);
		update_option( 'pdf_embed_seo_settings', $settings );

		// Test the filter.
		$filtered = apply_filters( 'pdf_embed_seo_archive_title', 'Custom Heading' );
		$this->assertEquals( 'Custom Heading', $filtered );

		// Test filter override.
		add_filter( 'pdf_embed_seo_archive_title', function() {
			return 'Filter Override';
		} );

		$filtered = apply_filters( 'pdf_embed_seo_archive_title', 'Custom Heading' );
		$this->assertEquals( 'Filter Override', $filtered );
	}

	/**
	 * Test REST API endpoint URL format.
	 */
	public function test_rest_api_url_format() {
		$rest_url = rest_url( 'pdf-embed-seo/v1/documents/' );

		// Should contain the correct namespace.
		$this->assertStringContainsString( 'pdf-embed-seo/v1/documents/', $rest_url );
	}

	/**
	 * Test REST API data endpoint returns PDF URL.
	 */
	public function test_rest_api_data_endpoint() {
		global $wp_rest_server;
		$server = $wp_rest_server = new WP_REST_Server();
		do_action( 'rest_api_init' );

		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/data' );
		$response = $server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertArrayHasKey( 'id', $data );
		$this->assertArrayHasKey( 'pdf_url', $data );
		$this->assertArrayHasKey( 'allow_download', $data );
		$this->assertArrayHasKey( 'allow_print', $data );
		$this->assertEquals( $this->pdf_id, $data['id'] );
	}

	/**
	 * Test REST API data endpoint does not require nonce.
	 */
	public function test_rest_api_no_nonce_required() {
		global $wp_rest_server;
		$server = $wp_rest_server = new WP_REST_Server();
		do_action( 'rest_api_init' );

		// Request without any authentication.
		wp_set_current_user( 0 ); // Logged out user.

		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/data' );
		$response = $server->dispatch( $request );

		// Should still work without nonce (public endpoint).
		$this->assertEquals( 200, $response->get_status() );
	}

	/**
	 * Test REST API data endpoint for unpublished PDF.
	 */
	public function test_rest_api_unpublished_pdf() {
		global $wp_rest_server;
		$server = $wp_rest_server = new WP_REST_Server();
		do_action( 'rest_api_init' );

		// Create draft PDF.
		$draft_id = $this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Draft PDF',
			'post_status' => 'draft',
		) );

		wp_set_current_user( 0 ); // Logged out user.

		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $draft_id . '/data' );
		$response = $server->dispatch( $request );

		// Should return 403 for unpublished content.
		$this->assertEquals( 403, $response->get_status() );
	}

	/**
	 * Test settings page has archive styling fields registered.
	 */
	public function test_archive_styling_fields_registered() {
		$admin = new PDF_Embed_SEO_Admin();
		$admin->register_settings();

		global $wp_settings_fields;

		$section_fields = isset( $wp_settings_fields['pdf-embed-seo-optimize-settings']['pdf_embed_seo_archive'] )
			? $wp_settings_fields['pdf-embed-seo-optimize-settings']['pdf_embed_seo_archive']
			: array();

		// Check that archive styling fields exist.
		$this->assertArrayHasKey( 'archive_heading', $section_fields );
		$this->assertArrayHasKey( 'archive_heading_alignment', $section_fields );
		$this->assertArrayHasKey( 'archive_font_color', $section_fields );
		$this->assertArrayHasKey( 'archive_background_color', $section_fields );
	}

	/**
	 * Test complete settings sanitization with all archive styling fields.
	 */
	public function test_complete_archive_styling_sanitization() {
		$admin = new PDF_Embed_SEO_Admin();

		$input = array(
			'archive_heading'           => 'My PDF Library',
			'archive_heading_alignment' => 'left',
			'archive_font_color'        => '#333333',
			'archive_background_color'  => '#ffffff',
		);

		$sanitized = $admin->sanitize_settings( $input );

		$this->assertEquals( 'My PDF Library', $sanitized['archive_heading'] );
		$this->assertEquals( 'left', $sanitized['archive_heading_alignment'] );
		$this->assertEquals( '#333333', $sanitized['archive_font_color'] );
		$this->assertEquals( '#ffffff', $sanitized['archive_background_color'] );
	}
}
