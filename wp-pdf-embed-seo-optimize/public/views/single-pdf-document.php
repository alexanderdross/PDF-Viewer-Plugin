<?php
/**
 * Template for displaying a single PDF document.
 *
 * This template can be overridden by copying it to your theme:
 * yourtheme/single-pdf_document.php
 *
 * Features:
 * - SEO optimized with Schema.org breadcrumb markup
 * - Fully accessible with ARIA labels and title attributes
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get document info.
$post_id     = get_the_ID();
$pdf_title   = get_the_title( $post_id );
$pdf_url     = get_permalink( $post_id );
$archive_url = get_post_type_archive_link( 'pdf_document' );
$site_name   = get_bloginfo( 'name' );
$site_url    = home_url( '/' );

// Get settings.
$settings         = PDF_Embed_SEO::get_setting();
$show_breadcrumbs = isset( $settings['show_breadcrumbs'] ) ? $settings['show_breadcrumbs'] : true;

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
			"name": <?php echo wp_json_encode( __( 'PDF Documents', 'wp-pdf-embed-seo-optimize' ) ); ?>,
			"item": <?php echo wp_json_encode( $archive_url ); ?>
		},
		{
			"@type": "ListItem",
			"position": 3,
			"name": <?php echo wp_json_encode( $pdf_title ); ?>,
			"item": <?php echo wp_json_encode( $pdf_url ); ?>
		}
	]
}
</script>

<div id="primary" class="content-area pdf-embed-seo-optimize-single">
	<main id="main" class="site-main" role="main">

		<?php // Visible breadcrumb navigation (JSON-LD schema is always output above for SEO). ?>
		<?php if ( $show_breadcrumbs ) : ?>
			<nav class="pdf-embed-seo-optimize-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'wp-pdf-embed-seo-optimize' ); ?>">
				<ol class="pdf-embed-seo-optimize-breadcrumb-list">
					<li class="pdf-embed-seo-optimize-breadcrumb-item">
						<?php /* translators: %s: Site name */ ?>
						<a href="<?php echo esc_url( $site_url ); ?>" title="<?php echo esc_attr( sprintf( __( 'Go to %s homepage', 'wp-pdf-embed-seo-optimize' ), $site_name ) ); ?>">
							<?php echo esc_html( $site_name ); ?>
						</a>
					</li>
					<li class="pdf-embed-seo-optimize-breadcrumb-item">
						<a href="<?php echo esc_url( $archive_url ); ?>" title="<?php esc_attr_e( 'View all PDF documents', 'wp-pdf-embed-seo-optimize' ); ?>">
							<?php esc_html_e( 'PDF Documents', 'wp-pdf-embed-seo-optimize' ); ?>
						</a>
					</li>
					<li class="pdf-embed-seo-optimize-breadcrumb-item pdf-embed-seo-optimize-breadcrumb-current" aria-current="page">
						<?php echo esc_html( $pdf_title ); ?>
					</li>
				</ol>
			</nav>
		<?php endif; ?>

		<?php
		while ( have_posts() ) :
			the_post();

			$allow_download = PDF_Embed_SEO_Post_Type::is_download_allowed( $post_id );
			$allow_print    = PDF_Embed_SEO_Post_Type::is_print_allowed( $post_id );
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'pdf-embed-seo-optimize-article' ); ?>>
				<header class="entry-header pdf-embed-seo-optimize-header">
					<h1 class="entry-title pdf-embed-seo-optimize-title">
						<?php echo esc_html( $pdf_title ); ?>
					</h1>

					<?php if ( has_excerpt() ) : ?>
						<div class="pdf-embed-seo-optimize-excerpt">
							<?php the_excerpt(); ?>
						</div>
					<?php endif; ?>
				</header>

				<div class="entry-content pdf-embed-seo-optimize-content">
					<?php
					// Output the PDF viewer.
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Viewer HTML is safely constructed with escaped values.
					echo PDF_Embed_SEO_Frontend::get_viewer_html( $post_id );
					?>

					<?php
					// Output post content (description) if any.
					$content = get_the_content();
					if ( ! empty( $content ) ) :
						?>
						<div class="pdf-embed-seo-optimize-description">
							<?php the_content(); ?>
						</div>
					<?php endif; ?>
				</div>

				<footer class="entry-footer pdf-embed-seo-optimize-footer">
					<?php
					// Show post meta.
					$published_date = get_the_date();
					$modified_date  = get_the_modified_date();
					?>
					<div class="pdf-embed-seo-optimize-meta">
						<span class="pdf-embed-seo-optimize-published">
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<?php
								printf(
									/* translators: %s: Published date */
									esc_html__( 'Published: %s', 'wp-pdf-embed-seo-optimize' ),
									esc_html( $published_date )
								);
								?>
							</time>
						</span>

						<?php if ( $published_date !== $modified_date ) : ?>
							<span class="pdf-embed-seo-optimize-modified">
								<time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>">
									<?php
									printf(
										/* translators: %s: Modified date */
										esc_html__( 'Updated: %s', 'wp-pdf-embed-seo-optimize' ),
										esc_html( $modified_date )
									);
									?>
								</time>
							</span>
						<?php endif; ?>
					</div>
				</footer>

			</article>

			<?php
		endwhile;
		?>

	</main>
</div>

<?php
get_sidebar();
get_footer();
