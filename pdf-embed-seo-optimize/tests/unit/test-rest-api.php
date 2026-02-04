<?php
/**
 * Unit tests for REST API endpoints.
 *
 * @package PDF_Embed_SEO
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test REST API functionality.
 */
class Test_PDF_REST_API extends WP_UnitTestCase {

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

		// Create a test PDF document.
		$this->pdf_id = $this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Test API PDF',
			'post_status' => 'publish',
			'post_name'   => 'test-api-pdf',
		) );

		update_post_meta( $this->pdf_id, '_pdf_allow_download', true );
		update_post_meta( $this->pdf_id, '_pdf_allow_print', true );
		update_post_meta( $this->pdf_id, '_pdf_view_count', 50 );
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
	 * Test REST routes are registered.
	 */
	public function test_routes_registered() {
		$routes = $this->server->get_routes();

		$this->assertArrayHasKey( '/pdf-embed-seo/v1', $routes );
		$this->assertArrayHasKey( '/pdf-embed-seo/v1/documents', $routes );
		$this->assertArrayHasKey( '/pdf-embed-seo/v1/documents/(?P<id>[\d]+)', $routes );
		$this->assertArrayHasKey( '/pdf-embed-seo/v1/settings', $routes );
	}

	/**
	 * Test GET /documents endpoint.
	 */
	public function test_get_documents() {
		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertArrayHasKey( 'documents', $data );
		$this->assertArrayHasKey( 'total', $data );
		$this->assertArrayHasKey( 'pages', $data );
		$this->assertGreaterThanOrEqual( 1, count( $data['documents'] ) );
	}

	/**
	 * Test GET /documents with pagination.
	 */
	public function test_get_documents_pagination() {
		// Create additional documents.
		for ( $i = 0; $i < 15; $i++ ) {
			$this->factory->post->create( array(
				'post_type'   => 'pdf_document',
				'post_title'  => 'Test PDF ' . $i,
				'post_status' => 'publish',
			) );
		}

		$request = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents' );
		$request->set_param( 'per_page', 5 );
		$request->set_param( 'page', 1 );

		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 5, count( $data['documents'] ) );
		$this->assertGreaterThan( 1, $data['pages'] );
	}

	/**
	 * Test GET /documents/{id} endpoint.
	 */
	public function test_get_single_document() {
		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $this->pdf_id );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertEquals( $this->pdf_id, $data['id'] );
		$this->assertEquals( 'Test API PDF', $data['title'] );
		$this->assertEquals( 'test-api-pdf', $data['slug'] );
		$this->assertTrue( $data['allow_download'] );
		$this->assertTrue( $data['allow_print'] );
	}

	/**
	 * Test GET /documents/{id} with invalid ID.
	 */
	public function test_get_single_document_invalid_id() {
		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/999999' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );
	}

	/**
	 * Test GET /documents/{id}/data endpoint.
	 */
	public function test_get_document_data() {
		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/data' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertEquals( $this->pdf_id, $data['id'] );
		$this->assertArrayHasKey( 'pdf_url', $data );
		$this->assertArrayHasKey( 'allow_download', $data );
		$this->assertArrayHasKey( 'allow_print', $data );
	}

	/**
	 * Test POST /documents/{id}/view endpoint.
	 */
	public function test_track_document_view() {
		$initial_count = (int) get_post_meta( $this->pdf_id, '_pdf_view_count', true );

		$request  = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/view' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertTrue( $data['success'] );
		$this->assertEquals( $initial_count + 1, $data['views'] );

		// Verify meta was updated.
		$new_count = (int) get_post_meta( $this->pdf_id, '_pdf_view_count', true );
		$this->assertEquals( $initial_count + 1, $new_count );
	}

	/**
	 * Test GET /settings endpoint.
	 */
	public function test_get_settings() {
		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/settings' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertArrayHasKey( 'viewer_theme', $data );
		$this->assertArrayHasKey( 'default_allow_download', $data );
		$this->assertArrayHasKey( 'default_allow_print', $data );
		$this->assertArrayHasKey( 'archive_url', $data );
		$this->assertArrayHasKey( 'is_premium', $data );
	}

	/**
	 * Test documents sorting.
	 */
	public function test_documents_sorting() {
		// Create documents with different titles.
		$this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Alpha Document',
			'post_status' => 'publish',
		) );
		$this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Zebra Document',
			'post_status' => 'publish',
		) );

		// Test ascending order by title.
		$request = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents' );
		$request->set_param( 'orderby', 'title' );
		$request->set_param( 'order', 'asc' );

		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$titles = array_column( $data['documents'], 'title' );
		$sorted = $titles;
		sort( $sorted );

		$this->assertEquals( $sorted, $titles );
	}

	/**
	 * Test documents search.
	 */
	public function test_documents_search() {
		$this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Unique Searchable Title',
			'post_status' => 'publish',
		) );

		$request = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents' );
		$request->set_param( 'search', 'Unique Searchable' );

		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 1, count( $data['documents'] ) );
		$this->assertStringContainsString( 'Unique Searchable', $data['documents'][0]['title'] );
	}

	/**
	 * Test unpublished documents are not returned.
	 */
	public function test_unpublished_documents_hidden() {
		$draft_id = $this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Draft Document',
			'post_status' => 'draft',
		) );

		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $draft_id );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );
	}
}
