<?php
/**
 * MonCala AI Theme - Asset Enqueuing
 *
 * Loads all CSS and JavaScript resources for the theme
 *
 * @package MonCala_AI
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue theme stylesheets
 * Includes design system, typography, components, and layout
 */
function moncala_enqueue_stylesheets() {
	// Design system variables
	wp_enqueue_style(
		'moncala-variables',
		MONCALA_THEME_URI . '/assets/css/variables.css',
		array(),
		MONCALA_VERSION,
		'all'
	);

	// Typography system (fonts and text styles)
	wp_enqueue_style(
		'moncala-typography',
		MONCALA_THEME_URI . '/assets/css/typography.css',
		array( 'moncala-variables' ),
		MONCALA_VERSION,
		'all'
	);

	// Component styles (buttons, cards, badges, alerts)
	wp_enqueue_style(
		'moncala-components',
		MONCALA_THEME_URI . '/assets/css/components.css',
		array( 'moncala-typography' ),
		MONCALA_VERSION,
		'all'
	);

	// Main layout and utilities
	wp_enqueue_style(
		'moncala-main',
		MONCALA_THEME_URI . '/assets/css/main.css',
		array( 'moncala-components' ),
		MONCALA_VERSION,
		'all'
	);

	// Blog styles (archive, single posts, components)
	wp_enqueue_style(
		'moncala-blog',
		MONCALA_THEME_URI . '/assets/css/blog.css',
		array( 'moncala-main' ),
		MONCALA_VERSION,
		'all'
	);

	// WordPress block editor styles (Phase 2+)
	wp_enqueue_style(
		'wp-block-library'
	);
}
add_action( 'wp_enqueue_scripts', 'moncala_enqueue_stylesheets', 10 );

/**
 * Enqueue admin stylesheet for WordPress block editor
 */
function moncala_enqueue_admin_styles() {
	wp_enqueue_style(
		'moncala-editor',
		MONCALA_THEME_URI . '/assets/css/variables.css',
		array( 'wp-edit-blocks' ),
		MONCALA_VERSION
	);
}
add_action( 'enqueue_block_editor_assets', 'moncala_enqueue_admin_styles' );

/**
 * Enqueue theme JavaScript
 * (Scripts for interactive components - Phase 2+)
 */
function moncala_enqueue_scripts() {
	// Main theme JavaScript
	wp_enqueue_script(
		'moncala-main',
		MONCALA_THEME_URI . '/assets/js/main.js',
		array(),
		MONCALA_VERSION,
		array(
			'strategy'  => 'async',
			'in_footer' => true,
		)
	);

	// Blog functionality (TOC, reading progress, social sharing)
	if ( moncala_is_blog() ) {
		wp_enqueue_script(
			'moncala-blog',
			MONCALA_THEME_URI . '/assets/js/blog.js',
			array(),
			MONCALA_VERSION,
			array(
				'strategy'  => 'async',
				'in_footer' => true,
			)
		);
	}

	// Comment reply script (if comments are enabled)
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Localize script with theme data
	wp_localize_script(
		'moncala-main',
		'moncalaTheme',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'moncala_nonce' ),
			'homeUrl'  => esc_url( home_url() ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'moncala_enqueue_scripts' );

/**
 * Defer non-critical CSS and optimize font loading
 */
function moncala_optimize_css_delivery() {
	// Preload critical fonts
	echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Fira+Code:wght@400;500;700&display=swap" as="style">' . "\n";

	// Prefetch DNS for Google Fonts
	echo '<link rel="dns-prefetch" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="dns-prefetch" href="https://fonts.gstatic.com">' . "\n";
}
add_action( 'wp_head', 'moncala_optimize_css_delivery', 1 );

/**
 * Add async/defer attributes to scripts
 * Performance optimization
 */
function moncala_script_loader_tag( $tag, $handle, $src ) {
	// Only process theme scripts
	if ( 'moncala-main' !== $handle ) {
		return $tag;
	}

	// Add async attribute for non-critical scripts
	return str_replace( ' src', ' async src', $tag );
}
add_filter( 'script_loader_tag', 'moncala_script_loader_tag', 10, 3 );

/**
 * Remove default WordPress emojis to reduce HTTP requests
 * (Emoji support can be re-enabled if needed)
 */
function moncala_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'moncala_disable_emojis' );
