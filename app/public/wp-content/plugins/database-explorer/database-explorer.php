<?php
/**
 * Plugin Name: Database Explorer
 * Plugin URI: https://github.com/moncalaworks-cpu/personal-portfolio
 * Description: Learn WordPress data structure, database schema, and data relationships
 * Version: 1.0.0
 * Author: moncalaworks-cpu
 * Author URI: https://github.com/moncalaworks-cpu
 * Text Domain: database-explorer
 * Domain Path: /languages
 * License: GPL v2 or later
 *
 * This plugin demonstrates:
 * - WordPress database structure (12 core tables)
 * - Post types and post meta data
 * - Taxonomies, terms, and relationships
 * - User roles and capabilities
 * - WordPress options for settings storage
 * - Using WP_Query to retrieve and filter posts
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PLUGIN ACTIVATION: Create sample data
 * Demonstrates: register_activation_hook, create posts, add meta, create terms
 */
register_activation_hook( __FILE__, 'de_plugin_activated' );

function de_plugin_activated() {
	// Create portfolio category
	$portfolio_term = wp_insert_term(
		'Portfolio',
		'category',
		[
			'slug'        => 'portfolio',
			'description' => 'Portfolio projects and case studies'
		]
	);

	// Create sample project posts
	for ( $i = 1; $i <= 3; $i++ ) {
		$post_id = wp_insert_post( [
			'post_title'   => "Project $i",
			'post_content' => "This is project $i description",
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_author'  => 1,
		] );

		// Add post meta data
		add_post_meta( $post_id, 'project_status', 'completed' );
		add_post_meta( $post_id, 'project_date', '2025-' . str_pad( $i, 2, '0', STR_PAD_LEFT ) . '-15' );
		add_post_meta( $post_id, 'project_tags', 'wordpress,php,design' );
		add_post_meta( $post_id, 'project_priority', $i );

		// Assign to portfolio category
		if ( ! is_wp_error( $portfolio_term ) ) {
			wp_set_post_terms( $post_id, $portfolio_term['term_id'], 'category' );
		}
	}

	// Store plugin settings in wp_options
	update_option( 'de_plugin_enabled', true );
	update_option( 'de_plugin_title', 'Database Explorer' );
	update_option( 'de_plugin_version', '1.0.0' );

	error_log( 'Database Explorer plugin activated with sample data' );
}

/**
 * PLUGIN DEACTIVATION: Cleanup
 * Demonstrates: register_deactivation_hook
 */
register_deactivation_hook( __FILE__, 'de_plugin_deactivated' );

function de_plugin_deactivated() {
	// Remove plugin options
	delete_option( 'de_plugin_enabled' );
	delete_option( 'de_plugin_title' );
	delete_option( 'de_plugin_version' );

	error_log( 'Database Explorer plugin deactivated' );
}

/**
 * ADMIN MENU: Add database explorer to admin panel
 * Demonstrates: add_action on admin_menu, add_menu_page
 */
add_action( 'admin_menu', 'de_register_admin_menu' );

function de_register_admin_menu() {
	add_menu_page(
		'Database Explorer',
		'Database Explorer',
		'manage_options',
		'de-explorer',
		'de_render_admin_page',
		'dashicons-database',
		25
	);
}

/**
 * ADMIN PAGE RENDERING
 * Demonstrates: get_posts, get_post_meta, get_terms, get_users, get_option
 */
function de_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized' );
	}
	?>
	<div class="wrap">
		<h1>Database Explorer</h1>
		<p>This page demonstrates WordPress data structure and database queries.</p>

		<!-- WordPress Options -->
		<div class="card">
			<h2>WordPress Options (wp_options table)</h2>
			<p><strong>Plugin Title:</strong> <?php echo esc_html( get_option( 'de_plugin_title' ) ); ?></p>
			<p><strong>Plugin Version:</strong> <?php echo esc_html( get_option( 'de_plugin_version' ) ); ?></p>
			<p><strong>Enabled:</strong> <?php echo get_option( 'de_plugin_enabled' ) ? 'Yes' : 'No'; ?></p>
		</div>

		<!-- Posts with WP_Query -->
		<div class="card">
			<h2>Posts and Post Meta (wp_posts, wp_postmeta tables)</h2>
			<?php
			// Use WP_Query to retrieve posts with meta filtering
			$args = [
				'post_type'      => 'post',
				'posts_per_page' => -1,
				'meta_key'       => 'project_status',
				'meta_value'     => 'completed',
				'orderby'        => 'meta_value_num',
				'meta_key'       => 'project_priority',
				'order'          => 'ASC',
			];

			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				echo '<table class="wp-list-table widefat">';
				echo '<thead><tr><th>ID</th><th>Title</th><th>Status</th><th>Date</th><th>Priority</th><th>Tags</th></tr></thead><tbody>';

				while ( $query->have_posts() ) {
					$query->the_post();
					$post_id = get_the_ID();
					?>
					<tr>
						<td><?php echo esc_html( $post_id ); ?></td>
						<td><?php echo esc_html( get_the_title() ); ?></td>
						<td><?php echo esc_html( get_post_meta( $post_id, 'project_status', true ) ); ?></td>
						<td><?php echo esc_html( get_post_meta( $post_id, 'project_date', true ) ); ?></td>
						<td><?php echo esc_html( get_post_meta( $post_id, 'project_priority', true ) ); ?></td>
						<td><?php echo esc_html( get_post_meta( $post_id, 'project_tags', true ) ); ?></td>
					</tr>
					<?php
				}

				echo '</tbody></table>';
				wp_reset_postdata();
			} else {
				echo 'No posts found.';
			}
			?>
		</div>

		<!-- Taxonomies and Terms -->
		<div class="card">
			<h2>Taxonomies & Terms (wp_terms, wp_term_taxonomy, wp_term_relationships)</h2>
			<?php
			// Get all categories (terms)
			$terms = get_terms( [
				'taxonomy'   => 'category',
				'hide_empty' => false,
			] );

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				echo '<table class="wp-list-table widefat">';
				echo '<thead><tr><th>Term ID</th><th>Name</th><th>Slug</th><th>Description</th><th>Post Count</th></tr></thead><tbody>';

				foreach ( $terms as $term ) {
					?>
					<tr>
						<td><?php echo esc_html( $term->term_id ); ?></td>
						<td><?php echo esc_html( $term->name ); ?></td>
						<td><?php echo esc_html( $term->slug ); ?></td>
						<td><?php echo esc_html( $term->description ); ?></td>
						<td><?php echo esc_html( $term->count ); ?></td>
					</tr>
					<?php
				}

				echo '</tbody></table>';
			} else {
				echo 'No terms found.';
			}
			?>
		</div>

		<!-- Users and Capabilities -->
		<div class="card">
			<h2>Users & Capabilities (wp_users, wp_usermeta)</h2>
			<?php
			// Get all users
			$users = get_users();

			if ( ! empty( $users ) ) {
				echo '<table class="wp-list-table widefat">';
				echo '<thead><tr><th>User ID</th><th>Username</th><th>Email</th><th>Role</th><th>Can Edit Posts</th></tr></thead><tbody>';

				foreach ( $users as $user ) {
					$can_edit = $user->has_cap( 'edit_posts' ) ? 'Yes' : 'No';
					$role = isset( $user->roles[0] ) ? $user->roles[0] : 'none';
					?>
					<tr>
						<td><?php echo esc_html( $user->ID ); ?></td>
						<td><?php echo esc_html( $user->user_login ); ?></td>
						<td><?php echo esc_html( $user->user_email ); ?></td>
						<td><?php echo esc_html( $role ); ?></td>
						<td><?php echo esc_html( $can_edit ); ?></td>
					</tr>
					<?php
				}

				echo '</tbody></table>';
			} else {
				echo 'No users found.';
			}
			?>
		</div>

		<style>
			.card {
				background: white;
				border: 1px solid #ccc;
				border-radius: 5px;
				padding: 15px;
				margin: 15px 0;
			}
			.card h2 {
				margin-top: 0;
				border-bottom: 2px solid #0073aa;
				padding-bottom: 10px;
			}
		</style>
	</div>
	<?php
}
