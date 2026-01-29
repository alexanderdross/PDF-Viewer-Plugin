<?php
/**
 * Unit tests for PDF Document Post Type.
 *
 * @package PDF_Embed_SEO
 * @subpackage Tests
 */

/**
 * Test PDF Document Post Type functionality.
 */
class Test_PDF_Post_Type extends WP_UnitTestCase {

	/**
	 * Test post type registration.
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'pdf_document' ) );
	}

	/**
	 * Test post type labels.
	 */
	public function test_post_type_labels() {
		$post_type = get_post_type_object( 'pdf_document' );

		$this->assertEquals( 'PDF Documents', $post_type->labels->name );
		$this->assertEquals( 'PDF Document', $post_type->labels->singular_name );
		$this->assertEquals( 'Add New', $post_type->labels->add_new );
		$this->assertEquals( 'Add New PDF Document', $post_type->labels->add_new_item );
	}

	/**
	 * Test post type supports.
	 */
	public function test_post_type_supports() {
		$this->assertTrue( post_type_supports( 'pdf_document', 'title' ) );
		$this->assertTrue( post_type_supports( 'pdf_document', 'editor' ) );
		$this->assertTrue( post_type_supports( 'pdf_document', 'thumbnail' ) );
		$this->assertTrue( post_type_supports( 'pdf_document', 'excerpt' ) );
	}

	/**
	 * Test post type is public.
	 */
	public function test_post_type_is_public() {
		$post_type = get_post_type_object( 'pdf_document' );
		$this->assertTrue( $post_type->public );
	}

	/**
	 * Test post type has archive.
	 */
	public function test_post_type_has_archive() {
		$post_type = get_post_type_object( 'pdf_document' );
		$this->assertEquals( 'pdf', $post_type->has_archive );
	}

	/**
	 * Test post type rewrite slug.
	 */
	public function test_post_type_rewrite_slug() {
		$post_type = get_post_type_object( 'pdf_document' );
		$this->assertEquals( 'pdf', $post_type->rewrite['slug'] );
	}

	/**
	 * Test creating a PDF document.
	 */
	public function test_create_pdf_document() {
		$post_id = $this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Test PDF Document',
			'post_status' => 'publish',
		) );

		$this->assertIsInt( $post_id );
		$this->assertGreaterThan( 0, $post_id );

		$post = get_post( $post_id );
		$this->assertEquals( 'pdf_document', $post->post_type );
		$this->assertEquals( 'Test PDF Document', $post->post_title );
	}

	/**
	 * Test PDF meta fields.
	 */
	public function test_pdf_meta_fields() {
		$post_id = $this->factory->post->create( array(
			'post_type' => 'pdf_document',
		) );

		// Test setting meta.
		update_post_meta( $post_id, '_pdf_allow_download', true );
		update_post_meta( $post_id, '_pdf_allow_print', false );
		update_post_meta( $post_id, '_pdf_view_count', 100 );

		// Test getting meta.
		$this->assertTrue( (bool) get_post_meta( $post_id, '_pdf_allow_download', true ) );
		$this->assertFalse( (bool) get_post_meta( $post_id, '_pdf_allow_print', true ) );
		$this->assertEquals( 100, (int) get_post_meta( $post_id, '_pdf_view_count', true ) );
	}

	/**
	 * Test PDF document URL structure.
	 */
	public function test_pdf_document_url() {
		$post_id = $this->factory->post->create( array(
			'post_type'  => 'pdf_document',
			'post_title' => 'Test PDF URL',
			'post_name'  => 'test-pdf-url',
		) );

		$permalink = get_permalink( $post_id );
		$this->assertStringContainsString( '/pdf/', $permalink );
		$this->assertStringContainsString( 'test-pdf-url', $permalink );
	}
}
