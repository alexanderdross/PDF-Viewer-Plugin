<?php
/**
 * Template for displaying the PDF documents archive.
 *
 * This template can be overridden by copying it to your theme:
 * yourtheme/archive-pdf_document.php
 *
 * Features:
 * - Supports list and grid display styles (configurable in settings)
 * - SEO optimized with Schema.org breadcrumb markup
 * - Fully accessible with ARIA labels and title attributes
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template file with scoped variables.
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Established public API hooks.

// Get settings.
$settings          = PDF_Embed_SEO::get_setting();
$display_style     = isset( $settings['archive_display_style'] ) ? $settings['archive_display_style'] : 'grid';
$show_description  = isset( $settings['archive_show_description'] ) ? $settings['archive_show_description'] : true;
$show_view_count   = isset( $settings['archive_show_view_count'] ) ? $settings['archive_show_view_count'] : true;
$show_breadcrumbs  = isset( $settings['show_breadcrumbs'] ) ? $settings['show_breadcrumbs'] : true;

// Archive styling settings.
$custom_heading           = isset( $settings['archive_heading'] ) && ! empty( $settings['archive_heading'] ) ? $settings['archive_heading'] : __( 'PDF Documents', 'pdf-embed-seo-optimize' );
$content_alignment        = isset( $settings['archive_heading_alignment'] ) ? $settings['archive_heading_alignment'] : 'center';
$font_color               = isset( $settings['archive_font_color'] ) ? $settings['archive_font_color'] : '';
$background_color         = isset( $settings['archive_background_color'] ) ? $settings['archive_background_color'] : '';
$item_background_color    = isset( $settings['archive_item_background_color'] ) ? $settings['archive_item_background_color'] : '';
$layout_width             = isset( $settings['archive_layout_width'] ) ? $settings['archive_layout_width'] : 'boxed';

// Archive info.
$archive_url   = get_post_type_archive_link( 'pdf_document' );
$archive_title = apply_filters( 'pdf_embed_seo_archive_title', $custom_heading );
$archive_desc  = apply_filters( 'pdf_embed_seo_archive_description', __( 'Browse all available PDF documents.', 'pdf-embed-seo-optimize' ) );
$site_name     = get_bloginfo( 'name' );
$site_url      = home_url( '/' );

// Build header inline styles.
$header_styles = array();
if ( ! empty( $content_alignment ) ) {
	$header_styles[] = 'text-align: ' . esc_attr( $content_alignment );
}
if ( ! empty( $font_color ) ) {
	$header_styles[] = 'color: ' . esc_attr( $font_color );
}
if ( ! empty( $background_color ) ) {
	$header_styles[] = 'background-color: ' . esc_attr( $background_color );
	$header_styles[] = 'padding: 20px';
	$header_styles[] = 'border-radius: 8px';
	$header_styles[] = 'margin-bottom: 20px';
}
$header_style_attr = ! empty( $header_styles ) ? ' style="' . esc_attr( implode( '; ', $header_styles ) ) . '"' : '';

// Build content style for list/grid (alignment, font color, background color).
$content_styles = array();
if ( ! empty( $content_alignment ) ) {
	$content_styles[] = 'text-align: ' . esc_attr( $content_alignment );
}
if ( ! empty( $font_color ) ) {
	$content_styles[] = 'color: ' . esc_attr( $font_color );
}
if ( ! empty( $background_color ) ) {
	$content_styles[] = 'background-color: ' . esc_attr( $background_color );
	$content_styles[] = 'padding: 20px';
	$content_styles[] = 'border-radius: 8px';
}
$content_style_attr = ! empty( $content_styles ) ? ' style="' . esc_attr( implode( '; ', $content_styles ) ) . '"' : '';

// Build item container style (list/grid background).
$item_container_styles = array();
if ( ! empty( $item_background_color ) ) {
	$item_container_styles[] = 'background-color: ' . esc_attr( $item_background_color );
}
$item_container_style_attr = ! empty( $item_container_styles ) ? ' style="' . esc_attr( implode( '; ', $item_container_styles ) ) . '"' : '';

// Archive container classes.
$archive_classes = array( 'content-area', 'pdf-embed-seo-optimize-archive' );
if ( 'full-width' === $layout_width ) {
	$archive_classes[] = 'pdf-embed-seo-optimize-archive-full-width';
}

get_header();
?>

<?php // Breadcrumb Schema.org markup. ?>
<script type="application/ld+json">
{
	"@context": "https://schema.org",
	"@type": "BreadcrumbList",
	"itemListElement": [
		{
			"@type": "ListItem",
			"position": 1,
			"name": <?php echo wp_json_encode( $site_name ); ?>,
			"item": <?php echo wp_json_encode( $site_url ); ?>
		},
		{
			"@type": "ListItem",
			"position": 2,
			"name": <?php echo wp_json_encode( $archive_title ); ?>,
			"item": <?php echo wp_json_encode( $archive_url ); ?>
		}
	]
}
</script>

<div id="primary" class="<?php echo esc_attr( implode( ' ', $archive_classes ) ); ?>">
	<main id="main" class="site-main" role="main">

		<?php // Visible breadcrumb navigation (JSON-LD schema is always output above for SEO). ?>
		<?php if ( $show_breadcrumbs ) : ?>
			<nav class="pdf-embed-seo-optimize-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'pdf-embed-seo-optimize' ); ?>">
				<ol class="pdf-embed-seo-optimize-breadcrumb-list">
					<li class="pdf-embed-seo-optimize-breadcrumb-item">
						<?php /* translators: %s: Site name */ ?>
						<a href="<?php echo esc_url( $site_url ); ?>" title="<?php echo esc_attr( sprintf( __( 'Go to %s homepage', 'pdf-embed-seo-optimize' ), $site_name ) ); ?>">
							<?php echo esc_html( $site_name ); ?>
						</a>
					</li>
					<li class="pdf-embed-seo-optimize-breadcrumb-item pdf-embed-seo-optimize-breadcrumb-current" aria-current="page">
						<?php echo esc_html( $archive_title ); ?>
					</li>
				</ol>
			</nav>
		<?php endif; ?>

		<header class="page-header pdf-embed-seo-optimize-archive-header"<?php echo $header_style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped during construction. ?>>
			<h1 class="page-title pdf-embed-seo-optimize-archive-title">
				<?php echo esc_html( $archive_title ); ?>
			</h1>

			<?php if ( ! empty( $archive_desc ) ) : ?>
				<div class="archive-description pdf-embed-seo-optimize-archive-description">
					<p><?php echo esc_html( $archive_desc ); ?></p>
				</div>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>

			<?php if ( 'list' === $display_style ) : ?>
				<?php
				// List View - Simple clean list with PDF icon and title only.
				// Build list nav styles.
				$list_nav_styles = array();
				if ( ! empty( $content_alignment ) ) {
					$list_nav_styles[] = 'text-align: ' . esc_attr( $content_alignment );
				}
				if ( ! empty( $item_background_color ) ) {
					$list_nav_styles[] = 'background-color: ' . esc_attr( $item_background_color );
				}
				$list_nav_style_attr = ! empty( $list_nav_styles ) ? ' style="' . esc_attr( implode( '; ', $list_nav_styles ) ) . '"' : '';
				?>
				<nav class="pdf-embed-seo-optimize-list-nav"<?php echo $list_nav_style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped during construction. ?> aria-label="<?php esc_attr_e( 'PDF Documents List', 'pdf-embed-seo-optimize' ); ?>">
					<ul class="pdf-embed-seo-optimize-list" role="list">
						<?php
						while ( have_posts() ) :
							the_post();

							$post_id   = get_the_ID();
							$pdf_title = get_the_title();
							$pdf_url   = get_permalink();
							?>

							<li class="pdf-embed-seo-optimize-list-item">
								<?php
								/* translators: %s: PDF document title */
								$view_title_attr = sprintf( __( 'View %s', 'pdf-embed-seo-optimize' ), $pdf_title );
								?>
								<a href="<?php echo esc_url( $pdf_url ); ?>"
								   class="pdf-embed-seo-optimize-list-link"
								   title="<?php echo esc_attr( $view_title_attr ); ?>">
									<span class="pdf-embed-seo-optimize-list-icon" aria-hidden="true">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
											<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13H10v4H8.5v-4zm3 0H13v4h-1.5v-4zm3 0H16v4h-1.5v-4z"/>
										</svg>
									</span>
									<span class="pdf-embed-seo-optimize-list-title"><?php echo esc_html( $pdf_title ); ?></span>
								</a>
							</li>

						<?php endwhile; ?>
					</ul>
				</nav>

			<?php else : ?>
				<?php
				// Grid View - Card layout with thumbnails.
				// Map text-align to justify-content for grid layout.
				$grid_styles = array();
				$grid_justify = 'center';
				if ( 'left' === $content_alignment ) {
					$grid_justify = 'flex-start';
				} elseif ( 'right' === $content_alignment ) {
					$grid_justify = 'flex-end';
				}
				$grid_styles[] = 'justify-content: ' . esc_attr( $grid_justify );
				if ( ! empty( $item_background_color ) ) {
					$grid_styles[] = 'background-color: ' . esc_attr( $item_background_color );
					$grid_styles[] = 'padding: 20px';
					$grid_styles[] = 'border-radius: 8px';
				}
				$grid_style = ! empty( $grid_styles ) ? ' style="' . esc_attr( implode( '; ', $grid_styles ) ) . '"' : '';
				?>
				<section class="pdf-embed-seo-optimize-grid"<?php echo $grid_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped during construction. ?> aria-label="<?php esc_attr_e( 'PDF Documents Gallery', 'pdf-embed-seo-optimize' ); ?>">
					<?php
					while ( have_posts() ) :
						the_post();

						$post_id    = get_the_ID();
						$view_count = PDF_Embed_SEO_Post_Type::get_view_count( $post_id );
						$pdf_title  = get_the_title();
						$pdf_date   = get_the_date();
						$pdf_url    = get_permalink();
						$pdf_desc   = get_the_excerpt();
						?>

						<?php
						/* translators: %s: PDF document title */
						$card_view_title = sprintf( __( 'View %s', 'pdf-embed-seo-optimize' ), $pdf_title );
						/* translators: %s: PDF document title */
						$card_view_aria = sprintf( __( 'View %s PDF document', 'pdf-embed-seo-optimize' ), $pdf_title );
						/* translators: %s: PDF document title */
						$card_thumb_alt = sprintf( __( 'Thumbnail for %s', 'pdf-embed-seo-optimize' ), $pdf_title );
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'pdf-embed-seo-optimize-card' ); ?> itemscope itemtype="https://schema.org/DigitalDocument">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="pdf-embed-seo-optimize-card-thumbnail">
									<a href="<?php echo esc_url( $pdf_url ); ?>"
									   title="<?php echo esc_attr( $card_view_title ); ?>"
									   aria-label="<?php echo esc_attr( $card_view_aria ); ?>">
										<?php
										the_post_thumbnail(
											'medium',
											array(
												'class'    => 'pdf-embed-seo-optimize-thumb',
												'alt'      => $card_thumb_alt,
												'itemprop' => 'thumbnailUrl',
												'loading'  => 'lazy',
											)
										);
										?>
									</a>
								</div>
							<?php else : ?>
								<div class="pdf-embed-seo-optimize-card-thumbnail pdf-embed-seo-optimize-card-thumbnail-placeholder">
									<a href="<?php echo esc_url( $pdf_url ); ?>"
									   title="<?php echo esc_attr( $card_view_title ); ?>"
									   aria-label="<?php echo esc_attr( $card_view_aria ); ?>">
										<span class="dashicons dashicons-pdf" aria-hidden="true"></span>
										<span class="screen-reader-text"><?php esc_html_e( 'PDF Document', 'pdf-embed-seo-optimize' ); ?></span>
									</a>
								</div>
							<?php endif; ?>

							<div class="pdf-embed-seo-optimize-card-content">
								<h2 class="pdf-embed-seo-optimize-card-title">
									<a href="<?php echo esc_url( $pdf_url ); ?>"
									   title="<?php echo esc_attr( $card_view_title ); ?>"
									   itemprop="url">
										<span itemprop="name"><?php echo esc_html( $pdf_title ); ?></span>
									</a>
								</h2>

								<?php if ( $show_description && has_excerpt() ) : ?>
									<div class="pdf-embed-seo-optimize-card-excerpt" itemprop="description">
										<?php the_excerpt(); ?>
									</div>
								<?php endif; ?>

								<div class="pdf-embed-seo-optimize-card-meta">
									<time class="pdf-embed-seo-optimize-card-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" itemprop="datePublished">
										<?php echo esc_html( $pdf_date ); ?>
									</time>

									<?php if ( $show_view_count && $view_count > 0 ) : ?>
										<?php
										/* translators: %s: Number of views */
										$card_views_label = sprintf( _n( '%s view', '%s views', $view_count, 'pdf-embed-seo-optimize' ), number_format_i18n( $view_count ) );
										?>
										<span class="pdf-embed-seo-optimize-card-views" aria-label="<?php echo esc_attr( $card_views_label ); ?>">
											<?php echo esc_html( $card_views_label ); ?>
										</span>
									<?php endif; ?>
								</div>

								<a href="<?php echo esc_url( $pdf_url ); ?>"
								   class="pdf-embed-seo-optimize-card-link"
								   title="<?php echo esc_attr( $card_view_aria ); ?>"
								   aria-label="<?php echo esc_attr( $card_view_aria ); ?>">
									<?php esc_html_e( 'View PDF', 'pdf-embed-seo-optimize' ); ?>
									<span class="screen-reader-text"><?php echo esc_html( $pdf_title ); ?></span>
								</a>
							</div>
						</article>

					<?php endwhile; ?>
				</section>

			<?php endif; ?>

			<?php
			// Pagination with accessibility.
			the_posts_pagination(
				array(
					'mid_size'           => 2,
					'prev_text'          => '<span aria-hidden="true">&laquo;</span> <span class="screen-reader-text">' . __( 'Previous page', 'pdf-embed-seo-optimize' ) . '</span>' . __( 'Previous', 'pdf-embed-seo-optimize' ),
					'next_text'          => __( 'Next', 'pdf-embed-seo-optimize' ) . ' <span class="screen-reader-text">' . __( 'Next page', 'pdf-embed-seo-optimize' ) . '</span><span aria-hidden="true">&raquo;</span>',
					'class'              => 'pdf-embed-seo-optimize-pagination',
					'before_page_number' => '<span class="screen-reader-text">' . __( 'Page', 'pdf-embed-seo-optimize' ) . ' </span>',
					'aria_label'         => __( 'PDF Documents pagination', 'pdf-embed-seo-optimize' ),
				)
			);
			?>

		<?php else : ?>

			<div class="pdf-embed-seo-optimize-no-results" role="status">
				<p><?php esc_html_e( 'No PDF documents have been published yet.', 'pdf-embed-seo-optimize' ); ?></p>
			</div>

		<?php endif; ?>

	</main>
</div>

<?php
// Note: get_sidebar() intentionally removed - PDF archive should be full-width.
get_footer();
