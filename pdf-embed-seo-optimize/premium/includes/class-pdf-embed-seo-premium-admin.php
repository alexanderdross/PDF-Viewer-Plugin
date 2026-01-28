<?php
/**
 * Premium Admin
 *
 * Admin settings and features for premium version.
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium admin class.
 */
class PDF_Embed_SEO_Premium_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add premium settings section.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add premium settings tab.
		add_filter( 'pdf_embed_seo_settings_tabs', array( $this, 'add_settings_tab' ) );

		// Enqueue admin scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add license settings.
		add_action( 'admin_menu', array( $this, 'add_license_page' ), 99 );

		// Add premium badge to admin.
		add_action( 'admin_footer', array( $this, 'add_premium_badge' ) );

		// Modify plugin row.
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_links' ), 10, 2 );

		// Change plugin name to show Premium.
		add_filter( 'all_plugins', array( $this, 'modify_plugin_name' ) );

		// Modify plugin action links for premium.
		add_filter( 'plugin_action_links_' . PDF_EMBED_SEO_PLUGIN_BASENAME, array( $this, 'modify_plugin_action_links' ), 20 );
	}

	/**
	 * Modify plugin name to show Premium version.
	 *
	 * @param array $plugins All plugins.
	 * @return array Modified plugins.
	 */
	public function modify_plugin_name( $plugins ) {
		// Use the actual plugin basename constant.
		$plugin_file = PDF_EMBED_SEO_PLUGIN_BASENAME;
		if ( isset( $plugins[ $plugin_file ] ) ) {
			$plugins[ $plugin_file ]['Name'] = 'PDF Embed & SEO Optimize (Premium)';
			$plugins[ $plugin_file ]['Title'] = 'PDF Embed & SEO Optimize (Premium)';
			$plugins[ $plugin_file ]['PluginURI'] = 'https://pdfviewer.drossmedia.de';
		}
		return $plugins;
	}

	/**
	 * Modify plugin action links for premium version.
	 *
	 * @param array $links Action links.
	 * @return array Modified links.
	 */
	public function modify_plugin_action_links( $links ) {
		// Remove "Get Premium" link since we have premium.
		unset( $links['premium'] );

		// Add "Changelog" link.
		$links['changelog'] = sprintf(
			'<a href="%s" target="_blank">%s</a>',
			'https://pdfviewer.drossmedia.de/changelog/',
			esc_html__( 'Changelog', 'pdf-embed-seo-optimize' )
		);

		return $links;
	}

	/**
	 * Register premium settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'pdf_embed_seo_premium_settings', 'pdf_embed_seo_premium_settings', array( $this, 'sanitize_settings' ) );

		// Register license settings.
		register_setting(
			'pdf_embed_seo_license',
			'pdf_embed_seo_premium_license_key',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// Add capability filter for license settings page.
		add_filter( 'option_page_capability_pdf_embed_seo_license', array( $this, 'get_license_page_capability' ) );

		// Premium features section.
		add_settings_section(
			'pdf_premium_features',
			__( 'Premium Features', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_features_section' ),
			'pdf-embed-seo-premium'
		);

		// Enable/disable features.
		add_settings_field(
			'enable_categories',
			__( 'PDF Categories', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_toggle_field' ),
			'pdf-embed-seo-premium',
			'pdf_premium_features',
			array(
				'id'          => 'enable_categories',
				'description' => __( 'Enable PDF categories and tags.', 'pdf-embed-seo-optimize' ),
			)
		);

		add_settings_field(
			'enable_password',
			__( 'Password Protection', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_toggle_field' ),
			'pdf-embed-seo-premium',
			'pdf_premium_features',
			array(
				'id'          => 'enable_password',
				'description' => __( 'Enable password protection for PDFs.', 'pdf-embed-seo-optimize' ),
			)
		);

		add_settings_field(
			'enable_roles',
			__( 'Role Restrictions', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_toggle_field' ),
			'pdf-embed-seo-premium',
			'pdf_premium_features',
			array(
				'id'          => 'enable_roles',
				'description' => __( 'Enable user role restrictions for PDFs.', 'pdf-embed-seo-optimize' ),
			)
		);

		add_settings_field(
			'enable_analytics',
			__( 'Advanced Analytics', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_toggle_field' ),
			'pdf-embed-seo-premium',
			'pdf_premium_features',
			array(
				'id'          => 'enable_analytics',
				'description' => __( 'Enable detailed PDF analytics.', 'pdf-embed-seo-optimize' ),
			)
		);

		add_settings_field(
			'enable_search',
			__( 'Text Search', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_toggle_field' ),
			'pdf-embed-seo-premium',
			'pdf_premium_features',
			array(
				'id'          => 'enable_search',
				'description' => __( 'Enable text search in PDF viewer.', 'pdf-embed-seo-optimize' ),
			)
		);

		add_settings_field(
			'enable_bookmarks',
			__( 'Bookmarks Panel', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_toggle_field' ),
			'pdf-embed-seo-premium',
			'pdf_premium_features',
			array(
				'id'          => 'enable_bookmarks',
				'description' => __( 'Enable bookmarks/outline panel in viewer.', 'pdf-embed-seo-optimize' ),
			)
		);

		add_settings_field(
			'enable_progress',
			__( 'Reading Progress', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_toggle_field' ),
			'pdf-embed-seo-premium',
			'pdf_premium_features',
			array(
				'id'          => 'enable_progress',
				'description' => __( 'Save and resume reading progress.', 'pdf-embed-seo-optimize' ),
			)
		);

		add_settings_field(
			'enable_sitemap',
			__( 'PDF Sitemap', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_toggle_field' ),
			'pdf-embed-seo-premium',
			'pdf_premium_features',
			array(
				'id'          => 'enable_sitemap',
				'description' => __( 'Generate dedicated XML sitemap for PDFs.', 'pdf-embed-seo-optimize' ),
			)
		);

		// Archive Redirect section (Premium).
		add_settings_section(
			'pdf_premium_redirect',
			__( 'Archive Page Redirect', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_redirect_section' ),
			'pdf-embed-seo-premium'
		);

		add_settings_field(
			'archive_redirect_enabled',
			__( 'Enable Archive Redirect', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_toggle_field' ),
			'pdf-embed-seo-premium',
			'pdf_premium_redirect',
			array(
				'id'          => 'archive_redirect_enabled',
				'description' => __( 'Redirect the PDF archive page (/pdf/) to another URL.', 'pdf-embed-seo-optimize' ),
			)
		);

		add_settings_field(
			'archive_redirect_type',
			__( 'Redirect Type', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_redirect_type_field' ),
			'pdf-embed-seo-premium',
			'pdf_premium_redirect'
		);

		add_settings_field(
			'archive_redirect_url',
			__( 'Redirect URL', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_redirect_url_field' ),
			'pdf-embed-seo-premium',
			'pdf_premium_redirect'
		);
	}

	/**
	 * Sanitize premium settings.
	 *
	 * @param array $input Input settings.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		$toggles = array(
			'enable_categories',
			'enable_password',
			'enable_roles',
			'enable_analytics',
			'enable_search',
			'enable_bookmarks',
			'enable_progress',
			'enable_sitemap',
			'archive_redirect_enabled',
		);

		foreach ( $toggles as $key ) {
			$sanitized[ $key ] = isset( $input[ $key ] ) ? '1' : '';
		}

		// Redirect type.
		$sanitized['archive_redirect_type'] = isset( $input['archive_redirect_type'] ) && in_array( $input['archive_redirect_type'], array( '301', '302' ), true )
			? $input['archive_redirect_type']
			: '301';

		// Redirect URL.
		$sanitized['archive_redirect_url'] = isset( $input['archive_redirect_url'] )
			? esc_url_raw( $input['archive_redirect_url'] )
			: home_url( '/' );

		return $sanitized;
	}

	/**
	 * Render features section description.
	 *
	 * @return void
	 */
	public function render_features_section() {
		echo '<p>' . esc_html__( 'Enable or disable premium features as needed.', 'pdf-embed-seo-optimize' ) . '</p>';
	}

	/**
	 * Render redirect section description.
	 *
	 * @return void
	 */
	public function render_redirect_section() {
		echo '<p>' . esc_html__( 'Configure automatic redirect from the PDF archive page (/pdf/) to another URL. Useful if you want to direct visitors to your homepage or a custom landing page instead of the archive.', 'pdf-embed-seo-optimize' ) . '</p>';
	}

	/**
	 * Render redirect type field.
	 *
	 * @return void
	 */
	public function render_redirect_type_field() {
		$settings = get_option( 'pdf_embed_seo_premium_settings', array() );
		$value    = isset( $settings['archive_redirect_type'] ) ? $settings['archive_redirect_type'] : '301';
		?>
		<select name="pdf_embed_seo_premium_settings[archive_redirect_type]" id="archive_redirect_type">
			<option value="301" <?php selected( $value, '301' ); ?>>
				<?php esc_html_e( '301 - Permanent Redirect (recommended for SEO)', 'pdf-embed-seo-optimize' ); ?>
			</option>
			<option value="302" <?php selected( $value, '302' ); ?>>
				<?php esc_html_e( '302 - Temporary Redirect', 'pdf-embed-seo-optimize' ); ?>
			</option>
		</select>
		<p class="description">
			<?php esc_html_e( '301 is permanent and passes SEO value. Use 302 for temporary redirects.', 'pdf-embed-seo-optimize' ); ?>
		</p>
		<?php
	}

	/**
	 * Render redirect URL field.
	 *
	 * @return void
	 */
	public function render_redirect_url_field() {
		$settings = get_option( 'pdf_embed_seo_premium_settings', array() );
		$value    = isset( $settings['archive_redirect_url'] ) ? $settings['archive_redirect_url'] : home_url( '/' );
		?>
		<input type="url"
			   name="pdf_embed_seo_premium_settings[archive_redirect_url]"
			   id="archive_redirect_url"
			   value="<?php echo esc_url( $value ); ?>"
			   class="regular-text"
			   placeholder="<?php echo esc_attr( home_url( '/' ) ); ?>"
		/>
		<p class="description">
			<?php esc_html_e( 'Enter the URL where visitors should be redirected (e.g., your homepage).', 'pdf-embed-seo-optimize' ); ?>
		</p>
		<?php
	}

	/**
	 * Render toggle field.
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_toggle_field( $args ) {
		$settings = get_option( 'pdf_embed_seo_premium_settings', array() );
		$value    = isset( $settings[ $args['id'] ] ) ? $settings[ $args['id'] ] : '1';
		?>
		<label class="pdf-toggle">
			<input type="checkbox" name="pdf_embed_seo_premium_settings[<?php echo esc_attr( $args['id'] ); ?>]" value="1" <?php checked( $value, '1' ); ?> />
			<span class="pdf-toggle-slider"></span>
		</label>
		<?php if ( ! empty( $args['description'] ) ) : ?>
			<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Add settings tab.
	 *
	 * @param array $tabs Existing tabs.
	 * @return array
	 */
	public function add_settings_tab( $tabs ) {
		$tabs['premium'] = __( 'Premium', 'pdf-embed-seo-optimize' );
		return $tabs;
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook Current admin page.
	 * @return void
	 */
	public function enqueue_admin_scripts( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen || 'pdf_document' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_style(
			'pdf-embed-seo-premium-admin',
			PDF_EMBED_SEO_PREMIUM_URL . 'assets/css/premium-admin.css',
			array(),
			PDF_EMBED_SEO_PREMIUM_VERSION
		);

		wp_enqueue_script(
			'pdf-embed-seo-premium-admin',
			PDF_EMBED_SEO_PREMIUM_URL . 'assets/js/premium-admin.js',
			array( 'jquery' ),
			PDF_EMBED_SEO_PREMIUM_VERSION,
			true
		);
	}

	/**
	 * Get capability for license settings page.
	 *
	 * @return string Capability required.
	 */
	public function get_license_page_capability() {
		return 'manage_options';
	}

	/**
	 * Add license page.
	 *
	 * @return void
	 */
	public function add_license_page() {
		add_submenu_page(
			'edit.php?post_type=pdf_document',
			__( 'License', 'pdf-embed-seo-optimize' ),
			__( 'License', 'pdf-embed-seo-optimize' ),
			'manage_options',
			'pdf-license',
			array( $this, 'render_license_page' )
		);
	}

	/**
	 * Render license page.
	 *
	 * @return void
	 */
	public function render_license_page() {
		$license_key    = get_option( 'pdf_embed_seo_premium_license_key', '' );
		$license_status = get_option( 'pdf_embed_seo_premium_license_status', 'valid' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'License Settings', 'pdf-embed-seo-optimize' ); ?></h1>

			<div class="pdf-license-status <?php echo 'valid' === $license_status ? 'active' : 'inactive'; ?>">
				<span class="dashicons <?php echo 'valid' === $license_status ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
				<?php if ( 'valid' === $license_status ) : ?>
					<strong><?php esc_html_e( 'License Active', 'pdf-embed-seo-optimize' ); ?></strong>
					<p><?php esc_html_e( 'Your premium license is active. Thank you for your support!', 'pdf-embed-seo-optimize' ); ?></p>
				<?php else : ?>
					<strong><?php esc_html_e( 'License Inactive', 'pdf-embed-seo-optimize' ); ?></strong>
					<p><?php esc_html_e( 'Please enter a valid license key to activate premium features.', 'pdf-embed-seo-optimize' ); ?></p>
				<?php endif; ?>
			</div>

			<form method="post" action="options.php">
				<?php settings_fields( 'pdf_embed_seo_license' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="license_key"><?php esc_html_e( 'License Key', 'pdf-embed-seo-optimize' ); ?></label>
						</th>
						<td>
							<input type="password" id="license_key" name="pdf_embed_seo_premium_license_key" value="<?php echo esc_attr( $license_key ); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e( 'Enter your license key to activate automatic updates and premium support.', 'pdf-embed-seo-optimize' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'Save License', 'pdf-embed-seo-optimize' ) ); ?>
			</form>

			<p class="pdf-embed-seo-get-license" style="margin-top: 20px;">
				<?php
				printf(
					/* translators: %s: link to purchase page */
					esc_html__( 'Don\'t have a license key? %s', 'pdf-embed-seo-optimize' ),
					'<a href="https://pdfviewer.drossmedia.de/" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr__( 'Get a premium license (opens in new tab)', 'pdf-embed-seo-optimize' ) . '" title="' . esc_attr__( 'Get a premium license', 'pdf-embed-seo-optimize' ) . '">' . esc_html__( 'Get a premium license', 'pdf-embed-seo-optimize' ) . '</a>'
				);
				?>
			</p>

			<p class="pdf-embed-seo-optimize-credit" style="text-align: center; margin-top: 30px; color: #666; font-size: 13px;">
				<?php
				printf(
					/* translators: %1$s: heart symbol, %2$s: Dross:Media link */
					esc_html__( 'made with %1$s by %2$s', 'pdf-embed-seo-optimize' ),
					'<span style="color: #e25555;" aria-hidden="true">♥</span><span class="screen-reader-text">' . esc_html__( 'love', 'pdf-embed-seo-optimize' ) . '</span>',
					'<a href="https://dross.net/media/" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr__( 'Visit Dross:Media website (opens in new tab)', 'pdf-embed-seo-optimize' ) . '" title="' . esc_attr__( 'Visit Dross:Media website', 'pdf-embed-seo-optimize' ) . '">Dross:Media</a>'
				);
				?>
			</p>
		</div>

		<style>
			.pdf-license-status {
				padding: 20px;
				border-radius: 4px;
				margin: 20px 0;
			}
			.pdf-license-status.active {
				background: #d4edda;
				border: 1px solid #c3e6cb;
				color: #155724;
			}
			.pdf-license-status.inactive {
				background: #fff3cd;
				border: 1px solid #ffeeba;
				color: #856404;
			}
			.pdf-license-status .dashicons {
				font-size: 24px;
				width: 24px;
				height: 24px;
				vertical-align: middle;
				margin-right: 10px;
			}
			.pdf-license-status strong {
				font-size: 16px;
				vertical-align: middle;
			}
			.pdf-license-status p {
				margin: 10px 0 0 34px;
			}
		</style>
		<?php
	}

	/**
	 * Add premium badge to admin.
	 *
	 * @return void
	 */
	public function add_premium_badge() {
		$screen = get_current_screen();
		if ( ! $screen || 'pdf_document' !== $screen->post_type ) {
			return;
		}
		?>
		<style>
			.pdf-premium-badge {
				display: inline-block;
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				color: #fff;
				font-size: 10px;
				font-weight: 600;
				padding: 2px 6px;
				border-radius: 3px;
				text-transform: uppercase;
				margin-left: 5px;
				vertical-align: middle;
			}
		</style>
		<?php
	}

	/**
	 * Add plugin row links.
	 *
	 * @param array  $links Plugin row links.
	 * @param string $file  Plugin file.
	 * @return array
	 */
	public function add_plugin_row_links( $links, $file ) {
		if ( strpos( $file, 'pdf-embed-seo-optimize' ) !== false ) {
			$links[] = '<a href="' . esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-license' ) ) . '">' . esc_html__( 'License', 'pdf-embed-seo-optimize' ) . '</a>';
			$links[] = '<a href="https://pdfviewer.drossmedia.de/documentation/" target="_blank">' . esc_html__( 'Documentation', 'pdf-embed-seo-optimize' ) . '</a>';
		}

		return $links;
	}
}
