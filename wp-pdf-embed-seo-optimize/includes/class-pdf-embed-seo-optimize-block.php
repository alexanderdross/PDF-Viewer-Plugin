<?php
/**
 * Gutenberg Block for PDF Embed & SEO Optimize.
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Embed_SEO_Block
 *
 * Handles Gutenberg block registration and rendering.
 */
class PDF_Embed_SEO_Block {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Register the Gutenberg block.
	 *
	 * @return void
	 */
	public function register_block() {
		// Skip if Gutenberg is not available.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			'pdf-embed-seo/pdf-viewer',
			array(
				'editor_script'   => 'pdf-embed-seo-block-editor',
				'editor_style'    => 'pdf-embed-seo-block-editor-style',
				'render_callback' => array( $this, 'render_block' ),
				'attributes'      => array(
					'pdfId'  => array(
						'type'    => 'number',
						'default' => 0,
					),
					'width'  => array(
						'type'    => 'string',
						'default' => '100%',
					),
					'height' => array(
						'type'    => 'string',
						'default' => '600px',
					),
					'align'  => array(
						'type'    => 'string',
						'default' => 'none',
					),
				),
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
				),
			)
		);
	}

	/**
	 * Enqueue block editor scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_editor_assets() {
		// Block editor script.
		wp_enqueue_script(
			'pdf-embed-seo-block-editor',
			PDF_EMBED_SEO_PLUGIN_URL . 'blocks/pdf-viewer/editor.js',
			array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-data' ),
			PDF_EMBED_SEO_VERSION,
			true
		);

		// Pass data to the block editor script.
		wp_localize_script(
			'pdf-embed-seo-block-editor',
			'pdfEmbedSeoBlock',
			array(
				'pdfs'   => $this->get_pdf_options(),
				'nonce'  => wp_create_nonce( 'pdf_embed_seo_block' ),
				'i18n'   => array(
					'blockTitle'       => __( 'PDF Viewer', 'wp-pdf-embed-seo-optimize' ),
					'blockDescription' => __( 'Embed a PDF document from your library.', 'wp-pdf-embed-seo-optimize' ),
					'selectPdf'        => __( 'Select a PDF Document', 'wp-pdf-embed-seo-optimize' ),
					'noPdfSelected'    => __( 'No PDF selected. Choose a PDF document from the dropdown.', 'wp-pdf-embed-seo-optimize' ),
					'noPdfsAvailable'  => __( 'No PDF documents available. Create one first.', 'wp-pdf-embed-seo-optimize' ),
					'width'            => __( 'Width', 'wp-pdf-embed-seo-optimize' ),
					'height'           => __( 'Height', 'wp-pdf-embed-seo-optimize' ),
					'dimensions'       => __( 'Dimensions', 'wp-pdf-embed-seo-optimize' ),
					'preview'          => __( 'Preview', 'wp-pdf-embed-seo-optimize' ),
					'viewPdf'          => __( 'View PDF', 'wp-pdf-embed-seo-optimize' ),
				),
			)
		);

		// Block editor styles.
		wp_enqueue_style(
			'pdf-embed-seo-block-editor-style',
			PDF_EMBED_SEO_PLUGIN_URL . 'blocks/pdf-viewer/editor.css',
			array( 'wp-edit-blocks' ),
			PDF_EMBED_SEO_VERSION
		);
	}

	/**
	 * Get available PDF documents as options.
	 *
	 * @return array
	 */
	private function get_pdf_options() {
		$pdfs = get_posts(
			array(
				'post_type'      => 'pdf_document',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		$options = array();

		foreach ( $pdfs as $pdf ) {
			$options[] = array(
				'value'     => $pdf->ID,
				'label'     => $pdf->post_title,
				'permalink' => get_permalink( $pdf->ID ),
				'thumbnail' => get_the_post_thumbnail_url( $pdf->ID, 'thumbnail' ),
			);
		}

		return $options;
	}

	/**
	 * Render the block on the frontend.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render_block( $attributes ) {
		$pdf_id = isset( $attributes['pdfId'] ) ? absint( $attributes['pdfId'] ) : 0;
		$width  = isset( $attributes['width'] ) ? sanitize_text_field( $attributes['width'] ) : '100%';
		$height = isset( $attributes['height'] ) ? sanitize_text_field( $attributes['height'] ) : '600px';
		$align  = isset( $attributes['align'] ) ? sanitize_text_field( $attributes['align'] ) : 'none';

		// Return empty if no PDF selected.
		if ( ! $pdf_id ) {
			return '';
		}

		// Validate post exists and is a PDF document.
		$post = get_post( $pdf_id );
		if ( ! $post || 'pdf_document' !== $post->post_type || 'publish' !== $post->post_status ) {
			return '<p class="pdf-embed-seo-error">' . esc_html__( 'PDF document not found.', 'wp-pdf-embed-seo-optimize' ) . '</p>';
		}

		// Use the shortcode to render the PDF viewer.
		$shortcode = sprintf(
			'[pdf_viewer id="%d" width="%s" height="%s"]',
			$pdf_id,
			esc_attr( $width ),
			esc_attr( $height )
		);

		$output = do_shortcode( $shortcode );

		// Wrap with alignment class if needed.
		if ( 'none' !== $align && ! empty( $align ) ) {
			$output = sprintf(
				'<div class="wp-block-pdf-embed-seo-pdf-viewer align%s">%s</div>',
				esc_attr( $align ),
				$output
			);
		}

		return $output;
	}
}
