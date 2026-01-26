<?php
/**
 * Shortcodes for PDF Viewer 2026.
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Viewer_2026_Shortcodes
 *
 * Handles all shortcode functionality.
 */
class PDF_Viewer_2026_Shortcodes {

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
			return '<p class="pdf-viewer-2026-error">' . esc_html__( 'PDF document not found.', 'pdf-viewer-2026' ) . '</p>';
		}

		// Check if PDF file exists.
		$pdf_url = PDF_Viewer_2026_Post_Type::get_pdf_url( $post_id );

		if ( ! $pdf_url ) {
			return '<p class="pdf-viewer-2026-error">' . esc_html__( 'No PDF file attached to this document.', 'pdf-viewer-2026' ) . '</p>';
		}

		// Enqueue scripts.
		$this->enqueue_viewer_scripts( $post_id );

		// Get viewer HTML.
		$viewer_html = PDF_Viewer_2026_Frontend::get_viewer_html( $post_id );

		// Wrap with custom dimensions.
		$width  = esc_attr( $atts['width'] );
		$height = esc_attr( $atts['height'] );

		return sprintf(
			'<div class="pdf-viewer-2026-shortcode" data-post-id="%d" style="width: %s; height: %s;">%s</div>',
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
		$query_args = apply_filters( 'pdf_viewer_2026_sitemap_query_args', $query_args, $atts );

		$pdfs = new WP_Query( $query_args );

		if ( ! $pdfs->have_posts() ) {
			return '<p class="pdf-viewer-2026-no-pdfs">' . esc_html__( 'No PDF documents found.', 'pdf-viewer-2026' ) . '</p>';
		}

		// Build output.
		$output = '<ul class="pdf-viewer-2026-sitemap">';

		while ( $pdfs->have_posts() ) {
			$pdfs->the_post();

			$title     = get_the_title();
			$permalink = get_permalink();
			$thumbnail = '';

			// Get thumbnail if available.
			if ( has_post_thumbnail() ) {
				$thumbnail = get_the_post_thumbnail( get_the_ID(), 'thumbnail', array( 'class' => 'pdf-viewer-2026-sitemap-thumb' ) );
			}

			$output .= sprintf(
				'<li class="pdf-viewer-2026-sitemap-item">%s<a href="%s">%s</a></li>',
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
		$allow_download = PDF_Viewer_2026_Post_Type::is_download_allowed( $post_id );
		$allow_print    = PDF_Viewer_2026_Post_Type::is_print_allowed( $post_id );
		$viewer_theme   = PDF_Viewer_2026::get_setting( 'viewer_theme', 'light' );

		// PDF.js library from Mozilla CDN.
		$pdfjs_version = '4.0.379';

		wp_enqueue_script(
			'pdfjs',
			'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/' . $pdfjs_version . '/pdf.min.js',
			array(),
			$pdfjs_version,
			true
		);

		// Set the worker source from CDN.
		wp_add_inline_script(
			'pdfjs',
			'pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/' . $pdfjs_version . '/pdf.worker.min.js";'
		);

		// Viewer styles.
		wp_enqueue_style(
			'pdf-viewer-2026-viewer',
			PDF_VIEWER_2026_PLUGIN_URL . 'public/css/viewer-styles.css',
			array(),
			PDF_VIEWER_2026_VERSION
		);

		// Enqueue dashicons for the toolbar buttons.
		wp_enqueue_style( 'dashicons' );

		// Viewer scripts.
		wp_enqueue_script(
			'pdf-viewer-2026-viewer',
			PDF_VIEWER_2026_PLUGIN_URL . 'public/js/viewer-scripts.js',
			array( 'jquery', 'pdfjs' ),
			PDF_VIEWER_2026_VERSION,
			true
		);

		wp_localize_script(
			'pdf-viewer-2026-viewer',
			'pdfViewer2026',
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'pdf_viewer_2026_get_pdf' ),
				'postId'        => $post_id,
				'allowDownload' => $allow_download,
				'allowPrint'    => $allow_print,
				'viewerTheme'   => $viewer_theme,
				'strings'       => array(
					'loading'    => __( 'Loading PDF...', 'pdf-viewer-2026' ),
					'error'      => __( 'Error loading PDF', 'pdf-viewer-2026' ),
					'page'       => __( 'Page', 'pdf-viewer-2026' ),
					'of'         => __( 'of', 'pdf-viewer-2026' ),
					'zoomIn'     => __( 'Zoom In', 'pdf-viewer-2026' ),
					'zoomOut'    => __( 'Zoom Out', 'pdf-viewer-2026' ),
					'download'   => __( 'Download', 'pdf-viewer-2026' ),
					'print'      => __( 'Print', 'pdf-viewer-2026' ),
					'prevPage'   => __( 'Previous Page', 'pdf-viewer-2026' ),
					'nextPage'   => __( 'Next Page', 'pdf-viewer-2026' ),
					'fullscreen' => __( 'Fullscreen', 'pdf-viewer-2026' ),
				),
			)
		);
	}
}
