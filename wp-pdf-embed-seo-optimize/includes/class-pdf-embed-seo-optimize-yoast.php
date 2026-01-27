<?php
/**
 * Yoast SEO Integration and Schema Markup for PDF Viewer 2026.
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Embed_SEO_Yoast
 *
 * Handles integration with Yoast SEO plugin and outputs JSON-LD schema markup.
 */
class PDF_Embed_SEO_Yoast {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Always output JSON-LD schema for PDF documents (independent of Yoast).
		add_action( 'wp_head', array( $this, 'output_json_ld_schema' ), 1 );

		// Yoast-specific integrations (only if Yoast is active).
		if ( $this->is_yoast_active() ) {
			add_filter( 'wpseo_accessible_post_types', array( $this, 'add_post_type_support' ) );
			add_filter( 'wpseo_opengraph_image', array( $this, 'customize_og_image' ) );
			add_filter( 'wpseo_twitter_image', array( $this, 'customize_twitter_image' ) );
			add_filter( 'wpseo_sitemap_post_type_archive_link', array( $this, 'sitemap_archive_link' ), 10, 2 );
			add_filter( 'wpseo_robots', array( $this, 'modify_robots' ) );

			// Prevent duplicate schema when Yoast is active.
			add_filter( 'wpseo_schema_graph_pieces', array( $this, 'add_schema_pieces' ), 10, 2 );
		}
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
	 * Output JSON-LD schema in the head.
	 *
	 * This outputs schema markup for PDF documents and archive page, independent of Yoast SEO.
	 *
	 * @return void
	 */
	public function output_json_ld_schema() {
		// Handle archive page.
		if ( is_post_type_archive( 'pdf_document' ) ) {
			$this->output_archive_schema();
			return;
		}

		// Handle single PDF document pages.
		if ( ! is_singular( 'pdf_document' ) ) {
			return;
		}

		// If Yoast is active, it will handle schema output via its own system.
		if ( $this->is_yoast_active() ) {
			return;
		}

		$post_id = get_the_ID();
		$schema  = $this->generate_digital_document_schema( $post_id );

		if ( empty( $schema ) ) {
			return;
		}

		// Output JSON-LD.
		echo "\n<!-- PDF Embed & SEO Optimize - DigitalDocument Schema -->\n";
		echo '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		echo "\n</script>\n";
	}

	/**
	 * Output JSON-LD schema for the PDF archive page.
	 *
	 * @return void
	 */
	public function output_archive_schema() {
		$archive_url = get_post_type_archive_link( 'pdf_document' );
		$site_name   = get_bloginfo( 'name' );

		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'CollectionPage',
			'@id'         => $archive_url . '#collectionpage',
			'name'        => __( 'PDF Documents', 'pdf-embed-seo-optimize' ),
			'description' => __( 'Browse all available PDF documents.', 'pdf-embed-seo-optimize' ),
			'url'         => $archive_url,
			'isPartOf'    => array(
				'@type' => 'WebSite',
				'@id'   => home_url( '/' ) . '#website',
				'name'  => $site_name,
				'url'   => home_url( '/' ),
			),
			'mentions'    => array(
				'@type'       => 'Organization',
				'name'        => 'Dross:Media',
				'url'         => 'https://dross.net/#media',
				'description' => 'AI Search, GEO & SEO Strategy for the search of tomorrow – directly from the Web & Search Lead of a pharmaceutical company.',
			),
		);

		/**
		 * Filter the archive page schema data.
		 *
		 * @param array $schema The schema data.
		 */
		$schema = apply_filters( 'pdf_embed_seo_archive_schema_data', $schema );

		// Output JSON-LD.
		echo "\n<!-- PDF Embed & SEO Optimize - Archive Page Schema -->\n";
		echo '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		echo "\n</script>\n";
	}

	/**
	 * Generate DigitalDocument schema for a PDF.
	 *
	 * @param int $post_id The post ID.
	 * @return array The schema data.
	 */
	public function generate_digital_document_schema( $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post || 'pdf_document' !== $post->post_type ) {
			return array();
		}

		$file_id   = get_post_meta( $post_id, '_pdf_file_id', true );
		$file_url  = get_post_meta( $post_id, '_pdf_file_url', true );
		$site_name = get_bloginfo( 'name' );
		$site_url  = home_url( '/' );

		// Build the main schema.
		$schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'DigitalDocument',
			'@id'             => get_permalink( $post_id ) . '#digitaldocument',
			'name'            => get_the_title( $post_id ),
			'headline'        => get_the_title( $post_id ),
			'url'             => get_permalink( $post_id ),
			'encodingFormat'  => 'application/pdf',
			'fileFormat'      => 'application/pdf',
			'datePublished'   => get_the_date( 'c', $post_id ),
			'dateModified'    => get_the_modified_date( 'c', $post_id ),
			'inLanguage'      => get_bloginfo( 'language' ),
		);

		// Add description if available.
		$excerpt = get_the_excerpt( $post_id );
		if ( ! empty( $excerpt ) ) {
			$schema['description'] = wp_strip_all_tags( $excerpt );
			$schema['abstract']    = wp_strip_all_tags( $excerpt );
		}

		// Add author information.
		$author_id = $post->post_author;
		if ( $author_id ) {
			$author_name = get_the_author_meta( 'display_name', $author_id );
			$author_url  = get_author_posts_url( $author_id );

			$schema['author'] = array(
				'@type' => 'Person',
				'@id'   => $author_url . '#author',
				'name'  => $author_name,
				'url'   => $author_url,
			);

			$schema['creator'] = array(
				'@type' => 'Person',
				'name'  => $author_name,
			);
		}

		// Add publisher (the website).
		$schema['publisher'] = array(
			'@type' => 'Organization',
			'@id'   => $site_url . '#organization',
			'name'  => $site_name,
			'url'   => $site_url,
		);

		// Add logo if site has one.
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
			if ( $logo_url ) {
				$schema['publisher']['logo'] = array(
					'@type' => 'ImageObject',
					'url'   => $logo_url,
				);
			}
		}

		// Add featured image / thumbnail.
		if ( has_post_thumbnail( $post_id ) ) {
			$thumbnail_id   = get_post_thumbnail_id( $post_id );
			$thumbnail_data = wp_get_attachment_image_src( $thumbnail_id, 'large' );

			if ( $thumbnail_data ) {
				$schema['thumbnailUrl'] = $thumbnail_data[0];
				$schema['image']        = array(
					'@type'  => 'ImageObject',
					'@id'    => get_permalink( $post_id ) . '#primaryimage',
					'url'    => $thumbnail_data[0],
					'width'  => $thumbnail_data[1],
					'height' => $thumbnail_data[2],
				);
			}
		}

		// Add permissions info.
		$allow_download = PDF_Embed_SEO_Post_Type::is_download_allowed( $post_id );
		$allow_print    = PDF_Embed_SEO_Post_Type::is_print_allowed( $post_id );

		$permissions = array();
		if ( $allow_download ) {
			$permissions[] = 'Download';
		}
		if ( $allow_print ) {
			$permissions[] = 'Print';
		}

		if ( ! empty( $permissions ) ) {
			$schema['usageInfo'] = implode( ', ', $permissions ) . ' allowed';
		}

		// Add breadcrumb reference.
		$schema['isPartOf'] = array(
			'@type' => 'WebPage',
			'@id'   => get_permalink( $post_id ) . '#webpage',
			'url'   => get_permalink( $post_id ),
			'name'  => get_the_title( $post_id ),
		);

		// Add main entity of page.
		$schema['mainEntityOfPage'] = array(
			'@type' => 'WebPage',
			'@id'   => get_permalink( $post_id ),
		);

		/**
		 * Filter the DigitalDocument schema data.
		 *
		 * @param array $schema  The schema data.
		 * @param int   $post_id The post ID.
		 */
		return apply_filters( 'pdf_embed_seo_schema_data', $schema, $post_id );
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
	 * Add custom schema pieces for PDF documents (Yoast integration).
	 *
	 * @param array $pieces  The current schema pieces.
	 * @param mixed $context The schema context.
	 * @return array
	 */
	public function add_schema_pieces( $pieces, $context ) {
		if ( ! is_singular( 'pdf_document' ) ) {
			return $pieces;
		}

		$pieces[] = new PDF_Embed_SEO_Schema_Piece( $context );

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
			$data['title']       = get_the_title( $post_id );
			$data['description'] = get_the_excerpt( $post_id );
			return $data;
		}

		$data['title']       = WPSEO_Meta::get_value( 'title', $post_id );
		$data['description'] = WPSEO_Meta::get_value( 'metadesc', $post_id );
		$data['og_title']    = WPSEO_Meta::get_value( 'opengraph-title', $post_id );
		$data['og_desc']     = WPSEO_Meta::get_value( 'opengraph-description', $post_id );
		$data['og_image']    = WPSEO_Meta::get_value( 'opengraph-image', $post_id );

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
 * Custom Schema piece for PDF Documents (Yoast integration).
 *
 * Adds DigitalDocument schema when Yoast SEO is active.
 */
class PDF_Embed_SEO_Schema_Piece {

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

		$file_id  = get_post_meta( $post_id, '_pdf_file_id', true );
		$file_url = get_post_meta( $post_id, '_pdf_file_url', true );

		$schema = array(
			'@type'          => 'DigitalDocument',
			'@id'            => get_permalink( $post_id ) . '#digitaldocument',
			'name'           => get_the_title( $post_id ),
			'headline'       => get_the_title( $post_id ),
			'description'    => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
			'url'            => get_permalink( $post_id ),
			'encodingFormat' => 'application/pdf',
			'fileFormat'     => 'application/pdf',
			'datePublished'  => get_the_date( 'c', $post_id ),
			'dateModified'   => get_the_modified_date( 'c', $post_id ),
			'inLanguage'     => get_bloginfo( 'language' ),
		);

		// Add author.
		$author_id = $post->post_author;
		if ( $author_id ) {
			$schema['author'] = array(
				'@type' => 'Person',
				'@id'   => get_author_posts_url( $author_id ) . '#author',
				'name'  => get_the_author_meta( 'display_name', $author_id ),
			);
		}

		// Add featured image.
		if ( has_post_thumbnail( $post_id ) ) {
			$thumbnail_id   = get_post_thumbnail_id( $post_id );
			$thumbnail_data = wp_get_attachment_image_src( $thumbnail_id, 'large' );

			if ( $thumbnail_data ) {
				$schema['thumbnailUrl'] = $thumbnail_data[0];
				$schema['image']        = array(
					'@type'  => 'ImageObject',
					'url'    => $thumbnail_data[0],
					'width'  => $thumbnail_data[1],
					'height' => $thumbnail_data[2],
				);
			}
		}

		// Main entity reference.
		$schema['mainEntityOfPage'] = array(
			'@id' => get_permalink( $post_id ),
		);

		return $schema;
	}
}
