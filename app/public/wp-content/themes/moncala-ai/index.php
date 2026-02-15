<?php
/**
 * MonCala AI Theme - Main Index Template
 *
 * Fallback template for displaying content
 * More specific templates (single.php, page.php, archive.php) will override this
 *
 * @package MonCala_AI
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

	<main id="main" class="site-main">
		<?php
		if ( have_posts() ) {
			?>
			<div class="posts-container">
				<?php
				while ( have_posts() ) {
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header">
							<?php
							if ( 'page' !== get_post_type() ) {
								echo 'Archives:';
								the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h1>' );
							} else {
								the_title( '<h1 class="entry-title">', '</h1>' );
							}

							if ( 'post' === get_post_type() ) {
								?>
								<div class="entry-meta">
									<?php
									printf(
										esc_html__( 'Published on %s', 'moncala-ai' ),
										'<time class="entry-date" datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time>'
									);
									?>
								</div>
								<?php
							}
							?>
						</header>

						<div class="entry-content">
							<?php
							the_excerpt();

							wp_link_pages( array(
								'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'moncala-ai' ),
								'after'  => '</div>',
							) );
							?>
						</div>

						<footer class="entry-footer">
							<?php
							if ( 'post' === get_post_type() ) {
								the_tags( '<span class="tag-links">', ' ', '</span>' );
							}
							?>
						</footer>
					</article>
					<?php
				}

				// Pagination
				the_posts_pagination( array(
					'mid_size'           => 2,
					'prev_text'          => esc_html__( 'Previous', 'moncala-ai' ),
					'next_text'          => esc_html__( 'Next', 'moncala-ai' ),
					'screen_reader_text' => esc_html__( 'Posts navigation', 'moncala-ai' ),
				) );
				?>
			</div>
			<?php
		} else {
			?>
			<div class="no-posts">
				<h2><?php esc_html_e( 'No posts found', 'moncala-ai' ); ?></h2>
				<p><?php esc_html_e( 'It looks like nothing was found at this location.', 'moncala-ai' ); ?></p>
			</div>
			<?php
		}
		?>
	</main>

<?php
get_footer();
