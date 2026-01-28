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
	const VERSION = '1.2.1';

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

		// Initialize admin (only in admin context).
		if ( is_admin() ) {
			new PDF_Embed_SEO_Premium_Admin();
		}

		/**
		 * Fires after premium features are initialized.
		 *
		 * @since 1.0.0
		 */
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
						<strong><?php esc_html_e( 'PDF Embed & SEO Optimize:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
						<?php esc_html_e( 'Your premium license has expired. Premium features have been disabled and the plugin is now running in free mode.', 'wp-pdf-embed-seo-optimize' ); ?>
						<a href="<?php echo esc_url( $renew_url ); ?>" target="_blank"><?php esc_html_e( 'Renew License', 'wp-pdf-embed-seo-optimize' ); ?></a>
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
						<strong><?php esc_html_e( 'PDF Embed & SEO Optimize:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
						<?php
						printf(
							/* translators: %d: Number of grace period days remaining. */
							esc_html__( 'Your license has expired! You have %d days remaining in your grace period before premium features are disabled.', 'wp-pdf-embed-seo-optimize' ),
							$grace_days
						);
						?>
						<a href="<?php echo esc_url( $renew_url ); ?>" target="_blank"><?php esc_html_e( 'Renew Now', 'wp-pdf-embed-seo-optimize' ); ?></a>
					</p>
				</div>
				<?php
				break;

			case 'inactive':
				?>
				<div class="notice notice-info is-dismissible">
					<p>
						<strong><?php esc_html_e( 'PDF Embed & SEO Optimize:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
						<?php esc_html_e( 'Enter your license key to activate premium features.', 'wp-pdf-embed-seo-optimize' ); ?>
						<a href="<?php echo esc_url( $license_url ); ?>"><?php esc_html_e( 'Enter License', 'wp-pdf-embed-seo-optimize' ); ?></a>
					</p>
				</div>
				<?php
				break;

			case 'invalid':
				?>
				<div class="notice notice-error is-dismissible">
					<p>
						<strong><?php esc_html_e( 'PDF Embed & SEO Optimize:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
						<?php esc_html_e( 'Your license key is invalid. Please check your license key or contact support.', 'wp-pdf-embed-seo-optimize' ); ?>
						<a href="<?php echo esc_url( $license_url ); ?>"><?php esc_html_e( 'Update License', 'wp-pdf-embed-seo-optimize' ); ?></a>
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
							<strong><?php esc_html_e( 'PDF Embed & SEO Optimize:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
							<?php
							printf(
								/* translators: %d: Number of days until license expires. */
								esc_html__( 'Your license will expire in %d days. Renew now to maintain premium features.', 'wp-pdf-embed-seo-optimize' ),
								$days_remaining
							);
							?>
							<a href="<?php echo esc_url( $renew_url ); ?>" target="_blank"><?php esc_html_e( 'Renew License', 'wp-pdf-embed-seo-optimize' ); ?></a>
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
function pdf_embed_seo_premium() {
	return PDF_Embed_SEO_Premium::get_instance();
}

// Initialize premium features.
pdf_embed_seo_premium();
