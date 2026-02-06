<?php
/**
 * Frontend functionality for PDF Viewer 2026.
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Embed_SEO_Frontend
 *
 * Handles frontend rendering and template loading.
 */
class PDF_Embed_SEO_Frontend {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'load_templates' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'pre_get_posts', array( $this, 'modify_archive_query' ) );
		add_action( 'wp', array( $this, 'track_view' ) );
		add_action( 'wp_ajax_pdf_embed_seo_get_pdf', array( $this, 'ajax_get_pdf' ) );
		add_action( 'wp_ajax_nopriv_pdf_embed_seo_get_pdf', array( $this, 'ajax_get_pdf' ) );
		add_action( 'wp_head', array( $this, 'output_favicon' ), 5 );
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

			return PDF_EMBED_SEO_PLUGIN_DIR . 'public/views/single-pdf-document.php';
		}

		if ( is_post_type_archive( 'pdf_document' ) ) {
			// Check if theme has a custom template.
			$theme_template = locate_template( 'archive-pdf_document.php' );

			if ( $theme_template ) {
				return $theme_template;
			}

			return PDF_EMBED_SEO_PLUGIN_DIR . 'public/views/archive-pdf-document.php';
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

		// Enqueue dashicons on frontend (required for toolbar button icons).
		wp_enqueue_style( 'dashicons' );

		// Viewer styles.
		wp_enqueue_style(
			'pdf-embed-seo-optimize-viewer',
			PDF_EMBED_SEO_PLUGIN_URL . 'public/css/viewer-styles.css',
			array( 'dashicons' ),
			PDF_EMBED_SEO_VERSION
		);

		// Only load viewer scripts on single PDF pages.
		if ( is_singular( 'pdf_document' ) ) {
			$post_id        = get_the_ID();
			$allow_download = PDF_Embed_SEO_Post_Type::is_download_allowed( $post_id );
			$allow_print    = PDF_Embed_SEO_Post_Type::is_print_allowed( $post_id );
			$viewer_theme   = PDF_Embed_SEO::get_setting( 'viewer_theme', 'light' );

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
					'restUrl'       => rest_url( 'pdf-embed-seo/v1/documents/' ),
					'restNonce'     => wp_create_nonce( 'wp_rest' ),
					'postId'        => $post_id,
					'allowDownload' => $allow_download,
					'allowPrint'    => $allow_print,
					'viewerTheme'   => $viewer_theme,
					'strings'       => array(
						'loading'     => __( 'Loading PDF...', 'pdf-embed-seo-optimize' ),
						'error'       => __( 'Error loading PDF', 'pdf-embed-seo-optimize' ),
						'page'        => __( 'Page', 'pdf-embed-seo-optimize' ),
						'of'          => __( 'of', 'pdf-embed-seo-optimize' ),
						'zoomIn'      => __( 'Zoom In', 'pdf-embed-seo-optimize' ),
						'zoomOut'     => __( 'Zoom Out', 'pdf-embed-seo-optimize' ),
						'download'    => __( 'Download', 'pdf-embed-seo-optimize' ),
						'print'       => __( 'Print', 'pdf-embed-seo-optimize' ),
						'prevPage'    => __( 'Previous Page', 'pdf-embed-seo-optimize' ),
						'nextPage'    => __( 'Next Page', 'pdf-embed-seo-optimize' ),
						'fullscreen'  => __( 'Fullscreen', 'pdf-embed-seo-optimize' ),
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
			$posts_per_page = PDF_Embed_SEO::get_setting( 'archive_posts_per_page', 12 );

			/**
			 * Filter the archive query arguments.
			 *
			 * @param array $query_args The query arguments.
			 */
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Established public API hook.
			$posts_per_page = apply_filters( 'pdf_embed_seo_archive_query', $posts_per_page );

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

			PDF_Embed_SEO_Post_Type::increment_view_count( $post_id );
		}
	}

	/**
	 * Output custom favicon for PDF pages.
	 *
	 * @return void
	 */
	public function output_favicon() {
		// Only output on PDF document pages.
		if ( ! is_singular( 'pdf_document' ) && ! is_post_type_archive( 'pdf_document' ) ) {
			return;
		}

		$favicon_url = PDF_Embed_SEO::get_setting( 'favicon_url', '' );

		if ( empty( $favicon_url ) ) {
			return;
		}

		// Determine the favicon type based on file extension.
		$extension = strtolower( pathinfo( wp_parse_url( $favicon_url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
		$type      = 'image/x-icon';

		switch ( $extension ) {
			case 'png':
				$type = 'image/png';
				break;
			case 'gif':
				$type = 'image/gif';
				break;
			case 'svg':
				$type = 'image/svg+xml';
				break;
			case 'ico':
			default:
				$type = 'image/x-icon';
				break;
		}

		// Output the favicon link tags.
		echo '<link rel="icon" href="' . esc_url( $favicon_url ) . '" type="' . esc_attr( $type ) . '">' . "\n";
		echo '<link rel="shortcut icon" href="' . esc_url( $favicon_url ) . '" type="' . esc_attr( $type ) . '">' . "\n";

		// Also add apple-touch-icon for iOS devices if it's a PNG.
		if ( 'image/png' === $type ) {
			echo '<link rel="apple-touch-icon" href="' . esc_url( $favicon_url ) . '">' . "\n";
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
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'pdf_embed_seo_get_pdf' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'pdf-embed-seo-optimize' ) ) );
		}

		// Get post ID.
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		if ( ! $post_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid request.', 'pdf-embed-seo-optimize' ) ) );
		}

		// Check if post exists and is published.
		$post = get_post( $post_id );

		if ( ! $post || 'pdf_document' !== $post->post_type || 'publish' !== $post->post_status ) {
			wp_send_json_error( array( 'message' => __( 'PDF not found.', 'pdf-embed-seo-optimize' ) ) );
		}

		// Get PDF URL.
		$pdf_url = PDF_Embed_SEO_Post_Type::get_pdf_url( $post_id );

		if ( ! $pdf_url ) {
			wp_send_json_error( array( 'message' => __( 'No PDF file attached.', 'pdf-embed-seo-optimize' ) ) );
		}

		// Get permissions.
		$allow_download = PDF_Embed_SEO_Post_Type::is_download_allowed( $post_id );
		$allow_print    = PDF_Embed_SEO_Post_Type::is_print_allowed( $post_id );

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
		// Check password protection (Premium feature).
		if ( class_exists( 'PDF_Embed_SEO_Premium_Password' ) ) {
			if ( PDF_Embed_SEO_Premium_Password::is_protected( $post_id ) && ! PDF_Embed_SEO_Premium_Password::has_access( $post_id ) ) {
				return PDF_Embed_SEO_Premium_Password::get_password_form( $post_id );
			}
		}

		$allow_download = PDF_Embed_SEO_Post_Type::is_download_allowed( $post_id );
		$allow_print    = PDF_Embed_SEO_Post_Type::is_print_allowed( $post_id );
		$viewer_theme   = PDF_Embed_SEO::get_setting( 'viewer_theme', 'light' );

		ob_start();
		?>
		<div class="pdf-embed-seo-optimize-container" data-theme="<?php echo esc_attr( $viewer_theme ); ?>">
			<div class="pdf-embed-seo-optimize-toolbar">
				<div class="pdf-embed-seo-optimize-toolbar-left">
					<button type="button" class="pdf-embed-seo-optimize-btn pdf-embed-seo-optimize-prev" aria-label="<?php esc_attr_e( 'Previous Page', 'pdf-embed-seo-optimize' ); ?>">
						<span class="dashicons dashicons-arrow-left-alt2"></span>
					</button>
					<span class="pdf-embed-seo-optimize-page-info">
						<input type="number" class="pdf-embed-seo-optimize-page-input" min="1" value="1" aria-label="<?php esc_attr_e( 'Current Page', 'pdf-embed-seo-optimize' ); ?>">
						<span class="pdf-embed-seo-optimize-page-separator">/</span>
						<span class="pdf-embed-seo-optimize-total-pages">0</span>
					</span>
					<button type="button" class="pdf-embed-seo-optimize-btn pdf-embed-seo-optimize-next" aria-label="<?php esc_attr_e( 'Next Page', 'pdf-embed-seo-optimize' ); ?>">
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</button>
				</div>

				<div class="pdf-embed-seo-optimize-toolbar-center">
					<button type="button" class="pdf-embed-seo-optimize-btn pdf-embed-seo-optimize-zoom-out" aria-label="<?php esc_attr_e( 'Zoom Out', 'pdf-embed-seo-optimize' ); ?>">
						<span class="dashicons dashicons-minus"></span>
					</button>
					<span class="pdf-embed-seo-optimize-zoom-level">100%</span>
					<button type="button" class="pdf-embed-seo-optimize-btn pdf-embed-seo-optimize-zoom-in" aria-label="<?php esc_attr_e( 'Zoom In', 'pdf-embed-seo-optimize' ); ?>">
						<span class="dashicons dashicons-plus"></span>
					</button>
				</div>

				<div class="pdf-embed-seo-optimize-toolbar-right">
					<?php if ( $allow_print ) : ?>
						<button type="button" class="pdf-embed-seo-optimize-btn pdf-embed-seo-optimize-print" aria-label="<?php esc_attr_e( 'Print', 'pdf-embed-seo-optimize' ); ?>">
							<span class="dashicons dashicons-printer"></span>
						</button>
					<?php endif; ?>

					<?php if ( $allow_download ) : ?>
						<button type="button" class="pdf-embed-seo-optimize-btn pdf-embed-seo-optimize-download" aria-label="<?php esc_attr_e( 'Download', 'pdf-embed-seo-optimize' ); ?>">
							<span class="dashicons dashicons-download"></span>
						</button>
					<?php endif; ?>

					<button type="button" class="pdf-embed-seo-optimize-btn pdf-embed-seo-optimize-fullscreen" aria-label="<?php esc_attr_e( 'Fullscreen', 'pdf-embed-seo-optimize' ); ?>">
						<span class="dashicons dashicons-fullscreen-alt"></span>
					</button>
				</div>
			</div>

			<div class="pdf-embed-seo-optimize-viewer">
				<div class="pdf-embed-seo-optimize-loading">
					<span class="spinner"></span>
					<span class="pdf-embed-seo-optimize-loading-text"><?php esc_html_e( 'Loading PDF...', 'pdf-embed-seo-optimize' ); ?></span>
				</div>
				<canvas class="pdf-embed-seo-optimize-canvas"></canvas>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
