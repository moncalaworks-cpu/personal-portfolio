<?php
/**
 * Template Name: Portfolio
 * Template Post Type: page
 *
 * Portfolio page showcasing project work
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
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-portfolio' ); ?>>
				<header class="page-header portfolio-header">
					<div class="container">
						<h1 class="page-title"><?php the_title(); ?></h1>
						<p class="page-subtitle text-muted">
							<?php esc_html_e( 'Real-world implementations of AI integration and legacy system modernization', 'moncala-ai' ); ?>
						</p>
					</div>
				</header>

				<div class="page-content">
					<div class="container">
						<?php the_content(); ?>
					</div>
				</div>

				<!-- Projects Grid -->
				<section class="projects-section">
					<div class="container">
						<h2><?php esc_html_e( 'Featured Projects', 'moncala-ai' ); ?></h2>

						<div class="projects-grid grid grid-2">
							<!-- Project 1 -->
							<div class="project-card">
								<div class="project-header">
									<h3 class="project-title">
										<?php esc_html_e( 'AI-Powered Legacy Code Analysis Tool', 'moncala-ai' ); ?>
									</h3>
								</div>
								<div class="project-body">
									<p class="project-description text-muted">
										<?php esc_html_e( 'Developed an automated system for analyzing 15+ year old Python codebases using GPT-4 API. The tool identifies code patterns, suggests refactoring opportunities, and generates migration plans for modernization.', 'moncala-ai' ); ?>
									</p>

									<div class="project-challenges">
										<h4><?php esc_html_e( 'Challenges Solved', 'moncala-ai' ); ?></h4>
										<ul>
											<li><?php esc_html_e( 'Handling large, undocumented codebases', 'moncala-ai' ); ?></li>
											<li><?php esc_html_e( 'Prompt engineering for code analysis', 'moncala-ai' ); ?></li>
											<li><?php esc_html_e( 'Cost optimization for API usage', 'moncala-ai' ); ?></li>
										</ul>
									</div>

									<div class="project-tech">
										<span class="badge badge-secondary">Python</span>
										<span class="badge badge-secondary">OpenAI</span>
										<span class="badge badge-secondary">AST Parsing</span>
										<span class="badge badge-secondary">Git Integration</span>
									</div>
								</div>
							</div>

							<!-- Project 2 -->
							<div class="project-card">
								<div class="project-header">
									<h3 class="project-title">
										<?php esc_html_e( 'Gradual ML Integration Framework', 'moncala-ai' ); ?>
									</h3>
								</div>
								<div class="project-body">
									<p class="project-description text-muted">
										<?php esc_html_e( 'Built a microservice wrapper enabling TensorFlow model deployment to production systems without downtime. Uses shadow deployment and A/B testing for safe model rollout.', 'moncala-ai' ); ?>
									</p>

									<div class="project-challenges">
										<h4><?php esc_html_e( 'Challenges Solved', 'moncala-ai' ); ?></h4>
										<ul>
											<li><?php esc_html_e( 'Zero-downtime model deployment', 'moncala-ai' ); ?></li>
											<li><?php esc_html_e( 'Feature flag implementation', 'moncala-ai' ); ?></li>
											<li><?php esc_html_e( 'Model versioning and rollback', 'moncala-ai' ); ?></li>
										</ul>
									</div>

									<div class="project-tech">
										<span class="badge badge-secondary">Node.js</span>
										<span class="badge badge-secondary">TensorFlow</span>
										<span class="badge badge-secondary">Docker</span>
										<span class="badge badge-secondary">Kubernetes</span>
									</div>
								</div>
							</div>

							<!-- Project 3 -->
							<div class="project-card">
								<div class="project-header">
									<h3 class="project-title">
										<?php esc_html_e( 'Legacy Database to Vector Store Migration', 'moncala-ai' ); ?>
									</h3>
								</div>
								<div class="project-body">
									<p class="project-description text-muted">
										<?php esc_html_e( 'Designed and implemented a complete ETL pipeline migrating structured data from PostgreSQL to Pinecone vector database, enabling semantic search capabilities while maintaining data consistency.', 'moncala-ai' ); ?>
									</p>

									<div class="project-challenges">
										<h4><?php esc_html_e( 'Challenges Solved', 'moncala-ai' ); ?></h4>
										<ul>
											<li><?php esc_html_e( 'Large-scale data migration', 'moncala-ai' ); ?></li>
											<li><?php esc_html_e( 'Vector embedding generation', 'moncala-ai' ); ?></li>
											<li><?php esc_html_e( 'Data validation and reconciliation', 'moncala-ai' ); ?></li>
										</ul>
									</div>

									<div class="project-tech">
										<span class="badge badge-secondary">Python</span>
										<span class="badge badge-secondary">PostgreSQL</span>
										<span class="badge badge-secondary">Pinecone</span>
										<span class="badge badge-secondary">OpenAI Embeddings</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- CTA Section -->
				<section class="cta-section">
					<div class="container text-center">
						<h2><?php esc_html_e( 'Interested in Similar Work?', 'moncala-ai' ); ?></h2>
						<p class="text-muted">
							<?php esc_html_e( 'Let\'s discuss how we can help modernize and scale your systems with AI integration.', 'moncala-ai' ); ?>
						</p>
						<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn-primary btn-lg">
							<?php esc_html_e( 'Start a Project', 'moncala-ai' ); ?>
						</a>
					</div>
				</section>
			</article>
			<?php
		}
	}
	?>
</main>

<?php
get_footer();
