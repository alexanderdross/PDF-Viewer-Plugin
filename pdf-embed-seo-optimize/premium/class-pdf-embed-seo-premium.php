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
	const VERSION = '1.1.0';

	/**
	 * License status.
	 *
	 * @var string
	 */
	private $license_status = 'valid';

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
	 * @return bool
	 */
	public function is_license_valid() {
		// For now, always return true (license system to be implemented).
		// In production, this would check against a license server.
		$this->license_status = get_option( 'pdf_embed_seo_premium_license_status', 'valid' );
		return 'valid' === $this->license_status;
	}

	/**
	 * Display license notices.
	 *
	 * @return void
	 */
	public function license_notices() {
		if ( 'valid' !== $this->license_status ) {
			?>
			<div class="notice notice-warning is-dismissible">
				<p>
					<?php
					printf(
						/* translators: %s: Settings page URL. */
						esc_html__( 'PDF Embed & SEO Optimize Pro: Please enter a valid license key to enable premium features. %s', 'pdf-embed-seo-optimize' ),
						'<a href="' . esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-embed-seo-settings' ) ) . '">' . esc_html__( 'Enter License', 'pdf-embed-seo-optimize' ) . '</a>'
					);
					?>
				</p>
			</div>
			<?php
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
