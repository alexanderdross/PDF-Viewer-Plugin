<?php
/**
 * Template for displaying the PDF documents archive.
 *
 * This template can be overridden by copying it to your theme:
 * yourtheme/archive-pdf_document.php
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div id="primary" class="content-area pdf-viewer-2026-archive">
	<main id="main" class="site-main">

		<header class="page-header pdf-viewer-2026-archive-header">
			<h1 class="page-title pdf-viewer-2026-archive-title">
				<?php
				/**
				 * Filter the archive page title.
				 *
				 * @param string $title The archive page title.
				 */
				echo esc_html( apply_filters( 'pdf_viewer_2026_archive_title', __( 'PDF Documents', 'pdf-viewer-2026' ) ) );
				?>
			</h1>

			<?php
			/**
			 * Filter the archive page description.
			 *
			 * @param string $description The archive page description.
			 */
			$archive_description = apply_filters( 'pdf_viewer_2026_archive_description', __( 'Browse all available PDF documents.', 'pdf-viewer-2026' ) );

			if ( ! empty( $archive_description ) ) :
				?>
				<div class="archive-description pdf-viewer-2026-archive-description">
					<p><?php echo esc_html( $archive_description ); ?></p>
				</div>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>

			<div class="pdf-viewer-2026-grid">
				<?php
				while ( have_posts() ) :
					the_post();

					$post_id    = get_the_ID();
					$view_count = PDF_Viewer_2026_Post_Type::get_view_count( $post_id );
					?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'pdf-viewer-2026-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="pdf-viewer-2026-card-thumbnail">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium', array( 'class' => 'pdf-viewer-2026-thumb' ) ); ?>
								</a>
							</div>
						<?php else : ?>
							<div class="pdf-viewer-2026-card-thumbnail pdf-viewer-2026-card-thumbnail-placeholder">
								<a href="<?php the_permalink(); ?>">
									<span class="dashicons dashicons-pdf"></span>
								</a>
							</div>
						<?php endif; ?>

						<div class="pdf-viewer-2026-card-content">
							<h2 class="pdf-viewer-2026-card-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<?php if ( has_excerpt() ) : ?>
								<div class="pdf-viewer-2026-card-excerpt">
									<?php the_excerpt(); ?>
								</div>
							<?php endif; ?>

							<div class="pdf-viewer-2026-card-meta">
								<span class="pdf-viewer-2026-card-date">
									<?php echo esc_html( get_the_date() ); ?>
								</span>

								<?php if ( $view_count > 0 ) : ?>
									<span class="pdf-viewer-2026-card-views">
										<?php
										printf(
											/* translators: %s: View count */
											esc_html( _n( '%s view', '%s views', $view_count, 'pdf-viewer-2026' ) ),
											esc_html( number_format_i18n( $view_count ) )
										);
										?>
									</span>
								<?php endif; ?>
							</div>

							<a href="<?php the_permalink(); ?>" class="pdf-viewer-2026-card-link">
								<?php esc_html_e( 'View PDF', 'pdf-viewer-2026' ); ?>
								<span class="screen-reader-text"><?php the_title(); ?></span>
							</a>
						</div>
					</article>

				<?php endwhile; ?>
			</div>

			<?php
			// Pagination.
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => __( '&laquo; Previous', 'pdf-viewer-2026' ),
					'next_text' => __( 'Next &raquo;', 'pdf-viewer-2026' ),
					'class'     => 'pdf-viewer-2026-pagination',
				)
			);
			?>

		<?php else : ?>

			<div class="pdf-viewer-2026-no-results">
				<p><?php esc_html_e( 'No PDF documents have been published yet.', 'pdf-viewer-2026' ); ?></p>
			</div>

		<?php endif; ?>

	</main>
</div>

<?php
get_sidebar();
get_footer();
