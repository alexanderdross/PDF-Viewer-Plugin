<?php
/**
 * Template for displaying a single PDF document.
 *
 * This template can be overridden by copying it to your theme:
 * yourtheme/single-pdf_document.php
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div id="primary" class="content-area pdf-embed-seo-optimize-single">
	<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			$post_id        = get_the_ID();
			$allow_download = PDF_Embed_SEO_Post_Type::is_download_allowed( $post_id );
			$allow_print    = PDF_Embed_SEO_Post_Type::is_print_allowed( $post_id );
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'pdf-embed-seo-optimize-article' ); ?>>
				<header class="entry-header pdf-embed-seo-optimize-header">
					<?php the_title( '<h1 class="entry-title pdf-embed-seo-optimize-title">', '</h1>' ); ?>

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
							<?php
							printf(
								/* translators: %s: Published date */
								esc_html__( 'Published: %s', 'wp-pdf-embed-seo-optimize' ),
								esc_html( $published_date )
							);
							?>
						</span>

						<?php if ( $published_date !== $modified_date ) : ?>
							<span class="pdf-embed-seo-optimize-modified">
								<?php
								printf(
									/* translators: %s: Modified date */
									esc_html__( 'Updated: %s', 'wp-pdf-embed-seo-optimize' ),
									esc_html( $modified_date )
								);
								?>
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
