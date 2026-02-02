<?php
/**
 * Unit tests for Premium REST API.
 *
 * @package PDF_Embed_SEO_Premium
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Premium REST API functionality.
 */
class Test_PDF_Premium_REST_API extends WP_UnitTestCase {

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
	 * Admin user ID.
	 *
	 * @var int
	 */
	protected $admin_id;

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
			'post_title'  => 'Premium API Test PDF',
			'post_status' => 'publish',
		) );

		$this->admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
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
	 * Test premium analytics endpoint exists.
	 */
	public function test_analytics_endpoint_exists() {
		$routes = $this->server->get_routes();

		if ( class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->assertArrayHasKey( '/pdf-embed-seo/v1/analytics', $routes );
		} else {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}
	}

	/**
	 * Test GET /analytics requires authentication.
	 */
	public function test_analytics_requires_auth() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		wp_set_current_user( 0 ); // Anonymous.

		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/analytics' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test GET /analytics with admin user.
	 */
	public function test_analytics_with_admin() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		wp_set_current_user( $this->admin_id );

		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/analytics' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertArrayHasKey( 'total_views', $data );
		$this->assertArrayHasKey( 'period', $data );
	}

	/**
	 * Test GET /analytics with period parameter.
	 */
	public function test_analytics_with_period() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		wp_set_current_user( $this->admin_id );

		$request = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/analytics' );
		$request->set_param( 'period', '7days' );

		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( '7days', $data['period'] );
	}

	/**
	 * Test GET /documents/{id}/progress endpoint.
	 */
	public function test_get_progress_endpoint() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/progress' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertArrayHasKey( 'document_id', $data );
		$this->assertArrayHasKey( 'progress', $data );
	}

	/**
	 * Test POST /documents/{id}/progress endpoint.
	 */
	public function test_save_progress_endpoint() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		$user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/progress' );
		$request->set_body_params( array(
			'page'   => 10,
			'scroll' => 0.5,
			'zoom'   => 1.0,
		) );

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertTrue( $data['success'] );
	}

	/**
	 * Test POST /documents/{id}/verify-password endpoint.
	 */
	public function test_verify_password_endpoint() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		// Set up password protected PDF.
		update_post_meta( $this->pdf_id, '_pdf_password_protected', true );
		update_post_meta( $this->pdf_id, '_pdf_password', wp_hash_password( 'testpassword' ) );

		$request = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/verify-password' );
		$request->set_body_params( array(
			'password' => 'testpassword',
		) );

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertTrue( $data['success'] );
		$this->assertArrayHasKey( 'access_token', $data );
	}

	/**
	 * Test verify-password with wrong password.
	 */
	public function test_verify_password_wrong() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		// Set up password protected PDF.
		update_post_meta( $this->pdf_id, '_pdf_password_protected', true );
		update_post_meta( $this->pdf_id, '_pdf_password', wp_hash_password( 'correctpassword' ) );

		$request = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/verify-password' );
		$request->set_body_params( array(
			'password' => 'wrongpassword',
		) );

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 403, $response->get_status() );

		$data = $response->get_data();
		$this->assertFalse( $data['success'] );
	}

	/**
	 * Test GET /categories endpoint.
	 */
	public function test_get_categories_endpoint() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/categories' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertArrayHasKey( 'categories', $data );
	}

	/**
	 * Test GET /tags endpoint.
	 */
	public function test_get_tags_endpoint() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/tags' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertArrayHasKey( 'tags', $data );
	}

	/**
	 * Test settings includes is_premium flag.
	 */
	public function test_settings_shows_premium() {
		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/settings' );
		$response = $this->server->dispatch( $request );

		$data = $response->get_data();

		$this->assertArrayHasKey( 'is_premium', $data );
		if ( function_exists( 'pdf_embed_seo_is_premium' ) && pdf_embed_seo_is_premium() ) {
			$this->assertTrue( $data['is_premium'] );
		}
	}
}
