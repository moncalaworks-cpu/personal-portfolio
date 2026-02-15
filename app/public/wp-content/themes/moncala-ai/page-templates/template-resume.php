<?php
/**
 * Template Name: Resume
 * Template Post Type: page
 *
 * Resume/Bio page with professional information
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
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-resume' ); ?>>
				<header class="page-header resume-header">
					<div class="container">
						<h1 class="page-title"><?php the_title(); ?></h1>
					</div>
				</header>

				<div class="page-content">
					<div class="container">
						<?php the_content(); ?>
					</div>
				</div>

				<!-- Professional Summary -->
				<section class="summary-section">
					<div class="container">
						<h2><?php esc_html_e( 'Professional Summary', 'moncala-ai' ); ?></h2>
						<p class="summary-text text-muted">
							<?php esc_html_e( 'Full-stack engineer with 10+ years of experience architecting and implementing AI integration solutions for enterprise systems. Specialized in bringing cutting-edge machine learning and language models into production legacy codebases without disruption.', 'moncala-ai' ); ?>
						</p>
					</div>
				</section>

				<!-- Core Competencies -->
				<section class="skills-section">
					<div class="container">
						<h2><?php esc_html_e( 'Core Competencies', 'moncala-ai' ); ?></h2>

						<div class="skills-grid grid grid-2">
							<div class="skill-category">
								<h3><?php esc_html_e( 'AI & Machine Learning', 'moncala-ai' ); ?></h3>
								<ul class="skill-list">
									<li><?php esc_html_e( 'Large Language Model Integration (OpenAI, Anthropic, Ollama)', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'Retrieval Augmented Generation (RAG) Systems', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'TensorFlow & PyTorch Model Deployment', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'Vector Databases (Pinecone, Weaviate, Qdrant)', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'Prompt Engineering & Chain-of-Thought Patterns', 'moncala-ai' ); ?></li>
								</ul>
							</div>

							<div class="skill-category">
								<h3><?php esc_html_e( 'Legacy System Modernization', 'moncala-ai' ); ?></h3>
								<ul class="skill-list">
									<li><?php esc_html_e( 'Gradual Migration Strategies', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'Database Refactoring & Schema Evolution', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'Monolithic to Microservices', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'Code Analysis & Automated Refactoring', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'API Gateway & Proxy Pattern Implementation', 'moncala-ai' ); ?></li>
								</ul>
							</div>

							<div class="skill-category">
								<h3><?php esc_html_e( 'Backend Technologies', 'moncala-ai' ); ?></h3>
								<ul class="skill-list">
									<li><?php esc_html_e( 'Python (async, FastAPI, Django, Celery)', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'Node.js (Express, NestJS, GraphQL)', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'PostgreSQL, MongoDB, Redis, DynamoDB', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'Message Queues (RabbitMQ, Kafka, SQS)', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'REST & GraphQL API Design', 'moncala-ai' ); ?></li>
								</ul>
							</div>

							<div class="skill-category">
								<h3><?php esc_html_e( 'DevOps & Infrastructure', 'moncala-ai' ); ?></h3>
								<ul class="skill-list">
									<li><?php esc_html_e( 'Docker & Kubernetes Container Orchestration', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'CI/CD Pipelines (GitHub Actions, GitLab CI, Jenkins)', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'AWS (EC2, ECS, Lambda, RDS, S3)', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'Infrastructure as Code (Terraform, CloudFormation)', 'moncala-ai' ); ?></li>
									<li><?php esc_html_e( 'Monitoring & Logging (Prometheus, ELK, DataDog)', 'moncala-ai' ); ?></li>
								</ul>
							</div>
						</div>
					</div>
				</section>

				<!-- Experience -->
				<section class="experience-section">
					<div class="container">
						<h2><?php esc_html_e( 'Professional Experience', 'moncala-ai' ); ?></h2>

						<div class="experience-timeline">
							<div class="experience-item">
								<h3 class="experience-title">
									<?php esc_html_e( 'Senior AI Integration Engineer', 'moncala-ai' ); ?>
								</h3>
								<p class="experience-company">
									<?php esc_html_e( 'MonCala Works LLC', 'moncala-ai' ); ?>
									<span class="experience-period">2024 - Present</span>
								</p>
								<p class="experience-description text-muted">
									<?php esc_html_e( 'Founded and leading consulting practice specializing in pragmatic AI integration for enterprise legacy systems. Architecting and implementing production-grade AI solutions for Fortune 500 companies.', 'moncala-ai' ); ?>
								</p>
							</div>

							<div class="experience-item">
								<h3 class="experience-title">
									<?php esc_html_e( 'Technical Lead, AI & Infrastructure', 'moncala-ai' ); ?>
								</h3>
								<p class="experience-company">
									<?php esc_html_e( 'Previous Enterprise (Confidential)', 'moncala-ai' ); ?>
									<span class="experience-period">2018 - 2024</span>
								</p>
								<p class="experience-description text-muted">
									<?php esc_html_e( 'Led team of 5 engineers on modernization initiatives. Designed and implemented AI integration framework, managed Kubernetes infrastructure, and reduced deployment time from weeks to hours.', 'moncala-ai' ); ?>
								</p>
							</div>

							<div class="experience-item">
								<h3 class="experience-title">
									<?php esc_html_e( 'Full Stack Engineer', 'moncala-ai' ); ?>
								</h3>
								<p class="experience-company">
									<?php esc_html_e( 'Various Startups & Scale-ups', 'moncala-ai' ); ?>
									<span class="experience-period">2014 - 2018</span>
								</p>
								<p class="experience-description text-muted">
									<?php esc_html_e( 'Built backend systems, developed DevOps infrastructure, and led technical initiatives at growth-stage companies. Hands-on contributor to product development and architectural decisions.', 'moncala-ai' ); ?>
								</p>
							</div>
						</div>
					</div>
				</section>

				<!-- CTA Section -->
				<section class="cta-section">
					<div class="container text-center">
						<h2><?php esc_html_e( 'Ready to Work Together?', 'moncala-ai' ); ?></h2>
						<p class="text-muted">
							<?php esc_html_e( 'Available for consulting engagements on AI integration, system modernization, and architecture design.', 'moncala-ai' ); ?>
						</p>
						<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn-primary btn-lg">
							<?php esc_html_e( 'Get in Touch', 'moncala-ai' ); ?>
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
