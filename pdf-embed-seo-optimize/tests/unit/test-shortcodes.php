<?php
/**
 * Unit tests for Shortcodes.
 *
 * @package PDF_Embed_SEO
 * @subpackage Tests
 */

/**
 * Test Shortcode functionality.
 */
class Test_PDF_Shortcodes extends WP_UnitTestCase {

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
			'post_type'   => 'pdf_document',
			'post_title'  => 'Shortcode Test PDF',
			'post_status' => 'publish',
		) );

		update_post_meta( $this->pdf_id, '_pdf_file_url', 'https://example.com/test.pdf' );
		update_post_meta( $this->pdf_id, '_pdf_allow_download', true );
		update_post_meta( $this->pdf_id, '_pdf_allow_print', true );
	}

	/**
	 * Test [pdf_viewer] shortcode is registered.
	 */
	public function test_pdf_viewer_shortcode_registered() {
		$this->assertTrue( shortcode_exists( 'pdf_viewer' ) );
	}

	/**
	 * Test [pdf_viewer_sitemap] shortcode is registered.
	 */
	public function test_pdf_viewer_sitemap_shortcode_registered() {
		$this->assertTrue( shortcode_exists( 'pdf_viewer_sitemap' ) );
	}

	/**
	 * Test [pdf_viewer] with ID attribute.
	 */
	public function test_pdf_viewer_with_id() {
		$output = do_shortcode( '[pdf_viewer id="' . $this->pdf_id . '"]' );

		$this->assertStringContainsString( 'pdf-viewer', $output );
		$this->assertStringContainsString( 'data-pdf-url', $output );
	}

	/**
	 * Test [pdf_viewer] with custom dimensions.
	 */
	public function test_pdf_viewer_with_dimensions() {
		$output = do_shortcode( '[pdf_viewer id="' . $this->pdf_id . '" width="600px" height="400px"]' );

		$this->assertStringContainsString( '600px', $output );
		$this->assertStringContainsString( '400px', $output );
	}

	/**
	 * Test [pdf_viewer] with invalid ID.
	 */
	public function test_pdf_viewer_invalid_id() {
		$output = do_shortcode( '[pdf_viewer id="999999"]' );

		// Should return empty or error message.
		$this->assertTrue( empty( $output ) || strpos( $output, 'error' ) !== false || strpos( $output, 'not found' ) !== false );
	}

	/**
	 * Test [pdf_viewer_sitemap] output.
	 */
	public function test_pdf_viewer_sitemap() {
		// Create additional documents.
		$this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Sitemap PDF 1',
			'post_status' => 'publish',
		) );
		$this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Sitemap PDF 2',
			'post_status' => 'publish',
		) );

		$output = do_shortcode( '[pdf_viewer_sitemap]' );

		$this->assertStringContainsString( 'Sitemap PDF 1', $output );
		$this->assertStringContainsString( 'Sitemap PDF 2', $output );
	}

	/**
	 * Test [pdf_viewer_sitemap] with limit.
	 */
	public function test_pdf_viewer_sitemap_with_limit() {
		// Create several documents.
		for ( $i = 0; $i < 10; $i++ ) {
			$this->factory->post->create( array(
				'post_type'   => 'pdf_document',
				'post_title'  => 'Limited PDF ' . $i,
				'post_status' => 'publish',
			) );
		}

		$output = do_shortcode( '[pdf_viewer_sitemap limit="3"]' );

		// Count occurrences of list items or links.
		preg_match_all( '/<li|<a href/', $output, $matches );
		$this->assertLessThanOrEqual( 6, count( $matches[0] ) ); // 3 items * 2 potential matches.
	}

	/**
	 * Test [pdf_viewer_sitemap] ordering.
	 */
	public function test_pdf_viewer_sitemap_ordering() {
		$this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'AAA First',
			'post_status' => 'publish',
		) );
		$this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'ZZZ Last',
			'post_status' => 'publish',
		) );

		$output = do_shortcode( '[pdf_viewer_sitemap orderby="title" order="ASC"]' );

		$pos_aaa = strpos( $output, 'AAA First' );
		$pos_zzz = strpos( $output, 'ZZZ Last' );

		$this->assertLessThan( $pos_zzz, $pos_aaa );
	}
}
