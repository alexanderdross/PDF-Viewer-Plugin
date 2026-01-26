<?php
/**
 * Yoast SEO Integration for PDF Viewer 2026.
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Viewer_2026_Yoast
 *
 * Handles integration with Yoast SEO plugin for enhanced SEO control.
 */
class PDF_Viewer_2026_Yoast {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Check if Yoast SEO is active.
		if ( ! $this->is_yoast_active() ) {
			return;
		}

		// Add Yoast support for our post type.
		add_filter( 'wpseo_accessible_post_types', array( $this, 'add_post_type_support' ) );

		// Customize the OpenGraph image.
		add_filter( 'wpseo_opengraph_image', array( $this, 'customize_og_image' ) );

		// Add custom schema for PDF documents.
		add_filter( 'wpseo_schema_graph_pieces', array( $this, 'add_schema_pieces' ), 10, 2 );

		// Customize Twitter card.
		add_filter( 'wpseo_twitter_image', array( $this, 'customize_twitter_image' ) );

		// Add sitemap support.
		add_filter( 'wpseo_sitemap_post_type_archive_link', array( $this, 'sitemap_archive_link' ), 10, 2 );

		// Custom meta robots.
		add_filter( 'wpseo_robots', array( $this, 'modify_robots' ) );
	}

	/**
	 * Check if Yoast SEO is active.
	 *
	 * @return bool
	 */
	public function is_yoast_active() {
		return defined( 'WPSEO_VERSION' ) || class_exists( 'WPSEO_Options' );
	}

	/**
	 * Add PDF document post type to Yoast accessible post types.
	 *
	 * @param array $post_types Array of accessible post types.
	 * @return array
	 */
	public function add_post_type_support( $post_types ) {
		$post_types['pdf_document'] = 'pdf_document';
		return $post_types;
	}

	/**
	 * Customize OpenGraph image for PDF documents.
	 *
	 * @param string $image The current OG image URL.
	 * @return string
	 */
	public function customize_og_image( $image ) {
		if ( ! is_singular( 'pdf_document' ) ) {
			return $image;
		}

		$post_id = get_the_ID();

		// Use featured image if available.
		if ( has_post_thumbnail( $post_id ) ) {
			$thumbnail_id  = get_post_thumbnail_id( $post_id );
			$thumbnail_url = wp_get_attachment_image_url( $thumbnail_id, 'large' );

			if ( $thumbnail_url ) {
				return $thumbnail_url;
			}
		}

		return $image;
	}

	/**
	 * Customize Twitter card image for PDF documents.
	 *
	 * @param string $image The current Twitter image URL.
	 * @return string
	 */
	public function customize_twitter_image( $image ) {
		if ( ! is_singular( 'pdf_document' ) ) {
			return $image;
		}

		$post_id = get_the_ID();

		// Use featured image if available.
		if ( has_post_thumbnail( $post_id ) ) {
			$thumbnail_id  = get_post_thumbnail_id( $post_id );
			$thumbnail_url = wp_get_attachment_image_url( $thumbnail_id, 'large' );

			if ( $thumbnail_url ) {
				return $thumbnail_url;
			}
		}

		return $image;
	}

	/**
	 * Add custom schema pieces for PDF documents.
	 *
	 * @param array                 $pieces  The current schema pieces.
	 * @param WPSEO_Schema_Context  $context The schema context.
	 * @return array
	 */
	public function add_schema_pieces( $pieces, $context ) {
		if ( ! is_singular( 'pdf_document' ) ) {
			return $pieces;
		}

		// Add our custom schema piece.
		$pieces[] = new PDF_Viewer_2026_Schema_Piece( $context );

		return $pieces;
	}

	/**
	 * Modify sitemap archive link for PDF documents.
	 *
	 * @param string $link      The archive link.
	 * @param string $post_type The post type.
	 * @return string
	 */
	public function sitemap_archive_link( $link, $post_type ) {
		if ( 'pdf_document' === $post_type ) {
			return get_post_type_archive_link( 'pdf_document' );
		}

		return $link;
	}

	/**
	 * Modify robots meta tag for PDF documents if needed.
	 *
	 * @param string $robots The current robots value.
	 * @return string
	 */
	public function modify_robots( $robots ) {
		// Allow all PDF documents to be indexed by default.
		// Individual pages can still be set to noindex via Yoast.
		return $robots;
	}

	/**
	 * Get SEO data for a PDF document.
	 *
	 * @param int $post_id The post ID.
	 * @return array
	 */
	public static function get_seo_data( $post_id ) {
		$data = array(
			'title'       => '',
			'description' => '',
			'og_title'    => '',
			'og_desc'     => '',
			'og_image'    => '',
		);

		if ( ! class_exists( 'WPSEO_Meta' ) ) {
			// Yoast not active, use default values.
			$data['title']       = get_the_title( $post_id );
			$data['description'] = get_the_excerpt( $post_id );
			return $data;
		}

		// Get Yoast meta values.
		$data['title']       = WPSEO_Meta::get_value( 'title', $post_id );
		$data['description'] = WPSEO_Meta::get_value( 'metadesc', $post_id );
		$data['og_title']    = WPSEO_Meta::get_value( 'opengraph-title', $post_id );
		$data['og_desc']     = WPSEO_Meta::get_value( 'opengraph-description', $post_id );
		$data['og_image']    = WPSEO_Meta::get_value( 'opengraph-image', $post_id );

		// Fallback to default values if Yoast values are empty.
		if ( empty( $data['title'] ) ) {
			$data['title'] = get_the_title( $post_id );
		}

		if ( empty( $data['description'] ) ) {
			$data['description'] = get_the_excerpt( $post_id );
		}

		return $data;
	}
}

/**
 * Custom Schema piece for PDF Documents.
 *
 * Adds DigitalDocument schema for PDF files.
 */
class PDF_Viewer_2026_Schema_Piece {

	/**
	 * The schema context.
	 *
	 * @var object
	 */
	private $context;

	/**
	 * Constructor.
	 *
	 * @param object $context The schema context.
	 */
	public function __construct( $context ) {
		$this->context = $context;
	}

	/**
	 * Check if this piece is needed.
	 *
	 * @return bool
	 */
	public function is_needed() {
		return is_singular( 'pdf_document' );
	}

	/**
	 * Generate the schema data.
	 *
	 * @return array
	 */
	public function generate() {
		$post_id = get_the_ID();
		$post    = get_post( $post_id );

		if ( ! $post ) {
			return array();
		}

		$file_id = get_post_meta( $post_id, '_pdf_file_id', true );

		$schema = array(
			'@type'           => 'DigitalDocument',
			'@id'             => get_permalink( $post_id ) . '#digitaldocument',
			'name'            => get_the_title( $post_id ),
			'description'     => get_the_excerpt( $post_id ),
			'url'             => get_permalink( $post_id ),
			'encodingFormat'  => 'application/pdf',
			'datePublished'   => get_the_date( 'c', $post_id ),
			'dateModified'    => get_the_modified_date( 'c', $post_id ),
			'inLanguage'      => get_bloginfo( 'language' ),
		);

		// Add author.
		$author_id = $post->post_author;
		if ( $author_id ) {
			$schema['author'] = array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', $author_id ),
			);
		}

		// Add featured image as thumbnail.
		if ( has_post_thumbnail( $post_id ) ) {
			$thumbnail_id  = get_post_thumbnail_id( $post_id );
			$thumbnail_url = wp_get_attachment_image_url( $thumbnail_id, 'large' );

			if ( $thumbnail_url ) {
				$schema['thumbnailUrl'] = $thumbnail_url;
			}
		}

		// Add file information if available.
		if ( $file_id ) {
			$file_path = get_attached_file( $file_id );

			if ( $file_path && file_exists( $file_path ) ) {
				$schema['contentSize'] = size_format( filesize( $file_path ) );
			}
		}

		// Add view count.
		$view_count = PDF_Viewer_2026_Post_Type::get_view_count( $post_id );
		if ( $view_count > 0 ) {
			$schema['interactionStatistic'] = array(
				'@type'                => 'InteractionCounter',
				'interactionType'      => 'https://schema.org/ReadAction',
				'userInteractionCount' => $view_count,
			);
		}

		return $schema;
	}
}
