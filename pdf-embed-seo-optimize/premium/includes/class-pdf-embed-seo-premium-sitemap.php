<?php
/**
 * Premium PDF Sitemap
 *
 * Generates a dedicated XML sitemap for PDF documents.
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium sitemap class.
 */
class PDF_Embed_SEO_Premium_Sitemap {

	/**
	 * Sitemap slug.
	 *
	 * @var string
	 */
	const SITEMAP_SLUG = 'pdf-sitemap.xml';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Register sitemap rewrite rule.
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );

		// Handle sitemap request.
		add_action( 'template_redirect', array( $this, 'render_sitemap' ) );

		// Add sitemap to robots.txt.
		add_filter( 'robots_txt', array( $this, 'add_to_robots' ), 10, 2 );

		// Ping search engines on PDF publish.
		add_action( 'publish_pdf_document', array( $this, 'ping_search_engines' ) );

		// WordPress native sitemap integration (WP 5.5+).
		add_filter( 'wp_sitemaps_post_types', array( $this, 'add_to_core_sitemap' ) );

		// Yoast sitemap integration.
		add_filter( 'wpseo_sitemap_index', array( $this, 'add_to_yoast_index' ) );
		add_action( 'init', array( $this, 'register_yoast_sitemap' ) );
	}

	/**
	 * Add rewrite rules for sitemap.
	 *
	 * @return void
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule(
			'^' . self::SITEMAP_SLUG . '$',
			'index.php?pdf_sitemap=1',
			'top'
		);

		add_rewrite_tag( '%pdf_sitemap%', '1' );
	}

	/**
	 * Render sitemap XML.
	 *
	 * @return void
	 */
	public function render_sitemap() {
		if ( ! get_query_var( 'pdf_sitemap' ) ) {
			return;
		}

		$this->output_sitemap();
		exit;
	}

	/**
	 * Output sitemap XML content.
	 *
	 * @return void
	 */
	public function output_sitemap() {
		header( 'Content-Type: application/xml; charset=utf-8' );
		header( 'X-Robots-Tag: noindex, follow' );

		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<?xml-stylesheet type="text/xsl" href="' . esc_url( $this->get_stylesheet_url() ) . '"?>' . "\n";
		?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php
		// Get all published PDF documents.
		$pdfs = get_posts(
			array(
				'post_type'      => 'pdf_document',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'modified',
				'order'          => 'DESC',
			)
		);

		foreach ( $pdfs as $pdf ) :
			$thumbnail_id  = get_post_thumbnail_id( $pdf->ID );
			$thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'full' ) : '';
			?>
	<url>
		<loc><?php echo esc_url( get_permalink( $pdf->ID ) ); ?></loc>
		<lastmod><?php echo esc_html( gmdate( 'c', strtotime( $pdf->post_modified_gmt ) ) ); ?></lastmod>
		<changefreq><?php echo esc_html( $this->get_changefreq( $pdf ) ); ?></changefreq>
		<priority><?php echo esc_html( $this->get_priority( $pdf ) ); ?></priority>
<?php if ( $thumbnail_url ) : ?>
		<image:image>
			<image:loc><?php echo esc_url( $thumbnail_url ); ?></image:loc>
			<image:title><?php echo esc_html( get_the_title( $pdf->ID ) ); ?></image:title>
		</image:image>
<?php endif; ?>
	</url>
<?php
		endforeach;
		?>
</urlset>
		<?php
	}

	/**
	 * Get sitemap stylesheet URL.
	 *
	 * @return string
	 */
	private function get_stylesheet_url() {
		return PDF_EMBED_SEO_PREMIUM_URL . 'assets/sitemap-style.xsl';
	}

	/**
	 * Get change frequency for a PDF.
	 *
	 * @param WP_Post $pdf PDF post object.
	 * @return string
	 */
	private function get_changefreq( $pdf ) {
		$modified_time = strtotime( $pdf->post_modified_gmt );
		$days_ago      = ( time() - $modified_time ) / DAY_IN_SECONDS;

		if ( $days_ago < 7 ) {
			return 'daily';
		} elseif ( $days_ago < 30 ) {
			return 'weekly';
		} elseif ( $days_ago < 365 ) {
			return 'monthly';
		}

		return 'yearly';
	}

	/**
	 * Get priority for a PDF.
	 *
	 * @param WP_Post $pdf PDF post object.
	 * @return string
	 */
	private function get_priority( $pdf ) {
		// Get view count for priority calculation.
		$view_count = get_post_meta( $pdf->ID, '_pdf_view_count', true );
		$view_count = $view_count ? absint( $view_count ) : 0;

		if ( $view_count > 1000 ) {
			return '0.9';
		} elseif ( $view_count > 500 ) {
			return '0.8';
		} elseif ( $view_count > 100 ) {
			return '0.7';
		} elseif ( $view_count > 50 ) {
			return '0.6';
		}

		return '0.5';
	}

	/**
	 * Add sitemap to robots.txt.
	 *
	 * @param string $output Robots.txt content.
	 * @param bool   $public Whether the site is public.
	 * @return string
	 */
	public function add_to_robots( $output, $public ) {
		if ( $public ) {
			$output .= "\n# PDF Sitemap\n";
			$output .= 'Sitemap: ' . home_url( '/' . self::SITEMAP_SLUG ) . "\n";
		}

		return $output;
	}

	/**
	 * Ping search engines when PDF is published.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function ping_search_engines( $post_id ) {
		// Only ping once per hour.
		$last_ping = get_option( 'pdf_sitemap_last_ping', 0 );
		if ( time() - $last_ping < HOUR_IN_SECONDS ) {
			return;
		}

		$sitemap_url = home_url( '/' . self::SITEMAP_SLUG );

		// Ping Google.
		wp_remote_get( 'https://www.google.com/ping?sitemap=' . rawurlencode( $sitemap_url ), array( 'blocking' => false ) );

		// Ping Bing.
		wp_remote_get( 'https://www.bing.com/ping?sitemap=' . rawurlencode( $sitemap_url ), array( 'blocking' => false ) );

		update_option( 'pdf_sitemap_last_ping', time() );
	}

	/**
	 * Add PDF documents to WordPress core sitemap.
	 *
	 * @param array $post_types Post types in sitemap.
	 * @return array
	 */
	public function add_to_core_sitemap( $post_types ) {
		$post_types['pdf_document'] = get_post_type_object( 'pdf_document' );
		return $post_types;
	}

	/**
	 * Add PDF sitemap to Yoast index.
	 *
	 * @param string $sitemap_index Sitemap index content.
	 * @return string
	 */
	public function add_to_yoast_index( $sitemap_index ) {
		$sitemap_url = home_url( '/pdf-sitemap.xml' );
		$date        = gmdate( 'c' );

		$sitemap_index .= "<sitemap>\n";
		$sitemap_index .= "\t<loc>{$sitemap_url}</loc>\n";
		$sitemap_index .= "\t<lastmod>{$date}</lastmod>\n";
		$sitemap_index .= "</sitemap>\n";

		return $sitemap_index;
	}

	/**
	 * Register sitemap with Yoast SEO.
	 *
	 * @return void
	 */
	public function register_yoast_sitemap() {
		if ( ! class_exists( 'WPSEO_Sitemaps' ) ) {
			return;
		}

		add_action(
			'wpseo_register_sitemap_providers',
			function ( $providers ) {
				$providers[] = $this;
				return $providers;
			}
		);
	}

	/**
	 * Get sitemap URL.
	 *
	 * @return string
	 */
	public static function get_sitemap_url() {
		return home_url( '/' . self::SITEMAP_SLUG );
	}

	/**
	 * Get total number of PDFs in sitemap.
	 *
	 * @return int
	 */
	public static function get_pdf_count() {
		$count = wp_count_posts( 'pdf_document' );
		return isset( $count->publish ) ? $count->publish : 0;
	}

	/**
	 * Flush sitemap cache.
	 *
	 * @return void
	 */
	public static function flush_cache() {
		// Clear any object cache.
		wp_cache_delete( 'pdf_sitemap', 'pdf_embed_seo' );

		// Clear Yoast cache if available.
		if ( class_exists( 'WPSEO_Sitemaps_Cache' ) ) {
			WPSEO_Sitemaps_Cache::clear( array( 'pdf_document' ) );
		}
	}
}
