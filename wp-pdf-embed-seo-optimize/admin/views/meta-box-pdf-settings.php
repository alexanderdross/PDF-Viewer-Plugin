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
			<?php esc_html_e( 'Allow Download', 'wp-pdf-embed-seo-optimize' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Allow users to download the PDF file.', 'wp-pdf-embed-seo-optimize' ); ?>
	</p>

	<p>
		<label>
			<input
				type="checkbox"
				name="pdf_allow_print"
				value="1"
				<?php checked( $allow_print, true ); ?>
			>
			<?php esc_html_e( 'Allow Print', 'wp-pdf-embed-seo-optimize' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Allow users to print the PDF document.', 'wp-pdf-embed-seo-optimize' ); ?>
	</p>
</div>
