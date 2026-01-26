<?php
/**
 * Custom Post Type registration for PDF Documents.
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Viewer_2026_Post_Type
 *
 * Registers and manages the pdf_document custom post type.
 */
class PDF_Viewer_2026_Post_Type {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const POST_TYPE = 'pdf_document';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_meta' ) );
	}

	/**
	 * Register the PDF Document custom post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'PDF Documents', 'Post type general name', 'pdf-viewer-2026' ),
			'singular_name'         => _x( 'PDF Document', 'Post type singular name', 'pdf-viewer-2026' ),
			'menu_name'             => _x( 'PDF Documents', 'Admin Menu text', 'pdf-viewer-2026' ),
			'name_admin_bar'        => _x( 'PDF Document', 'Add New on Toolbar', 'pdf-viewer-2026' ),
			'add_new'               => __( 'Add New', 'pdf-viewer-2026' ),
			'add_new_item'          => __( 'Add New PDF Document', 'pdf-viewer-2026' ),
			'new_item'              => __( 'New PDF Document', 'pdf-viewer-2026' ),
			'edit_item'             => __( 'Edit PDF Document', 'pdf-viewer-2026' ),
			'view_item'             => __( 'View PDF Document', 'pdf-viewer-2026' ),
			'all_items'             => __( 'All PDF Documents', 'pdf-viewer-2026' ),
			'search_items'          => __( 'Search PDF Documents', 'pdf-viewer-2026' ),
			'parent_item_colon'     => __( 'Parent PDF Documents:', 'pdf-viewer-2026' ),
			'not_found'             => __( 'No PDF documents found.', 'pdf-viewer-2026' ),
			'not_found_in_trash'    => __( 'No PDF documents found in Trash.', 'pdf-viewer-2026' ),
			'featured_image'        => _x( 'PDF Cover Image', 'Overrides the "Featured Image" phrase', 'pdf-viewer-2026' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the "Set featured image" phrase', 'pdf-viewer-2026' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the "Remove featured image" phrase', 'pdf-viewer-2026' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the "Use as featured image" phrase', 'pdf-viewer-2026' ),
			'archives'              => _x( 'PDF Document Archives', 'The post type archive label', 'pdf-viewer-2026' ),
			'insert_into_item'      => _x( 'Insert into PDF document', 'Overrides the "Insert into post" phrase', 'pdf-viewer-2026' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this PDF document', 'Overrides the "Uploaded to this post" phrase', 'pdf-viewer-2026' ),
			'filter_items_list'     => _x( 'Filter PDF documents list', 'Screen reader text', 'pdf-viewer-2026' ),
			'items_list_navigation' => _x( 'PDF documents list navigation', 'Screen reader text', 'pdf-viewer-2026' ),
			'items_list'            => _x( 'PDF documents list', 'Screen reader text', 'pdf-viewer-2026' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'rewrite'            => array(
				'slug'       => 'pdf',
				'with_front' => false,
			),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-pdf',
			'supports'           => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
				'custom-fields',
				'revisions',
			),
		);

		/**
		 * Filter the arguments for registering the pdf_document post type.
		 *
		 * @param array $args Post type arguments.
		 */
		$args = apply_filters( 'pdf_viewer_2026_post_type_args', $args );

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Register custom meta fields for the post type.
	 *
	 * @return void
	 */
	public function register_meta() {
		// PDF File ID.
		register_post_meta(
			self::POST_TYPE,
			'_pdf_file_id',
			array(
				'type'              => 'integer',
				'description'       => __( 'The attachment ID of the PDF file.', 'pdf-viewer-2026' ),
				'single'            => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => array( $this, 'auth_callback' ),
				'show_in_rest'      => true,
			)
		);

		// PDF File URL (stored for convenience/performance).
		register_post_meta(
			self::POST_TYPE,
			'_pdf_file_url',
			array(
				'type'              => 'string',
				'description'       => __( 'The URL of the PDF file.', 'pdf-viewer-2026' ),
				'single'            => true,
				'sanitize_callback' => 'esc_url_raw',
				'auth_callback'     => array( $this, 'auth_callback' ),
				'show_in_rest'      => true,
			)
		);

		// Allow Download.
		register_post_meta(
			self::POST_TYPE,
			'_pdf_allow_download',
			array(
				'type'              => 'boolean',
				'description'       => __( 'Whether users can download the PDF.', 'pdf-viewer-2026' ),
				'single'            => true,
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => array( $this, 'auth_callback' ),
				'show_in_rest'      => true,
			)
		);

		// Allow Print.
		register_post_meta(
			self::POST_TYPE,
			'_pdf_allow_print',
			array(
				'type'              => 'boolean',
				'description'       => __( 'Whether users can print the PDF.', 'pdf-viewer-2026' ),
				'single'            => true,
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => array( $this, 'auth_callback' ),
				'show_in_rest'      => true,
			)
		);

		// View Count.
		register_post_meta(
			self::POST_TYPE,
			'_pdf_view_count',
			array(
				'type'              => 'integer',
				'description'       => __( 'Number of times the PDF has been viewed.', 'pdf-viewer-2026' ),
				'single'            => true,
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'auth_callback'     => array( $this, 'auth_callback' ),
				'show_in_rest'      => true,
			)
		);
	}

	/**
	 * Authorization callback for meta fields.
	 *
	 * @param bool   $allowed   Whether the user can add the post meta.
	 * @param string $meta_key  The meta key.
	 * @param int    $post_id   The post ID.
	 * @param int    $user_id   The user ID.
	 * @param string $cap       The meta capability.
	 * @param array  $caps      The user's capabilities.
	 * @return bool
	 */
	public function auth_callback( $allowed, $meta_key, $post_id, $user_id, $cap, $caps ) {
		return current_user_can( 'edit_post', $post_id );
	}

	/**
	 * Get PDF file URL by post ID.
	 *
	 * @param int $post_id The post ID.
	 * @return string|false The PDF URL or false if not found.
	 */
	public static function get_pdf_url( $post_id ) {
		$file_id = get_post_meta( $post_id, '_pdf_file_id', true );

		if ( ! $file_id ) {
			return false;
		}

		$url = wp_get_attachment_url( $file_id );

		return $url ? $url : false;
	}

	/**
	 * Check if download is allowed for a PDF.
	 *
	 * @param int $post_id The post ID.
	 * @return bool
	 */
	public static function is_download_allowed( $post_id ) {
		$allowed = get_post_meta( $post_id, '_pdf_allow_download', true );
		return (bool) $allowed;
	}

	/**
	 * Check if print is allowed for a PDF.
	 *
	 * @param int $post_id The post ID.
	 * @return bool
	 */
	public static function is_print_allowed( $post_id ) {
		$allowed = get_post_meta( $post_id, '_pdf_allow_print', true );
		return (bool) $allowed;
	}

	/**
	 * Get view count for a PDF.
	 *
	 * @param int $post_id The post ID.
	 * @return int
	 */
	public static function get_view_count( $post_id ) {
		$count = get_post_meta( $post_id, '_pdf_view_count', true );
		return absint( $count );
	}

	/**
	 * Increment view count for a PDF.
	 *
	 * @param int $post_id The post ID.
	 * @return int The new view count.
	 */
	public static function increment_view_count( $post_id ) {
		$count = self::get_view_count( $post_id );
		$count++;
		update_post_meta( $post_id, '_pdf_view_count', $count );

		/**
		 * Fires when a PDF document is viewed.
		 *
		 * @param int $post_id   The post ID.
		 * @param int $count     The new view count.
		 */
		do_action( 'pdf_viewer_2026_pdf_viewed', $post_id, $count );

		return $count;
	}
}
