<?php
/**
 * Unit tests for Premium Expiring Access Links feature.
 *
 * @package PDF_Embed_SEO_Premium
 * @subpackage Tests
 * @since 1.2.5
 */

/**
 * Test Premium Expiring Access Links functionality.
 */
class Test_PDF_Premium_Expiring_Links extends WP_UnitTestCase {

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
	 * Subscriber user ID.
	 *
	 * @var int
	 */
	protected $subscriber_id;

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
			'post_title'  => 'Expiring Links Test PDF',
			'post_status' => 'publish',
		) );

		$this->admin_id      = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$this->subscriber_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
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
	 * Test expiring link generation endpoint exists.
	 */
	public function test_generate_endpoint_exists() {
		$routes = $this->server->get_routes();

		if ( class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->assertArrayHasKey( '/pdf-embed-seo/v1/documents/(?P<id>[\d]+)/expiring-link', $routes );
		} else {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}
	}

	/**
	 * Test POST /documents/{id}/expiring-link requires admin.
	 */
	public function test_generate_requires_admin() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		wp_set_current_user( $this->subscriber_id );

		$request  = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/expiring-link' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 403, $response->get_status() );
	}

	/**
	 * Test POST /documents/{id}/expiring-link as admin.
	 */
	public function test_generate_as_admin() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		wp_set_current_user( $this->admin_id );

		$request = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/expiring-link' );
		$request->set_body_params( array(
			'expires_in' => 3600, // 1 hour.
			'max_uses'   => 5,
		) );

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertTrue( $data['success'] );
		$this->assertArrayHasKey( 'token', $data );
		$this->assertArrayHasKey( 'url', $data );
		$this->assertArrayHasKey( 'expires_at', $data );
		$this->assertEquals( 5, $data['max_uses'] );
	}

	/**
	 * Test generate link with default expiration.
	 */
	public function test_generate_default_expiration() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		wp_set_current_user( $this->admin_id );

		$request  = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/expiring-link' );
		$response = $this->server->dispatch( $request );

		$data = $response->get_data();

		// Default should be 24 hours (86400 seconds).
		$expected_expiry = time() + 86400;
		$actual_expiry   = strtotime( $data['expires_at'] );

		// Allow 5 second tolerance.
		$this->assertLessThanOrEqual( 5, abs( $expected_expiry - $actual_expiry ) );
	}

	/**
	 * Test validate expiring link endpoint.
	 */
	public function test_validate_endpoint_exists() {
		$routes = $this->server->get_routes();

		if ( class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$pattern = '/pdf-embed-seo/v1/documents/(?P<id>[\d]+)/expiring-link/(?P<token>[a-zA-Z0-9]+)';
			$this->assertArrayHasKey( $pattern, $routes );
		} else {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}
	}

	/**
	 * Test GET /documents/{id}/expiring-link/{token} with valid token.
	 */
	public function test_validate_valid_token() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		// First generate a link.
		wp_set_current_user( $this->admin_id );

		$gen_request = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/expiring-link' );
		$gen_request->set_body_params( array(
			'expires_in' => 3600,
			'max_uses'   => 5,
		) );

		$gen_response = $this->server->dispatch( $gen_request );
		$gen_data     = $gen_response->get_data();
		$token        = $gen_data['token'];

		// Now validate the token (anonymous user).
		wp_set_current_user( 0 );

		$val_request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/expiring-link/' . $token );
		$val_response = $this->server->dispatch( $val_request );

		$this->assertEquals( 200, $val_response->get_status() );

		$val_data = $val_response->get_data();
		$this->assertTrue( $val_data['valid'] );
		$this->assertArrayHasKey( 'pdf_url', $val_data );
	}

	/**
	 * Test GET /documents/{id}/expiring-link/{token} with invalid token.
	 */
	public function test_validate_invalid_token() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		$request  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/expiring-link/invalidtoken123' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );

		$data = $response->get_data();
		$this->assertFalse( $data['valid'] );
		$this->assertEquals( 'invalid_token', $data['error'] );
	}

	/**
	 * Test expiring link max uses enforcement.
	 */
	public function test_max_uses_enforcement() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		// Generate a link with max_uses = 2.
		wp_set_current_user( $this->admin_id );

		$gen_request = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/expiring-link' );
		$gen_request->set_body_params( array(
			'expires_in' => 3600,
			'max_uses'   => 2,
		) );

		$gen_response = $this->server->dispatch( $gen_request );
		$gen_data     = $gen_response->get_data();
		$token        = $gen_data['token'];

		wp_set_current_user( 0 );

		// First use - should succeed.
		$request1  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/expiring-link/' . $token );
		$response1 = $this->server->dispatch( $request1 );
		$this->assertEquals( 200, $response1->get_status() );

		// Second use - should succeed.
		$request2  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/expiring-link/' . $token );
		$response2 = $this->server->dispatch( $request2 );
		$this->assertEquals( 200, $response2->get_status() );

		// Third use - should fail (max uses exceeded).
		$request3  = new WP_REST_Request( 'GET', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/expiring-link/' . $token );
		$response3 = $this->server->dispatch( $request3 );
		$this->assertEquals( 403, $response3->get_status() );

		$data3 = $response3->get_data();
		$this->assertEquals( 'max_uses_exceeded', $data3['error'] );
	}

	/**
	 * Test generate link fires action hook.
	 */
	public function test_generate_fires_action() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		$action_fired   = false;
		$captured_id    = null;
		$captured_token = null;

		add_action( 'pdf_embed_seo_expiring_link_generated', function( $post_id, $token ) use ( &$action_fired, &$captured_id, &$captured_token ) {
			$action_fired   = true;
			$captured_id    = $post_id;
			$captured_token = $token;
		}, 10, 2 );

		wp_set_current_user( $this->admin_id );

		$request = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/' . $this->pdf_id . '/expiring-link' );
		$this->server->dispatch( $request );

		$this->assertTrue( $action_fired );
		$this->assertEquals( $this->pdf_id, $captured_id );
		$this->assertNotEmpty( $captured_token );
	}

	/**
	 * Test generate link for non-existent document.
	 */
	public function test_generate_invalid_document() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		wp_set_current_user( $this->admin_id );

		$request  = new WP_REST_Request( 'POST', '/pdf-embed-seo/v1/documents/99999/expiring-link' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );
	}
}
