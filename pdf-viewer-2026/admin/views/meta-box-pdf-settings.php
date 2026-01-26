<?php
/**
 * Meta box view for PDF settings (permissions).
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="pdf-viewer-2026-settings">
	<p>
		<label>
			<input
				type="checkbox"
				name="pdf_allow_download"
				value="1"
				<?php checked( $allow_download, true ); ?>
			>
			<?php esc_html_e( 'Allow Download', 'pdf-viewer-2026' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Allow users to download the PDF file.', 'pdf-viewer-2026' ); ?>
	</p>

	<p>
		<label>
			<input
				type="checkbox"
				name="pdf_allow_print"
				value="1"
				<?php checked( $allow_print, true ); ?>
			>
			<?php esc_html_e( 'Allow Print', 'pdf-viewer-2026' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Allow users to print the PDF document.', 'pdf-viewer-2026' ); ?>
	</p>
</div>
