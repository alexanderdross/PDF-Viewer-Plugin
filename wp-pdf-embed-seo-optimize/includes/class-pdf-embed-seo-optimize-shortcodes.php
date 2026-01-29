<?php
/**
 * Shortcodes for PDF Viewer 2026.
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Embed_SEO_Shortcodes
 *
 * Handles all shortcode functionality.
 */
class PDF_Embed_SEO_Shortcodes {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'pdf_viewer', array( $this, 'render_pdf_viewer' ) );
		add_shortcode( 'pdf_viewer_sitemap', array( $this, 'render_pdf_sitemap' ) );
	}

	/**
	 * Render the PDF viewer shortcode.
	 *
	 * Usage: [pdf_viewer] or [pdf_viewer id="123"] or [pdf_viewer id="123" width="100%" height="600px"]
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_pdf_viewer( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'     => 0,
				'width'  => '100%',
				'height' => '800px',
			),
			$atts,
			'pdf_viewer'
		);

		// Get post ID.
		$post_id = absint( $atts['id'] );

		// If no ID provided, use current post.
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		// Validate post.
		$post = get_post( $post_id );

		if ( ! $post || 'pdf_document' !== $post->post_type || 'publish' !== $post->post_status ) {
			return '<p class="pdf-embed-seo-optimize-error">' . esc_html__( 'PDF document not found.', 'pdf-embed-seo-optimize' ) . '</p>';
		}

		// Check if PDF file exists.
		$pdf_url = PDF_Embed_SEO_Post_Type::get_pdf_url( $post_id );

		if ( ! $pdf_url ) {
			return '<p class="pdf-embed-seo-optimize-error">' . esc_html__( 'No PDF file attached to this document.', 'pdf-embed-seo-optimize' ) . '</p>';
		}

		// Enqueue scripts.
		$this->enqueue_viewer_scripts( $post_id );

		// Get viewer HTML.
		$viewer_html = PDF_Embed_SEO_Frontend::get_viewer_html( $post_id );

		// Wrap with custom dimensions.
		$width  = esc_attr( $atts['width'] );
		$height = esc_attr( $atts['height'] );

		return sprintf(
			'<div class="pdf-embed-seo-optimize-shortcode" data-post-id="%d" style="width: %s; height: %s;">%s</div>',
			esc_attr( $post_id ),
			$width,
			$height,
			$viewer_html
		);
	}

	/**
	 * Render the PDF sitemap shortcode.
	 *
	 * Usage: [pdf_viewer_sitemap] or [pdf_viewer_sitemap orderby="date" order="DESC" limit="10"]
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_pdf_sitemap( $atts ) {
		$atts = shortcode_atts(
			array(
				'orderby' => 'title',
				'order'   => 'ASC',
				'limit'   => -1,
			),
			$atts,
			'pdf_viewer_sitemap'
		);

		// Sanitize attributes.
		$valid_orderby = array( 'title', 'date', 'menu_order', 'modified' );
		$orderby       = in_array( $atts['orderby'], $valid_orderby, true ) ? $atts['orderby'] : 'title';
		$order         = in_array( strtoupper( $atts['order'] ), array( 'ASC', 'DESC' ), true ) ? strtoupper( $atts['order'] ) : 'ASC';
		$limit         = intval( $atts['limit'] );

		// Query PDF documents.
		$query_args = array(
			'post_type'      => 'pdf_document',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => $orderby,
			'order'          => $order,
		);

		/**
		 * Filter the sitemap query arguments.
		 *
		 * @param array $query_args The query arguments.
		 * @param array $atts       The shortcode attributes.
		 */
		$query_args = apply_filters( 'pdf_embed_seo_sitemap_query_args', $query_args, $atts );

		$pdfs = new WP_Query( $query_args );

		if ( ! $pdfs->have_posts() ) {
			return '<p class="pdf-embed-seo-optimize-no-pdfs">' . esc_html__( 'No PDF documents found.', 'pdf-embed-seo-optimize' ) . '</p>';
		}

		// Build output.
		$output = '<ul class="pdf-embed-seo-optimize-sitemap">';

		while ( $pdfs->have_posts() ) {
			$pdfs->the_post();

			$title     = get_the_title();
			$permalink = get_permalink();
			$thumbnail = '';

			// Get thumbnail if available.
			if ( has_post_thumbnail() ) {
				$thumbnail = get_the_post_thumbnail( get_the_ID(), 'thumbnail', array( 'class' => 'pdf-embed-seo-optimize-sitemap-thumb' ) );
			}

			$output .= sprintf(
				'<li class="pdf-embed-seo-optimize-sitemap-item">%s<a href="%s">%s</a></li>',
				$thumbnail,
				esc_url( $permalink ),
				esc_html( $title )
			);
		}

		$output .= '</ul>';

		wp_reset_postdata();

		return $output;
	}

	/**
	 * Enqueue viewer scripts for shortcode.
	 *
	 * @param int $post_id The post ID.
	 * @return void
	 */
	private function enqueue_viewer_scripts( $post_id ) {
		$allow_download = PDF_Embed_SEO_Post_Type::is_download_allowed( $post_id );
		$allow_print    = PDF_Embed_SEO_Post_Type::is_print_allowed( $post_id );
		$viewer_theme   = PDF_Embed_SEO::get_setting( 'viewer_theme', 'light' );

		// PDF.js library - bundled locally (v3.x for UMD compatibility).
		$pdfjs_version = '3.11.174';

		wp_enqueue_script(
			'pdfjs',
			PDF_EMBED_SEO_PLUGIN_URL . 'assets/pdfjs/pdf.min.js',
			array(),
			$pdfjs_version,
			true
		);

		// Set the worker source from local assets.
		wp_add_inline_script(
			'pdfjs',
			'pdfjsLib.GlobalWorkerOptions.workerSrc = "' . esc_js( PDF_EMBED_SEO_PLUGIN_URL . 'assets/pdfjs/pdf.worker.min.js' ) . '";'
		);

		// Viewer styles.
		wp_enqueue_style(
			'pdf-embed-seo-optimize-viewer',
			PDF_EMBED_SEO_PLUGIN_URL . 'public/css/viewer-styles.css',
			array(),
			PDF_EMBED_SEO_VERSION
		);

		// Enqueue dashicons for the toolbar buttons.
		wp_enqueue_style( 'dashicons' );

		// Viewer scripts.
		wp_enqueue_script(
			'pdf-embed-seo-optimize-viewer',
			PDF_EMBED_SEO_PLUGIN_URL . 'public/js/viewer-scripts.js',
			array( 'jquery', 'pdfjs' ),
			PDF_EMBED_SEO_VERSION,
			true
		);

		wp_localize_script(
			'pdf-embed-seo-optimize-viewer',
			'pdfEmbedSeo',
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'pdf_embed_seo_get_pdf' ),
				'postId'        => $post_id,
				'allowDownload' => $allow_download,
				'allowPrint'    => $allow_print,
				'viewerTheme'   => $viewer_theme,
				'strings'       => array(
					'loading'    => __( 'Loading PDF...', 'pdf-embed-seo-optimize' ),
					'error'      => __( 'Error loading PDF', 'pdf-embed-seo-optimize' ),
					'page'       => __( 'Page', 'pdf-embed-seo-optimize' ),
					'of'         => __( 'of', 'pdf-embed-seo-optimize' ),
					'zoomIn'     => __( 'Zoom In', 'pdf-embed-seo-optimize' ),
					'zoomOut'    => __( 'Zoom Out', 'pdf-embed-seo-optimize' ),
					'download'   => __( 'Download', 'pdf-embed-seo-optimize' ),
					'print'      => __( 'Print', 'pdf-embed-seo-optimize' ),
					'prevPage'   => __( 'Previous Page', 'pdf-embed-seo-optimize' ),
					'nextPage'   => __( 'Next Page', 'pdf-embed-seo-optimize' ),
					'fullscreen' => __( 'Fullscreen', 'pdf-embed-seo-optimize' ),
				),
			)
		);
	}
}
