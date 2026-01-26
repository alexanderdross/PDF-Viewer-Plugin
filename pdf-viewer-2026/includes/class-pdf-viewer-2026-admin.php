<?php
/**
 * Admin functionality for PDF Viewer 2026.
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Viewer_2026_Admin
 *
 * Handles all admin functionality including meta boxes and settings.
 */
class PDF_Viewer_2026_Admin {

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
			'pdf_viewer_2026_file',
			__( 'PDF File', 'pdf-viewer-2026' ),
			array( $this, 'render_file_meta_box' ),
			'pdf_document',
			'normal',
			'high'
		);

		add_meta_box(
			'pdf_viewer_2026_settings',
			__( 'PDF Settings', 'pdf-viewer-2026' ),
			array( $this, 'render_settings_meta_box' ),
			'pdf_document',
			'side',
			'default'
		);

		add_meta_box(
			'pdf_viewer_2026_stats',
			__( 'View Statistics', 'pdf-viewer-2026' ),
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
		wp_nonce_field( 'pdf_viewer_2026_save_meta', 'pdf_viewer_2026_meta_nonce' );

		$file_id  = get_post_meta( $post->ID, '_pdf_file_id', true );
		$file_url = get_post_meta( $post->ID, '_pdf_file_url', true );

		include PDF_VIEWER_2026_PLUGIN_DIR . 'admin/views/meta-box-pdf-file.php';
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
		$defaults = PDF_Viewer_2026::get_setting();

		// If no value set, use defaults.
		if ( '' === $allow_download && isset( $defaults['default_allow_download'] ) ) {
			$allow_download = $defaults['default_allow_download'];
		}
		if ( '' === $allow_print && isset( $defaults['default_allow_print'] ) ) {
			$allow_print = $defaults['default_allow_print'];
		}

		include PDF_VIEWER_2026_PLUGIN_DIR . 'admin/views/meta-box-pdf-settings.php';
	}

	/**
	 * Render the statistics meta box.
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_stats_meta_box( $post ) {
		$view_count = PDF_Viewer_2026_Post_Type::get_view_count( $post->ID );

		include PDF_VIEWER_2026_PLUGIN_DIR . 'admin/views/meta-box-pdf-stats.php';
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
		if ( ! isset( $_POST['pdf_viewer_2026_meta_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pdf_viewer_2026_meta_nonce'] ) ), 'pdf_viewer_2026_save_meta' ) ) {
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
			'pdf_viewer_2026_settings_saved',
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
				'pdf-viewer-2026-admin',
				PDF_VIEWER_2026_PLUGIN_URL . 'admin/css/admin-styles.css',
				array(),
				PDF_VIEWER_2026_VERSION
			);

			wp_enqueue_script(
				'pdf-viewer-2026-admin',
				PDF_VIEWER_2026_PLUGIN_URL . 'admin/js/admin-scripts.js',
				array( 'jquery', 'media-upload' ),
				PDF_VIEWER_2026_VERSION,
				true
			);

			wp_localize_script(
				'pdf-viewer-2026-admin',
				'pdfViewer2026Admin',
				array(
					'selectPdf'   => __( 'Select PDF File', 'pdf-viewer-2026' ),
					'usePdf'      => __( 'Use this PDF', 'pdf-viewer-2026' ),
					'removePdf'   => __( 'Remove PDF', 'pdf-viewer-2026' ),
					'noPdfSelect' => __( 'No PDF selected', 'pdf-viewer-2026' ),
				)
			);
		}

		// Load on settings page.
		if ( 'pdf_document_page_pdf-viewer-2026-settings' === $hook ) {
			wp_enqueue_style(
				'pdf-viewer-2026-admin',
				PDF_VIEWER_2026_PLUGIN_URL . 'admin/css/admin-styles.css',
				array(),
				PDF_VIEWER_2026_VERSION
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
			__( 'Docs & Usage', 'pdf-viewer-2026' ),
			__( 'Docs', 'pdf-viewer-2026' ),
			'edit_posts',
			'pdf-viewer-2026-docs',
			array( $this, 'render_docs_page' )
		);

		// Add Settings page.
		add_submenu_page(
			'edit.php?post_type=pdf_document',
			__( 'PDF Viewer Settings', 'pdf-viewer-2026' ),
			__( 'Settings', 'pdf-viewer-2026' ),
			'manage_options',
			'pdf-viewer-2026-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the documentation page.
	 *
	 * @return void
	 */
	public function render_docs_page() {
		include PDF_VIEWER_2026_PLUGIN_DIR . 'admin/views/docs-page.php';
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'pdf_viewer_2026_settings_group',
			'pdf_viewer_2026_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);

		// Default Settings Section.
		add_settings_section(
			'pdf_viewer_2026_defaults',
			__( 'Default Settings', 'pdf-viewer-2026' ),
			array( $this, 'render_defaults_section' ),
			'pdf-viewer-2026-settings'
		);

		add_settings_field(
			'default_allow_download',
			__( 'Allow Download by Default', 'pdf-viewer-2026' ),
			array( $this, 'render_checkbox_field' ),
			'pdf-viewer-2026-settings',
			'pdf_viewer_2026_defaults',
			array(
				'label_for' => 'default_allow_download',
				'key'       => 'default_allow_download',
			)
		);

		add_settings_field(
			'default_allow_print',
			__( 'Allow Print by Default', 'pdf-viewer-2026' ),
			array( $this, 'render_checkbox_field' ),
			'pdf-viewer-2026-settings',
			'pdf_viewer_2026_defaults',
			array(
				'label_for' => 'default_allow_print',
				'key'       => 'default_allow_print',
			)
		);

		// Viewer Settings Section.
		add_settings_section(
			'pdf_viewer_2026_viewer',
			__( 'Viewer Settings', 'pdf-viewer-2026' ),
			array( $this, 'render_viewer_section' ),
			'pdf-viewer-2026-settings'
		);

		add_settings_field(
			'viewer_theme',
			__( 'Viewer Theme', 'pdf-viewer-2026' ),
			array( $this, 'render_select_field' ),
			'pdf-viewer-2026-settings',
			'pdf_viewer_2026_viewer',
			array(
				'label_for' => 'viewer_theme',
				'key'       => 'viewer_theme',
				'options'   => array(
					'light' => __( 'Light', 'pdf-viewer-2026' ),
					'dark'  => __( 'Dark', 'pdf-viewer-2026' ),
				),
			)
		);

		// Archive Settings Section.
		add_settings_section(
			'pdf_viewer_2026_archive',
			__( 'Archive Settings', 'pdf-viewer-2026' ),
			array( $this, 'render_archive_section' ),
			'pdf-viewer-2026-settings'
		);

		add_settings_field(
			'archive_posts_per_page',
			__( 'PDFs per Page', 'pdf-viewer-2026' ),
			array( $this, 'render_number_field' ),
			'pdf-viewer-2026-settings',
			'pdf_viewer_2026_archive',
			array(
				'label_for' => 'archive_posts_per_page',
				'key'       => 'archive_posts_per_page',
				'min'       => 1,
				'max'       => 100,
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

		$sanitized['default_allow_download'] = ! empty( $input['default_allow_download'] );
		$sanitized['default_allow_print']    = ! empty( $input['default_allow_print'] );
		$sanitized['viewer_theme']           = isset( $input['viewer_theme'] ) && in_array( $input['viewer_theme'], array( 'light', 'dark' ), true )
			? $input['viewer_theme']
			: 'light';
		$sanitized['archive_posts_per_page'] = isset( $input['archive_posts_per_page'] )
			? absint( $input['archive_posts_per_page'] )
			: 12;

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

		include PDF_VIEWER_2026_PLUGIN_DIR . 'admin/views/settings-page.php';
	}

	/**
	 * Render defaults section description.
	 *
	 * @return void
	 */
	public function render_defaults_section() {
		echo '<p>' . esc_html__( 'Set default permissions for new PDF documents.', 'pdf-viewer-2026' ) . '</p>';
	}

	/**
	 * Render viewer section description.
	 *
	 * @return void
	 */
	public function render_viewer_section() {
		echo '<p>' . esc_html__( 'Customize the PDF viewer appearance.', 'pdf-viewer-2026' ) . '</p>';
	}

	/**
	 * Render archive section description.
	 *
	 * @return void
	 */
	public function render_archive_section() {
		echo '<p>' . esc_html__( 'Configure the PDF archive page.', 'pdf-viewer-2026' ) . '</p>';
	}

	/**
	 * Render a checkbox field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_checkbox_field( $args ) {
		$settings = PDF_Viewer_2026::get_setting();
		$value    = isset( $settings[ $args['key'] ] ) ? $settings[ $args['key'] ] : false;
		?>
		<input
			type="checkbox"
			id="<?php echo esc_attr( $args['key'] ); ?>"
			name="pdf_viewer_2026_settings[<?php echo esc_attr( $args['key'] ); ?>]"
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
		$settings = PDF_Viewer_2026::get_setting();
		$value    = isset( $settings[ $args['key'] ] ) ? $settings[ $args['key'] ] : '';
		?>
		<select
			id="<?php echo esc_attr( $args['key'] ); ?>"
			name="pdf_viewer_2026_settings[<?php echo esc_attr( $args['key'] ); ?>]"
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
		$settings = PDF_Viewer_2026::get_setting();
		$value    = isset( $settings[ $args['key'] ] ) ? $settings[ $args['key'] ] : 12;
		?>
		<input
			type="number"
			id="<?php echo esc_attr( $args['key'] ); ?>"
			name="pdf_viewer_2026_settings[<?php echo esc_attr( $args['key'] ); ?>]"
			value="<?php echo esc_attr( $value ); ?>"
			min="<?php echo esc_attr( $args['min'] ); ?>"
			max="<?php echo esc_attr( $args['max'] ); ?>"
			class="small-text"
		/>
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
				$new_columns['pdf_file']    = __( 'PDF File', 'pdf-viewer-2026' );
				$new_columns['permissions'] = __( 'Permissions', 'pdf-viewer-2026' );
				$new_columns['views']       = __( 'Views', 'pdf-viewer-2026' );
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
					echo '<em>' . esc_html__( 'No file attached', 'pdf-viewer-2026' ) . '</em>';
				}
				break;

			case 'permissions':
				$allow_download = PDF_Viewer_2026_Post_Type::is_download_allowed( $post_id );
				$allow_print    = PDF_Viewer_2026_Post_Type::is_print_allowed( $post_id );

				$permissions = array();
				if ( $allow_download ) {
					$permissions[] = __( 'Download', 'pdf-viewer-2026' );
				}
				if ( $allow_print ) {
					$permissions[] = __( 'Print', 'pdf-viewer-2026' );
				}

				if ( empty( $permissions ) ) {
					echo '<em>' . esc_html__( 'View only', 'pdf-viewer-2026' ) . '</em>';
				} else {
					echo esc_html( implode( ', ', $permissions ) );
				}
				break;

			case 'views':
				$view_count = PDF_Viewer_2026_Post_Type::get_view_count( $post_id );
				echo esc_html( number_format_i18n( $view_count ) );
				break;
		}
	}
}
