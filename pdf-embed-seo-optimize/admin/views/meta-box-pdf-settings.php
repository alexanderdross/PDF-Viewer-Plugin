<?php
/**
 * Meta box view for PDF settings (permissions).
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="pdf-embed-seo-optimize-settings">
	<p>
		<label>
			<input
				type="checkbox"
				name="pdf_allow_download"
				value="1"
				<?php checked( $allow_download, true ); ?>
			>
			<?php esc_html_e( 'Allow Download', 'pdf-embed-seo-optimize' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Allow users to download the PDF file.', 'pdf-embed-seo-optimize' ); ?>
	</p>

	<p>
		<label>
			<input
				type="checkbox"
				name="pdf_allow_print"
				value="1"
				<?php checked( $allow_print, true ); ?>
			>
			<?php esc_html_e( 'Allow Print', 'pdf-embed-seo-optimize' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Allow users to print the PDF document.', 'pdf-embed-seo-optimize' ); ?>
	</p>

	<hr style="margin: 15px 0;">

	<p>
		<label>
			<input
				type="checkbox"
				name="pdf_standalone_mode"
				value="1"
				<?php checked( $standalone_mode, true ); ?>
			>
			<?php esc_html_e( 'Standalone Mode', 'pdf-embed-seo-optimize' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Display PDF in a clean fullscreen view without theme header, footer, or breadcrumbs.', 'pdf-embed-seo-optimize' ); ?>
	</p>
</div>
