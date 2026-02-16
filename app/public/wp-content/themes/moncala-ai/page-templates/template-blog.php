<?php
/**
 * Template Name: Blog
 * Template Post Type: page
 *
 * MonCala AI Theme - Blog Page Template
 *
 * Displays blog archive with posts list, search, filters, and pagination
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
	<!-- Breadcrumb Navigation -->
	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<header class="archive-header">
		<div class="container">
			<h1 class="archive-title">
				<?php esc_html_e( 'Blog', 'moncala-ai' ); ?>
			</h1>
			<p class="archive-description text-muted">
				<?php esc_html_e( 'Insights on AI integration, consulting, and development practices', 'moncala-ai' ); ?>
			</p>
		</div>
	</header>

	<!-- Search and Filter Section -->
	<div class="archive-filters">
		<div class="container">
			<form method="get" class="archive-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input
					type="hidden"
					name="post_type"
					value="post"
				>
				<input
					type="search"
					name="s"
					class="archive-search-input"
					placeholder="<?php esc_attr_e( 'Search posts...', 'moncala-ai' ); ?>"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					aria-label="<?php esc_attr_e( 'Search posts', 'moncala-ai' ); ?>"
				>
				<button type="submit" class="archive-search-button" aria-label="<?php esc_attr_e( 'Search', 'moncala-ai' ); ?>">
					<svg class="icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
						<circle cx="8.5" cy="8.5" r="6.5" stroke="currentColor" stroke-width="2"/>
						<path d="M13 13L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					</svg>
				</button>
			</form>

			<?php
			// Category Filter
			$current_category = get_query_var( 'cat' );
			$categories       = get_categories(
				array(
					'orderby' => 'name',
					'order'   => 'ASC',
				)
			);

			if ( ! empty( $categories ) ) :
				?>
				<div class="archive-filter-group">
					<label for="category-filter" class="archive-filter-label">
						<?php esc_html_e( 'Category:', 'moncala-ai' ); ?>
					</label>
					<select id="category-filter" class="archive-filter-select" onchange="window.location.href=this.value;">
						<option value="<?php echo esc_url( home_url( '/blog/' ) ); ?>">
							<?php esc_html_e( 'All Categories', 'moncala-ai' ); ?>
						</option>
						<?php
						foreach ( $categories as $category ) {
							$category_url = get_category_link( $category );
							$selected     = selected( $current_category, $category->term_id, false );
							printf(
								'<option value="%s" %s>%s</option>',
								esc_url( $category_url ),
								$selected,
								esc_html( $category->name )
							);
						}
						?>
					</select>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="container">
		<?php
		// Query recent posts for this blog page
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => 5,
			'paged'          => $paged,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		// Add search query if present
		if ( ! empty( $_GET['s'] ) ) {
			$args['s'] = sanitize_text_field( $_GET['s'] );
		}

		$blog_query = new WP_Query( $args );

		if ( $blog_query->have_posts() ) {
			?>
			<div class="posts-grid grid grid-2">
				<?php
				while ( $blog_query->have_posts() ) {
					$blog_query->the_post();
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
				'base'               => add_query_arg( 'paged', '%#%', get_permalink( get_the_ID() ) ),
			) );
			?>
			<?php
		} else {
			?>
			<div class="no-posts">
				<h2><?php esc_html_e( 'No posts found', 'moncala-ai' ); ?></h2>
				<p><?php esc_html_e( 'No articles match your search. Try browsing by category or tag.', 'moncala-ai' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="btn btn-primary">
					<?php esc_html_e( 'Back to Blog', 'moncala-ai' ); ?>
				</a>
			</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
</main>

<?php
get_footer();
