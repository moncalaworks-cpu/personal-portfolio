<?php
/**
 * MonCala AI Theme - Page Template
 *
 * Default template for pages and custom page templates
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
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-article' ); ?>>
				<header class="page-header">
					<div class="container">
						<h1 class="page-title"><?php the_title(); ?></h1>
						<?php
						if ( ! is_front_page() ) {
							?>
							<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'moncala-ai' ); ?>">
								<ol>
									<li>
										<a href="<?php echo esc_url( home_url() ); ?>">
											<?php esc_html_e( 'Home', 'moncala-ai' ); ?>
										</a>
									</li>
									<li class="current">
										<?php the_title(); ?>
									</li>
								</ol>
							</nav>
							<?php
						}
						?>
					</div>
				</header>

				<div class="page-content">
					<div class="container">
						<?php
						the_content();

						// Page navigation
						wp_link_pages( array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'moncala-ai' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
						) );
						?>
					</div>
				</div>

				<?php
				// Comments section (if enabled)
				if ( comments_open() || get_comments_number() ) {
					?>
					<div class="page-comments">
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
	} else {
		?>
		<div class="no-posts">
			<div class="container">
				<h2><?php esc_html_e( 'Page Not Found', 'moncala-ai' ); ?></h2>
				<p><?php esc_html_e( 'The page you are looking for could not be found.', 'moncala-ai' ); ?></p>
				<a href="<?php echo esc_url( home_url() ); ?>" class="btn btn-primary">
					<?php esc_html_e( 'Back to Home', 'moncala-ai' ); ?>
				</a>
			</div>
		</div>
		<?php
	}
	?>
</main>

<?php
get_footer();
