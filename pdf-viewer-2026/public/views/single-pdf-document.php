<?php
/**
 * Template for displaying a single PDF document.
 *
 * This template can be overridden by copying it to your theme:
 * yourtheme/single-pdf_document.php
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div id="primary" class="content-area pdf-viewer-2026-single">
	<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			$post_id        = get_the_ID();
			$allow_download = PDF_Viewer_2026_Post_Type::is_download_allowed( $post_id );
			$allow_print    = PDF_Viewer_2026_Post_Type::is_print_allowed( $post_id );
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'pdf-viewer-2026-article' ); ?>>
				<header class="entry-header pdf-viewer-2026-header">
					<?php the_title( '<h1 class="entry-title pdf-viewer-2026-title">', '</h1>' ); ?>

					<?php if ( has_excerpt() ) : ?>
						<div class="pdf-viewer-2026-excerpt">
							<?php the_excerpt(); ?>
						</div>
					<?php endif; ?>
				</header>

				<div class="entry-content pdf-viewer-2026-content">
					<?php
					// Output the PDF viewer.
					echo PDF_Viewer_2026_Frontend::get_viewer_html( $post_id );
					?>

					<?php
					// Output post content (description) if any.
					$content = get_the_content();
					if ( ! empty( $content ) ) :
						?>
						<div class="pdf-viewer-2026-description">
							<?php the_content(); ?>
						</div>
					<?php endif; ?>
				</div>

				<footer class="entry-footer pdf-viewer-2026-footer">
					<?php
					// Show post meta.
					$published_date = get_the_date();
					$modified_date  = get_the_modified_date();
					?>
					<div class="pdf-viewer-2026-meta">
						<span class="pdf-viewer-2026-published">
							<?php
							printf(
								/* translators: %s: Published date */
								esc_html__( 'Published: %s', 'pdf-viewer-2026' ),
								esc_html( $published_date )
							);
							?>
						</span>

						<?php if ( $published_date !== $modified_date ) : ?>
							<span class="pdf-viewer-2026-modified">
								<?php
								printf(
									/* translators: %s: Modified date */
									esc_html__( 'Updated: %s', 'pdf-viewer-2026' ),
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
