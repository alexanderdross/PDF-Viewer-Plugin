<?php
/**
 * Frontend functionality for PDF Viewer 2026.
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Viewer_2026_Frontend
 *
 * Handles frontend rendering and template loading.
 */
class PDF_Viewer_2026_Frontend {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'load_templates' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'pre_get_posts', array( $this, 'modify_archive_query' ) );
		add_action( 'wp', array( $this, 'track_view' ) );
		add_action( 'wp_ajax_pdf_viewer_2026_get_pdf', array( $this, 'ajax_get_pdf' ) );
		add_action( 'wp_ajax_nopriv_pdf_viewer_2026_get_pdf', array( $this, 'ajax_get_pdf' ) );
	}

	/**
	 * Load custom templates for PDF documents.
	 *
	 * @param string $template The template to load.
	 * @return string
	 */
	public function load_templates( $template ) {
		if ( is_singular( 'pdf_document' ) ) {
			// Check if theme has a custom template.
			$theme_template = locate_template( 'single-pdf_document.php' );

			if ( $theme_template ) {
				return $theme_template;
			}

			return PDF_VIEWER_2026_PLUGIN_DIR . 'public/views/single-pdf-document.php';
		}

		if ( is_post_type_archive( 'pdf_document' ) ) {
			// Check if theme has a custom template.
			$theme_template = locate_template( 'archive-pdf_document.php' );

			if ( $theme_template ) {
				return $theme_template;
			}

			return PDF_VIEWER_2026_PLUGIN_DIR . 'public/views/archive-pdf-document.php';
		}

		return $template;
	}

	/**
	 * Enqueue frontend scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Only enqueue on PDF document pages.
		if ( ! is_singular( 'pdf_document' ) && ! is_post_type_archive( 'pdf_document' ) ) {
			return;
		}

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

		// Only load viewer scripts on single PDF pages.
		if ( is_singular( 'pdf_document' ) ) {
			$post_id        = get_the_ID();
			$allow_download = PDF_Viewer_2026_Post_Type::is_download_allowed( $post_id );
			$allow_print    = PDF_Viewer_2026_Post_Type::is_print_allowed( $post_id );
			$viewer_theme   = PDF_Viewer_2026::get_setting( 'viewer_theme', 'light' );

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
						'loading'     => __( 'Loading PDF...', 'pdf-viewer-2026' ),
						'error'       => __( 'Error loading PDF', 'pdf-viewer-2026' ),
						'page'        => __( 'Page', 'pdf-viewer-2026' ),
						'of'          => __( 'of', 'pdf-viewer-2026' ),
						'zoomIn'      => __( 'Zoom In', 'pdf-viewer-2026' ),
						'zoomOut'     => __( 'Zoom Out', 'pdf-viewer-2026' ),
						'download'    => __( 'Download', 'pdf-viewer-2026' ),
						'print'       => __( 'Print', 'pdf-viewer-2026' ),
						'prevPage'    => __( 'Previous Page', 'pdf-viewer-2026' ),
						'nextPage'    => __( 'Next Page', 'pdf-viewer-2026' ),
						'fullscreen'  => __( 'Fullscreen', 'pdf-viewer-2026' ),
					),
				)
			);
		}
	}

	/**
	 * Modify the archive query for PDF documents.
	 *
	 * @param WP_Query $query The query object.
	 * @return WP_Query
	 */
	public function modify_archive_query( $query ) {
		if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'pdf_document' ) ) {
			$posts_per_page = PDF_Viewer_2026::get_setting( 'archive_posts_per_page', 12 );

			/**
			 * Filter the archive query arguments.
			 *
			 * @param array $query_args The query arguments.
			 */
			$posts_per_page = apply_filters( 'pdf_viewer_2026_archive_query', $posts_per_page );

			$query->set( 'posts_per_page', $posts_per_page );
		}

		return $query;
	}

	/**
	 * Track PDF views.
	 *
	 * @return void
	 */
	public function track_view() {
		if ( is_singular( 'pdf_document' ) && ! is_admin() && ! is_preview() ) {
			$post_id = get_the_ID();

			// Don't track views for admins/editors if they're logged in.
			if ( current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			PDF_Viewer_2026_Post_Type::increment_view_count( $post_id );
		}
	}

	/**
	 * AJAX handler to get PDF URL.
	 *
	 * This prevents direct exposure of PDF URLs in the page source.
	 *
	 * @return void
	 */
	public function ajax_get_pdf() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'pdf_viewer_2026_get_pdf' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'pdf-viewer-2026' ) ) );
		}

		// Get post ID.
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		if ( ! $post_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid request.', 'pdf-viewer-2026' ) ) );
		}

		// Check if post exists and is published.
		$post = get_post( $post_id );

		if ( ! $post || 'pdf_document' !== $post->post_type || 'publish' !== $post->post_status ) {
			wp_send_json_error( array( 'message' => __( 'PDF not found.', 'pdf-viewer-2026' ) ) );
		}

		// Get PDF URL.
		$pdf_url = PDF_Viewer_2026_Post_Type::get_pdf_url( $post_id );

		if ( ! $pdf_url ) {
			wp_send_json_error( array( 'message' => __( 'No PDF file attached.', 'pdf-viewer-2026' ) ) );
		}

		// Get permissions.
		$allow_download = PDF_Viewer_2026_Post_Type::is_download_allowed( $post_id );
		$allow_print    = PDF_Viewer_2026_Post_Type::is_print_allowed( $post_id );

		wp_send_json_success(
			array(
				'url'           => $pdf_url,
				'allowDownload' => $allow_download,
				'allowPrint'    => $allow_print,
				'title'         => get_the_title( $post_id ),
			)
		);
	}

	/**
	 * Get the viewer HTML for a PDF.
	 *
	 * @param int $post_id The post ID.
	 * @return string
	 */
	public static function get_viewer_html( $post_id ) {
		$allow_download = PDF_Viewer_2026_Post_Type::is_download_allowed( $post_id );
		$allow_print    = PDF_Viewer_2026_Post_Type::is_print_allowed( $post_id );
		$viewer_theme   = PDF_Viewer_2026::get_setting( 'viewer_theme', 'light' );

		ob_start();
		?>
		<div class="pdf-viewer-2026-container" data-theme="<?php echo esc_attr( $viewer_theme ); ?>">
			<div class="pdf-viewer-2026-toolbar">
				<div class="pdf-viewer-2026-toolbar-left">
					<button type="button" class="pdf-viewer-2026-btn pdf-viewer-2026-prev" aria-label="<?php esc_attr_e( 'Previous Page', 'pdf-viewer-2026' ); ?>">
						<span class="dashicons dashicons-arrow-left-alt2"></span>
					</button>
					<span class="pdf-viewer-2026-page-info">
						<input type="number" class="pdf-viewer-2026-page-input" min="1" value="1" aria-label="<?php esc_attr_e( 'Current Page', 'pdf-viewer-2026' ); ?>">
						<span class="pdf-viewer-2026-page-separator">/</span>
						<span class="pdf-viewer-2026-total-pages">0</span>
					</span>
					<button type="button" class="pdf-viewer-2026-btn pdf-viewer-2026-next" aria-label="<?php esc_attr_e( 'Next Page', 'pdf-viewer-2026' ); ?>">
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</button>
				</div>

				<div class="pdf-viewer-2026-toolbar-center">
					<button type="button" class="pdf-viewer-2026-btn pdf-viewer-2026-zoom-out" aria-label="<?php esc_attr_e( 'Zoom Out', 'pdf-viewer-2026' ); ?>">
						<span class="dashicons dashicons-minus"></span>
					</button>
					<span class="pdf-viewer-2026-zoom-level">100%</span>
					<button type="button" class="pdf-viewer-2026-btn pdf-viewer-2026-zoom-in" aria-label="<?php esc_attr_e( 'Zoom In', 'pdf-viewer-2026' ); ?>">
						<span class="dashicons dashicons-plus"></span>
					</button>
				</div>

				<div class="pdf-viewer-2026-toolbar-right">
					<?php if ( $allow_print ) : ?>
						<button type="button" class="pdf-viewer-2026-btn pdf-viewer-2026-print" aria-label="<?php esc_attr_e( 'Print', 'pdf-viewer-2026' ); ?>">
							<span class="dashicons dashicons-printer"></span>
						</button>
					<?php endif; ?>

					<?php if ( $allow_download ) : ?>
						<button type="button" class="pdf-viewer-2026-btn pdf-viewer-2026-download" aria-label="<?php esc_attr_e( 'Download', 'pdf-viewer-2026' ); ?>">
							<span class="dashicons dashicons-download"></span>
						</button>
					<?php endif; ?>

					<button type="button" class="pdf-viewer-2026-btn pdf-viewer-2026-fullscreen" aria-label="<?php esc_attr_e( 'Fullscreen', 'pdf-viewer-2026' ); ?>">
						<span class="dashicons dashicons-fullscreen-alt"></span>
					</button>
				</div>
			</div>

			<div class="pdf-viewer-2026-viewer">
				<div class="pdf-viewer-2026-loading">
					<span class="spinner"></span>
					<span class="pdf-viewer-2026-loading-text"><?php esc_html_e( 'Loading PDF...', 'pdf-viewer-2026' ); ?></span>
				</div>
				<canvas class="pdf-viewer-2026-canvas"></canvas>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
