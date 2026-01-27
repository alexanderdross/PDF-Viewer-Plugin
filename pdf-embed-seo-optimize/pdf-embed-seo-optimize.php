<?php
/**
 * Plugin Name:       PDF Embed & SEO Optimize
 * Plugin URI:        https://github.com/alexanderdross/PDF-Viewer-2026
 * Description:       A powerful WordPress plugin that integrates Mozilla's PDF.js viewer to serve PDFs through a viewer URL, enhancing SEO with Schema Data, Open Graph Tags, Twitter Cards, and other Meta Tags. Control print/download permissions per PDF. Full Yoast SEO integration for title, slug, and meta description control.
 * Version:           1.2.1
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Dross:Media
 * Author URI:        https://github.com/alexanderdross
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pdf-embed-seo-optimize
 * Domain Path:       /languages
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version.
 */
define( 'PDF_EMBED_SEO_VERSION', '1.2.1' );

/**
 * Plugin directory path.
 */
define( 'PDF_EMBED_SEO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'PDF_EMBED_SEO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'PDF_EMBED_SEO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Minimum required WordPress version.
 */
define( 'PDF_EMBED_SEO_MIN_WP_VERSION', '5.8' );

/**
 * Minimum required PHP version.
 */
define( 'PDF_EMBED_SEO_MIN_PHP_VERSION', '7.4' );

/**
 * Main plugin class.
 */
final class PDF_Embed_SEO {

	/**
	 * Single instance of the class.
	 *
	 * @var PDF_Embed_SEO|null
	 */
	private static $instance = null;

	/**
	 * Post type instance.
	 *
	 * @var PDF_Embed_SEO_Post_Type|null
	 */
	public $post_type = null;

	/**
	 * Admin instance.
	 *
	 * @var PDF_Embed_SEO_Admin|null
	 */
	public $admin = null;

	/**
	 * Frontend instance.
	 *
	 * @var PDF_Embed_SEO_Frontend|null
	 */
	public $frontend = null;

	/**
	 * Yoast integration instance.
	 *
	 * @var PDF_Embed_SEO_Yoast|null
	 */
	public $yoast = null;

	/**
	 * Shortcodes instance.
	 *
	 * @var PDF_Embed_SEO_Shortcodes|null
	 */
	public $shortcodes = null;

	/**
	 * Block instance.
	 *
	 * @var PDF_Embed_SEO_Block|null
	 */
	public $block = null;

	/**
	 * Thumbnail instance.
	 *
	 * @var PDF_Embed_SEO_Thumbnail|null
	 */
	public $thumbnail = null;

	/**
	 * REST API instance.
	 *
	 * @var PDF_Embed_SEO_REST_API|null
	 */
	public $rest_api = null;

	/**
	 * Get the single instance of the class.
	 *
	 * @return PDF_Embed_SEO
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
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include required files.
	 *
	 * @return void
	 */
	private function includes() {
		// Core classes.
		require_once PDF_EMBED_SEO_PLUGIN_DIR . 'includes/class-pdf-embed-seo-optimize-post-type.php';
		require_once PDF_EMBED_SEO_PLUGIN_DIR . 'includes/class-pdf-embed-seo-optimize-frontend.php';
		require_once PDF_EMBED_SEO_PLUGIN_DIR . 'includes/class-pdf-embed-seo-optimize-yoast.php';
		require_once PDF_EMBED_SEO_PLUGIN_DIR . 'includes/class-pdf-embed-seo-optimize-shortcodes.php';
		require_once PDF_EMBED_SEO_PLUGIN_DIR . 'includes/class-pdf-embed-seo-optimize-block.php';
		require_once PDF_EMBED_SEO_PLUGIN_DIR . 'includes/class-pdf-embed-seo-optimize-thumbnail.php';
		require_once PDF_EMBED_SEO_PLUGIN_DIR . 'includes/class-pdf-embed-seo-optimize-rest-api.php';

		// Admin classes (only in admin context).
		if ( is_admin() ) {
			require_once PDF_EMBED_SEO_PLUGIN_DIR . 'includes/class-pdf-embed-seo-optimize-admin.php';
		}
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		// Initialize components.
		add_action( 'init', array( $this, 'init' ), 0 );

		// Activation hook.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		// Deactivation hook.
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Initialize plugin components.
	 *
	 * @return void
	 */
	public function init() {
		// Initialize post type.
		$this->post_type = new PDF_Embed_SEO_Post_Type();

		// Initialize frontend.
		$this->frontend = new PDF_Embed_SEO_Frontend();

		// Initialize Yoast integration.
		$this->yoast = new PDF_Embed_SEO_Yoast();

		// Initialize shortcodes.
		$this->shortcodes = new PDF_Embed_SEO_Shortcodes();

		// Initialize Gutenberg block.
		$this->block = new PDF_Embed_SEO_Block();

		// Initialize thumbnail generator.
		$this->thumbnail = new PDF_Embed_SEO_Thumbnail();

		// Initialize REST API.
		$this->rest_api = new PDF_Embed_SEO_REST_API();

		// Initialize admin (only in admin context).
		if ( is_admin() ) {
			$this->admin = new PDF_Embed_SEO_Admin();
		}
	}

	/**
	 * Plugin activation.
	 *
	 * @return void
	 */
	public function activate() {
		// Check WordPress version.
		if ( version_compare( get_bloginfo( 'version' ), PDF_EMBED_SEO_MIN_WP_VERSION, '<' ) ) {
			deactivate_plugins( PDF_EMBED_SEO_PLUGIN_BASENAME );
			wp_die(
				sprintf(
					/* translators: %s: Minimum WordPress version required. */
					esc_html__( 'PDF Viewer 2026 requires WordPress %s or higher.', 'pdf-embed-seo-optimize' ),
					esc_html( PDF_EMBED_SEO_MIN_WP_VERSION )
				)
			);
		}

		// Check PHP version.
		if ( version_compare( PHP_VERSION, PDF_EMBED_SEO_MIN_PHP_VERSION, '<' ) ) {
			deactivate_plugins( PDF_EMBED_SEO_PLUGIN_BASENAME );
			wp_die(
				sprintf(
					/* translators: %s: Minimum PHP version required. */
					esc_html__( 'PDF Viewer 2026 requires PHP %s or higher.', 'pdf-embed-seo-optimize' ),
					esc_html( PDF_EMBED_SEO_MIN_PHP_VERSION )
				)
			);
		}

		// Register post type to flush rewrite rules.
		$post_type = new PDF_Embed_SEO_Post_Type();
		$post_type->register_post_type();

		// Set default options.
		$default_options = array(
			'default_allow_download'   => false,
			'default_allow_print'      => false,
			'auto_generate_thumbnails' => true,
			'archive_posts_per_page'   => 12,
			'viewer_theme'             => 'light',
		);

		if ( false === get_option( 'pdf_embed_seo_settings' ) ) {
			add_option( 'pdf_embed_seo_settings', $default_options );
		}

		// Store version for upgrade routines.
		update_option( 'pdf_embed_seo_version', PDF_EMBED_SEO_VERSION );

		// Flush rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation.
	 *
	 * @return void
	 */
	public function deactivate() {
		// Flush rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Get plugin settings.
	 *
	 * @param string $key     Optional. Specific setting key to retrieve.
	 * @param mixed  $default Optional. Default value if setting doesn't exist.
	 * @return mixed
	 */
	public static function get_setting( $key = '', $default = null ) {
		$settings = get_option( 'pdf_embed_seo_settings', array() );

		if ( empty( $key ) ) {
			return $settings;
		}

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Update plugin settings.
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value Setting value.
	 * @return bool
	 */
	public static function update_setting( $key, $value ) {
		$settings         = get_option( 'pdf_embed_seo_settings', array() );
		$settings[ $key ] = $value;
		return update_option( 'pdf_embed_seo_settings', $settings );
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
 * Returns the main instance of the plugin.
 *
 * @return PDF_Embed_SEO
 */
function pdf_embed_seo() {
	return PDF_Embed_SEO::get_instance();
}

// Initialize the plugin.
pdf_embed_seo();

/**
 * Load premium features if available.
 *
 * Premium features are loaded from the /premium/ directory.
 * This allows the premium version to extend the free version
 * with additional functionality.
 */
function pdf_embed_seo_load_premium() {
	$premium_file = PDF_EMBED_SEO_PLUGIN_DIR . 'premium/class-pdf-embed-seo-premium.php';

	if ( file_exists( $premium_file ) ) {
		require_once $premium_file;
	}
}
add_action( 'plugins_loaded', 'pdf_embed_seo_load_premium', 15 );

/**
 * Check if premium features are active.
 *
 * @return bool
 */
function pdf_embed_seo_is_premium() {
	return defined( 'PDF_EMBED_SEO_IS_PREMIUM' ) && PDF_EMBED_SEO_IS_PREMIUM;
}
