<?php
/**
 * Plugin Name: Hello World
 * Plugin URI: https://github.com/moncalaworks-cpu/personal-portfolio
 * Description: A simple Hello World page demonstrating WordPress core concepts
 * Version: 1.0.0
 * Author: moncalaworks-cpu
 * Author URI: https://github.com/moncalaworks-cpu
 * Text Domain: hello-world
 * Domain Path: /languages
 * License: GPL v2 or later
 *
 * This plugin demonstrates:
 * - WordPress plugin structure and header comments
 * - Using hooks to register content
 * - Rendering output to the frontend
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Hook into WordPress initialization to register our custom page
add_action( 'init', 'hw_register_hello_world_page' );

/**
 * Register the Hello World page/post type
 * Demonstrates: add_action, wp_insert_post, post_type registration
 */
function hw_register_hello_world_page() {
	// Create a page if it doesn't exist
	$existing_page = get_page_by_title( 'Hello World' );

	if ( ! $existing_page ) {
		wp_insert_post( [
			'post_title'     => 'Hello World',
			'post_content'   => 'This is a Hello World page created programmatically by the Hello World plugin.',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'post_author'    => 1,
		] );
	}
}

// Hook into page display to add our custom styling
add_action( 'wp_enqueue_scripts', 'hw_enqueue_styles' );

/**
 * Enqueue our custom stylesheet
 * Demonstrates: wp_enqueue_style, plugin asset management
 */
function hw_enqueue_styles() {
	// Only enqueue on the Hello World page
	if ( is_page( 'hello-world' ) ) {
		wp_enqueue_style(
			'hello-world-style',
			plugin_dir_url( __FILE__ ) . 'css/hello-world.css',
			[],
			'1.0.0'
		);
	}
}

// Add custom content filter to display Hello World message
add_filter( 'the_content', 'hw_add_hello_message' );

/**
 * Filter page content to add custom Hello World message
 * Demonstrates: add_filter, apply_filters, is_page, get_the_ID
 */
function hw_add_hello_message( $content ) {
	// Only modify on our Hello World page
	if ( is_page( 'hello-world' ) && ! is_admin() ) {
		$hello_message = '<div class="hello-world-container">';
		$hello_message .= '<h1 class="hello-world-title">🎉 Hello, WordPress World!</h1>';
		$hello_message .= '<p class="hello-world-subtitle">This page was created using a WordPress plugin.</p>';
		$hello_message .= '<div class="hello-world-content">' . $content . '</div>';
		$hello_message .= '</div>';

		return $hello_message;
	}

	return $content;
}

// Display activation message
register_activation_hook( __FILE__, 'hw_plugin_activated' );

/**
 * Plugin activation hook
 * Demonstrates: register_activation_hook, plugin lifecycle management
 */
function hw_plugin_activated() {
	// This runs when the plugin is activated
	// You can use this for setup tasks like creating database tables, default options, etc.
	error_log( 'Hello World plugin activated' );
}

// Display deactivation message
register_deactivation_hook( __FILE__, 'hw_plugin_deactivated' );

/**
 * Plugin deactivation hook
 * Demonstrates: register_deactivation_hook, cleanup
 */
function hw_plugin_deactivated() {
	// This runs when the plugin is deactivated
	// You can use this for cleanup tasks
	error_log( 'Hello World plugin deactivated' );
}
