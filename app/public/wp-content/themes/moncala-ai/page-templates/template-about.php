<?php
/**
 * Template Name: About Us
 * Template Post Type: page
 *
 * About Us page showcasing company mission and expertise
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
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-about' ); ?>>
				<header class="page-header about-header">
					<div class="container">
						<h1 class="page-title"><?php the_title(); ?></h1>
					</div>
				</header>

				<div class="page-content">
					<div class="container">
						<?php the_content(); ?>
					</div>
				</div>

				<!-- Expertise Section -->
				<section class="expertise-section">
					<div class="container">
						<h2><?php esc_html_e( 'Our Expertise', 'moncala-ai' ); ?></h2>

						<div class="expertise-grid grid grid-3">
							<div class="expertise-card card">
								<h3 class="expertise-title">
									<span class="expertise-icon">🤖</span>
									<?php esc_html_e( 'AI Integration', 'moncala-ai' ); ?>
								</h3>
								<p class="expertise-description text-muted">
									<?php esc_html_e( 'Safe, pragmatic integration of LLMs, RAG systems, and machine learning models into existing production systems.', 'moncala-ai' ); ?>
								</p>
							</div>

							<div class="expertise-card card">
								<h3 class="expertise-title">
									<span class="expertise-icon">🏗️</span>
									<?php esc_html_e( 'Legacy Modernization', 'moncala-ai' ); ?>
								</h3>
								<p class="expertise-description text-muted">
									<?php esc_html_e( 'Strategic refactoring and modernization of 10+ year old codebases without disrupting operations.', 'moncala-ai' ); ?>
								</p>
							</div>

							<div class="expertise-card card">
								<h3 class="expertise-title">
									<span class="expertise-icon">⚙️</span>
									<?php esc_html_e( 'DevOps & Architecture', 'moncala-ai' ); ?>
								</h3>
								<p class="expertise-description text-muted">
									<?php esc_html_e( 'Containerization, CI/CD pipelines, and scalable infrastructure design for enterprise applications.', 'moncala-ai' ); ?>
								</p>
							</div>
						</div>
					</div>
				</section>

				<!-- Team Section -->
				<section class="team-section">
					<div class="container">
						<h2><?php esc_html_e( 'About the Founder', 'moncala-ai' ); ?></h2>
						<div class="team-member">
							<div class="member-bio">
								<h3><?php esc_html_e( 'Founder & Technical Lead', 'moncala-ai' ); ?></h3>
								<p class="text-muted">
									<?php esc_html_e( 'Full-stack engineer with 10+ years of experience integrating cutting-edge technologies into legacy systems. Specializes in pragmatic AI integration, database architecture, and DevOps automation.', 'moncala-ai' ); ?>
								</p>
								<div class="member-links">
									<a href="<?php echo esc_url( home_url( '/resume' ) ); ?>" class="btn btn-outlined btn-sm">
										<?php esc_html_e( 'View Resume', 'moncala-ai' ); ?>
									</a>
									<a href="<?php echo esc_url( home_url( '/blog' ) ); ?>" class="btn btn-ghost btn-sm">
										<?php esc_html_e( 'Read Articles', 'moncala-ai' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- CTA Section -->
				<section class="cta-section">
					<div class="container text-center">
						<h2><?php esc_html_e( 'Let\'s Talk About Your Legacy Systems', 'moncala-ai' ); ?></h2>
						<p class="text-muted">
							<?php esc_html_e( 'Schedule a free consultation to discuss how AI integration can modernize your infrastructure.', 'moncala-ai' ); ?>
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
