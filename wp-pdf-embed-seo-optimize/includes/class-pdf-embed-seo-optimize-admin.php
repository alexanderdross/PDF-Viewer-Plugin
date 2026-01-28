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
	}

	/**
	 * Add meta boxes to the PDF document edit screen.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'pdf_embed_seo_file',
			__( 'PDF File', 'wp-pdf-embed-seo-optimize' ),
			array( $this, 'render_file_meta_box' ),
			'pdf_document',
			'normal',
			'high'
		);

		add_meta_box(
			'pdf_embed_seo_settings',
			__( 'PDF Settings', 'wp-pdf-embed-seo-optimize' ),
			array( $this, 'render_settings_meta_box' ),
			'pdf_document',
			'side',
			'default'
		);

		add_meta_box(
			'pdf_embed_seo_stats',
			__( 'View Statistics', 'wp-pdf-embed-seo-optimize' ),
			array( $this, 'render_stats_meta_box' ),
			'pdf_document',
			'side',
			'default'
		);
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
		$allow_download = get_post_meta( $post->ID, '_pdf_allow_download', true );
		$allow_print    = get_post_meta( $post->ID, '_pdf_allow_print', true );

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
					'selectPdf'   => __( 'Select PDF File', 'wp-pdf-embed-seo-optimize' ),
					'usePdf'      => __( 'Use this PDF', 'wp-pdf-embed-seo-optimize' ),
					'removePdf'   => __( 'Remove PDF', 'wp-pdf-embed-seo-optimize' ),
					'noPdfSelect' => __( 'No PDF selected', 'wp-pdf-embed-seo-optimize' ),
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
			__( 'Docs & Usage', 'wp-pdf-embed-seo-optimize' ),
			__( 'Docs', 'wp-pdf-embed-seo-optimize' ),
			'edit_posts',
			'pdf-embed-seo-optimize-docs',
			array( $this, 'render_docs_page' )
		);

		// Add Settings page.
		add_submenu_page(
			'edit.php?post_type=pdf_document',
			__( 'PDF Viewer Settings', 'wp-pdf-embed-seo-optimize' ),
			__( 'Settings', 'wp-pdf-embed-seo-optimize' ),
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

		// Default Settings Section.
		add_settings_section(
			'pdf_embed_seo_defaults',
			__( 'Default Settings', 'wp-pdf-embed-seo-optimize' ),
			array( $this, 'render_defaults_section' ),
			'pdf-embed-seo-optimize-settings'
		);

		add_settings_field(
			'default_allow_download',
			__( 'Allow Download by Default', 'wp-pdf-embed-seo-optimize' ),
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
			__( 'Allow Print by Default', 'wp-pdf-embed-seo-optimize' ),
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
			__( 'Auto-generate Thumbnails', 'wp-pdf-embed-seo-optimize' ),
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
			__( 'Viewer Settings', 'wp-pdf-embed-seo-optimize' ),
			array( $this, 'render_viewer_section' ),
			'pdf-embed-seo-optimize-settings'
		);

		add_settings_field(
			'viewer_theme',
			__( 'Viewer Theme', 'wp-pdf-embed-seo-optimize' ),
			array( $this, 'render_select_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_viewer',
			array(
				'label_for' => 'viewer_theme',
				'key'       => 'viewer_theme',
				'options'   => array(
					'light' => __( 'Light', 'wp-pdf-embed-seo-optimize' ),
					'dark'  => __( 'Dark', 'wp-pdf-embed-seo-optimize' ),
				),
			)
		);

		// Archive Settings Section.
		add_settings_section(
			'pdf_embed_seo_archive',
			__( 'Archive Settings', 'wp-pdf-embed-seo-optimize' ),
			array( $this, 'render_archive_section' ),
			'pdf-embed-seo-optimize-settings'
		);

		add_settings_field(
			'archive_posts_per_page',
			__( 'PDFs per Page', 'wp-pdf-embed-seo-optimize' ),
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
			__( 'Archive Display Style', 'wp-pdf-embed-seo-optimize' ),
			array( $this, 'render_select_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_archive',
			array(
				'label_for'   => 'archive_display_style',
				'key'         => 'archive_display_style',
				'options'     => array(
					'list' => __( 'List View (Simple bullet-style list)', 'wp-pdf-embed-seo-optimize' ),
					'grid' => __( 'Grid View (Thumbnail cards)', 'wp-pdf-embed-seo-optimize' ),
				),
				'description' => __( 'Choose how PDF documents are displayed on the archive page.', 'wp-pdf-embed-seo-optimize' ),
			)
		);

		add_settings_field(
			'archive_show_description',
			__( 'Show Description in Archive', 'wp-pdf-embed-seo-optimize' ),
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
			__( 'Show View Count in Archive', 'wp-pdf-embed-seo-optimize' ),
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
			__( 'Show Visible Breadcrumbs', 'wp-pdf-embed-seo-optimize' ),
			array( $this, 'render_breadcrumb_field' ),
			'pdf-embed-seo-optimize-settings',
			'pdf_embed_seo_archive',
			array(
				'label_for' => 'show_breadcrumbs',
				'key'       => 'show_breadcrumbs',
			)
		);
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
	 * Render defaults section description.
	 *
	 * @return void
	 */
	public function render_defaults_section() {
		echo '<p>' . esc_html__( 'Set default permissions for new PDF documents.', 'wp-pdf-embed-seo-optimize' ) . '</p>';
	}

	/**
	 * Render viewer section description.
	 *
	 * @return void
	 */
	public function render_viewer_section() {
		echo '<p>' . esc_html__( 'Customize the PDF viewer appearance.', 'wp-pdf-embed-seo-optimize' ) . '</p>';
	}

	/**
	 * Render archive section description.
	 *
	 * @return void
	 */
	public function render_archive_section() {
		echo '<p>' . esc_html__( 'Configure the PDF archive page.', 'wp-pdf-embed-seo-optimize' ) . '</p>';
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
			<?php esc_html_e( 'Display visible breadcrumb navigation on PDF pages. The JSON-LD breadcrumb schema for SEO is always included regardless of this setting.', 'wp-pdf-embed-seo-optimize' ); ?>
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
				<?php esc_html_e( 'Automatically generate a thumbnail from the first page of the PDF when no featured image is set.', 'wp-pdf-embed-seo-optimize' ); ?>
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
				$new_columns['pdf_file']    = __( 'PDF File', 'wp-pdf-embed-seo-optimize' );
				$new_columns['permissions'] = __( 'Permissions', 'wp-pdf-embed-seo-optimize' );
				$new_columns['views']       = __( 'Views', 'wp-pdf-embed-seo-optimize' );
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
					echo '<em>' . esc_html__( 'No file attached', 'wp-pdf-embed-seo-optimize' ) . '</em>';
				}
				break;

			case 'permissions':
				$allow_download = PDF_Embed_SEO_Post_Type::is_download_allowed( $post_id );
				$allow_print    = PDF_Embed_SEO_Post_Type::is_print_allowed( $post_id );

				$permissions = array();
				if ( $allow_download ) {
					$permissions[] = __( 'Download', 'wp-pdf-embed-seo-optimize' );
				}
				if ( $allow_print ) {
					$permissions[] = __( 'Print', 'wp-pdf-embed-seo-optimize' );
				}

				if ( empty( $permissions ) ) {
					echo '<em>' . esc_html__( 'View only', 'wp-pdf-embed-seo-optimize' ) . '</em>';
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
