<?php
/**
 * Premium Taxonomies
 *
 * Adds PDF Categories and Tags taxonomies.
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium taxonomies class.
 */
class PDF_Embed_SEO_Premium_Taxonomies {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_taxonomies' ), 5 );
		add_filter( 'pdf_embed_seo_archive_query', array( $this, 'filter_archive_by_taxonomy' ) );
	}

	/**
	 * Register PDF taxonomies.
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		// PDF Categories.
		$category_labels = array(
			'name'                       => _x( 'PDF Categories', 'taxonomy general name', 'pdf-embed-seo-optimize' ),
			'singular_name'              => _x( 'PDF Category', 'taxonomy singular name', 'pdf-embed-seo-optimize' ),
			'search_items'               => __( 'Search PDF Categories', 'pdf-embed-seo-optimize' ),
			'popular_items'              => __( 'Popular PDF Categories', 'pdf-embed-seo-optimize' ),
			'all_items'                  => __( 'All PDF Categories', 'pdf-embed-seo-optimize' ),
			'parent_item'                => __( 'Parent PDF Category', 'pdf-embed-seo-optimize' ),
			'parent_item_colon'          => __( 'Parent PDF Category:', 'pdf-embed-seo-optimize' ),
			'edit_item'                  => __( 'Edit PDF Category', 'pdf-embed-seo-optimize' ),
			'update_item'                => __( 'Update PDF Category', 'pdf-embed-seo-optimize' ),
			'add_new_item'               => __( 'Add New PDF Category', 'pdf-embed-seo-optimize' ),
			'new_item_name'              => __( 'New PDF Category Name', 'pdf-embed-seo-optimize' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'pdf-embed-seo-optimize' ),
			'add_or_remove_items'        => __( 'Add or remove categories', 'pdf-embed-seo-optimize' ),
			'choose_from_most_used'      => __( 'Choose from the most used categories', 'pdf-embed-seo-optimize' ),
			'not_found'                  => __( 'No PDF categories found.', 'pdf-embed-seo-optimize' ),
			'menu_name'                  => __( 'Categories', 'pdf-embed-seo-optimize' ),
			'back_to_items'              => __( '&larr; Back to PDF Categories', 'pdf-embed-seo-optimize' ),
		);

		$category_args = array(
			'labels'             => $category_labels,
			'hierarchical'       => true,
			'public'             => true,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => true,
			'show_in_rest'       => true,
			'rewrite'            => array(
				'slug'         => 'pdf/category',
				'with_front'   => false,
				'hierarchical' => true,
			),
		);

		register_taxonomy( 'pdf_category', 'pdf_document', $category_args );

		// PDF Tags.
		$tag_labels = array(
			'name'                       => _x( 'PDF Tags', 'taxonomy general name', 'pdf-embed-seo-optimize' ),
			'singular_name'              => _x( 'PDF Tag', 'taxonomy singular name', 'pdf-embed-seo-optimize' ),
			'search_items'               => __( 'Search PDF Tags', 'pdf-embed-seo-optimize' ),
			'popular_items'              => __( 'Popular PDF Tags', 'pdf-embed-seo-optimize' ),
			'all_items'                  => __( 'All PDF Tags', 'pdf-embed-seo-optimize' ),
			'edit_item'                  => __( 'Edit PDF Tag', 'pdf-embed-seo-optimize' ),
			'update_item'                => __( 'Update PDF Tag', 'pdf-embed-seo-optimize' ),
			'add_new_item'               => __( 'Add New PDF Tag', 'pdf-embed-seo-optimize' ),
			'new_item_name'              => __( 'New PDF Tag Name', 'pdf-embed-seo-optimize' ),
			'separate_items_with_commas' => __( 'Separate tags with commas', 'pdf-embed-seo-optimize' ),
			'add_or_remove_items'        => __( 'Add or remove tags', 'pdf-embed-seo-optimize' ),
			'choose_from_most_used'      => __( 'Choose from the most used tags', 'pdf-embed-seo-optimize' ),
			'not_found'                  => __( 'No PDF tags found.', 'pdf-embed-seo-optimize' ),
			'menu_name'                  => __( 'Tags', 'pdf-embed-seo-optimize' ),
			'back_to_items'              => __( '&larr; Back to PDF Tags', 'pdf-embed-seo-optimize' ),
		);

		$tag_args = array(
			'labels'             => $tag_labels,
			'hierarchical'       => false,
			'public'             => true,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => true,
			'show_in_rest'       => true,
			'rewrite'            => array(
				'slug'       => 'pdf/tag',
				'with_front' => false,
			),
		);

		register_taxonomy( 'pdf_tag', 'pdf_document', $tag_args );
	}

	/**
	 * Filter archive query by taxonomy.
	 *
	 * @param array $query_args Query arguments.
	 * @return array
	 */
	public function filter_archive_by_taxonomy( $query_args ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Archive filter, no action taken.
		if ( isset( $_GET['pdf_category'] ) && ! empty( $_GET['pdf_category'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'pdf_category',
				'field'    => 'slug',
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Archive filter, no action taken.
				'terms'    => sanitize_text_field( wp_unslash( $_GET['pdf_category'] ) ),
			);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Archive filter, no action taken.
		if ( isset( $_GET['pdf_tag'] ) && ! empty( $_GET['pdf_tag'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'pdf_tag',
				'field'    => 'slug',
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Archive filter, no action taken.
				'terms'    => sanitize_text_field( wp_unslash( $_GET['pdf_tag'] ) ),
			);
		}

		return $query_args;
	}

	/**
	 * Get PDF categories.
	 *
	 * @param array $args Optional. Arguments to pass to get_terms().
	 * @return array
	 */
	public static function get_categories( $args = array() ) {
		$defaults = array(
			'taxonomy'   => 'pdf_category',
			'hide_empty' => false,
		);
		$args     = wp_parse_args( $args, $defaults );
		return get_terms( $args );
	}

	/**
	 * Get PDF tags.
	 *
	 * @param array $args Optional. Arguments to pass to get_terms().
	 * @return array
	 */
	public static function get_tags( $args = array() ) {
		$defaults = array(
			'taxonomy'   => 'pdf_tag',
			'hide_empty' => false,
		);
		$args     = wp_parse_args( $args, $defaults );
		return get_terms( $args );
	}

	/**
	 * Get category filter dropdown HTML.
	 *
	 * @param string $selected Currently selected category slug.
	 * @return string
	 */
	public static function get_category_filter_dropdown( $selected = '' ) {
		$categories = self::get_categories( array( 'hide_empty' => true ) );

		if ( empty( $categories ) || is_wp_error( $categories ) ) {
			return '';
		}

		$output  = '<select name="pdf_category" class="pdf-category-filter">';
		$output .= '<option value="">' . esc_html__( 'All Categories', 'pdf-embed-seo-optimize' ) . '</option>';

		foreach ( $categories as $category ) {
			$output .= sprintf(
				'<option value="%s" %s>%s (%d)</option>',
				esc_attr( $category->slug ),
				selected( $selected, $category->slug, false ),
				esc_html( $category->name ),
				absint( $category->count )
			);
		}

		$output .= '</select>';

		return $output;
	}

	/**
	 * Get tag cloud HTML.
	 *
	 * @param array $args Optional. Arguments for the tag cloud.
	 * @return string
	 */
	public static function get_tag_cloud( $args = array() ) {
		$defaults = array(
			'taxonomy' => 'pdf_tag',
			'smallest' => 10,
			'largest'  => 22,
			'unit'     => 'px',
			'number'   => 45,
			'format'   => 'flat',
			'echo'     => false,
		);
		$args     = wp_parse_args( $args, $defaults );
		return wp_tag_cloud( $args );
	}
}
