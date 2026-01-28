<?php
/**
 * Meta box view for PDF file upload.
 *
 * @package PDF_Embed_SEO
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

<div class="pdf-embed-seo-optimize-file-upload">
	<div class="pdf-embed-seo-optimize-file-preview" <?php echo $file_id ? '' : 'style="display:none;"'; ?>>
		<div class="pdf-embed-seo-optimize-file-info">
			<span class="dashicons dashicons-pdf"></span>
			<span class="pdf-embed-seo-optimize-file-name"><?php echo esc_html( $file_name ); ?></span>
		</div>
		<?php if ( $file_url ) : ?>
			<a href="<?php echo esc_url( $file_url ); ?>" target="_blank" class="button button-small">
				<?php esc_html_e( 'View PDF', 'wp-pdf-embed-seo-optimize' ); ?>
			</a>
		<?php endif; ?>
	</div>

	<div class="pdf-embed-seo-optimize-file-actions">
		<button type="button" class="button button-primary pdf-embed-seo-optimize-upload-btn" <?php echo $file_id ? 'style="display:none;"' : ''; ?>>
			<span class="dashicons dashicons-upload"></span>
			<?php esc_html_e( 'Select PDF File', 'wp-pdf-embed-seo-optimize' ); ?>
		</button>

		<button type="button" class="button pdf-embed-seo-optimize-change-btn" <?php echo $file_id ? '' : 'style="display:none;"'; ?>>
			<?php esc_html_e( 'Change PDF', 'wp-pdf-embed-seo-optimize' ); ?>
		</button>

		<button type="button" class="button button-link-delete pdf-embed-seo-optimize-remove-btn" <?php echo $file_id ? '' : 'style="display:none;"'; ?>>
			<?php esc_html_e( 'Remove PDF', 'wp-pdf-embed-seo-optimize' ); ?>
		</button>
	</div>

	<input type="hidden" name="pdf_file_id" id="pdf_file_id" value="<?php echo esc_attr( $file_id ); ?>">

	<p class="description">
		<?php esc_html_e( 'Select a PDF file from your Media Library or upload a new one.', 'wp-pdf-embed-seo-optimize' ); ?>
	</p>
	<p class="description" style="margin-top: 10px; padding: 8px; background: #f0f6fc; border-left: 3px solid #2271b1;">
		<strong><?php esc_html_e( 'How it works:', 'wp-pdf-embed-seo-optimize' ); ?></strong><br>
		<?php esc_html_e( 'The PDF viewer is displayed automatically on this document\'s page. Use the content editor above only for additional text or descriptions you want to show below the PDF viewer.', 'wp-pdf-embed-seo-optimize' ); ?>
	</p>
</div>
