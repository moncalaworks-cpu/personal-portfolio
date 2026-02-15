<?php
/**
 * Hello World Plugin
 *
 * Demonstrates WordPress fundamentals for learning:
 * - Plugin hooks (init, wp_enqueue_scripts, the_content)
 * - Creating pages programmatically (wp_insert_post)
 * - Enqueueing styles and scripts
 * - Filtering content
 *
 * @wordpress-plugin
 * Plugin Name: Hello World
 * Version: 1.0.0
 * Description: A simple Hello World page demonstrating WordPress plugin fundamentals
 * Author: Learning Project
 * License: GPL v2 or later
 * Text Domain: hello-world
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Create the Hello World page on plugin activation
register_activation_hook( __FILE__, 'hello_world_create_page' );

function hello_world_create_page() {
	// Check if page already exists
	$existing_page = get_page_by_title( 'Hello World', OBJECT, 'page' );

	if ( ! $existing_page ) {
		// Create the page
		wp_insert_post( [
			'post_title'   => 'Hello World',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'Hello World page created by the Hello World plugin.',
			'post_name'    => 'hello-world',
		] );
	}
}

// Enqueue custom CSS on frontend
add_action( 'wp_enqueue_scripts', 'hello_world_enqueue_styles' );

function hello_world_enqueue_styles() {
	wp_enqueue_style( 'hello-world-style', plugin_dir_url( __FILE__ ) . 'css/hello-world.css' );
}

// Filter page content to add custom wrapper
add_filter( 'the_content', 'hello_world_wrap_content' );

function hello_world_wrap_content( $content ) {
	// Only apply to Hello World page
	if ( is_page( 'hello-world' ) ) {
		$wrapped = '<div class="hello-world-container">';
		$wrapped .= '<h1 class="hello-world-title">🎉 Hello, WordPress World!</h1>';
		$wrapped .= '<p class="hello-world-subtitle">This page was created using a WordPress plugin.</p>';
		$wrapped .= '<div class="hello-world-content">' . $content . '</div>';
		$wrapped .= '</div>';

		return $wrapped;
	}

	return $content;
}
