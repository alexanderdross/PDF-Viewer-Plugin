<?php
/**
 * Unit tests for Premium Download Tracking feature.
 *
 * @package PDF_Embed_SEO_Premium
 * @subpackage Tests
 * @since 1.2.5
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Premium Download Tracking functionality.
 */
class Test_PDF_Premium_Download_Tracking extends WP_UnitTestCase {

	/**
	 * REST server instance.
	 *
	 * @var WP_REST_Server
	 */
	protected $server;

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

		global $wp_rest_server;
		$this->server = $wp_rest_server = new WP_REST_Server();
		do_action( 'rest_api_init' );

		$this->pdf_id = $this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Download Tracking Test PDF',
			'post_status' => 'publish',
		) );

		// Initialize download count to 0.
		update_post_meta( $this->pdf_id, '_pdf_download_count', 0 );
	}

	/**
	 * Tear down test fixtures.
	 */
	public function tearDown(): void {
		global $wp_rest_server;
		$wp_rest_server = null;
		parent::tearDown();
	}

	/**
	 * Test download endpoint exists.
	 */
	public function test_download_endpoint_exists() {
		$routes = $this->server->get_routes();

		if ( class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->assertArrayHasKey( '/pdf-embed-seo/v1/documents/(?P<id>[\d]+)/download', $routes );
		} else {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}
	}

	/**
	 * Test POST /documents/{id}/download increments counter.
	 */
	public function test_track_download_increments_count() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		$initial_count = (int) get_post_meta( $this->pdf_id, '_pdf_download_count', true );

		$request  = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/download' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertTrue( $data['success'] );
		$this->assertEquals( $initial_count + 1, $data['download_count'] );

		// Verify in database.
		$new_count = (int) get_post_meta( $this->pdf_id, '_pdf_download_count', true );
		$this->assertEquals( $initial_count + 1, $new_count );
	}

	/**
	 * Test download tracking response includes timestamp.
	 */
	public function test_track_download_includes_timestamp() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		$request  = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/download' );
		$response = $this->server->dispatch( $request );

		$data = $response->get_data();
		$this->assertArrayHasKey( 'timestamp', $data );
	}

	/**
	 * Test download tracking for non-existent document.
	 */
	public function test_track_download_invalid_id() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		$request  = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/99999/download' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );
	}

	/**
	 * Test download count is separate from view count.
	 */
	public function test_download_count_separate_from_views() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		// Set initial counts.
		update_post_meta( $this->pdf_id, '_pdf_view_count', 10 );
		update_post_meta( $this->pdf_id, '_pdf_download_count', 5 );

		// Track a download.
		$request  = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/download' );
		$this->server->dispatch( $request );

		// Verify view count unchanged.
		$view_count     = (int) get_post_meta( $this->pdf_id, '_pdf_view_count', true );
		$download_count = (int) get_post_meta( $this->pdf_id, '_pdf_download_count', true );

		$this->assertEquals( 10, $view_count );
		$this->assertEquals( 6, $download_count );
	}

	/**
	 * Test download tracking fires action hook.
	 */
	public function test_track_download_fires_action() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		$action_fired = false;
		$captured_id  = null;

		add_action( 'pdf_embed_seo_download_tracked', function( $post_id ) use ( &$action_fired, &$captured_id ) {
			$action_fired = true;
			$captured_id  = $post_id;
		} );

		$request = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/download' );
		$this->server->dispatch( $request );

		$this->assertTrue( $action_fired );
		$this->assertEquals( $this->pdf_id, $captured_id );
	}
}
