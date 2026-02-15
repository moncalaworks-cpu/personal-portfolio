<?php
/**
 * Task Manager Activator Class
 *
 * Handles plugin activation and deactivation
 * Creates database table and manages capabilities
 *
 * @package TaskManager
 */

namespace TaskManager\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activator class for Task Manager plugin
 *
 * Manages plugin activation: creates custom table, sets up capabilities
 * Manages plugin deactivation: removes capabilities (table retained)
 */
class Activator {
	/**
	 * Activate plugin
	 *
	 * - Creates custom database table
	 * - Adds custom capabilities to admin and editor roles
	 * - Initializes plugin options
	 */
	public static function activate() {
		// Create database table
		self::create_table();

		// Add capabilities
		self::add_capabilities();

		// Initialize settings
		self::initialize_settings();

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Deactivate plugin
	 *
	 * - Removes custom capabilities
	 * - Table is retained for data persistence
	 */
	public static function deactivate() {
		// Remove capabilities
		self::remove_capabilities();

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Create custom database table for tasks
	 *
	 * Demonstrates $wpdb->query() for table creation
	 * Uses prepared statement pattern for safety
	 */
	private static function create_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'tm_tasks';
		$charset_collate = $wpdb->get_charset_collate();

		/**
		 * SQL for creating tasks table
		 *
		 * Columns:
		 * - id: Primary key, auto-increment
		 * - title: Task title (required)
		 * - description: Task description (optional)
		 * - status: Task status (todo, in_progress, done)
		 * - priority: Task priority (low, medium, high)
		 * - due_date: Task due date (optional)
		 * - created_by: User ID of task creator
		 * - created_at: Task creation timestamp
		 * - updated_at: Last update timestamp
		 */
		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			title varchar(255) NOT NULL,
			description longtext,
			status varchar(20) NOT NULL DEFAULT 'todo',
			priority varchar(20) NOT NULL DEFAULT 'medium',
			due_date date,
			created_by bigint(20) UNSIGNED NOT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			KEY status (status),
			KEY priority (priority),
			KEY created_by (created_by),
			KEY created_at (created_at)
		) {$charset_collate};";

		// Execute table creation
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Add custom capabilities to roles
	 *
	 * Creates capabilities: manage_tasks, create_tasks, edit_tasks, delete_tasks
	 * Assigns to admin and editor roles for demonstration
	 */
	private static function add_capabilities() {
		$admin_role  = get_role( 'administrator' );
		$editor_role = get_role( 'editor' );

		if ( ! $admin_role || ! $editor_role ) {
			return;
		}

		// Define capabilities
		$capabilities = [
			'manage_tasks' => 'Manage all tasks',
			'create_tasks' => 'Create tasks',
			'edit_tasks'   => 'Edit tasks',
			'delete_tasks' => 'Delete tasks',
		];

		// Add capabilities to admin (all capabilities)
		foreach ( $capabilities as $cap => $label ) {
			$admin_role->add_cap( $cap );
		}

		// Add capabilities to editor (all except manage_tasks)
		foreach ( array_diff_key( $capabilities, [ 'manage_tasks' => '' ] ) as $cap => $label ) {
			$editor_role->add_cap( $cap );
		}
	}

	/**
	 * Remove custom capabilities
	 *
	 * Called on plugin deactivation to clean up capabilities
	 */
	private static function remove_capabilities() {
		$admin_role  = get_role( 'administrator' );
		$editor_role = get_role( 'editor' );

		if ( ! $admin_role || ! $editor_role ) {
			return;
		}

		// Define capabilities to remove
		$capabilities = [ 'manage_tasks', 'create_tasks', 'edit_tasks', 'delete_tasks' ];

		// Remove from admin role
		foreach ( $capabilities as $cap ) {
			$admin_role->remove_cap( $cap );
		}

		// Remove from editor role
		foreach ( array_diff( $capabilities, [ 'manage_tasks' ] ) as $cap ) {
			$editor_role->remove_cap( $cap );
		}
	}

	/**
	 * Initialize plugin settings
	 *
	 * Stores default settings in wp_options and initializes database version
	 */
	private static function initialize_settings() {
		// Initialize plugin settings
		$existing_settings = get_option( 'tm_settings' );

		if ( ! $existing_settings ) {
			$default_settings = [
				'default_status'   => 'todo',
				'default_priority' => 'medium',
				'tasks_per_page'   => 20,
				'show_completed'   => true,
			];

			add_option( 'tm_settings', $default_settings );
		}

		// Initialize database version if not already set
		$existing_db_version = get_option( 'tm_db_version' );

		if ( ! $existing_db_version ) {
			// If this is a fresh install, set to current version
			// Otherwise, migrations will update it
			add_option( 'tm_db_version', '1.0.0' );
		}
	}
}
