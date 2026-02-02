<?php
/**
 * Premium Features Loader
 *
 * Handles loading and initialization of premium features.
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium features loader class.
 */
final class PDF_Embed_SEO_Premium {

	/**
	 * Single instance of the class.
	 *
	 * @var PDF_Embed_SEO_Premium|null
	 */
	private static $instance = null;

	/**
	 * Premium version.
	 *
	 * @var string
	 */
	const VERSION = '1.2.7';

	/**
	 * License status.
	 * Possible values: 'valid', 'expired', 'invalid', 'inactive'
	 *
	 * @var string
	 */
	private $license_status = 'valid';

	/**
	 * License expiration date.
	 *
	 * @var string|null
	 */
	private $license_expires = null;

	/**
	 * Grace period in days after license expiration.
	 *
	 * @var int
	 */
	const GRACE_PERIOD_DAYS = 14;

	/**
	 * Taxonomies instance.
	 *
	 * @var PDF_Embed_SEO_Premium_Taxonomies|null
	 */
	public $taxonomies = null;

	/**
	 * Password protection instance.
	 *
	 * @var PDF_Embed_SEO_Premium_Password|null
	 */
	public $password = null;

	/**
	 * Role restrictions instance.
	 *
	 * @var PDF_Embed_SEO_Premium_Roles|null
	 */
	public $roles = null;

	/**
	 * Analytics instance.
	 *
	 * @var PDF_Embed_SEO_Premium_Analytics|null
	 */
	public $analytics = null;

	/**
	 * Viewer enhancements instance.
	 *
	 * @var PDF_Embed_SEO_Premium_Viewer|null
	 */
	public $viewer = null;

	/**
	 * Bulk operations instance.
	 *
	 * @var PDF_Embed_SEO_Premium_Bulk|null
	 */
	public $bulk = null;

	/**
	 * Sitemap instance.
	 *
	 * @var PDF_Embed_SEO_Premium_Sitemap|null
	 */
	public $sitemap = null;

	/**
	 * REST API instance.
	 *
	 * @var PDF_Embed_SEO_Premium_REST_API|null
	 */
	public $rest_api = null;

	/**
	 * Schema enhancements instance.
	 *
	 * @var PDF_Embed_SEO_Premium_Schema|null
	 */
	public $schema = null;

	/**
	 * Get the single instance of the class.
	 *
	 * @return PDF_Embed_SEO_Premium
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define premium constants.
	 *
	 * @return void
	 */
	private function define_constants() {
		define( 'PDF_EMBED_SEO_PREMIUM_VERSION', self::VERSION );
		define( 'PDF_EMBED_SEO_PREMIUM_DIR', plugin_dir_path( __FILE__ ) );
		define( 'PDF_EMBED_SEO_PREMIUM_URL', plugin_dir_url( __FILE__ ) );
		define( 'PDF_EMBED_SEO_IS_PREMIUM', true );
	}

	/**
	 * Include required premium files.
	 *
	 * @return void
	 */
	private function includes() {
		// Premium feature classes.
		require_once PDF_EMBED_SEO_PREMIUM_DIR . 'includes/class-pdf-embed-seo-premium-taxonomies.php';
		require_once PDF_EMBED_SEO_PREMIUM_DIR . 'includes/class-pdf-embed-seo-premium-password.php';
		require_once PDF_EMBED_SEO_PREMIUM_DIR . 'includes/class-pdf-embed-seo-premium-roles.php';
		require_once PDF_EMBED_SEO_PREMIUM_DIR . 'includes/class-pdf-embed-seo-premium-analytics.php';
		require_once PDF_EMBED_SEO_PREMIUM_DIR . 'includes/class-pdf-embed-seo-premium-viewer.php';
		require_once PDF_EMBED_SEO_PREMIUM_DIR . 'includes/class-pdf-embed-seo-premium-bulk.php';
		require_once PDF_EMBED_SEO_PREMIUM_DIR . 'includes/class-pdf-embed-seo-premium-sitemap.php';
		require_once PDF_EMBED_SEO_PREMIUM_DIR . 'includes/class-pdf-embed-seo-premium-rest-api.php';
		require_once PDF_EMBED_SEO_PREMIUM_DIR . 'includes/class-pdf-embed-seo-premium-schema.php';

		// Admin classes (only in admin context).
		if ( is_admin() ) {
			require_once PDF_EMBED_SEO_PREMIUM_DIR . 'includes/class-pdf-embed-seo-premium-admin.php';
		}
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ), 5 );
		add_action( 'admin_notices', array( $this, 'license_notices' ) );
		add_filter( 'pdf_embed_seo_is_premium', '__return_true' );

		// Archive redirect (premium feature).
		add_action( 'template_redirect', array( $this, 'maybe_redirect_archive' ) );

		// Always initialize admin for license page (regardless of license status).
		if ( is_admin() ) {
			// Register license page early (admin_menu fires before admin_init).
			add_action( 'admin_menu', array( $this, 'add_license_page' ), 99 );
			add_action( 'admin_init', array( $this, 'register_license_settings' ) );
			add_action( 'admin_init', array( $this, 'init_admin_always' ) );
		}
	}

	/**
	 * Register license settings.
	 * This runs on admin_init to ensure settings are available for the license page.
	 *
	 * @return void
	 */
	public function register_license_settings() {
		register_setting(
			'pdf_embed_seo_license',
			'pdf_embed_seo_premium_license_key',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// Add capability filter for license settings page.
		add_filter( 'option_page_capability_pdf_embed_seo_license', function() {
			return 'manage_options';
		} );

		// Handle license key validation on save.
		add_action( 'update_option_pdf_embed_seo_premium_license_key', array( $this, 'validate_license_key' ), 10, 2 );
		add_action( 'add_option_pdf_embed_seo_premium_license_key', array( $this, 'validate_license_key_on_add' ), 10, 2 );
	}

	/**
	 * Validate license key when option is added.
	 *
	 * @param string $option Option name.
	 * @param mixed  $value  Option value.
	 * @return void
	 */
	public function validate_license_key_on_add( $option, $value ) {
		$this->validate_license( $value );
	}

	/**
	 * Validate license key when updated.
	 *
	 * @param mixed $old_value Old value.
	 * @param mixed $new_value New value.
	 * @return void
	 */
	public function validate_license_key( $old_value, $new_value ) {
		$this->validate_license( $new_value );
	}

	/**
	 * Process license key validation.
	 *
	 * @param string $license_key The license key to validate.
	 * @return void
	 */
	private function validate_license( $license_key ) {
		if ( empty( $license_key ) ) {
			update_option( 'pdf_embed_seo_premium_license_status', 'inactive' );
			delete_option( 'pdf_embed_seo_premium_license_expires' );
			return;
		}

		// Minimum length check (20+ characters).
		if ( strlen( $license_key ) < 20 ) {
			update_option( 'pdf_embed_seo_premium_license_status', 'invalid' );
			delete_option( 'pdf_embed_seo_premium_license_expires' );
			return;
		}

		// Test/Development license keys for unlimited validity.
		$test_key_patterns = array(
			'/^PDF\$UNLIMITED#[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i',
			'/^PDF\$DEV#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i',
		);

		foreach ( $test_key_patterns as $pattern ) {
			if ( preg_match( $pattern, $license_key ) ) {
				update_option( 'pdf_embed_seo_premium_license_status', 'valid' );
				delete_option( 'pdf_embed_seo_premium_license_expires' );
				return;
			}
		}

		// Standard license key validation.
		if ( preg_match( '/^PDF\$PRO#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}-[A-Z0-9]{4}![A-Z0-9]{4}$/i', $license_key ) ) {
			update_option( 'pdf_embed_seo_premium_license_status', 'valid' );
			update_option( 'pdf_embed_seo_premium_license_expires', gmdate( 'Y-m-d', strtotime( '+1 year' ) ) );
			return;
		}

		// Invalid key format.
		update_option( 'pdf_embed_seo_premium_license_status', 'invalid' );
		delete_option( 'pdf_embed_seo_premium_license_expires' );
	}

	/**
	 * Add license page to admin menu.
	 * This is hooked early to ensure the page is always accessible.
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
		$license_status = get_option( 'pdf_embed_seo_premium_license_status', 'inactive' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'License Settings', 'pdf-embed-seo-optimize' ); ?></h1>

			<div class="pdf-license-status <?php echo 'valid' === $license_status ? 'active' : 'inactive'; ?>" style="padding: 15px; margin: 20px 0; border-radius: 4px; <?php echo 'valid' === $license_status ? 'background: #d4edda; border-left: 4px solid #28a745;' : 'background: #fff3cd; border-left: 4px solid #ffc107;'; ?>">
				<span class="dashicons <?php echo 'valid' === $license_status ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>" style="font-size: 24px; margin-right: 10px;"></span>
				<?php if ( 'valid' === $license_status ) : ?>
					<strong><?php esc_html_e( 'License Active', 'pdf-embed-seo-optimize' ); ?></strong>
					<p style="margin: 5px 0 0;"><?php esc_html_e( 'Your premium license is active. Thank you for your support!', 'pdf-embed-seo-optimize' ); ?></p>
				<?php else : ?>
					<strong><?php esc_html_e( 'License Inactive', 'pdf-embed-seo-optimize' ); ?></strong>
					<p style="margin: 5px 0 0;"><?php esc_html_e( 'Please enter a valid license key to activate premium features.', 'pdf-embed-seo-optimize' ); ?></p>
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
				<?php submit_button( __( 'Save License Key', 'pdf-embed-seo-optimize' ) ); ?>
			</form>

			<p>
				<?php
				printf(
					/* translators: %s: link to purchase page */
					esc_html__( 'Don\'t have a license key? %s', 'pdf-embed-seo-optimize' ),
					'<a href="https://pdfviewer.drossmedia.de" target="_blank">' . esc_html__( 'Purchase a license', 'pdf-embed-seo-optimize' ) . '</a>'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Initialize admin components that should always be available.
	 * This ensures the license page is accessible even when license is inactive.
	 *
	 * @return void
	 */
	public function init_admin_always() {
		static $admin_initialized = false;

		// Only initialize once and only if license is NOT valid.
		// (If license is valid, admin is initialized in init() method).
		if ( $admin_initialized || $this->is_license_valid() ) {
			return;
		}

		$admin_initialized = true;
		new PDF_Embed_SEO_Premium_Admin();
	}

	/**
	 * Maybe redirect the archive page based on premium settings.
	 *
	 * @return void
	 */
	public function maybe_redirect_archive() {
		// Only proceed if on the PDF archive page.
		if ( ! is_post_type_archive( 'pdf_document' ) ) {
			return;
		}

		// Only if license is valid.
		if ( ! $this->is_license_valid() ) {
			return;
		}

		// Check if redirect is enabled.
		$settings = get_option( 'pdf_embed_seo_premium_settings', array() );
		$redirect_enabled = isset( $settings['archive_redirect_enabled'] ) && '1' === $settings['archive_redirect_enabled'];

		if ( ! $redirect_enabled ) {
			return;
		}

		// Get redirect settings.
		$redirect_type = isset( $settings['archive_redirect_type'] ) ? $settings['archive_redirect_type'] : '301';
		$redirect_url  = isset( $settings['archive_redirect_url'] ) ? $settings['archive_redirect_url'] : home_url( '/' );

		// Validate redirect URL.
		if ( empty( $redirect_url ) ) {
			$redirect_url = home_url( '/' );
		}

		// Perform redirect.
		$status_code = '301' === $redirect_type ? 301 : 302;
		wp_safe_redirect( $redirect_url, $status_code );
		exit;
	}

	/**
	 * Initialize premium components.
	 *
	 * @return void
	 */
	public function init() {
		// Only initialize if license is valid.
		if ( ! $this->is_license_valid() ) {
			return;
		}

		// Initialize taxonomies.
		$this->taxonomies = new PDF_Embed_SEO_Premium_Taxonomies();

		// Initialize password protection.
		$this->password = new PDF_Embed_SEO_Premium_Password();

		// Initialize role restrictions.
		$this->roles = new PDF_Embed_SEO_Premium_Roles();

		// Initialize analytics.
		$this->analytics = new PDF_Embed_SEO_Premium_Analytics();

		// Initialize viewer enhancements.
		$this->viewer = new PDF_Embed_SEO_Premium_Viewer();

		// Initialize bulk operations.
		$this->bulk = new PDF_Embed_SEO_Premium_Bulk();

		// Initialize sitemap.
		$this->sitemap = new PDF_Embed_SEO_Premium_Sitemap();

		// Initialize premium REST API.
		$this->rest_api = new PDF_Embed_SEO_Premium_REST_API();

		// Initialize premium schema enhancements (GEO/AEO/LLM optimization).
		$this->schema = new PDF_Embed_SEO_Premium_Schema();

		// Initialize admin (only in admin context).
		if ( is_admin() ) {
			new PDF_Embed_SEO_Premium_Admin();
		}

		/**
		 * Fires after premium features are initialized.
		 *
		 * @since 1.0.0
		 */
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Established public API hook.
		do_action( 'pdf_embed_seo_premium_init' );
	}

	/**
	 * Check if license is valid.
	 *
	 * Validates license status and expiration date.
	 * If license is expired, premium features are disabled and
	 * the plugin falls back to free version functionality.
	 *
	 * @return bool True if license is valid, false otherwise.
	 */
	public function is_license_valid() {
		$this->license_status  = get_option( 'pdf_embed_seo_premium_license_status', 'inactive' );
		$this->license_expires = get_option( 'pdf_embed_seo_premium_license_expires', null );

		// If license was never activated, run in free mode.
		if ( 'inactive' === $this->license_status || empty( $this->license_status ) ) {
			return false;
		}

		// If license is marked as invalid, run in free mode.
		if ( 'invalid' === $this->license_status ) {
			return false;
		}

		// Check expiration date if license was previously valid.
		if ( 'valid' === $this->license_status && ! empty( $this->license_expires ) ) {
			$expires_timestamp = strtotime( $this->license_expires );
			$current_timestamp = current_time( 'timestamp' );

			// Check if license has expired.
			if ( $expires_timestamp < $current_timestamp ) {
				// Check if within grace period.
				$grace_end = $expires_timestamp + ( self::GRACE_PERIOD_DAYS * DAY_IN_SECONDS );

				if ( $current_timestamp > $grace_end ) {
					// Grace period ended - mark as expired and disable premium.
					update_option( 'pdf_embed_seo_premium_license_status', 'expired' );
					$this->license_status = 'expired';
					return false;
				}

				// Within grace period - show warning but allow premium.
				$this->license_status = 'grace_period';
				return true;
			}
		}

		// License is expired (manually set or detected).
		if ( 'expired' === $this->license_status ) {
			return false;
		}

		return 'valid' === $this->license_status || 'grace_period' === $this->license_status;
	}

	/**
	 * Get license status.
	 *
	 * @return string License status.
	 */
	public function get_license_status() {
		return $this->license_status;
	}

	/**
	 * Get license expiration date.
	 *
	 * @return string|null Expiration date or null if not set.
	 */
	public function get_license_expires() {
		return $this->license_expires;
	}

	/**
	 * Get days remaining until license expires.
	 *
	 * @return int|null Days remaining or null if no expiration.
	 */
	public function get_days_remaining() {
		if ( empty( $this->license_expires ) ) {
			return null;
		}

		$expires_timestamp = strtotime( $this->license_expires );
		$current_timestamp = current_time( 'timestamp' );
		$diff              = $expires_timestamp - $current_timestamp;

		if ( $diff <= 0 ) {
			return 0;
		}

		return ceil( $diff / DAY_IN_SECONDS );
	}

	/**
	 * Display license notices.
	 *
	 * Shows appropriate notices based on license status:
	 * - Expired: Premium features disabled, fallback to free version.
	 * - Grace period: Warning with days remaining.
	 * - Inactive: Prompt to enter license key.
	 * - Invalid: Error message.
	 *
	 * @return void
	 */
	public function license_notices() {
		$license_url = admin_url( 'edit.php?post_type=pdf_document&page=pdf-license' );
		$renew_url   = 'https://pdfviewer.drossmedia.de/renew/';

		switch ( $this->license_status ) {
			case 'expired':
				?>
				<div class="notice notice-error">
					<p>
						<strong><?php esc_html_e( 'PDF Embed & SEO Optimize:', 'pdf-embed-seo-optimize' ); ?></strong>
						<?php esc_html_e( 'Your premium license has expired. Premium features have been disabled and the plugin is now running in free mode.', 'pdf-embed-seo-optimize' ); ?>
						<a href="<?php echo esc_url( $renew_url ); ?>" target="_blank"><?php esc_html_e( 'Renew License', 'pdf-embed-seo-optimize' ); ?></a>
					</p>
				</div>
				<?php
				break;

			case 'grace_period':
				$days_remaining = $this->get_days_remaining();
				$grace_days     = self::GRACE_PERIOD_DAYS - abs( $days_remaining );
				?>
				<div class="notice notice-warning">
					<p>
						<strong><?php esc_html_e( 'PDF Embed & SEO Optimize:', 'pdf-embed-seo-optimize' ); ?></strong>
						<?php
						printf(
							/* translators: %d: Number of grace period days remaining. */
							esc_html__( 'Your license has expired! You have %d days remaining in your grace period before premium features are disabled.', 'pdf-embed-seo-optimize' ),
							absint( $grace_days )
						);
						?>
						<a href="<?php echo esc_url( $renew_url ); ?>" target="_blank"><?php esc_html_e( 'Renew Now', 'pdf-embed-seo-optimize' ); ?></a>
					</p>
				</div>
				<?php
				break;

			case 'inactive':
				?>
				<div class="notice notice-info is-dismissible">
					<p>
						<strong><?php esc_html_e( 'PDF Embed & SEO Optimize:', 'pdf-embed-seo-optimize' ); ?></strong>
						<?php esc_html_e( 'Enter your license key to activate premium features.', 'pdf-embed-seo-optimize' ); ?>
						<a href="<?php echo esc_url( $license_url ); ?>"><?php esc_html_e( 'Enter License', 'pdf-embed-seo-optimize' ); ?></a>
					</p>
				</div>
				<?php
				break;

			case 'invalid':
				?>
				<div class="notice notice-error is-dismissible">
					<p>
						<strong><?php esc_html_e( 'PDF Embed & SEO Optimize:', 'pdf-embed-seo-optimize' ); ?></strong>
						<?php esc_html_e( 'Your license key is invalid. Please check your license key or contact support.', 'pdf-embed-seo-optimize' ); ?>
						<a href="<?php echo esc_url( $license_url ); ?>"><?php esc_html_e( 'Update License', 'pdf-embed-seo-optimize' ); ?></a>
					</p>
				</div>
				<?php
				break;

			case 'valid':
				// Check if license expires soon (within 30 days).
				$days_remaining = $this->get_days_remaining();
				if ( null !== $days_remaining && $days_remaining <= 30 ) {
					?>
					<div class="notice notice-warning is-dismissible">
						<p>
							<strong><?php esc_html_e( 'PDF Embed & SEO Optimize:', 'pdf-embed-seo-optimize' ); ?></strong>
							<?php
							printf(
								/* translators: %d: Number of days until license expires. */
								esc_html__( 'Your license will expire in %d days. Renew now to maintain premium features.', 'pdf-embed-seo-optimize' ),
								absint( $days_remaining )
							);
							?>
							<a href="<?php echo esc_url( $renew_url ); ?>" target="_blank"><?php esc_html_e( 'Renew License', 'pdf-embed-seo-optimize' ); ?></a>
						</p>
					</div>
					<?php
				}
				break;
		}
	}

	/**
	 * Get premium feature status.
	 *
	 * @param string $feature Feature name.
	 * @return bool
	 */
	public function is_feature_enabled( $feature ) {
		$premium_settings = get_option( 'pdf_embed_seo_premium_settings', array() );
		return isset( $premium_settings[ $feature ] ) ? (bool) $premium_settings[ $feature ] : true;
	}

	/**
	 * Prevent cloning.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 *
	 * @return void
	 */
	public function __wakeup() {
		throw new Exception( 'Cannot unserialize singleton' );
	}
}

/**
 * Returns the main instance of the premium plugin.
 *
 * @return PDF_Embed_SEO_Premium
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Part of established public API.
function pdf_embed_seo_premium() {
	return PDF_Embed_SEO_Premium::get_instance();
}

// Initialize premium features.
pdf_embed_seo_premium();
