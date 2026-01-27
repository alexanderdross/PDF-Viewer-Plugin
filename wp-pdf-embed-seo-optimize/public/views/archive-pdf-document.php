<?php
/**
 * Template for displaying the PDF documents archive.
 *
 * This template can be overridden by copying it to your theme:
 * yourtheme/archive-pdf_document.php
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div id="primary" class="content-area pdf-embed-seo-optimize-archive">
	<main id="main" class="site-main">

		<header class="page-header pdf-embed-seo-optimize-archive-header">
			<h1 class="page-title pdf-embed-seo-optimize-archive-title">
				<?php
				/**
				 * Filter the archive page title.
				 *
				 * @param string $title The archive page title.
				 */
				echo esc_html( apply_filters( 'pdf_embed_seo_archive_title', __( 'PDF Documents', 'wp-pdf-embed-seo-optimize' ) ) );
				?>
			</h1>

			<?php
			/**
			 * Filter the archive page description.
			 *
			 * @param string $description The archive page description.
			 */
			$archive_description = apply_filters( 'pdf_embed_seo_archive_description', __( 'Browse all available PDF documents.', 'wp-pdf-embed-seo-optimize' ) );

			if ( ! empty( $archive_description ) ) :
				?>
				<div class="archive-description pdf-embed-seo-optimize-archive-description">
					<p><?php echo esc_html( $archive_description ); ?></p>
				</div>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>

			<div class="pdf-embed-seo-optimize-grid">
				<?php
				while ( have_posts() ) :
					the_post();

					$post_id    = get_the_ID();
					$view_count = PDF_Embed_SEO_Post_Type::get_view_count( $post_id );
					?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'pdf-embed-seo-optimize-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="pdf-embed-seo-optimize-card-thumbnail">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium', array( 'class' => 'pdf-embed-seo-optimize-thumb' ) ); ?>
								</a>
							</div>
						<?php else : ?>
							<div class="pdf-embed-seo-optimize-card-thumbnail pdf-embed-seo-optimize-card-thumbnail-placeholder">
								<a href="<?php the_permalink(); ?>">
									<span class="dashicons dashicons-pdf"></span>
								</a>
							</div>
						<?php endif; ?>

						<div class="pdf-embed-seo-optimize-card-content">
							<h2 class="pdf-embed-seo-optimize-card-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<?php if ( has_excerpt() ) : ?>
								<div class="pdf-embed-seo-optimize-card-excerpt">
									<?php the_excerpt(); ?>
								</div>
							<?php endif; ?>

							<div class="pdf-embed-seo-optimize-card-meta">
								<span class="pdf-embed-seo-optimize-card-date">
									<?php echo esc_html( get_the_date() ); ?>
								</span>

								<?php if ( $view_count > 0 ) : ?>
									<span class="pdf-embed-seo-optimize-card-views">
										<?php
										printf(
											/* translators: %s: View count */
											esc_html( _n( '%s view', '%s views', $view_count, 'wp-pdf-embed-seo-optimize' ) ),
											esc_html( number_format_i18n( $view_count ) )
										);
										?>
									</span>
								<?php endif; ?>
							</div>

							<a href="<?php the_permalink(); ?>" class="pdf-embed-seo-optimize-card-link">
								<?php esc_html_e( 'View PDF', 'wp-pdf-embed-seo-optimize' ); ?>
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
					'prev_text' => __( '&laquo; Previous', 'wp-pdf-embed-seo-optimize' ),
					'next_text' => __( 'Next &raquo;', 'wp-pdf-embed-seo-optimize' ),
					'class'     => 'pdf-embed-seo-optimize-pagination',
				)
			);
			?>

		<?php else : ?>

			<div class="pdf-embed-seo-optimize-no-results">
				<p><?php esc_html_e( 'No PDF documents have been published yet.', 'wp-pdf-embed-seo-optimize' ); ?></p>
			</div>

		<?php endif; ?>

	</main>
</div>

<?php
get_sidebar();
get_footer();
