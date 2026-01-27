<?php
/**
 * Premium Viewer Enhancements
 *
 * Adds text search, bookmarks panel, and other viewer features.
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium viewer class.
 */
class PDF_Embed_SEO_Premium_Viewer {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Enqueue premium viewer scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Filter viewer options.
		add_filter( 'pdf_embed_seo_viewer_options', array( $this, 'add_premium_options' ), 10, 2 );

		// Add viewer controls.
		add_filter( 'pdf_embed_seo_viewer_controls', array( $this, 'add_premium_controls' ), 10, 2 );

		// Save reading progress.
		add_action( 'wp_ajax_pdf_save_reading_progress', array( $this, 'ajax_save_progress' ) );
		add_action( 'wp_ajax_nopriv_pdf_save_reading_progress', array( $this, 'ajax_save_progress' ) );

		// Get reading progress.
		add_action( 'wp_ajax_pdf_get_reading_progress', array( $this, 'ajax_get_progress' ) );
		add_action( 'wp_ajax_nopriv_pdf_get_reading_progress', array( $this, 'ajax_get_progress' ) );
	}

	/**
	 * Enqueue premium viewer scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! is_singular( 'pdf_document' ) ) {
			return;
		}

		wp_enqueue_script(
			'pdf-embed-seo-premium-viewer',
			PDF_EMBED_SEO_PREMIUM_URL . 'assets/js/premium-viewer.js',
			array( 'jquery', 'pdfjs' ),
			PDF_EMBED_SEO_PREMIUM_VERSION,
			true
		);

		wp_enqueue_style(
			'pdf-embed-seo-premium-viewer',
			PDF_EMBED_SEO_PREMIUM_URL . 'assets/css/premium-viewer.css',
			array(),
			PDF_EMBED_SEO_PREMIUM_VERSION
		);

		wp_localize_script(
			'pdf-embed-seo-premium-viewer',
			'pdfPremiumViewer',
			array(
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'pdf_premium_viewer' ),
				'postId'          => get_the_ID(),
				'userId'          => get_current_user_id(),
				'enableSearch'    => true,
				'enableBookmarks' => true,
				'enableProgress'  => is_user_logged_in(),
				'i18n'            => array(
					'search'           => __( 'Search in document', 'wp-pdf-embed-seo-optimize' ),
					'searchPlaceholder' => __( 'Enter search term...', 'wp-pdf-embed-seo-optimize' ),
					'noResults'        => __( 'No results found', 'wp-pdf-embed-seo-optimize' ),
					/* translators: %d: Number of search matches found */
					'matchesFound'     => __( '%d matches found', 'wp-pdf-embed-seo-optimize' ),
					'bookmarks'        => __( 'Bookmarks', 'wp-pdf-embed-seo-optimize' ),
					'noBookmarks'      => __( 'No bookmarks in this document', 'wp-pdf-embed-seo-optimize' ),
					/* translators: %d: Page number to resume reading from */
					'resumeReading'    => __( 'Resume from page %d?', 'wp-pdf-embed-seo-optimize' ),
					'resume'           => __( 'Resume', 'wp-pdf-embed-seo-optimize' ),
					'startOver'        => __( 'Start Over', 'wp-pdf-embed-seo-optimize' ),
				),
			)
		);
	}

	/**
	 * Add premium viewer options.
	 *
	 * @param array $options Viewer options.
	 * @param int   $post_id Post ID.
	 * @return array
	 */
	public function add_premium_options( $options, $post_id ) {
		$options['enableSearch']       = true;
		$options['enableBookmarks']    = true;
		$options['enableProgress']     = is_user_logged_in();
		$options['enableAnnotations']  = false; // Future feature.
		$options['enablePresentation'] = true;

		return $options;
	}

	/**
	 * Add premium viewer controls HTML.
	 *
	 * @param string $controls Existing controls HTML.
	 * @param int    $post_id  Post ID.
	 * @return string
	 */
	public function add_premium_controls( $controls, $post_id ) {
		ob_start();
		?>
		<!-- Search Control -->
		<div class="pdf-control-group pdf-search-group">
			<button type="button" class="pdf-control-btn pdf-search-toggle" title="<?php esc_attr_e( 'Search', 'wp-pdf-embed-seo-optimize' ); ?>">
				<span class="dashicons dashicons-search"></span>
			</button>
			<div class="pdf-search-panel" style="display: none;">
				<input type="text" class="pdf-search-input" placeholder="<?php esc_attr_e( 'Search...', 'wp-pdf-embed-seo-optimize' ); ?>" />
				<button type="button" class="pdf-search-prev" title="<?php esc_attr_e( 'Previous', 'wp-pdf-embed-seo-optimize' ); ?>">&lsaquo;</button>
				<button type="button" class="pdf-search-next" title="<?php esc_attr_e( 'Next', 'wp-pdf-embed-seo-optimize' ); ?>">&rsaquo;</button>
				<span class="pdf-search-results"></span>
				<button type="button" class="pdf-search-close">&times;</button>
			</div>
		</div>

		<!-- Bookmarks Control -->
		<div class="pdf-control-group pdf-bookmarks-group">
			<button type="button" class="pdf-control-btn pdf-bookmarks-toggle" title="<?php esc_attr_e( 'Bookmarks', 'wp-pdf-embed-seo-optimize' ); ?>">
				<span class="dashicons dashicons-book"></span>
			</button>
		</div>

		<!-- Presentation Mode -->
		<div class="pdf-control-group pdf-presentation-group">
			<button type="button" class="pdf-control-btn pdf-presentation-toggle" title="<?php esc_attr_e( 'Presentation Mode', 'wp-pdf-embed-seo-optimize' ); ?>">
				<span class="dashicons dashicons-slides"></span>
			</button>
		</div>
		<?php
		return $controls . ob_get_clean();
	}

	/**
	 * AJAX handler to save reading progress.
	 *
	 * @return void
	 */
	public function ajax_save_progress() {
		check_ajax_referer( 'pdf_premium_viewer', 'nonce' );

		$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$page_number = isset( $_POST['page_number'] ) ? absint( $_POST['page_number'] ) : 1;
		$user_id     = get_current_user_id();

		if ( ! $post_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid request.', 'wp-pdf-embed-seo-optimize' ) ) );
		}

		// Save progress for logged-in users.
		if ( $user_id ) {
			$progress_key = 'pdf_reading_progress_' . $post_id;
			update_user_meta( $user_id, $progress_key, $page_number );
			wp_send_json_success( array( 'saved' => true ) );
		}

		// For non-logged-in users, we'll rely on localStorage (handled in JS).
		wp_send_json_success( array( 'saved' => false, 'useLocalStorage' => true ) );
	}

	/**
	 * AJAX handler to get reading progress.
	 *
	 * @return void
	 */
	public function ajax_get_progress() {
		check_ajax_referer( 'pdf_premium_viewer', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$user_id = get_current_user_id();

		if ( ! $post_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid request.', 'wp-pdf-embed-seo-optimize' ) ) );
		}

		$page_number = 1;

		if ( $user_id ) {
			$progress_key = 'pdf_reading_progress_' . $post_id;
			$saved_page   = get_user_meta( $user_id, $progress_key, true );
			if ( $saved_page ) {
				$page_number = absint( $saved_page );
			}
		}

		wp_send_json_success(
			array(
				'page_number'     => $page_number,
				'useLocalStorage' => ! $user_id,
			)
		);
	}

	/**
	 * Get bookmarks/outline from PDF.
	 *
	 * Note: This is handled client-side by PDF.js using pdf.getOutline().
	 * This method provides a placeholder for server-side processing if needed.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	public static function get_pdf_outline( $post_id ) {
		// Bookmarks are extracted client-side via PDF.js.
		// This could be extended to cache outline data server-side.
		return array();
	}

	/**
	 * Render bookmarks sidebar HTML.
	 *
	 * @return string
	 */
	public static function get_bookmarks_sidebar_html() {
		ob_start();
		?>
		<div class="pdf-bookmarks-sidebar" style="display: none;">
			<div class="pdf-bookmarks-header">
				<h4><?php esc_html_e( 'Document Outline', 'wp-pdf-embed-seo-optimize' ); ?></h4>
				<button type="button" class="pdf-bookmarks-close">&times;</button>
			</div>
			<div class="pdf-bookmarks-content">
				<ul class="pdf-bookmarks-list">
					<!-- Populated by JavaScript -->
				</ul>
				<p class="pdf-no-bookmarks" style="display: none;">
					<?php esc_html_e( 'This document has no bookmarks.', 'wp-pdf-embed-seo-optimize' ); ?>
				</p>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render reading progress prompt HTML.
	 *
	 * @return string
	 */
	public static function get_progress_prompt_html() {
		ob_start();
		?>
		<div class="pdf-progress-prompt" style="display: none;">
			<div class="pdf-progress-prompt-content">
				<p class="pdf-progress-message"></p>
				<div class="pdf-progress-buttons">
					<button type="button" class="pdf-progress-resume button button-primary">
						<?php esc_html_e( 'Resume', 'wp-pdf-embed-seo-optimize' ); ?>
					</button>
					<button type="button" class="pdf-progress-start-over button">
						<?php esc_html_e( 'Start Over', 'wp-pdf-embed-seo-optimize' ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
