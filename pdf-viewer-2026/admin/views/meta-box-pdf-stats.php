<?php
/**
 * Meta box view for PDF statistics.
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="pdf-viewer-2026-stats">
	<div class="pdf-viewer-2026-stat-item">
		<span class="pdf-viewer-2026-stat-label"><?php esc_html_e( 'Total Views:', 'pdf-viewer-2026' ); ?></span>
		<span class="pdf-viewer-2026-stat-value"><?php echo esc_html( number_format_i18n( $view_count ) ); ?></span>
	</div>
</div>
