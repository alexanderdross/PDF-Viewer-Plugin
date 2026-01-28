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

	<?php
	// Premium settings section (only shown when premium is active with valid license).
	if ( defined( 'PDF_EMBED_SEO_PREMIUM_VERSION' ) ) :
		$license_status = get_option( 'pdf_embed_seo_premium_license_status', 'inactive' );
		if ( 'valid' === $license_status ) :
			?>
			<hr>
			<h2><?php esc_html_e( 'Premium Settings', 'pdf-embed-seo-optimize' ); ?></h2>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'pdf_embed_seo_premium_settings' );
				do_settings_sections( 'pdf-embed-seo-premium' );
				submit_button();
				?>
			</form>
			<?php
		endif;
	endif;
	?>

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
