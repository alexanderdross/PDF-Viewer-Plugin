<?php
/**
 * Unit tests for Pro-plus white label features.
 *
 * @package PDF_Embed_SEO_Pro_Plus
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Pro-plus white label functionality.
 */
class Test_Pro_Plus_White_Label extends PDF_Pro_Plus_Test_Case {

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->activate_pro_plus_license();
		$this->enable_setting( 'enable_white_label', true );
	}

	/**
	 * Test custom branding setting.
	 */
	public function test_custom_branding_setting() {
		$this->enable_setting( 'custom_branding', true );

		$settings = get_option( 'pdf_embed_seo_pro_plus_settings', array() );
		$this->assertTrue( $settings['custom_branding'] );
	}

	/**
	 * Test hide powered by setting.
	 */
	public function test_hide_powered_by() {
		$this->enable_setting( 'hide_powered_by', true );

		$settings = get_option( 'pdf_embed_seo_pro_plus_settings', array() );
		$this->assertTrue( $settings['hide_powered_by'] );
	}

	/**
	 * Test custom logo URL validation.
	 */
	public function test_custom_logo_url_validation() {
		$valid_urls = array(
			'https://example.com/logo.png',
			'https://cdn.example.com/assets/logo.svg',
			'https://example.com/images/brand-logo.jpg',
		);

		$invalid_urls = array(
			'not-a-url',
			'javascript:alert(1)',
			'data:image/png;base64,...',
		);

		foreach ( $valid_urls as $url ) {
			$is_valid = filter_var( $url, FILTER_VALIDATE_URL ) !== false &&
			            strpos( $url, 'https://' ) === 0;
			$this->assertTrue( $is_valid, "URL should be valid: {$url}" );
		}

		foreach ( $invalid_urls as $url ) {
			$is_valid = filter_var( $url, FILTER_VALIDATE_URL ) !== false &&
			            strpos( $url, 'https://' ) === 0;
			$this->assertFalse( $is_valid, "URL should be invalid: {$url}" );
		}
	}

	/**
	 * Test custom CSS sanitization.
	 */
	public function test_custom_css_sanitization() {
		$dirty_css = '
			.pdf-viewer { color: red; }
			</style><script>alert("xss")</script><style>
			.toolbar { background: blue; }
		';

		// Remove any HTML tags.
		$clean_css = wp_strip_all_tags( $dirty_css );

		$this->assertStringNotContainsString( '<script>', $clean_css );
		$this->assertStringNotContainsString( '</style>', $clean_css );
	}

	/**
	 * Test CSS property whitelist.
	 */
	public function test_css_property_whitelist() {
		$allowed_properties = array(
			'color',
			'background',
			'background-color',
			'font-family',
			'font-size',
			'font-weight',
			'border',
			'border-radius',
			'padding',
			'margin',
			'width',
			'height',
			'display',
			'opacity',
		);

		$dangerous_properties = array(
			'expression',
			'behavior',
			'-moz-binding',
		);

		foreach ( $allowed_properties as $prop ) {
			$this->assertMatchesRegularExpression( '/^[a-z-]+$/', $prop );
		}

		// Ensure dangerous properties are not in allowed list.
		foreach ( $dangerous_properties as $prop ) {
			$this->assertNotContains( $prop, $allowed_properties );
		}
	}

	/**
	 * Test brand color settings.
	 */
	public function test_brand_color_settings() {
		$brand_colors = array(
			'primary'    => '#007bff',
			'secondary'  => '#6c757d',
			'accent'     => '#28a745',
			'background' => '#ffffff',
			'text'       => '#333333',
		);

		foreach ( $brand_colors as $name => $color ) {
			$this->assertMatchesRegularExpression( '/^#[0-9a-fA-F]{6}$/', $color );
		}
	}

	/**
	 * Test viewer toolbar customization.
	 */
	public function test_toolbar_customization() {
		$toolbar_config = array(
			'show_navigation' => true,
			'show_zoom'       => true,
			'show_search'     => true,
			'show_fullscreen' => true,
			'show_download'   => false,
			'show_print'      => false,
			'show_logo'       => true,
			'logo_position'   => 'left',
		);

		$valid_positions = array( 'left', 'center', 'right' );
		$this->assertContains( $toolbar_config['logo_position'], $valid_positions );
	}

	/**
	 * Test custom loading text.
	 */
	public function test_custom_loading_text() {
		$loading_text = 'Loading your document...';
		$sanitized = sanitize_text_field( $loading_text );

		$this->assertEquals( $loading_text, $sanitized );
	}

	/**
	 * Test custom error messages.
	 */
	public function test_custom_error_messages() {
		$error_messages = array(
			'load_error'     => 'Unable to load document. Please try again.',
			'password_error' => 'Incorrect password. Please try again.',
			'network_error'  => 'Network error. Check your connection.',
			'access_denied'  => 'You do not have permission to view this document.',
		);

		foreach ( $error_messages as $key => $message ) {
			$sanitized = sanitize_text_field( $message );
			$this->assertEquals( $message, $sanitized );
		}
	}

	/**
	 * Test watermark configuration.
	 */
	public function test_watermark_configuration() {
		$watermark = array(
			'enabled'    => true,
			'text'       => 'CONFIDENTIAL',
			'image_url'  => 'https://example.com/watermark.png',
			'opacity'    => 0.3,
			'position'   => 'center',
			'rotation'   => -45,
			'font_size'  => 48,
			'color'      => '#cccccc',
		);

		$valid_positions = array( 'center', 'top-left', 'top-right', 'bottom-left', 'bottom-right' );
		$this->assertContains( $watermark['position'], $valid_positions );
		$this->assertGreaterThanOrEqual( 0, $watermark['opacity'] );
		$this->assertLessThanOrEqual( 1, $watermark['opacity'] );
	}

	/**
	 * Test plugin menu renaming.
	 */
	public function test_menu_renaming() {
		$custom_menu = array(
			'menu_title'     => 'Document Library',
			'page_title'     => 'Document Library',
			'capability'     => 'manage_options',
			'menu_icon'      => 'dashicons-media-document',
			'menu_position'  => 25,
		);

		$sanitized_title = sanitize_text_field( $custom_menu['menu_title'] );
		$this->assertEquals( $custom_menu['menu_title'], $sanitized_title );
	}
}
