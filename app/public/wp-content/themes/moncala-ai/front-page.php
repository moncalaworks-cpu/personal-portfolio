<?php
/**
 * MonCala AI Theme - Front Page Template
 *
 * Homepage showcasing:
 * - Hero section with company mission
 * - Featured blog posts
 * - Portfolio projects
 * - About/CTA section
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
	// Hero Section
	get_template_part( 'template-parts/hero-home' );

	// Featured Blog Posts Section
	?>
	<section class="section-featured-posts">
		<div class="container">
			<div class="section-header text-center">
				<h2><?php esc_html_e( 'Latest Articles', 'moncala-ai' ); ?></h2>
				<p class="text-muted">
					<?php esc_html_e( 'Expert insights on AI integration, legacy modernization, and technical architecture', 'moncala-ai' ); ?>
				</p>
			</div>

			<?php
			// Query recent posts
			$recent_posts = new WP_Query( array(
				'post_type'      => 'post',
				'posts_per_page' => 3,
				'orderby'        => 'date',
				'order'          => 'DESC',
			) );

			if ( $recent_posts->have_posts() ) {
				?>
				<div class="posts-grid grid grid-3">
					<?php
					while ( $recent_posts->have_posts() ) {
						$recent_posts->the_post();
						?>
						<article class="post-card card">
							<?php
							if ( has_post_thumbnail() ) {
								?>
								<figure class="post-card-figure">
									<?php
									the_post_thumbnail(
										'moncala-featured',
										array( 'class' => 'post-card-image' )
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
										$read_time = ceil( $word_count / 200 ); // 200 words per minute
										printf(
											esc_html__( '%d min read', 'moncala-ai' ),
											intval( $read_time )
										);
										?>
									</span>
								</div>

								<h3 class="post-card-title">
									<a href="<?php the_permalink(); ?>" class="post-card-link">
										<?php the_title(); ?>
									</a>
								</h3>
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
					wp_reset_postdata();
					?>
				</div>
				<?php
			}
			?>
		</div>
	</section>

	<?php
	// Portfolio Section
	?>
	<section class="section-portfolio" id="portfolio">
		<div class="container">
			<div class="section-header text-center">
				<h2><?php esc_html_e( 'Featured Projects', 'moncala-ai' ); ?></h2>
				<p class="text-muted">
					<?php esc_html_e( 'Real-world implementations of AI integration and legacy system modernization', 'moncala-ai' ); ?>
				</p>
			</div>

			<div class="projects-grid grid grid-3">
				<?php
				// Portfolio project 1
				?>
				<div class="project-card card">
					<div class="project-icon">
						<span class="icon-code">⚙️</span>
					</div>
					<h3 class="project-title">
						<?php esc_html_e( 'AI-Powered Legacy Code Analysis', 'moncala-ai' ); ?>
					</h3>
					<p class="project-description text-muted">
						<?php esc_html_e( 'Automated system for analyzing 15+ year old Python codebases using GPT-4, identifying optimization opportunities and refactoring patterns.', 'moncala-ai' ); ?>
					</p>
					<div class="project-tech">
						<span class="badge badge-secondary">Python</span>
						<span class="badge badge-secondary">OpenAI</span>
						<span class="badge badge-secondary">Git</span>
					</div>
				</div>

				<?php
				// Portfolio project 2
				?>
				<div class="project-card card">
					<div class="project-icon">
						<span class="icon-ml">🤖</span>
					</div>
					<h3 class="project-title">
						<?php esc_html_e( 'Gradual ML Integration Framework', 'moncala-ai' ); ?>
					</h3>
					<p class="project-description text-muted">
						<?php esc_html_e( 'Microservice wrapper enabling TensorFlow model deployment into production systems without downtime or risk.', 'moncala-ai' ); ?>
					</p>
					<div class="project-tech">
						<span class="badge badge-secondary">Node.js</span>
						<span class="badge badge-secondary">TensorFlow</span>
						<span class="badge badge-secondary">Docker</span>
					</div>
				</div>

				<?php
				// Portfolio project 3
				?>
				<div class="project-card card">
					<div class="project-icon">
						<span class="icon-database">💾</span>
					</div>
					<h3 class="project-title">
						<?php esc_html_e( 'Vector Store Migration Suite', 'moncala-ai' ); ?>
					</h3>
					<p class="project-description text-muted">
						<?php esc_html_e( 'Complete ETL pipeline migrating structured data from PostgreSQL to Pinecone vector database with validation and rollback.', 'moncala-ai' ); ?>
					</p>
					<div class="project-tech">
						<span class="badge badge-secondary">PostgreSQL</span>
						<span class="badge badge-secondary">Pinecone</span>
						<span class="badge badge-secondary">Python</span>
					</div>
				</div>
			</div>

			<div class="section-cta text-center">
				<a href="<?php echo esc_url( home_url( '/portfolio' ) ); ?>" class="btn btn-primary">
					<?php esc_html_e( 'View All Projects', 'moncala-ai' ); ?>
					<span aria-hidden="true">→</span>
				</a>
			</div>
		</div>
	</section>

	<?php
	// About/CTA Section
	?>
	<section class="section-cta-about">
		<div class="container">
			<div class="cta-content">
				<h2><?php esc_html_e( 'Ready to Integrate AI into Your Legacy Systems?', 'moncala-ai' ); ?></h2>
				<p>
					<?php esc_html_e( 'We specialize in safe, pragmatic AI integration for enterprise systems. Learn how we can modernize your infrastructure.', 'moncala-ai' ); ?>
				</p>
				<div class="cta-buttons">
					<a href="<?php echo esc_url( home_url( '/about' ) ); ?>" class="btn btn-primary">
						<?php esc_html_e( 'Learn About Us', 'moncala-ai' ); ?>
					</a>
					<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn-outlined">
						<?php esc_html_e( 'Get in Touch', 'moncala-ai' ); ?>
					</a>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
