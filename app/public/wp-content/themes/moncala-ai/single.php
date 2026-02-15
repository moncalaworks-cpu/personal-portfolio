<?php
/**
 * MonCala AI Theme - Single Post Template
 *
 * Displays individual blog posts with:
 * - Full content
 * - Code syntax highlighting
 * - Navigation to related posts
 * - Comments section
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
		while ( have_posts() ) {
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>
				<header class="post-header">
					<div class="container">
						<div class="post-meta-top">
							<?php
							// Categories
							$categories = get_the_category();
							if ( ! empty( $categories ) ) {
								foreach ( $categories as $category ) {
									?>
									<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="badge badge-primary">
										<?php echo esc_html( $category->name ); ?>
									</a>
									<?php
								}
							}
							?>
						</div>

						<h1 class="post-title"><?php the_title(); ?></h1>

						<div class="post-meta">
							<span class="post-author">
								<?php esc_html_e( 'By ', 'moncala-ai' ); ?>
								<?php the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ); ?>
							</span>
							<span class="post-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<?php echo esc_html( get_the_date() ); ?>
							</span>
							<span class="post-readtime">
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

						<?php
						// Featured image
						if ( has_post_thumbnail() ) {
							?>
							<figure class="post-featured-image">
								<?php
								the_post_thumbnail(
									'moncala-featured',
									array( 'class' => 'featured-image' )
								);
								?>
							</figure>
							<?php
						}
						?>
					</div>
				</header>

				<div class="post-content">
					<div class="container post-body">
						<?php the_content(); ?>

						<?php
						// Page navigation for multi-page posts
						wp_link_pages( array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'moncala-ai' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
						) );
						?>
					</div>
				</div>

				<footer class="post-footer">
					<div class="container">
						<div class="post-footer-content">
							<?php
							// Tags
							$tags = get_the_tags();
							if ( ! empty( $tags ) ) {
								?>
								<div class="post-tags">
									<h3><?php esc_html_e( 'Tags', 'moncala-ai' ); ?></h3>
									<div class="tag-list">
										<?php
										foreach ( $tags as $tag ) {
											?>
											<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="tag">
												<?php echo esc_html( $tag->name ); ?>
											</a>
											<?php
										}
										?>
									</div>
								</div>
								<?php
							}
							?>
						</div>

						<?php
						// Post navigation
						$prev_post = get_previous_post();
						$next_post = get_next_post();

						if ( $prev_post || $next_post ) {
							?>
							<nav class="post-navigation">
								<h3><?php esc_html_e( 'More Articles', 'moncala-ai' ); ?></h3>
								<div class="post-nav-links">
									<?php
									if ( $prev_post ) {
										?>
										<div class="post-nav-item post-nav-prev">
											<a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>">
												<span class="nav-arrow">←</span>
												<div>
													<div class="nav-label"><?php esc_html_e( 'Previous', 'moncala-ai' ); ?></div>
													<div class="nav-title"><?php echo esc_html( get_the_title( $prev_post->ID ) ); ?></div>
												</div>
											</a>
										</div>
										<?php
									}

									if ( $next_post ) {
										?>
										<div class="post-nav-item post-nav-next">
											<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>">
												<div>
													<div class="nav-label"><?php esc_html_e( 'Next', 'moncala-ai' ); ?></div>
													<div class="nav-title"><?php echo esc_html( get_the_title( $next_post->ID ) ); ?></div>
												</div>
												<span class="nav-arrow">→</span>
											</a>
										</div>
										<?php
									}
									?>
								</div>
							</nav>
							<?php
						}
						?>
					</div>
				</footer>

				<?php
				// Comments section
				if ( comments_open() || get_comments_number() ) {
					?>
					<div class="post-comments">
						<div class="container">
							<?php comments_template(); ?>
						</div>
					</div>
					<?php
				}
				?>
			</article>
			<?php
		}
	}
	?>
</main>

<?php
get_footer();
