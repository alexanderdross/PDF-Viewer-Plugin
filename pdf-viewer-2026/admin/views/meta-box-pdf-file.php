<?php
/**
 * Meta box view for PDF file upload.
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$file_name = '';
if ( $file_id ) {
	$file_path = get_attached_file( $file_id );
	if ( $file_path ) {
		$file_name = basename( $file_path );
	}
}
?>

<div class="pdf-viewer-2026-file-upload">
	<div class="pdf-viewer-2026-file-preview" <?php echo $file_id ? '' : 'style="display:none;"'; ?>>
		<div class="pdf-viewer-2026-file-info">
			<span class="dashicons dashicons-pdf"></span>
			<span class="pdf-viewer-2026-file-name"><?php echo esc_html( $file_name ); ?></span>
		</div>
		<?php if ( $file_url ) : ?>
			<a href="<?php echo esc_url( $file_url ); ?>" target="_blank" class="button button-small">
				<?php esc_html_e( 'View PDF', 'pdf-viewer-2026' ); ?>
			</a>
		<?php endif; ?>
	</div>

	<div class="pdf-viewer-2026-file-actions">
		<button type="button" class="button button-primary pdf-viewer-2026-upload-btn" <?php echo $file_id ? 'style="display:none;"' : ''; ?>>
			<span class="dashicons dashicons-upload"></span>
			<?php esc_html_e( 'Select PDF File', 'pdf-viewer-2026' ); ?>
		</button>

		<button type="button" class="button pdf-viewer-2026-change-btn" <?php echo $file_id ? '' : 'style="display:none;"'; ?>>
			<?php esc_html_e( 'Change PDF', 'pdf-viewer-2026' ); ?>
		</button>

		<button type="button" class="button button-link-delete pdf-viewer-2026-remove-btn" <?php echo $file_id ? '' : 'style="display:none;"'; ?>>
			<?php esc_html_e( 'Remove PDF', 'pdf-viewer-2026' ); ?>
		</button>
	</div>

	<input type="hidden" name="pdf_file_id" id="pdf_file_id" value="<?php echo esc_attr( $file_id ); ?>">

	<p class="description">
		<?php esc_html_e( 'Select a PDF file from your Media Library or upload a new one.', 'pdf-viewer-2026' ); ?>
	</p>
</div>
