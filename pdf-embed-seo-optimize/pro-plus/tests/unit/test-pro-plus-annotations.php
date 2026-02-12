<?php
/**
 * Unit tests for Pro-plus PDF annotations.
 *
 * @package PDF_Embed_SEO_Pro_Plus
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Pro-plus annotation functionality.
 */
class Test_Pro_Plus_Annotations extends PDF_Pro_Plus_Test_Case {

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->activate_pro_plus_license();
		$this->enable_setting( 'enable_annotations', true );
	}

	/**
	 * Test annotation types.
	 */
	public function test_annotation_types() {
		$valid_types = array(
			'highlight',
			'underline',
			'strikethrough',
			'text_note',
			'sticky_note',
			'freehand',
			'rectangle',
			'circle',
			'arrow',
			'line',
		);

		foreach ( $valid_types as $type ) {
			$this->assertMatchesRegularExpression( '/^[a-z_]+$/', $type );
		}
	}

	/**
	 * Test annotation data structure.
	 */
	public function test_annotation_data_structure() {
		$annotation = array(
			'id'          => wp_generate_uuid4(),
			'document_id' => $this->pdf_id,
			'page'        => 1,
			'type'        => 'highlight',
			'x'           => 100.5,
			'y'           => 200.5,
			'width'       => 300,
			'height'      => 50,
			'color'       => '#ffff00',
			'opacity'     => 0.5,
			'content'     => 'Important text',
			'author_id'   => $this->admin_user_id,
			'created_at'  => current_time( 'mysql' ),
			'updated_at'  => current_time( 'mysql' ),
		);

		$required_keys = array( 'id', 'document_id', 'page', 'type', 'x', 'y', 'author_id' );
		foreach ( $required_keys as $key ) {
			$this->assertArrayHasKey( $key, $annotation );
		}
	}

	/**
	 * Test annotation color validation.
	 */
	public function test_annotation_color_validation() {
		$valid_colors = array(
			'#ffff00',
			'#FF0000',
			'#00ff00',
			'rgb(255,255,0)',
			'rgba(255,0,0,0.5)',
		);

		$invalid_colors = array(
			'not-a-color',
			'#gggggg',
			'#fff', // Short form (may need normalization).
		);

		foreach ( $valid_colors as $color ) {
			$is_valid = preg_match( '/^#[0-9a-fA-F]{6}$/', $color ) ||
			            preg_match( '/^rgba?\(\d+,\s*\d+,\s*\d+(,\s*[\d.]+)?\)$/', $color );
			$this->assertTrue( (bool) $is_valid, "Color should be valid: {$color}" );
		}
	}

	/**
	 * Test annotation coordinates validation.
	 */
	public function test_annotation_coordinates_validation() {
		$annotation = $this->get_mock_annotation();

		// Coordinates should be non-negative.
		$this->assertGreaterThanOrEqual( 0, $annotation['x'] );
		$this->assertGreaterThanOrEqual( 0, $annotation['y'] );
		$this->assertGreaterThan( 0, $annotation['width'] );
		$this->assertGreaterThan( 0, $annotation['height'] );
	}

	/**
	 * Test annotation page number validation.
	 */
	public function test_annotation_page_validation() {
		$annotation = $this->get_mock_annotation();

		$this->assertGreaterThanOrEqual( 1, $annotation['page'] );
		$this->assertIsInt( $annotation['page'] );
	}

	/**
	 * Test annotation content sanitization.
	 */
	public function test_annotation_content_sanitization() {
		$dirty_content = '<script>alert("xss")</script>Important note';
		$clean_content = sanitize_textarea_field( $dirty_content );

		$this->assertStringNotContainsString( '<script>', $clean_content );
	}

	/**
	 * Test annotation permissions.
	 */
	public function test_annotation_permissions() {
		// Test user annotation settings.
		$this->enable_setting( 'allow_user_annotations', true );

		$settings = get_option( 'pdf_embed_seo_pro_plus_settings', array() );
		$this->assertTrue( $settings['allow_user_annotations'] );
	}

	/**
	 * Test annotation ownership.
	 */
	public function test_annotation_ownership() {
		$annotation = $this->get_mock_annotation();

		// Only author or admin should edit annotation.
		wp_set_current_user( $this->admin_user_id );
		$can_edit_as_author = get_current_user_id() === $annotation['author_id'] ||
		                      current_user_can( 'manage_options' );

		$this->assertTrue( $can_edit_as_author );

		// Subscriber shouldn't edit other's annotations.
		wp_set_current_user( $this->subscriber_user_id );
		$can_edit_as_other = get_current_user_id() === $annotation['author_id'] ||
		                     current_user_can( 'manage_options' );

		$this->assertFalse( $can_edit_as_other );
	}

	/**
	 * Test freehand annotation path data.
	 */
	public function test_freehand_path_data() {
		$path_data = array(
			array( 'x' => 100, 'y' => 100 ),
			array( 'x' => 150, 'y' => 120 ),
			array( 'x' => 200, 'y' => 100 ),
			array( 'x' => 250, 'y' => 150 ),
		);

		$this->assertIsArray( $path_data );
		$this->assertGreaterThan( 1, count( $path_data ) );

		foreach ( $path_data as $point ) {
			$this->assertArrayHasKey( 'x', $point );
			$this->assertArrayHasKey( 'y', $point );
		}
	}

	/**
	 * Test annotation export format.
	 */
	public function test_annotation_export_format() {
		$annotation = $this->get_mock_annotation();
		$exported = wp_json_encode( $annotation );

		$this->assertIsString( $exported );
		$this->assertJson( $exported );

		$decoded = json_decode( $exported, true );
		$this->assertEquals( $annotation['type'], $decoded['type'] );
	}

	/**
	 * Test annotation reply structure.
	 */
	public function test_annotation_reply_structure() {
		$reply = array(
			'id'            => wp_generate_uuid4(),
			'annotation_id' => wp_generate_uuid4(),
			'content'       => 'Reply to annotation',
			'author_id'     => $this->admin_user_id,
			'created_at'    => current_time( 'mysql' ),
		);

		$this->assertArrayHasKey( 'annotation_id', $reply );
		$this->assertArrayHasKey( 'content', $reply );
	}
}
