<?php
/**
 * Meta box view for PDF statistics.
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="pdf-embed-seo-optimize-stats">
	<div class="pdf-embed-seo-optimize-stat-item">
		<span class="pdf-embed-seo-optimize-stat-label"><?php esc_html_e( 'Total Views:', 'wp-pdf-embed-seo-optimize' ); ?></span>
		<span class="pdf-embed-seo-optimize-stat-value"><?php echo esc_html( number_format_i18n( $view_count ) ); ?></span>
	</div>
</div>
