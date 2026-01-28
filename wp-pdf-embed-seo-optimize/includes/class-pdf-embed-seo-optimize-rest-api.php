<?php
/**
 * REST API functionality for PDF Embed & SEO Optimize.
 *
 * Provides RESTful API endpoints for accessing PDF documents.
 *
 * @package PDF_Embed_SEO
 * @since   1.2.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API class.
 */
class PDF_Embed_SEO_REST_API {

	/**
	 * API namespace.
	 *
	 * @var string
	 */
	const API_NAMESPACE = 'pdf-embed-seo/v1';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		// GET /documents - List all PDF documents.
		register_rest_route(
			self::API_NAMESPACE,
			'/documents',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_documents' ),
				'permission_callback' => array( $this, 'get_documents_permissions_check' ),
				'args'                => $this->get_collection_params(),
			)
		);

		// GET /documents/{id} - Get single PDF document.
		register_rest_route(
			self::API_NAMESPACE,
			'/documents/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_document' ),
				'permission_callback' => array( $this, 'get_document_permissions_check' ),
				'args'                => array(
					'id' => array(
						'description'       => __( 'Unique identifier for the PDF document.', 'pdf-embed-seo-optimize' ),
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// GET /documents/{id}/data - Get PDF file data (secure endpoint).
		register_rest_route(
			self::API_NAMESPACE,
			'/documents/(?P<id>\d+)/data',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_document_data' ),
				'permission_callback' => array( $this, 'get_document_permissions_check' ),
				'args'                => array(
					'id' => array(
						'description'       => __( 'Unique identifier for the PDF document.', 'pdf-embed-seo-optimize' ),
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// POST /documents/{id}/view - Track PDF view (analytics).
		register_rest_route(
			self::API_NAMESPACE,
			'/documents/(?P<id>\d+)/view',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'track_view' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'id' => array(
						'description'       => __( 'Unique identifier for the PDF document.', 'pdf-embed-seo-optimize' ),
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// GET /settings - Get public plugin settings.
		register_rest_route(
			self::API_NAMESPACE,
			'/settings',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_settings' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get collection parameters for documents list.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'page'     => array(
				'description'       => __( 'Current page of the collection.', 'pdf-embed-seo-optimize' ),
				'type'              => 'integer',
				'default'           => 1,
				'minimum'           => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of items per page.', 'pdf-embed-seo-optimize' ),
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
			),
			'search'   => array(
				'description'       => __( 'Search term to filter documents.', 'pdf-embed-seo-optimize' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'orderby'  => array(
				'description'       => __( 'Sort collection by attribute.', 'pdf-embed-seo-optimize' ),
				'type'              => 'string',
				'default'           => 'date',
				'enum'              => array( 'date', 'title', 'modified', 'views' ),
				'sanitize_callback' => 'sanitize_text_field',
			),
			'order'    => array(
				'description'       => __( 'Order sort attribute ascending or descending.', 'pdf-embed-seo-optimize' ),
				'type'              => 'string',
				'default'           => 'desc',
				'enum'              => array( 'asc', 'desc' ),
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

	/**
	 * Check if user can list documents.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function get_documents_permissions_check( $request ) {
		// Public endpoint - anyone can list published PDFs.
		return true;
	}

	/**
	 * Check if user can view a document.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error
	 */
	public function get_document_permissions_check( $request ) {
		$post = get_post( $request->get_param( 'id' ) );

		if ( ! $post || 'pdf_document' !== $post->post_type ) {
			return new WP_Error(
				'rest_post_invalid_id',
				__( 'Invalid PDF document ID.', 'pdf-embed-seo-optimize' ),
				array( 'status' => 404 )
			);
		}

		// Check if published or user can edit.
		if ( 'publish' !== $post->post_status && ! current_user_can( 'edit_post', $post->ID ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to view this PDF document.', 'pdf-embed-seo-optimize' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Get list of PDF documents.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_documents( $request ) {
		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );
		$search   = $request->get_param( 'search' );
		$orderby  = $request->get_param( 'orderby' );
		$order    = $request->get_param( 'order' );

		$args = array(
			'post_type'      => 'pdf_document',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => 'views' === $orderby ? 'meta_value_num' : $orderby,
			'order'          => strtoupper( $order ),
		);

		if ( 'views' === $orderby ) {
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Required for ordering by view count.
			$args['meta_key'] = '_pdf_view_count';
		}

		if ( ! empty( $search ) ) {
			$args['s'] = $search;
		}

		$query     = new WP_Query( $args );
		$documents = array();

		foreach ( $query->posts as $post ) {
			$documents[] = $this->prepare_document_for_response( $post );
		}

		$response = rest_ensure_response( $documents );

		// Add pagination headers.
		$total_posts = $query->found_posts;
		$max_pages   = ceil( $total_posts / $per_page );

		$response->header( 'X-WP-Total', $total_posts );
		$response->header( 'X-WP-TotalPages', $max_pages );

		return $response;
	}

	/**
	 * Get single PDF document.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_document( $request ) {
		$post = get_post( $request->get_param( 'id' ) );

		if ( ! $post || 'pdf_document' !== $post->post_type ) {
			return new WP_Error(
				'rest_post_invalid_id',
				__( 'Invalid PDF document ID.', 'pdf-embed-seo-optimize' ),
				array( 'status' => 404 )
			);
		}

		return rest_ensure_response( $this->prepare_document_for_response( $post, true ) );
	}

	/**
	 * Get PDF file data securely.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_document_data( $request ) {
		$post_id = $request->get_param( 'id' );
		$post    = get_post( $post_id );

		if ( ! $post || 'pdf_document' !== $post->post_type ) {
			return new WP_Error(
				'rest_post_invalid_id',
				__( 'Invalid PDF document ID.', 'pdf-embed-seo-optimize' ),
				array( 'status' => 404 )
			);
		}

		$pdf_file_id = get_post_meta( $post_id, '_pdf_file_id', true );

		if ( ! $pdf_file_id ) {
			return new WP_Error(
				'rest_no_pdf_file',
				__( 'No PDF file attached to this document.', 'pdf-embed-seo-optimize' ),
				array( 'status' => 404 )
			);
		}

		$pdf_url = wp_get_attachment_url( $pdf_file_id );

		if ( ! $pdf_url ) {
			return new WP_Error(
				'rest_pdf_not_found',
				__( 'PDF file not found.', 'pdf-embed-seo-optimize' ),
				array( 'status' => 404 )
			);
		}

		// Get permissions.
		$allow_download = get_post_meta( $post_id, '_pdf_allow_download', true );
		$allow_print    = get_post_meta( $post_id, '_pdf_allow_print', true );

		$data = array(
			'id'             => $post_id,
			'pdf_url'        => $pdf_url,
			'allow_download' => (bool) $allow_download,
			'allow_print'    => (bool) $allow_print,
			'filename'       => basename( get_attached_file( $pdf_file_id ) ),
		);

		/**
		 * Filter PDF document data response.
		 *
		 * @since 1.2.0
		 *
		 * @param array $data    PDF data.
		 * @param int   $post_id Post ID.
		 */
		$data = apply_filters( 'pdf_embed_seo_rest_document_data', $data, $post_id );

		return rest_ensure_response( $data );
	}

	/**
	 * Track PDF view.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function track_view( $request ) {
		$post_id = $request->get_param( 'id' );
		$post    = get_post( $post_id );

		if ( ! $post || 'pdf_document' !== $post->post_type ) {
			return new WP_Error(
				'rest_post_invalid_id',
				__( 'Invalid PDF document ID.', 'pdf-embed-seo-optimize' ),
				array( 'status' => 404 )
			);
		}

		// Increment view count.
		$current_count = (int) get_post_meta( $post_id, '_pdf_view_count', true );
		$new_count     = $current_count + 1;
		update_post_meta( $post_id, '_pdf_view_count', $new_count );

		/**
		 * Fires when a PDF is viewed via REST API.
		 *
		 * @since 1.2.0
		 *
		 * @param int $post_id Post ID.
		 * @param int $count   New view count.
		 */
		do_action( 'pdf_embed_seo_pdf_viewed', $post_id, $new_count );

		return rest_ensure_response(
			array(
				'success' => true,
				'views'   => $new_count,
			)
		);
	}

	/**
	 * Get public plugin settings.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_settings( $request ) {
		$settings = array(
			'viewer_theme'    => PDF_Embed_SEO::get_setting( 'viewer_theme', 'light' ),
			'archive_url'     => get_post_type_archive_link( 'pdf_document' ),
			'is_premium'      => function_exists( 'pdf_embed_seo_is_premium' ) && pdf_embed_seo_is_premium(),
			'version'         => PDF_EMBED_SEO_VERSION,
			'api_version'     => '1.0',
			'endpoints'       => array(
				'documents' => rest_url( self::API_NAMESPACE . '/documents' ),
				'settings'  => rest_url( self::API_NAMESPACE . '/settings' ),
			),
		);

		/**
		 * Filter public settings response.
		 *
		 * @since 1.2.0
		 *
		 * @param array $settings Settings data.
		 */
		$settings = apply_filters( 'pdf_embed_seo_rest_settings', $settings );

		return rest_ensure_response( $settings );
	}

	/**
	 * Prepare document for API response.
	 *
	 * @param WP_Post $post    Post object.
	 * @param bool    $detailed Whether to include detailed info.
	 * @return array
	 */
	private function prepare_document_for_response( $post, $detailed = false ) {
		$data = array(
			'id'            => $post->ID,
			'title'         => get_the_title( $post ),
			'slug'          => $post->post_name,
			'url'           => get_permalink( $post ),
			'excerpt'       => get_the_excerpt( $post ),
			'date'          => get_the_date( 'c', $post ),
			'modified'      => get_the_modified_date( 'c', $post ),
			'views'         => (int) get_post_meta( $post->ID, '_pdf_view_count', true ),
			'thumbnail'     => get_the_post_thumbnail_url( $post->ID, 'medium' ),
			'allow_download' => (bool) get_post_meta( $post->ID, '_pdf_allow_download', true ),
			'allow_print'   => (bool) get_post_meta( $post->ID, '_pdf_allow_print', true ),
		);

		if ( $detailed ) {
			$data['content']  = apply_filters( 'the_content', $post->post_content );
			$data['data_url'] = rest_url( self::API_NAMESPACE . '/documents/' . $post->ID . '/data' );

			// Add SEO data if Yoast is active.
			if ( class_exists( 'WPSEO_Meta' ) ) {
				$data['seo'] = array(
					'title'       => WPSEO_Meta::get_value( 'title', $post->ID ),
					'description' => WPSEO_Meta::get_value( 'metadesc', $post->ID ),
				);
			}
		}

		/**
		 * Filter document data for API response.
		 *
		 * @since 1.2.0
		 *
		 * @param array   $data     Document data.
		 * @param WP_Post $post     Post object.
		 * @param bool    $detailed Whether detailed info is included.
		 */
		return apply_filters( 'pdf_embed_seo_rest_document', $data, $post, $detailed );
	}
}
