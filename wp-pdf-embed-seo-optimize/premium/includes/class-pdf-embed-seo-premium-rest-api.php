<?php
/**
 * Premium REST API functionality for PDF Embed & SEO Optimize.
 *
 * Extends the base REST API with premium endpoints for analytics,
 * reading progress, password protection, and bulk operations.
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.2.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium REST API class.
 */
class PDF_Embed_SEO_Premium_REST_API {

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
	 * Register premium REST API routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		// ==========================================
		// Analytics Endpoints
		// ==========================================

		// GET /analytics - Get analytics overview.
		register_rest_route(
			self::API_NAMESPACE,
			'/analytics',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_analytics' ),
				'permission_callback' => array( $this, 'analytics_permissions_check' ),
				'args'                => array(
					'period' => array(
						'description'       => __( 'Time period for analytics.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'string',
						'default'           => '30days',
						'enum'              => array( '7days', '30days', '90days', '12months', 'all' ),
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// GET /analytics/documents - Get per-document analytics.
		register_rest_route(
			self::API_NAMESPACE,
			'/analytics/documents',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_document_analytics' ),
				'permission_callback' => array( $this, 'analytics_permissions_check' ),
				'args'                => array(
					'period'   => array(
						'description'       => __( 'Time period for analytics.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'string',
						'default'           => '30days',
						'enum'              => array( '7days', '30days', '90days', '12months', 'all' ),
						'sanitize_callback' => 'sanitize_text_field',
					),
					'orderby'  => array(
						'description'       => __( 'Sort by field.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'string',
						'default'           => 'views',
						'enum'              => array( 'views', 'downloads', 'avg_time' ),
						'sanitize_callback' => 'sanitize_text_field',
					),
					'limit'    => array(
						'description'       => __( 'Number of documents to return.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'integer',
						'default'           => 10,
						'minimum'           => 1,
						'maximum'           => 100,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// GET /analytics/export - Export analytics as CSV.
		register_rest_route(
			self::API_NAMESPACE,
			'/analytics/export',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'export_analytics' ),
				'permission_callback' => array( $this, 'analytics_permissions_check' ),
				'args'                => array(
					'format' => array(
						'description'       => __( 'Export format.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'string',
						'default'           => 'csv',
						'enum'              => array( 'csv', 'json' ),
						'sanitize_callback' => 'sanitize_text_field',
					),
					'period' => array(
						'description'       => __( 'Time period for export.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'string',
						'default'           => 'all',
						'enum'              => array( '7days', '30days', '90days', '12months', 'all' ),
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// ==========================================
		// Reading Progress Endpoints
		// ==========================================

		// GET /documents/{id}/progress - Get reading progress.
		register_rest_route(
			self::API_NAMESPACE,
			'/documents/(?P<id>\d+)/progress',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_reading_progress' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'id' => array(
						'description'       => __( 'PDF document ID.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// POST /documents/{id}/progress - Save reading progress.
		register_rest_route(
			self::API_NAMESPACE,
			'/documents/(?P<id>\d+)/progress',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_reading_progress' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'id'      => array(
						'description'       => __( 'PDF document ID.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'page'    => array(
						'description'       => __( 'Current page number.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'integer',
						'required'          => true,
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
					),
					'scroll'  => array(
						'description'       => __( 'Scroll position (0-1).', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'number',
						'default'           => 0,
						'minimum'           => 0,
						'maximum'           => 1,
						'sanitize_callback' => 'floatval',
					),
					'zoom'    => array(
						'description'       => __( 'Zoom level.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'number',
						'default'           => 1,
						'minimum'           => 0.1,
						'maximum'           => 5,
						'sanitize_callback' => 'floatval',
					),
				),
			)
		);

		// ==========================================
		// Password Protection Endpoints
		// ==========================================

		// POST /documents/{id}/verify-password - Verify PDF password.
		register_rest_route(
			self::API_NAMESPACE,
			'/documents/(?P<id>\d+)/verify-password',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'verify_password' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'id'       => array(
						'description'       => __( 'PDF document ID.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'password' => array(
						'description'       => __( 'Password to verify.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'string',
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// ==========================================
		// Bulk Operations Endpoints (Admin)
		// ==========================================

		// POST /bulk/import - Start bulk import.
		register_rest_route(
			self::API_NAMESPACE,
			'/bulk/import',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'start_bulk_import' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
				'args'                => array(
					'source' => array(
						'description'       => __( 'Import source.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'string',
						'required'          => true,
						'enum'              => array( 'media_library', 'folder', 'url' ),
						'sanitize_callback' => 'sanitize_text_field',
					),
					'path'   => array(
						'description'       => __( 'Path or URL for import source.', 'wp-pdf-embed-seo-optimize' ),
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// GET /bulk/import/status - Get import status.
		register_rest_route(
			self::API_NAMESPACE,
			'/bulk/import/status',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_import_status' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
			)
		);

		// ==========================================
		// Categories & Tags Endpoints
		// ==========================================

		// GET /categories - Get PDF categories.
		register_rest_route(
			self::API_NAMESPACE,
			'/categories',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_categories' ),
				'permission_callback' => '__return_true',
			)
		);

		// GET /tags - Get PDF tags.
		register_rest_route(
			self::API_NAMESPACE,
			'/tags',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_tags' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Check analytics permissions.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function analytics_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Check admin permissions.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function admin_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get analytics overview.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_analytics( $request ) {
		$period = $request->get_param( 'period' );

		global $wpdb;
		$table_name = $wpdb->prefix . 'pdf_analytics';

		// Check if table exists.
		$table_exists = $wpdb->get_var(
			$wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name )
		);

		if ( ! $table_exists ) {
			// Return basic analytics from post meta.
			$total_views = $this->get_total_views_from_meta();
			return rest_ensure_response(
				array(
					'period'          => $period,
					'total_views'     => $total_views,
					'total_downloads' => 0,
					'unique_visitors' => 0,
					'avg_time_spent'  => 0,
					'top_documents'   => $this->get_top_documents( 5 ),
				)
			);
		}

		// Get date range.
		$date_range = $this->get_date_range( $period );

		// Get analytics data.
		$stats = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT
					COUNT(*) as total_views,
					COUNT(DISTINCT user_ip) as unique_visitors,
					AVG(time_spent) as avg_time_spent
				FROM {$table_name}
				WHERE created_at >= %s AND created_at <= %s",
				$date_range['start'],
				$date_range['end']
			),
			ARRAY_A
		);

		$data = array(
			'period'          => $period,
			'date_range'      => $date_range,
			'total_views'     => (int) $stats['total_views'],
			'unique_visitors' => (int) $stats['unique_visitors'],
			'avg_time_spent'  => round( (float) $stats['avg_time_spent'], 2 ),
			'top_documents'   => $this->get_top_documents( 5, $date_range ),
		);

		/**
		 * Filter analytics data.
		 *
		 * @since 1.2.0
		 *
		 * @param array  $data   Analytics data.
		 * @param string $period Time period.
		 */
		return rest_ensure_response( apply_filters( 'pdf_embed_seo_rest_analytics', $data, $period ) );
	}

	/**
	 * Get per-document analytics.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_document_analytics( $request ) {
		$period  = $request->get_param( 'period' );
		$orderby = $request->get_param( 'orderby' );
		$limit   = $request->get_param( 'limit' );

		$documents = $this->get_top_documents( $limit );

		return rest_ensure_response(
			array(
				'period'    => $period,
				'documents' => $documents,
			)
		);
	}

	/**
	 * Export analytics data.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function export_analytics( $request ) {
		$format = $request->get_param( 'format' );
		$period = $request->get_param( 'period' );

		$documents = $this->get_top_documents( 100 );

		if ( 'json' === $format ) {
			return rest_ensure_response(
				array(
					'period'    => $period,
					'exported'  => current_time( 'c' ),
					'documents' => $documents,
				)
			);
		}

		// Generate CSV.
		$csv_data   = array();
		$csv_data[] = array( 'ID', 'Title', 'Views', 'URL' );

		foreach ( $documents as $doc ) {
			$csv_data[] = array(
				$doc['id'],
				$doc['title'],
				$doc['views'],
				$doc['url'],
			);
		}

		$csv_content = '';
		foreach ( $csv_data as $row ) {
			$csv_content .= '"' . implode( '","', array_map( 'esc_attr', $row ) ) . '"' . "\n";
		}

		return rest_ensure_response(
			array(
				'format'   => 'csv',
				'filename' => 'pdf-analytics-' . gmdate( 'Y-m-d' ) . '.csv',
				'content'  => $csv_content,
			)
		);
	}

	/**
	 * Get reading progress for a document.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_reading_progress( $request ) {
		$post_id = $request->get_param( 'id' );

		// Get user identifier.
		$user_id = $this->get_user_identifier();

		// Get progress from transient/user meta.
		$progress_key = 'pdf_progress_' . $post_id . '_' . $user_id;

		if ( is_user_logged_in() ) {
			$progress = get_user_meta( get_current_user_id(), $progress_key, true );
		} else {
			$progress = get_transient( $progress_key );
		}

		if ( ! $progress ) {
			$progress = array(
				'page'   => 1,
				'scroll' => 0,
				'zoom'   => 1,
			);
		}

		return rest_ensure_response(
			array(
				'document_id' => $post_id,
				'progress'    => $progress,
				'last_read'   => isset( $progress['timestamp'] ) ? $progress['timestamp'] : null,
			)
		);
	}

	/**
	 * Save reading progress for a document.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function save_reading_progress( $request ) {
		$post_id = $request->get_param( 'id' );
		$page    = $request->get_param( 'page' );
		$scroll  = $request->get_param( 'scroll' );
		$zoom    = $request->get_param( 'zoom' );

		// Get user identifier.
		$user_id = $this->get_user_identifier();

		$progress = array(
			'page'      => $page,
			'scroll'    => $scroll,
			'zoom'      => $zoom,
			'timestamp' => current_time( 'c' ),
		);

		$progress_key = 'pdf_progress_' . $post_id . '_' . $user_id;

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), $progress_key, $progress );
		} else {
			set_transient( $progress_key, $progress, WEEK_IN_SECONDS );
		}

		return rest_ensure_response(
			array(
				'success'     => true,
				'document_id' => $post_id,
				'progress'    => $progress,
			)
		);
	}

	/**
	 * Verify PDF password.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function verify_password( $request ) {
		$post_id  = $request->get_param( 'id' );
		$password = $request->get_param( 'password' );

		$post = get_post( $post_id );

		if ( ! $post || 'pdf_document' !== $post->post_type ) {
			return new WP_Error(
				'invalid_document',
				__( 'Invalid PDF document.', 'wp-pdf-embed-seo-optimize' ),
				array( 'status' => 404 )
			);
		}

		// Check if password protection is enabled.
		$is_protected    = get_post_meta( $post_id, '_pdf_password_protected', true );
		$stored_password = get_post_meta( $post_id, '_pdf_password', true );

		if ( ! $is_protected ) {
			return rest_ensure_response(
				array(
					'success'   => true,
					'protected' => false,
					'message'   => __( 'This document is not password protected.', 'wp-pdf-embed-seo-optimize' ),
				)
			);
		}

		// Verify password.
		$is_valid = wp_check_password( $password, $stored_password );

		/**
		 * Filter password verification result.
		 *
		 * @since 1.2.0
		 *
		 * @param bool   $is_valid Whether password is valid.
		 * @param int    $post_id  Document ID.
		 * @param string $password Submitted password.
		 */
		$is_valid = apply_filters( 'pdf_embed_seo_verify_password', $is_valid, $post_id, $password );

		if ( $is_valid ) {
			// Set session/cookie for access.
			$token = wp_generate_password( 32, false );
			set_transient( 'pdf_access_' . $post_id . '_' . $token, true, HOUR_IN_SECONDS );

			return rest_ensure_response(
				array(
					'success'      => true,
					'access_token' => $token,
					'expires_in'   => HOUR_IN_SECONDS,
				)
			);
		}

		return new WP_Error(
			'invalid_password',
			__( 'Incorrect password.', 'wp-pdf-embed-seo-optimize' ),
			array( 'status' => 403 )
		);
	}

	/**
	 * Start bulk import.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function start_bulk_import( $request ) {
		$source = $request->get_param( 'source' );
		$path   = $request->get_param( 'path' );

		// Generate import ID.
		$import_id = wp_generate_uuid4();

		// Store import status.
		set_transient(
			'pdf_import_' . $import_id,
			array(
				'status'    => 'pending',
				'source'    => $source,
				'path'      => $path,
				'total'     => 0,
				'processed' => 0,
				'errors'    => array(),
				'started'   => current_time( 'c' ),
			),
			HOUR_IN_SECONDS
		);

		// Schedule background processing.
		wp_schedule_single_event( time(), 'pdf_embed_seo_process_import', array( $import_id ) );

		return rest_ensure_response(
			array(
				'success'   => true,
				'import_id' => $import_id,
				'status'    => 'pending',
				'message'   => __( 'Import started. Check status endpoint for progress.', 'wp-pdf-embed-seo-optimize' ),
			)
		);
	}

	/**
	 * Get import status.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_import_status( $request ) {
		// Get most recent import.
		$import_id = get_option( 'pdf_embed_seo_last_import_id' );

		if ( ! $import_id ) {
			return rest_ensure_response(
				array(
					'status'  => 'none',
					'message' => __( 'No recent imports found.', 'wp-pdf-embed-seo-optimize' ),
				)
			);
		}

		$status = get_transient( 'pdf_import_' . $import_id );

		if ( ! $status ) {
			return rest_ensure_response(
				array(
					'status'  => 'expired',
					'message' => __( 'Import status has expired.', 'wp-pdf-embed-seo-optimize' ),
				)
			);
		}

		return rest_ensure_response( $status );
	}

	/**
	 * Get PDF categories.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_categories( $request ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'pdf_category',
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $terms ) ) {
			$terms = array();
		}

		$categories = array();
		foreach ( $terms as $term ) {
			$categories[] = array(
				'id'          => $term->term_id,
				'name'        => $term->name,
				'slug'        => $term->slug,
				'description' => $term->description,
				'count'       => $term->count,
				'url'         => get_term_link( $term ),
			);
		}

		return rest_ensure_response( $categories );
	}

	/**
	 * Get PDF tags.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_tags( $request ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'pdf_tag',
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $terms ) ) {
			$terms = array();
		}

		$tags = array();
		foreach ( $terms as $term ) {
			$tags[] = array(
				'id'    => $term->term_id,
				'name'  => $term->name,
				'slug'  => $term->slug,
				'count' => $term->count,
				'url'   => get_term_link( $term ),
			);
		}

		return rest_ensure_response( $tags );
	}

	/**
	 * Get date range for period.
	 *
	 * @param string $period Period identifier.
	 * @return array
	 */
	private function get_date_range( $period ) {
		$end   = current_time( 'Y-m-d 23:59:59' );
		$start = current_time( 'Y-m-d 00:00:00' );

		switch ( $period ) {
			case '7days':
				$start = gmdate( 'Y-m-d 00:00:00', strtotime( '-7 days' ) );
				break;
			case '30days':
				$start = gmdate( 'Y-m-d 00:00:00', strtotime( '-30 days' ) );
				break;
			case '90days':
				$start = gmdate( 'Y-m-d 00:00:00', strtotime( '-90 days' ) );
				break;
			case '12months':
				$start = gmdate( 'Y-m-d 00:00:00', strtotime( '-12 months' ) );
				break;
			case 'all':
				$start = '1970-01-01 00:00:00';
				break;
		}

		return array(
			'start' => $start,
			'end'   => $end,
		);
	}

	/**
	 * Get total views from post meta.
	 *
	 * @return int
	 */
	private function get_total_views_from_meta() {
		global $wpdb;

		$total = $wpdb->get_var(
			"SELECT SUM(meta_value) FROM {$wpdb->postmeta}
			WHERE meta_key = '_pdf_view_count'"
		);

		return (int) $total;
	}

	/**
	 * Get top documents by views.
	 *
	 * @param int   $limit      Number of documents.
	 * @param array $date_range Optional date range.
	 * @return array
	 */
	private function get_top_documents( $limit = 5, $date_range = null ) {
		$args = array(
			'post_type'      => 'pdf_document',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'meta_key'       => '_pdf_view_count',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
		);

		$query     = new WP_Query( $args );
		$documents = array();

		foreach ( $query->posts as $post ) {
			$documents[] = array(
				'id'    => $post->ID,
				'title' => get_the_title( $post ),
				'url'   => get_permalink( $post ),
				'views' => (int) get_post_meta( $post->ID, '_pdf_view_count', true ),
			);
		}

		return $documents;
	}

	/**
	 * Get user identifier for progress tracking.
	 *
	 * @return string
	 */
	private function get_user_identifier() {
		if ( is_user_logged_in() ) {
			return 'user_' . get_current_user_id();
		}

		// Use IP hash for anonymous users.
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return 'anon_' . md5( $ip );
	}
}
