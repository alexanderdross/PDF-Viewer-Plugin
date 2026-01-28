<?php
/**
 * Admin functionality for PDF Viewer 2026.
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Embed_SEO_Admin
 *
 * Handles all admin functionality including meta boxes and settings.
 */
class PDF_Embed_SEO_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_pdf_document', array( $this, 'save_meta_box_data' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'manage_pdf_document_posts_columns', array( $this, 'add_custom_columns' ) );
		add_action( 'manage_pdf_document_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
		add_action( 'edit_form_after_title', array( $this, 'render_editor_help_notice' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
	}

	/**
	 * Add meta boxes to the PDF document edit screen.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'pdf_embed_seo_file',
			__( 'PDF File', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_file_meta_box' ),
			'pdf_document',
			'normal',
			'high'
		);

		add_meta_box(
			'pdf_embed_seo_settings',
			__( 'PDF Settings', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_settings_meta_box' ),
			'pdf_document',
			'side',
			'default'
		);

		add_meta_box(
			'pdf_embed_seo_stats',
			__( 'View Statistics', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_stats_meta_box' ),
			'pdf_document',
			'side',
			'default'
		);

		// Show AI optimization preview if premium is not active or no valid license.
		$show_ai_preview = true;
		if ( defined( 'PDF_EMBED_SEO_PREMIUM_VERSION' ) ) {
			$license_status = get_option( 'pdf_embed_seo_premium_license_status', 'inactive' );
			if ( 'valid' === $license_status ) {
				$show_ai_preview = false; // Premium handles this with full functionality.
			}
		}

		if ( $show_ai_preview ) {
			add_meta_box(
				'pdf_embed_seo_ai_preview',
				__( 'AI & Schema Optimization', 'pdf-embed-seo-optimize' ) . ' <span class="dashicons dashicons-lock" style="color: #dba617; font-size: 16px; vertical-align: middle;"></span>',
				array( $this, 'render_ai_preview_meta_box' ),
				'pdf_document',
				'normal',
				'low'
			);
		}
	}

	/**
	 * Render the PDF file upload meta box.
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_file_meta_box( $post ) {
		wp_nonce_field( 'pdf_embed_seo_save_meta', 'pdf_embed_seo_meta_nonce' );

		$file_id  = get_post_meta( $post->ID, '_pdf_file_id', true );
		$file_url = get_post_meta( $post->ID, '_pdf_file_url', true );

		include PDF_EMBED_SEO_PLUGIN_DIR . 'admin/views/meta-box-pdf-file.php';
	}

	/**
	 * Render the PDF settings meta box.
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_settings_meta_box( $post ) {
		$allow_download  = get_post_meta( $post->ID, '_pdf_allow_download', true );
		$allow_print     = get_post_meta( $post->ID, '_pdf_allow_print', true );
		$standalone_mode = get_post_meta( $post->ID, '_pdf_standalone_mode', true );

		// Get defaults from settings.
		$defaults = PDF_Embed_SEO::get_setting();

		// If no value set, use defaults.
		if ( '' === $allow_download && isset( $defaults['default_allow_download'] ) ) {
			$allow_download = $defaults['default_allow_download'];
		}
		if ( '' === $allow_print && isset( $defaults['default_allow_print'] ) ) {
			$allow_print = $defaults['default_allow_print'];
		}

		include PDF_EMBED_SEO_PLUGIN_DIR . 'admin/views/meta-box-pdf-settings.php';
	}

	/**
	 * Render the statistics meta box.
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_stats_meta_box( $post ) {
		$view_count = PDF_Embed_SEO_Post_Type::get_view_count( $post->ID );

		include PDF_EMBED_SEO_PLUGIN_DIR . 'admin/views/meta-box-pdf-stats.php';
	}

	/**
	 * Render AI optimization preview meta box (for free users).
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_ai_preview_meta_box( $post ) {
		$has_premium    = defined( 'PDF_EMBED_SEO_PREMIUM_VERSION' );
		$license_status = get_option( 'pdf_embed_seo_premium_license_status', 'inactive' );
		?>
		<div style="opacity: 0.6; pointer-events: none;">
			<p class="description" style="margin-bottom: 15px;">
				<?php esc_html_e( 'Optimize your PDF for AI assistants, voice search, and generative engines with advanced schema markup.', 'pdf-embed-seo-optimize' ); ?>
			</p>

			<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
				<div>
					<h4 style="margin: 0 0 8px;"><?php esc_html_e( 'AI Summary (TL;DR)', 'pdf-embed-seo-optimize' ); ?></h4>
					<textarea disabled rows="2" style="width: 100%;" placeholder="<?php esc_attr_e( 'A concise summary for AI assistants...', 'pdf-embed-seo-optimize' ); ?>"></textarea>
				</div>
				<div>
					<h4 style="margin: 0 0 8px;"><?php esc_html_e( 'Key Points', 'pdf-embed-seo-optimize' ); ?></h4>
					<textarea disabled rows="2" style="width: 100%;" placeholder="<?php esc_attr_e( 'Key takeaways, one per line...', 'pdf-embed-seo-optimize' ); ?>"></textarea>
				</div>
			</div>

			<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 15px;">
				<div>
					<label style="font-weight: 600;"><?php esc_html_e( 'Reading Time', 'pdf-embed-seo-optimize' ); ?></label><br>
					<input type="number" disabled placeholder="10" style="width: 80px;"> <?php esc_html_e( 'min', 'pdf-embed-seo-optimize' ); ?>
				</div>
				<div>
					<label style="font-weight: 600;"><?php esc_html_e( 'Difficulty', 'pdf-embed-seo-optimize' ); ?></label><br>
					<select disabled style="width: 100%;">
						<option><?php esc_html_e( 'Intermediate', 'pdf-embed-seo-optimize' ); ?></option>
					</select>
				</div>
				<div>
					<label style="font-weight: 600;"><?php esc_html_e( 'Document Type', 'pdf-embed-seo-optimize' ); ?></label><br>
					<select disabled style="width: 100%;">
						<option><?php esc_html_e( 'Guide / Tutorial', 'pdf-embed-seo-optimize' ); ?></option>
					</select>
				</div>
			</div>

			<div style="margin-top: 15px;">
				<h4 style="margin: 0 0 8px;"><?php esc_html_e( 'FAQ Schema (for Google Rich Results)', 'pdf-embed-seo-optimize' ); ?></h4>
				<div style="background: #f9f9f9; padding: 10px; border-radius: 4px;">
					<input type="text" disabled placeholder="<?php esc_attr_e( 'Question: What is covered in this PDF?', 'pdf-embed-seo-optimize' ); ?>" style="width: 100%; margin-bottom: 5px;">
					<textarea disabled rows="1" style="width: 100%;" placeholder="<?php esc_attr_e( 'Answer: This PDF covers...', 'pdf-embed-seo-optimize' ); ?>"></textarea>
				</div>
			</div>

			<div style="margin-top: 15px;">
				<h4 style="margin: 0 0 8px;"><?php esc_html_e( 'Table of Contents Schema', 'pdf-embed-seo-optimize' ); ?></h4>
				<div style="background: #f9f9f9; padding: 10px; border-radius: 4px; display: flex; gap: 10px;">
					<input type="text" disabled placeholder="<?php esc_attr_e( 'Section Title', 'pdf-embed-seo-optimize' ); ?>" style="flex: 3;">
					<input type="number" disabled placeholder="<?php esc_attr_e( 'Page', 'pdf-embed-seo-optimize' ); ?>" style="flex: 1;">
				</div>
			</div>
		</div>

		<?php if ( $has_premium && 'valid' !== $license_status ) : ?>
			<div style="text-align: center; padding: 15px; background: #fff3cd; border-radius: 4px; margin-top: 15px;">
				<p style="margin: 0 0 10px;">
					<strong><?php esc_html_e( 'License Required', 'pdf-embed-seo-optimize' ); ?></strong><br>
					<?php esc_html_e( 'Activate your premium license to use AI optimization features.', 'pdf-embed-seo-optimize' ); ?>
				</p>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-license' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Activate License', 'pdf-embed-seo-optimize' ); ?>
				</a>
			</div>
		<?php else : ?>
			<div style="text-align: center; padding: 15px; background: #f0f0f1; border-radius: 4px; margin-top: 15px;">
				<p style="margin: 0 0 10px;">
					<strong><?php esc_html_e( 'Unlock AI & Voice Search Optimization', 'pdf-embed-seo-optimize' ); ?></strong><br>
					<?php esc_html_e( 'Get FAQ schema, reading time, difficulty level, and more for better AI visibility!', 'pdf-embed-seo-optimize' ); ?>
				</p>
				<a href="https://pdfviewer.drossmedia.de" target="_blank" class="button button-primary">
					<?php esc_html_e( 'Get Premium', 'pdf-embed-seo-optimize' ); ?>
				</a>
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render help notice above the editor for PDF documents.
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_editor_help_notice( $post ) {
		if ( 'pdf_document' !== $post->post_type ) {
			return;
		}
		?>
		<div class="notice notice-info inline" style="margin: 15px 0;">
			<p>
				<strong><?php esc_html_e( 'Content Editor:', 'pdf-embed-seo-optimize' ); ?></strong>
				<?php esc_html_e( 'Use this area for optional descriptions or additional content. The PDF viewer is displayed automatically from the PDF File you select below.', 'pdf-embed-seo-optimize' ); ?>
			</p>
			<p>
				<strong><?php esc_html_e( 'Want to embed this PDF elsewhere?', 'pdf-embed-seo-optimize' ); ?></strong>
				<?php
				printf(
					/* translators: %s: shortcode example */
					esc_html__( 'Use the shortcode %s on any page or post.', 'pdf-embed-seo-optimize' ),
					'<code>[pdf_viewer id="' . esc_html( $post->ID ) . '"]</code>'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 * @return void
	 */
	public function save_meta_box_data( $post_id, $post ) {
		// Check nonce.
		if ( ! isset( $_POST['pdf_embed_seo_meta_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pdf_embed_seo_meta_nonce'] ) ), 'pdf_embed_seo_save_meta' ) ) {
			return;
		}

		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save PDF file ID.
		if ( isset( $_POST['pdf_file_id'] ) ) {
			$file_id = absint( $_POST['pdf_file_id'] );
			update_post_meta( $post_id, '_pdf_file_id', $file_id );

			// Also update the URL.
			if ( $file_id ) {
				$file_url = wp_get_attachment_url( $file_id );
				update_post_meta( $post_id, '_pdf_file_url', esc_url_raw( $file_url ) );
			} else {
				delete_post_meta( $post_id, '_pdf_file_url' );
			}
		}

		// Save download permission.
		$allow_download = isset( $_POST['pdf_allow_download'] ) ? true : false;
		update_post_meta( $post_id, '_pdf_allow_download', $allow_download );

		// Save print permission.
		$allow_print = isset( $_POST['pdf_allow_print'] ) ? true : false;
		update_post_meta( $post_id, '_pdf_allow_print', $allow_print );

		// Save standalone mode.
		$standalone_mode = isset( $_POST['pdf_standalone_mode'] ) ? true : false;
		update_post_meta( $post_id, '_pdf_standalone_mode', $standalone_mode );

		/**
		 * Fires when PDF settings are saved.
		 *
		 * @param int   $post_id  The post ID.
		 * @param array $settings The saved settings.
		 */
		do_action(
			'pdf_embed_seo_settings_saved',
			$post_id,
			array(
				'allow_download' => $allow_download,
				'allow_print'    => $allow_print,
			)
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook ) {
		global $post_type;

		// Only load on PDF document edit screens.
		if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'pdf_document' === $post_type ) {
			wp_enqueue_media();

			wp_enqueue_style(
				'pdf-embed-seo-optimize-admin',
				PDF_EMBED_SEO_PLUGIN_URL . 'admin/css/admin-styles.css',
				array(),
				PDF_EMBED_SEO_VERSION
			);

			wp_enqueue_script(
				'pdf-embed-seo-optimize-admin',
				PDF_EMBED_SEO_PLUGIN_URL . 'admin/js/admin-scripts.js',
				array( 'jquery', 'media-upload' ),
				PDF_EMBED_SEO_VERSION,
				true
			);

			wp_localize_script(
				'pdf-embed-seo-optimize-admin',
				'pdfEmbedSeoAdmin',
				array(
					'selectPdf'   => __( 'Select PDF File', 'pdf-embed-seo-optimize' ),
					'usePdf'      => __( 'Use this PDF', 'pdf-embed-seo-optimize' ),
					'removePdf'   => __( 'Remove PDF', 'pdf-embed-seo-optimize' ),
					'noPdfSelect' => __( 'No PDF selected', 'pdf-embed-seo-optimize' ),
				)
			);
		}

		// Load on settings page.
		if ( 'pdf_document_page_pdf-embed-seo-optimize-settings' === $hook ) {
			wp_enqueue_style(
				'pdf-embed-seo-optimize-admin',
				PDF_EMBED_SEO_PLUGIN_URL . 'admin/css/admin-styles.css',
				array(),
				PDF_EMBED_SEO_VERSION
			);
		}
	}

	/**
	 * Add settings page to the PDF Documents menu.
	 *
	 * @return void
	 */
	public function add_settings_page() {
		// Add Docs page.
		add_submenu_page(
			'edit.php?post_type=pdf_document',
			__( 'Docs & Usage', 'pdf-embed-seo-optimize' ),
			__( 'Docs', 'pdf-embed-seo-optimize' ),
			'edit_posts',
			'pdf-embed-seo-optimize-docs',
			array( $this, 'render_docs_page' )
		);

		// Add Settings page.
		add_submenu_page(
			'edit.php?post_type=pdf_document',
			__( 'PDF Viewer Settings', 'pdf-embed-seo-optimize' ),
			__( 'Settings', 'pdf-embed-seo-optimize' ),
			'manage_options',
			'pdf-embed-seo-optimize-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the documentation page.
	 *
	 * @return void
	 */
	public function render_docs_page() {
		include PDF_EMBED_SEO_PLUGIN_DIR . 'admin/views/docs-page.php';
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'pdf_embed_seo_settings_group',
			'pdf_embed_seo_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);

		// Add capability filter for settings form submission.
		add_filter( 'option_page_capability_pdf_embed_seo_settings_group', array( $this, 'get_settings_page_capability' ) );

		// Default Settings Section.
		add_settings_section(
			'pdf_embed_seo_defaults',
			__( 'Default Settings', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_defaults_section' ),
			'pdf-embed-seo-optimize-settings'
		);

		add_settings_field(
			'default_allow_download',
			__( 'Allow Download by Default', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_checkbox_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_defaults',
			array(
				'label_for' => 'default_allow_download',
				'key'       => 'default_allow_download',
			)
		);

		add_settings_field(
			'default_allow_print',
			__( 'Allow Print by Default', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_checkbox_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_defaults',
			array(
				'label_for' => 'default_allow_print',
				'key'       => 'default_allow_print',
			)
		);

		add_settings_field(
			'auto_generate_thumbnails',
			__( 'Auto-generate Thumbnails', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_thumbnail_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_defaults',
			array(
				'label_for' => 'auto_generate_thumbnails',
				'key'       => 'auto_generate_thumbnails',
			)
		);

		// Viewer Settings Section.
		add_settings_section(
			'pdf_embed_seo_viewer',
			__( 'Viewer Settings', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_viewer_section' ),
			'pdf-embed-seo-optimize-settings'
		);

		add_settings_field(
			'viewer_theme',
			__( 'Viewer Theme', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_select_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_viewer',
			array(
				'label_for' => 'viewer_theme',
				'key'       => 'viewer_theme',
				'options'   => array(
					'light' => __( 'Light', 'pdf-embed-seo-optimize' ),
					'dark'  => __( 'Dark', 'pdf-embed-seo-optimize' ),
				),
			)
		);

		// Archive Settings Section.
		add_settings_section(
			'pdf_embed_seo_archive',
			__( 'Archive Settings', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_archive_section' ),
			'pdf-embed-seo-optimize-settings'
		);

		add_settings_field(
			'archive_posts_per_page',
			__( 'PDFs per Page', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_number_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_archive',
			array(
				'label_for' => 'archive_posts_per_page',
				'key'       => 'archive_posts_per_page',
				'min'       => 1,
				'max'       => 100,
			)
		);

		add_settings_field(
			'archive_display_style',
			__( 'Archive Display Style', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_select_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_archive',
			array(
				'label_for'   => 'archive_display_style',
				'key'         => 'archive_display_style',
				'options'     => array(
					'list' => __( 'List View (Simple bullet-style list)', 'pdf-embed-seo-optimize' ),
					'grid' => __( 'Grid View (Thumbnail cards)', 'pdf-embed-seo-optimize' ),
				),
				'description' => __( 'Choose how PDF documents are displayed on the archive page.', 'pdf-embed-seo-optimize' ),
			)
		);

		add_settings_field(
			'archive_show_description',
			__( 'Show Description in Archive', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_checkbox_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_archive',
			array(
				'label_for' => 'archive_show_description',
				'key'       => 'archive_show_description',
			)
		);

		add_settings_field(
			'archive_show_view_count',
			__( 'Show View Count in Archive', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_checkbox_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_archive',
			array(
				'label_for' => 'archive_show_view_count',
				'key'       => 'archive_show_view_count',
			)
		);

		add_settings_field(
			'show_breadcrumbs',
			__( 'Show Visible Breadcrumbs', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_breadcrumb_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_archive',
			array(
				'label_for' => 'show_breadcrumbs',
				'key'       => 'show_breadcrumbs',
			)
		);

		// Premium Preview Section (only show if premium is not active).
		if ( ! defined( 'PDF_EMBED_SEO_IS_PREMIUM' ) || ! PDF_EMBED_SEO_IS_PREMIUM ) {
			add_settings_section(
				'pdf_embed_seo_premium_preview',
				__( 'Premium Features', 'pdf-embed-seo-optimize' ),
				array( $this, 'render_premium_preview_section' ),
				'pdf-embed-seo-optimize-settings'
			);

			add_settings_field(
				'archive_redirect_preview',
				__( 'Archive Page Redirect', 'pdf-embed-seo-optimize' ),
				array( $this, 'render_premium_redirect_preview' ),
				'pdf-embed-seo-optimize-settings',
				'pdf_embed_seo_premium_preview'
			);
		}
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $input The input to sanitize.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		$sanitized['default_allow_download']    = ! empty( $input['default_allow_download'] );
		$sanitized['default_allow_print']       = ! empty( $input['default_allow_print'] );
		$sanitized['auto_generate_thumbnails']  = ! empty( $input['auto_generate_thumbnails'] );
		$sanitized['viewer_theme']              = isset( $input['viewer_theme'] ) && in_array( $input['viewer_theme'], array( 'light', 'dark' ), true )
			? $input['viewer_theme']
			: 'light';
		$sanitized['archive_posts_per_page']    = isset( $input['archive_posts_per_page'] )
			? absint( $input['archive_posts_per_page'] )
			: 12;
		$sanitized['archive_display_style']     = isset( $input['archive_display_style'] ) && in_array( $input['archive_display_style'], array( 'list', 'grid' ), true )
			? $input['archive_display_style']
			: 'grid';
		$sanitized['archive_show_description']  = ! empty( $input['archive_show_description'] );
		$sanitized['archive_show_view_count']   = ! empty( $input['archive_show_view_count'] );
		$sanitized['show_breadcrumbs']          = ! empty( $input['show_breadcrumbs'] );

		return $sanitized;
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		include PDF_EMBED_SEO_PLUGIN_DIR . 'admin/views/settings-page.php';
	}

	/**
	 * Get the capability required for the settings page.
	 *
	 * @return string The required capability.
	 */
	public function get_settings_page_capability() {
		return 'manage_options';
	}

	/**
	 * Render defaults section description.
	 *
	 * @return void
	 */
	public function render_defaults_section() {
		echo '<p>' . esc_html__( 'Set default permissions for new PDF documents.', 'pdf-embed-seo-optimize' ) . '</p>';
	}

	/**
	 * Render viewer section description.
	 *
	 * @return void
	 */
	public function render_viewer_section() {
		echo '<p>' . esc_html__( 'Customize the PDF viewer appearance.', 'pdf-embed-seo-optimize' ) . '</p>';
	}

	/**
	 * Render archive section description.
	 *
	 * @return void
	 */
	public function render_archive_section() {
		echo '<p>' . esc_html__( 'Configure the PDF archive page.', 'pdf-embed-seo-optimize' ) . '</p>';
	}

	/**
	 * Render a checkbox field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_checkbox_field( $args ) {
		$settings = PDF_Embed_SEO::get_setting();
		$value    = isset( $settings[ $args['key'] ] ) ? $settings[ $args['key'] ] : false;
		?>
		<input
			type="checkbox"
			id="<?php echo esc_attr( $args['key'] ); ?>"
			name="pdf_embed_seo_settings[<?php echo esc_attr( $args['key'] ); ?>]"
			value="1"
			<?php checked( $value, true ); ?>
		/>
		<?php
	}

	/**
	 * Render a select field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_select_field( $args ) {
		$settings = PDF_Embed_SEO::get_setting();
		$value    = isset( $settings[ $args['key'] ] ) ? $settings[ $args['key'] ] : '';
		?>
		<select
			id="<?php echo esc_attr( $args['key'] ); ?>"
			name="pdf_embed_seo_settings[<?php echo esc_attr( $args['key'] ); ?>]"
		>
			<?php foreach ( $args['options'] as $option_value => $option_label ) : ?>
				<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $value, $option_value ); ?>>
					<?php echo esc_html( $option_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Render a number field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_number_field( $args ) {
		$settings = PDF_Embed_SEO::get_setting();
		$value    = isset( $settings[ $args['key'] ] ) ? $settings[ $args['key'] ] : 12;
		?>
		<input
			type="number"
			id="<?php echo esc_attr( $args['key'] ); ?>"
			name="pdf_embed_seo_settings[<?php echo esc_attr( $args['key'] ); ?>]"
			value="<?php echo esc_attr( $value ); ?>"
			min="<?php echo esc_attr( $args['min'] ); ?>"
			max="<?php echo esc_attr( $args['max'] ); ?>"
			class="small-text"
		/>
		<?php
	}

	/**
	 * Render the breadcrumb visibility field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_breadcrumb_field( $args ) {
		$settings = PDF_Embed_SEO::get_setting();
		$value    = isset( $settings[ $args['key'] ] ) ? $settings[ $args['key'] ] : true;
		?>
		<input
			type="checkbox"
			id="<?php echo esc_attr( $args['key'] ); ?>"
			name="pdf_embed_seo_settings[<?php echo esc_attr( $args['key'] ); ?>]"
			value="1"
			<?php checked( $value, true ); ?>
		/>
		<p class="description">
			<?php esc_html_e( 'Display visible breadcrumb navigation on PDF pages. The JSON-LD breadcrumb schema for SEO is always included regardless of this setting.', 'pdf-embed-seo-optimize' ); ?>
		</p>
		<?php
	}

	/**
	 * Render the thumbnail settings field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_thumbnail_field( $args ) {
		$settings    = PDF_Embed_SEO::get_setting();
		$value       = isset( $settings[ $args['key'] ] ) ? $settings[ $args['key'] ] : true;
		$availability = PDF_Embed_SEO_Thumbnail::check_availability();
		?>
		<input
			type="checkbox"
			id="<?php echo esc_attr( $args['key'] ); ?>"
			name="pdf_embed_seo_settings[<?php echo esc_attr( $args['key'] ); ?>]"
			value="1"
			<?php checked( $value, true ); ?>
			<?php disabled( ! $availability['available'] ); ?>
		/>
		<p class="description">
			<?php if ( $availability['available'] ) : ?>
				<?php esc_html_e( 'Automatically generate a thumbnail from the first page of the PDF when no featured image is set.', 'pdf-embed-seo-optimize' ); ?>
				<br>
				<span style="color: #00a32a;">
					<?php echo esc_html( $availability['message'] ); ?>
				</span>
			<?php else : ?>
				<span style="color: #d63638;">
					<?php echo esc_html( $availability['message'] ); ?>
				</span>
			<?php endif; ?>
		</p>
		<?php
	}

	/**
	 * Render premium preview section description.
	 *
	 * @return void
	 */
	public function render_premium_preview_section() {
		?>
		<p>
			<?php esc_html_e( 'The following features are available in the Premium version.', 'pdf-embed-seo-optimize' ); ?>
			<a href="https://pdfviewer.drossmedia.de/" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Upgrade to Premium', 'pdf-embed-seo-optimize' ); ?>
			</a>
		</p>
		<?php
	}

	/**
	 * Render premium redirect preview field (disabled).
	 *
	 * @return void
	 */
	public function render_premium_redirect_preview() {
		?>
		<div class="pdf-premium-preview-field" style="opacity: 0.6; pointer-events: none;">
			<label style="display: block; margin-bottom: 15px;">
				<input type="checkbox" disabled />
				<?php esc_html_e( 'Enable Archive Redirect', 'pdf-embed-seo-optimize' ); ?>
				<p class="description"><?php esc_html_e( 'Redirect the PDF archive page (/pdf/) to another URL.', 'pdf-embed-seo-optimize' ); ?></p>
			</label>

			<label style="display: block; margin-bottom: 15px;">
				<strong><?php esc_html_e( 'Redirect Type', 'pdf-embed-seo-optimize' ); ?></strong><br>
				<select disabled style="margin-top: 5px;">
					<option><?php esc_html_e( '301 - Permanent Redirect (recommended for SEO)', 'pdf-embed-seo-optimize' ); ?></option>
					<option><?php esc_html_e( '302 - Temporary Redirect', 'pdf-embed-seo-optimize' ); ?></option>
				</select>
			</label>

			<label style="display: block; margin-bottom: 10px;">
				<strong><?php esc_html_e( 'Redirect URL', 'pdf-embed-seo-optimize' ); ?></strong><br>
				<input type="url" disabled class="regular-text" placeholder="<?php echo esc_attr( home_url( '/' ) ); ?>" style="margin-top: 5px;" />
				<p class="description"><?php esc_html_e( 'Enter the URL where visitors should be redirected.', 'pdf-embed-seo-optimize' ); ?></p>
			</label>
		</div>

		<p style="margin-top: 15px;">
			<span class="dashicons dashicons-lock" style="color: #d63638;"></span>
			<em>
				<?php
				printf(
					/* translators: %s: link to premium page */
					esc_html__( 'This feature is available in %s', 'pdf-embed-seo-optimize' ),
					'<a href="https://pdfviewer.drossmedia.de/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'PDF Embed & SEO Optimize Premium', 'pdf-embed-seo-optimize' ) . '</a>'
				);
				?>
			</em>
		</p>
		<?php
	}

	/**
	 * Add dashboard widgets.
	 *
	 * @return void
	 */
	public function add_dashboard_widgets() {
		// Only show analytics preview if premium is not active with valid license.
		$show_analytics_preview = true;
		if ( defined( 'PDF_EMBED_SEO_PREMIUM_VERSION' ) ) {
			$license_status = get_option( 'pdf_embed_seo_premium_license_status', 'inactive' );
			if ( 'valid' === $license_status ) {
				$show_analytics_preview = false; // Premium handles analytics with full functionality.
			}
		}

		if ( $show_analytics_preview ) {
			wp_add_dashboard_widget(
				'pdf_embed_seo_analytics_preview',
				__( 'PDF Analytics', 'pdf-embed-seo-optimize' ) . ' <span class="dashicons dashicons-lock" style="color: #dba617; font-size: 16px; vertical-align: middle;"></span>',
				array( $this, 'render_analytics_dashboard_widget' )
			);
		}
	}

	/**
	 * Render analytics preview dashboard widget.
	 *
	 * @return void
	 */
	public function render_analytics_dashboard_widget() {
		// Get some basic stats to show in the preview.
		$total_pdfs = wp_count_posts( 'pdf_document' );
		$published  = isset( $total_pdfs->publish ) ? $total_pdfs->publish : 0;

		// Calculate total views across all PDFs.
		global $wpdb;
		$total_views = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = %s",
				'_pdf_view_count'
			)
		);
		$total_views = $total_views ? intval( $total_views ) : 0;

		// Get top 3 viewed PDFs.
		$top_pdfs = get_posts(
			array(
				'post_type'      => 'pdf_document',
				'posts_per_page' => 3,
				'meta_key'       => '_pdf_view_count',
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
				'post_status'    => 'publish',
			)
		);

		$has_premium    = defined( 'PDF_EMBED_SEO_PREMIUM_VERSION' );
		$license_status = get_option( 'pdf_embed_seo_premium_license_status', 'inactive' );
		?>
		<div class="pdf-analytics-preview">
			<!-- Real Stats Section -->
			<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
				<div style="text-align: center; padding: 15px; background: #f0f6fc; border-radius: 8px;">
					<div style="font-size: 28px; font-weight: bold; color: #2271b1;"><?php echo esc_html( number_format_i18n( $published ) ); ?></div>
					<div style="color: #50575e; font-size: 13px;"><?php esc_html_e( 'PDF Documents', 'pdf-embed-seo-optimize' ); ?></div>
				</div>
				<div style="text-align: center; padding: 15px; background: #fcf0f0; border-radius: 8px;">
					<div style="font-size: 28px; font-weight: bold; color: #d63638;"><?php echo esc_html( number_format_i18n( $total_views ) ); ?></div>
					<div style="color: #50575e; font-size: 13px;"><?php esc_html_e( 'Total Views', 'pdf-embed-seo-optimize' ); ?></div>
				</div>
			</div>

			<!-- Top PDFs -->
			<?php if ( ! empty( $top_pdfs ) ) : ?>
				<h4 style="margin: 0 0 10px; font-size: 13px; color: #1d2327;"><?php esc_html_e( 'Top Viewed PDFs', 'pdf-embed-seo-optimize' ); ?></h4>
				<ul style="margin: 0 0 15px; padding: 0; list-style: none;">
					<?php foreach ( $top_pdfs as $pdf ) : ?>
						<?php $views = PDF_Embed_SEO_Post_Type::get_view_count( $pdf->ID ); ?>
						<li style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f1;">
							<a href="<?php echo esc_url( get_edit_post_link( $pdf->ID ) ); ?>" style="text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 70%;">
								<?php echo esc_html( $pdf->post_title ); ?>
							</a>
							<span style="color: #50575e; font-size: 12px;"><?php echo esc_html( number_format_i18n( $views ) ); ?> <?php esc_html_e( 'views', 'pdf-embed-seo-optimize' ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<!-- Premium Features Preview (Blurred) -->
			<div style="position: relative; margin-top: 15px;">
				<div style="opacity: 0.4; pointer-events: none; filter: blur(1px);">
					<h4 style="margin: 0 0 10px; font-size: 13px; color: #1d2327;">
						<span class="dashicons dashicons-chart-area" style="font-size: 16px; vertical-align: middle;"></span>
						<?php esc_html_e( 'Premium Analytics', 'pdf-embed-seo-optimize' ); ?>
					</h4>
					<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
						<div style="padding: 10px; background: #f9f9f9; border-radius: 4px; text-align: center;">
							<div style="font-size: 18px; font-weight: bold;">2,847</div>
							<div style="font-size: 11px; color: #666;"><?php esc_html_e( 'Unique Visitors', 'pdf-embed-seo-optimize' ); ?></div>
						</div>
						<div style="padding: 10px; background: #f9f9f9; border-radius: 4px; text-align: center;">
							<div style="font-size: 18px; font-weight: bold;">4m 32s</div>
							<div style="font-size: 11px; color: #666;"><?php esc_html_e( 'Avg. Time', 'pdf-embed-seo-optimize' ); ?></div>
						</div>
					</div>
					<div style="background: #f9f9f9; border-radius: 4px; padding: 10px;">
						<div style="display: flex; align-items: flex-end; height: 60px; gap: 4px;">
							<?php
							// Fake chart bars.
							$bars = array( 30, 45, 35, 60, 50, 70, 55 );
							foreach ( $bars as $height ) :
								?>
								<div style="flex: 1; background: linear-gradient(to top, #2271b1, #72aee6); border-radius: 2px 2px 0 0; height: <?php echo esc_attr( $height ); ?>%;"></div>
							<?php endforeach; ?>
						</div>
						<div style="display: flex; justify-content: space-between; font-size: 10px; color: #666; margin-top: 5px;">
							<span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
						</div>
					</div>
				</div>

				<!-- Overlay CTA -->
				<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; background: rgba(255,255,255,0.95); padding: 20px 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); width: 80%;">
					<span class="dashicons dashicons-chart-bar" style="font-size: 32px; color: #2271b1; margin-bottom: 5px;"></span>
					<h4 style="margin: 5px 0; font-size: 14px;"><?php esc_html_e( 'Unlock Full Analytics', 'pdf-embed-seo-optimize' ); ?></h4>
					<p style="font-size: 12px; color: #666; margin: 0 0 10px;">
						<?php esc_html_e( 'Track visitors, time spent, referrers, and more!', 'pdf-embed-seo-optimize' ); ?>
					</p>
					<?php if ( $has_premium && 'valid' !== $license_status ) : ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-license' ) ); ?>" class="button button-primary" style="font-size: 12px;">
							<?php esc_html_e( 'Activate License', 'pdf-embed-seo-optimize' ); ?>
						</a>
					<?php else : ?>
						<a href="https://pdfviewer.drossmedia.de" target="_blank" class="button button-primary" style="font-size: 12px;">
							<?php esc_html_e( 'Get Premium', 'pdf-embed-seo-optimize' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<!-- Quick Links -->
			<div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #f0f0f1; text-align: center;">
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=pdf_document' ) ); ?>" style="text-decoration: none; font-size: 13px;">
					<?php esc_html_e( 'Manage PDFs', 'pdf-embed-seo-optimize' ); ?> →
				</a>
			</div>
		</div>
		<?php
	}

	/**
	 * Add custom columns to the PDF documents list table.
	 *
	 * @param array $columns The existing columns.
	 * @return array
	 */
	public function add_custom_columns( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $value ) {
			$new_columns[ $key ] = $value;

			// Add our columns after the title.
			if ( 'title' === $key ) {
				$new_columns['pdf_file']    = __( 'PDF File', 'pdf-embed-seo-optimize' );
				$new_columns['permissions'] = __( 'Permissions', 'pdf-embed-seo-optimize' );
				$new_columns['views']       = __( 'Views', 'pdf-embed-seo-optimize' );
			}
		}

		return $new_columns;
	}

	/**
	 * Render custom column content.
	 *
	 * @param string $column  The column name.
	 * @param int    $post_id The post ID.
	 * @return void
	 */
	public function render_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'pdf_file':
				$file_id = get_post_meta( $post_id, '_pdf_file_id', true );
				if ( $file_id ) {
					$file_name = basename( get_attached_file( $file_id ) );
					echo '<span class="dashicons dashicons-pdf"></span> ';
					echo esc_html( $file_name );
				} else {
					echo '<em>' . esc_html__( 'No file attached', 'pdf-embed-seo-optimize' ) . '</em>';
				}
				break;

			case 'permissions':
				$allow_download = PDF_Embed_SEO_Post_Type::is_download_allowed( $post_id );
				$allow_print    = PDF_Embed_SEO_Post_Type::is_print_allowed( $post_id );

				$permissions = array();
				if ( $allow_download ) {
					$permissions[] = __( 'Download', 'pdf-embed-seo-optimize' );
				}
				if ( $allow_print ) {
					$permissions[] = __( 'Print', 'pdf-embed-seo-optimize' );
				}

				if ( empty( $permissions ) ) {
					echo '<em>' . esc_html__( 'View only', 'pdf-embed-seo-optimize' ) . '</em>';
				} else {
					echo esc_html( implode( ', ', $permissions ) );
				}
				break;

			case 'views':
				$view_count = PDF_Embed_SEO_Post_Type::get_view_count( $post_id );
				echo esc_html( number_format_i18n( $view_count ) );
				break;
		}
	}
}
