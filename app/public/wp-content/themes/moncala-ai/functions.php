<?php
/**
 * MonCala AI Theme - Functions
 *
 * Entry point for all theme functionality.
 * Loads includes, sets up theme supports, and enqueues assets.
 *
 * @package MonCala_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define theme constants
define( 'MONCALA_VERSION', '1.0.0' );
define( 'MONCALA_THEME_DIR', get_template_directory() );
define( 'MONCALA_THEME_URI', get_template_directory_uri() );

/**
 * Phase 1 Setup: Basic theme configuration
 * Enables title tags, post thumbnails, and basic features
 */
function moncala_theme_setup() {
	// Enable title tag support
	add_theme_support( 'title-tag' );

	// Enable post thumbnails (featured images)
	add_theme_support( 'post-thumbnails' );

	// Enable automatic feed link
	add_theme_support( 'automatic-feed-links' );

	// Enable HTML5 support for search form, comment form, and comment list
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	// Enable block editor support
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
}
add_action( 'after_setup_theme', 'moncala_theme_setup' );

/**
 * Enqueue theme CSS
 * Loads the main stylesheet
 */
function moncala_enqueue_styles() {
	wp_enqueue_style(
		'moncala-style',
		MONCALA_THEME_URI . '/style.css',
		array(),
		MONCALA_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'moncala_enqueue_styles' );

/**
 * Register navigation menus
 * (Phase 3 will expand this)
 */
function moncala_register_menus() {
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'moncala-ai' ),
		'footer'  => esc_html__( 'Footer Menu', 'moncala-ai' ),
	) );
}
add_action( 'init', 'moncala_register_menus' );

/**
 * Add custom image sizes
 * (Phase 3-4 will use these)
 */
function moncala_add_image_sizes() {
	add_image_size( 'moncala-featured', 1200, 600, true );  // Featured image
	add_image_size( 'moncala-thumbnail', 400, 300, true );  // Card thumbnail
	add_image_size( 'moncala-portfolio', 600, 400, true );  // Portfolio item
}
add_action( 'init', 'moncala_add_image_sizes' );

/**
 * Set content width for embedded content
 */
if ( ! isset( $content_width ) ) {
	$content_width = 900;
}
