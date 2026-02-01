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

// Archive info.
$archive_url   = get_post_type_archive_link( 'pdf_document' );
$archive_title = apply_filters( 'pdf_embed_seo_archive_title', __( 'PDF Documents', 'pdf-embed-seo-optimize' ) );
$archive_desc  = apply_filters( 'pdf_embed_seo_archive_description', __( 'Browse all available PDF documents.', 'pdf-embed-seo-optimize' ) );
$site_name     = get_bloginfo( 'name' );
$site_url      = home_url( '/' );

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

<div id="primary" class="content-area pdf-embed-seo-optimize-archive">
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

		<header class="page-header pdf-embed-seo-optimize-archive-header">
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
				<?php // List View - Simple clean list with PDF icon and title only. ?>
				<nav class="pdf-embed-seo-optimize-list-nav" aria-label="<?php esc_attr_e( 'PDF Documents List', 'pdf-embed-seo-optimize' ); ?>">
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
				<?php // Grid View - Card layout with thumbnails. ?>
				<section class="pdf-embed-seo-optimize-grid" aria-label="<?php esc_attr_e( 'PDF Documents Gallery', 'pdf-embed-seo-optimize' ); ?>">
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
get_sidebar();
get_footer();
