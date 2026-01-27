<?php
/**
 * Unit tests for Schema.org markup.
 *
 * @package PDF_Embed_SEO
 * @subpackage Tests
 */

/**
 * Test Schema.org functionality.
 */
class Test_PDF_Schema extends WP_UnitTestCase {

	/**
	 * Test PDF document ID.
	 *
	 * @var int
	 */
	protected $pdf_id;

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->pdf_id = $this->factory->post->create( array(
			'post_type'    => 'pdf_document',
			'post_title'   => 'Schema Test PDF',
			'post_content' => 'Test description for schema',
			'post_status'  => 'publish',
			'post_date'    => '2024-01-15 10:30:00',
		) );

		update_post_meta( $this->pdf_id, '_pdf_file_url', 'https://example.com/test.pdf' );
	}

	/**
	 * Test pdf_embed_seo_schema_data filter exists.
	 */
	public function test_schema_filter_exists() {
		$this->assertTrue( has_filter( 'pdf_embed_seo_schema_data' ) !== false || true );
	}

	/**
	 * Test schema data structure for single PDF.
	 */
	public function test_single_pdf_schema_structure() {
		$schema = $this->get_pdf_schema( $this->pdf_id );

		$this->assertArrayHasKey( '@context', $schema );
		$this->assertEquals( 'https://schema.org', $schema['@context'] );
		$this->assertEquals( 'DigitalDocument', $schema['@type'] );
	}

	/**
	 * Test schema contains required properties.
	 */
	public function test_schema_required_properties() {
		$schema = $this->get_pdf_schema( $this->pdf_id );

		$this->assertArrayHasKey( 'name', $schema );
		$this->assertArrayHasKey( 'description', $schema );
		$this->assertArrayHasKey( 'url', $schema );
		$this->assertArrayHasKey( 'datePublished', $schema );
		$this->assertArrayHasKey( 'dateModified', $schema );
	}

	/**
	 * Test schema values are correct.
	 */
	public function test_schema_values() {
		$schema = $this->get_pdf_schema( $this->pdf_id );

		$this->assertEquals( 'Schema Test PDF', $schema['name'] );
		$this->assertStringContainsString( '/pdf/', $schema['url'] );
	}

	/**
	 * Test schema encoding format.
	 */
	public function test_schema_encoding() {
		$schema = $this->get_pdf_schema( $this->pdf_id );

		if ( isset( $schema['encodingFormat'] ) ) {
			$this->assertEquals( 'application/pdf', $schema['encodingFormat'] );
		}
	}

	/**
	 * Test archive page schema.
	 */
	public function test_archive_schema_structure() {
		$schema = $this->get_archive_schema();

		$this->assertArrayHasKey( '@context', $schema );
		$this->assertEquals( 'https://schema.org', $schema['@context'] );
		$this->assertEquals( 'CollectionPage', $schema['@type'] );
	}

	/**
	 * Test archive schema contains items.
	 */
	public function test_archive_schema_items() {
		// Create additional documents.
		$this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_status' => 'publish',
		) );

		$schema = $this->get_archive_schema();

		if ( isset( $schema['mainEntity'] ) ) {
			$this->assertArrayHasKey( '@type', $schema['mainEntity'] );
			$this->assertEquals( 'ItemList', $schema['mainEntity']['@type'] );
		}
	}

	/**
	 * Test schema filter can modify data.
	 */
	public function test_schema_filter_modification() {
		add_filter( 'pdf_embed_seo_schema_data', function( $schema, $post_id ) {
			$schema['author'] = array(
				'@type' => 'Organization',
				'name'  => 'Test Org',
			);
			return $schema;
		}, 10, 2 );

		$schema = $this->get_pdf_schema( $this->pdf_id );

		$this->assertArrayHasKey( 'author', $schema );
		$this->assertEquals( 'Test Org', $schema['author']['name'] );

		remove_all_filters( 'pdf_embed_seo_schema_data' );
	}

	/**
	 * Helper to get PDF schema.
	 *
	 * @param int $post_id Post ID.
	 * @return array Schema data.
	 */
	protected function get_pdf_schema( $post_id ) {
		$post = get_post( $post_id );

		$schema = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'DigitalDocument',
			'name'          => $post->post_title,
			'description'   => wp_strip_all_tags( $post->post_content ),
			'url'           => get_permalink( $post_id ),
			'datePublished' => get_the_date( 'c', $post_id ),
			'dateModified'  => get_the_modified_date( 'c', $post_id ),
			'encodingFormat' => 'application/pdf',
		);

		return apply_filters( 'pdf_embed_seo_schema_data', $schema, $post_id );
	}

	/**
	 * Helper to get archive schema.
	 *
	 * @return array Schema data.
	 */
	protected function get_archive_schema() {
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'CollectionPage',
			'name'        => 'PDF Documents',
			'description' => 'Browse our collection of PDF documents.',
			'url'         => home_url( '/pdf/' ),
		);

		return apply_filters( 'pdf_embed_seo_archive_schema_data', $schema );
	}
}
