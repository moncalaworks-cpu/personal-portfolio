<?php
/**
 * MonCala AI Theme - Archive Template
 *
 * Displays post archives by category, tag, date, or author
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
	<header class="archive-header">
		<div class="container">
			<?php
			if ( is_category() ) {
				?>
				<h1 class="archive-title">
					<?php single_cat_title(); ?>
				</h1>
				<p class="archive-description text-muted">
					<?php echo esc_html( category_description() ); ?>
				</p>
				<?php
			} elseif ( is_tag() ) {
				?>
				<h1 class="archive-title">
					<?php esc_html_e( 'Tag: ', 'moncala-ai' ); ?><?php single_tag_title(); ?>
				</h1>
				<?php
			} elseif ( is_author() ) {
				?>
				<h1 class="archive-title">
					<?php esc_html_e( 'Author: ', 'moncala-ai' ); ?><?php the_author_meta( 'display_name' ); ?>
				</h1>
				<?php
			} elseif ( is_date() ) {
				?>
				<h1 class="archive-title">
					<?php
					if ( is_year() ) {
						the_time( 'Y' );
					} elseif ( is_month() ) {
						the_time( 'F Y' );
					} elseif ( is_day() ) {
						the_time( 'F j, Y' );
					}
					?>
				</h1>
				<?php
			} elseif ( is_post_type_archive() ) {
				?>
				<h1 class="archive-title">
					<?php post_type_archive_title(); ?>
				</h1>
				<?php
			} else {
				?>
				<h1 class="archive-title">
					<?php esc_html_e( 'Blog', 'moncala-ai' ); ?>
				</h1>
				<?php
			}
			?>
		</div>
	</header>

	<div class="container">
		<?php
		if ( have_posts() ) {
			?>
			<div class="posts-grid grid grid-2">
				<?php
				while ( have_posts() ) {
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card card' ); ?>>
						<?php
						if ( has_post_thumbnail() ) {
							?>
							<figure class="post-card-figure">
								<?php
								the_post_thumbnail(
									'moncala-featured',
									array(
										'class' => 'post-card-image',
										'alt'   => sprintf(
											esc_attr__( 'Featured image for %s', 'moncala-ai' ),
											esc_attr( get_the_title() )
										),
									)
								);
								?>
							</figure>
							<?php
						}
						?>

						<header class="post-card-header">
							<div class="post-card-meta">
								<time class="post-card-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
									<?php echo esc_html( get_the_date() ); ?>
								</time>
								<span class="post-card-readtime">
									<?php
									// Calculate read time
									$content = get_the_content();
									$word_count = str_word_count( wp_strip_all_tags( $content ) );
									$read_time = ceil( $word_count / 200 );
									printf(
										esc_html__( '%d min read', 'moncala-ai' ),
										intval( $read_time )
									);
									?>
								</span>
							</div>

							<h2 class="post-card-title">
								<a href="<?php the_permalink(); ?>" class="post-card-link">
									<?php the_title(); ?>
								</a>
							</h2>
						</header>

						<div class="card-body">
							<?php
							// Categories
							$categories = get_the_category();
							if ( ! empty( $categories ) ) {
								?>
								<div class="post-categories">
									<?php
									foreach ( $categories as $category ) {
										?>
										<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="badge badge-primary">
											<?php echo esc_html( $category->name ); ?>
										</a>
										<?php
									}
									?>
								</div>
								<?php
							}
							?>

							<p class="post-excerpt text-muted">
								<?php
								$excerpt = wp_trim_words( get_the_content(), 30, '...' );
								echo wp_kses_post( $excerpt );
								?>
							</p>
						</div>

						<div class="card-footer">
							<a href="<?php the_permalink(); ?>" class="btn btn-outlined btn-sm">
								<?php esc_html_e( 'Read Article', 'moncala-ai' ); ?>
								<span aria-hidden="true">→</span>
							</a>
						</div>
					</article>
					<?php
				}
				?>
			</div>

			<?php
			// Pagination
			the_posts_pagination( array(
				'mid_size'           => 2,
				'prev_text'          => esc_html__( 'Previous', 'moncala-ai' ),
				'next_text'          => esc_html__( 'Next', 'moncala-ai' ),
				'screen_reader_text' => esc_html__( 'Posts navigation', 'moncala-ai' ),
			) );
			?>
			<?php
		} else {
			?>
			<div class="no-posts">
				<h2><?php esc_html_e( 'No posts found', 'moncala-ai' ); ?></h2>
				<p><?php esc_html_e( 'No articles match your search. Try browsing by category or tag.', 'moncala-ai' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/blog' ) ); ?>" class="btn btn-primary">
					<?php esc_html_e( 'Back to Blog', 'moncala-ai' ); ?>
				</a>
			</div>
			<?php
		}
		?>
	</div>
</main>

<?php
get_footer();
