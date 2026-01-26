<?php
/**
 * Settings page view.
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'PDF Viewer Settings', 'pdf-embed-seo-optimize' ); ?></h1>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'pdf_embed_seo_settings_group' );
		do_settings_sections( 'pdf-embed-seo-optimize-settings' );
		submit_button();
		?>
	</form>

	<hr>

	<h2><?php esc_html_e( 'Shortcode Usage', 'pdf-embed-seo-optimize' ); ?></h2>
	<p><?php esc_html_e( 'You can embed PDF viewers anywhere using shortcodes:', 'pdf-embed-seo-optimize' ); ?></p>
	<code>[pdf_viewer id="123"]</code>
	<p class="description"><?php esc_html_e( 'Replace 123 with the ID of your PDF document.', 'pdf-embed-seo-optimize' ); ?></p>

	<hr>

	<h2><?php esc_html_e( 'URL Structure', 'pdf-embed-seo-optimize' ); ?></h2>
	<p><?php esc_html_e( 'Your PDF documents are available at:', 'pdf-embed-seo-optimize' ); ?></p>
	<ul>
		<li>
			<strong><?php esc_html_e( 'Archive:', 'pdf-embed-seo-optimize' ); ?></strong>
			<code><?php echo esc_html( home_url( '/pdf/' ) ); ?></code>
		</li>
		<li>
			<strong><?php esc_html_e( 'Single PDF:', 'pdf-embed-seo-optimize' ); ?></strong>
			<code><?php echo esc_html( home_url( '/pdf/your-pdf-slug/' ) ); ?></code>
		</li>
	</ul>

	<hr>

	<h2><?php esc_html_e( 'Yoast SEO Integration', 'pdf-embed-seo-optimize' ); ?></h2>
	<?php if ( defined( 'WPSEO_VERSION' ) ) : ?>
		<p class="notice notice-success" style="padding: 10px;">
			<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
			<?php esc_html_e( 'Yoast SEO is active! You can configure SEO settings for each PDF document.', 'pdf-embed-seo-optimize' ); ?>
		</p>
	<?php else : ?>
		<p class="notice notice-warning" style="padding: 10px;">
			<span class="dashicons dashicons-warning" style="color: #ffb900;"></span>
			<?php esc_html_e( 'Yoast SEO is not active. Install and activate Yoast SEO to configure meta titles, descriptions, OG tags, and Twitter cards for your PDF documents.', 'pdf-embed-seo-optimize' ); ?>
		</p>
	<?php endif; ?>
</div>
