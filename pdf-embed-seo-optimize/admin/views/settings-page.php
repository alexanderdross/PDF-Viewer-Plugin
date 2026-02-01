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

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template file with scoped variables.
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
	// Premium settings section.
	$pdf_has_premium    = defined( 'PDF_EMBED_SEO_PREMIUM_VERSION' );
	$pdf_license_status = get_option( 'pdf_embed_seo_premium_license_status', 'inactive' );
	$pdf_license_valid  = 'valid' === $pdf_license_status;

	if ( $pdf_has_premium && $pdf_license_valid ) :
		// Show active premium settings.
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
	else :
		// Show premium preview for free users or users without valid license.
		?>
		<hr>
		<h2>
			<?php esc_html_e( 'Premium Settings', 'pdf-embed-seo-optimize' ); ?>
			<span class="dashicons dashicons-lock" style="color: #dba617; margin-left: 5px;"></span>
		</h2>

		<?php if ( $pdf_has_premium && ! $pdf_license_valid ) : ?>
			<div class="notice notice-warning" style="padding: 12px; margin-bottom: 20px;">
				<p>
					<strong><?php esc_html_e( 'License Required', 'pdf-embed-seo-optimize' ); ?></strong> -
					<?php
					printf(
						/* translators: %s: license page link */
						esc_html__( 'Please %s to unlock premium settings.', 'pdf-embed-seo-optimize' ),
						'<a href="' . esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-license' ) ) . '">' . esc_html__( 'activate your license', 'pdf-embed-seo-optimize' ) . '</a>'
					);
					?>
				</p>
			</div>
		<?php endif; ?>

		<div style="opacity: 0.6; pointer-events: none;">
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Premium Features', 'pdf-embed-seo-optimize' ); ?></th>
						<td>
							<p class="description"><?php esc_html_e( 'Configure premium features like categories, password protection, analytics, and more.', 'pdf-embed-seo-optimize' ); ?></p>
							<fieldset style="margin-top: 10px;">
								<label><input type="checkbox" disabled checked /> <?php esc_html_e( 'PDF Categories', 'pdf-embed-seo-optimize' ); ?></label><br>
								<label><input type="checkbox" disabled checked /> <?php esc_html_e( 'Password Protection', 'pdf-embed-seo-optimize' ); ?></label><br>
								<label><input type="checkbox" disabled checked /> <?php esc_html_e( 'Advanced Analytics', 'pdf-embed-seo-optimize' ); ?></label><br>
								<label><input type="checkbox" disabled checked /> <?php esc_html_e( 'Reading Progress', 'pdf-embed-seo-optimize' ); ?></label><br>
								<label><input type="checkbox" disabled checked /> <?php esc_html_e( 'PDF Sitemap', 'pdf-embed-seo-optimize' ); ?></label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Archive Page Redirect', 'pdf-embed-seo-optimize' ); ?></th>
						<td>
							<p class="description"><?php esc_html_e( 'Configure automatic redirect from the PDF archive page (/pdf/) to another URL.', 'pdf-embed-seo-optimize' ); ?></p>
							<fieldset style="margin-top: 10px;">
								<label><input type="checkbox" disabled /> <?php esc_html_e( 'Enable Archive Redirect', 'pdf-embed-seo-optimize' ); ?></label>
								<p class="description"><?php esc_html_e( 'Redirect the PDF archive page (/pdf/) to another URL.', 'pdf-embed-seo-optimize' ); ?></p>
							</fieldset>
							<fieldset style="margin-top: 10px;">
								<label><?php esc_html_e( 'Redirect Type', 'pdf-embed-seo-optimize' ); ?></label><br>
								<select disabled style="width: 300px;">
									<option><?php esc_html_e( '301 - Permanent Redirect (recommended for SEO)', 'pdf-embed-seo-optimize' ); ?></option>
								</select>
							</fieldset>
							<fieldset style="margin-top: 10px;">
								<label><?php esc_html_e( 'Redirect URL', 'pdf-embed-seo-optimize' ); ?></label><br>
								<input type="url" disabled value="<?php echo esc_attr( home_url( '/' ) ); ?>" class="regular-text" />
								<p class="description"><?php esc_html_e( 'Enter the URL where visitors should be redirected.', 'pdf-embed-seo-optimize' ); ?></p>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<?php if ( ! $pdf_has_premium ) : ?>
			<div style="text-align: center; padding: 20px; background: #f0f0f1; border-radius: 4px; margin-top: 20px;">
				<p style="font-size: 14px; margin-bottom: 15px;">
					<strong><?php esc_html_e( 'Unlock Premium Features', 'pdf-embed-seo-optimize' ); ?></strong><br>
					<?php esc_html_e( 'Get archive redirect, password protection, analytics, categories, XML sitemap, and more!', 'pdf-embed-seo-optimize' ); ?>
				</p>
				<a href="https://pdfviewer.drossmedia.de" target="_blank" class="button button-primary button-hero">
					<?php esc_html_e( 'Get Premium', 'pdf-embed-seo-optimize' ); ?>
				</a>
			</div>
		<?php endif; ?>
		<?php
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
