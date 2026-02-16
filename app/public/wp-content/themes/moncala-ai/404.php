<?php
/**
 * 404 Template - Page Not Found
 *
 * Displays when a page is not found with helpful navigation
 * and ADA compliance for accessibility
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
	<div class="container">
		<section class="error-404 not-found" role="main">
			<header class="page-header">
				<h1 class="page-title">
					<?php esc_html_e( '404 - Page Not Found', 'moncala-ai' ); ?>
				</h1>
				<p class="page-description">
					<?php esc_html_e( "We couldn't find the page you were looking for. It might have been moved or deleted.", 'moncala-ai' ); ?>
				</p>
			</header>

			<div class="page-content">
				<p>
					<?php esc_html_e( 'Here are some helpful links to get you back on track:', 'moncala-ai' ); ?>
				</p>

				<nav class="error-nav" aria-label="<?php esc_attr_e( 'Navigation after page not found', 'moncala-ai' ); ?>">
					<ul class="error-links">
						<li>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
								<?php esc_html_e( 'Return to Home', 'moncala-ai' ); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( home_url( '/blog' ) ); ?>" class="btn btn-outlined">
								<?php esc_html_e( 'Browse Blog', 'moncala-ai' ); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( home_url( '/portfolio' ) ); ?>" class="btn btn-outlined">
								<?php esc_html_e( 'View Portfolio', 'moncala-ai' ); ?>
							</a>
						</li>
					</ul>
				</nav>

				<hr class="divider">

				<h2><?php esc_html_e( 'Search Our Site', 'moncala-ai' ); ?></h2>
				<?php get_search_form(); ?>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
