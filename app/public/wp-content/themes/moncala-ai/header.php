<?php
/**
 * MonCala AI Theme - Header Template
 *
 * Displays the header HTML and WordPress head section
 *
 * @package MonCala_AI
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<!-- Skip to content link for keyboard users (ADA compliance) -->
	<a href="#main" class="skip-link">
		<?php esc_html_e( 'Skip to main content', 'moncala-ai' ); ?>
	</a>

	<div id="page" class="site">
		<header id="masthead" class="site-header" role="banner">
			<div class="header-content">
				<div class="site-branding">
					<?php
					if ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						?>
						<h1 class="site-title">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php bloginfo( 'name' ); ?>
							</a>
						</h1>
						<?php
						$moncala_description = get_bloginfo( 'description', 'display' );
						if ( $moncala_description ) {
							?>
							<p class="site-description"><?php echo esc_html( $moncala_description ); ?></p>
							<?php
						}
					}
					?>
				</div>

				<!-- Mobile menu toggle button -->
				<button class="mobile-menu-toggle" aria-label="<?php esc_attr_e( 'Toggle Menu', 'moncala-ai' ); ?>" aria-expanded="false">
					<span class="hamburger">
						<span></span>
						<span></span>
						<span></span>
					</span>
				</button>

				<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'moncala-ai' ); ?>">
					<ul id="primary-menu" class="menu">
						<li class="menu-item">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<?php esc_html_e( 'Home', 'moncala-ai' ); ?>
							</a>
						</li>
						<li class="menu-item">
							<a href="<?php echo esc_url( home_url( '/blog' ) ); ?>">
								<?php esc_html_e( 'Blog', 'moncala-ai' ); ?>
							</a>
						</li>
						<li class="menu-item">
							<a href="<?php echo esc_url( home_url( '/portfolio' ) ); ?>">
								<?php esc_html_e( 'Portfolio', 'moncala-ai' ); ?>
							</a>
						</li>
						<li class="menu-item">
							<a href="<?php echo esc_url( home_url( '/about' ) ); ?>">
								<?php esc_html_e( 'About', 'moncala-ai' ); ?>
							</a>
						</li>
						<li class="menu-item">
							<a href="<?php echo esc_url( home_url( '/resume' ) ); ?>">
								<?php esc_html_e( 'Resume', 'moncala-ai' ); ?>
							</a>
						</li>
					</ul>
				</nav>
			</div>
		</header>

		<div id="content" class="site-content">
