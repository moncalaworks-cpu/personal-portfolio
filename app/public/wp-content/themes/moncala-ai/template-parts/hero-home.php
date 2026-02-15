<?php
/**
 * Hero Section Template Part
 *
 * Displays the homepage hero with headline, subheadline, and CTAs
 *
 * @package MonCala_AI
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="hero-home">
	<div class="hero-content">
		<div class="container">
			<h1 class="hero-title">
				<?php esc_html_e( 'MonCala Works', 'moncala-ai' ); ?>
			</h1>
			<p class="hero-subtitle">
				<?php esc_html_e( 'AI Integration for Legacy Systems', 'moncala-ai' ); ?>
			</p>
			<p class="hero-description">
				<?php esc_html_e( 'Pragmatic, safe, and scalable AI modernization for enterprise codebases. Transform 15-year-old systems with LLMs, RAG, and vector databases—without downtime.', 'moncala-ai' ); ?>
			</p>

			<div class="hero-buttons">
				<a href="<?php echo esc_url( home_url( '/blog' ) ); ?>" class="btn btn-primary btn-lg">
					<?php esc_html_e( 'Read Our Blog', 'moncala-ai' ); ?>
				</a>
				<a href="<?php echo esc_url( home_url( '/about' ) ); ?>" class="btn btn-outlined btn-lg">
					<?php esc_html_e( 'Our Approach', 'moncala-ai' ); ?>
				</a>
			</div>
		</div>
	</div>

	<div class="hero-visual">
		<!-- Decorative gradient background -->
		<div class="hero-gradient"></div>
	</div>
</section>
