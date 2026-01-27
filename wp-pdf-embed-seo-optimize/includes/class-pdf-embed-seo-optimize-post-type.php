<?php
/**
 * Custom Post Type registration for PDF Documents.
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Embed_SEO_Post_Type
 *
 * Registers and manages the pdf_document custom post type.
 */
class PDF_Embed_SEO_Post_Type {

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
			'name'                  => _x( 'PDF Documents', 'Post type general name', 'wp-pdf-embed-seo-optimize' ),
			'singular_name'         => _x( 'PDF Document', 'Post type singular name', 'wp-pdf-embed-seo-optimize' ),
			'menu_name'             => _x( 'PDF Documents', 'Admin Menu text', 'wp-pdf-embed-seo-optimize' ),
			'name_admin_bar'        => _x( 'PDF Document', 'Add New on Toolbar', 'wp-pdf-embed-seo-optimize' ),
			'add_new'               => __( 'Add New', 'wp-pdf-embed-seo-optimize' ),
			'add_new_item'          => __( 'Add New PDF Document', 'wp-pdf-embed-seo-optimize' ),
			'new_item'              => __( 'New PDF Document', 'wp-pdf-embed-seo-optimize' ),
			'edit_item'             => __( 'Edit PDF Document', 'wp-pdf-embed-seo-optimize' ),
			'view_item'             => __( 'View PDF Document', 'wp-pdf-embed-seo-optimize' ),
			'all_items'             => __( 'All PDF Documents', 'wp-pdf-embed-seo-optimize' ),
			'search_items'          => __( 'Search PDF Documents', 'wp-pdf-embed-seo-optimize' ),
			'parent_item_colon'     => __( 'Parent PDF Documents:', 'wp-pdf-embed-seo-optimize' ),
			'not_found'             => __( 'No PDF documents found.', 'wp-pdf-embed-seo-optimize' ),
			'not_found_in_trash'    => __( 'No PDF documents found in Trash.', 'wp-pdf-embed-seo-optimize' ),
			'featured_image'        => _x( 'PDF Cover Image', 'Overrides the "Featured Image" phrase', 'wp-pdf-embed-seo-optimize' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the "Set featured image" phrase', 'wp-pdf-embed-seo-optimize' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the "Remove featured image" phrase', 'wp-pdf-embed-seo-optimize' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the "Use as featured image" phrase', 'wp-pdf-embed-seo-optimize' ),
			'archives'              => _x( 'PDF Document Archives', 'The post type archive label', 'wp-pdf-embed-seo-optimize' ),
			'insert_into_item'      => _x( 'Insert into PDF document', 'Overrides the "Insert into post" phrase', 'wp-pdf-embed-seo-optimize' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this PDF document', 'Overrides the "Uploaded to this post" phrase', 'wp-pdf-embed-seo-optimize' ),
			'filter_items_list'     => _x( 'Filter PDF documents list', 'Screen reader text', 'wp-pdf-embed-seo-optimize' ),
			'items_list_navigation' => _x( 'PDF documents list navigation', 'Screen reader text', 'wp-pdf-embed-seo-optimize' ),
			'items_list'            => _x( 'PDF documents list', 'Screen reader text', 'wp-pdf-embed-seo-optimize' ),
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
		$args = apply_filters( 'pdf_embed_seo_post_type_args', $args );

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
				'description'       => __( 'The attachment ID of the PDF file.', 'wp-pdf-embed-seo-optimize' ),
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
				'description'       => __( 'The URL of the PDF file.', 'wp-pdf-embed-seo-optimize' ),
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
				'description'       => __( 'Whether users can download the PDF.', 'wp-pdf-embed-seo-optimize' ),
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
				'description'       => __( 'Whether users can print the PDF.', 'wp-pdf-embed-seo-optimize' ),
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
				'description'       => __( 'Number of times the PDF has been viewed.', 'wp-pdf-embed-seo-optimize' ),
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
		do_action( 'pdf_embed_seo_pdf_viewed', $post_id, $count );

		return $count;
	}
}
