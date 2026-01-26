<?php
/**
 * Settings page view.
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'PDF Viewer Settings', 'pdf-viewer-2026' ); ?></h1>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'pdf_viewer_2026_settings_group' );
		do_settings_sections( 'pdf-viewer-2026-settings' );
		submit_button();
		?>
	</form>

	<hr>

	<h2><?php esc_html_e( 'Shortcode Usage', 'pdf-viewer-2026' ); ?></h2>
	<p><?php esc_html_e( 'You can embed PDF viewers anywhere using shortcodes:', 'pdf-viewer-2026' ); ?></p>
	<code>[pdf_viewer id="123"]</code>
	<p class="description"><?php esc_html_e( 'Replace 123 with the ID of your PDF document.', 'pdf-viewer-2026' ); ?></p>

	<hr>

	<h2><?php esc_html_e( 'URL Structure', 'pdf-viewer-2026' ); ?></h2>
	<p><?php esc_html_e( 'Your PDF documents are available at:', 'pdf-viewer-2026' ); ?></p>
	<ul>
		<li>
			<strong><?php esc_html_e( 'Archive:', 'pdf-viewer-2026' ); ?></strong>
			<code><?php echo esc_html( home_url( '/pdf/' ) ); ?></code>
		</li>
		<li>
			<strong><?php esc_html_e( 'Single PDF:', 'pdf-viewer-2026' ); ?></strong>
			<code><?php echo esc_html( home_url( '/pdf/your-pdf-slug/' ) ); ?></code>
		</li>
	</ul>

	<hr>

	<h2><?php esc_html_e( 'Yoast SEO Integration', 'pdf-viewer-2026' ); ?></h2>
	<?php if ( defined( 'WPSEO_VERSION' ) ) : ?>
		<p class="notice notice-success" style="padding: 10px;">
			<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
			<?php esc_html_e( 'Yoast SEO is active! You can configure SEO settings for each PDF document.', 'pdf-viewer-2026' ); ?>
		</p>
	<?php else : ?>
		<p class="notice notice-warning" style="padding: 10px;">
			<span class="dashicons dashicons-warning" style="color: #ffb900;"></span>
			<?php esc_html_e( 'Yoast SEO is not active. Install and activate Yoast SEO to configure meta titles, descriptions, OG tags, and Twitter cards for your PDF documents.', 'pdf-viewer-2026' ); ?>
		</p>
	<?php endif; ?>
</div>
