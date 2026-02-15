<?php
/**
 * Task Manager Plugin
 *
 * Demonstrates advanced WordPress plugin development:
 * - Class-based architecture with namespacing
 * - Custom database table with $wpdb operations
 * - Settings API implementation
 * - Admin pages and forms with security
 * - Custom capabilities and user roles
 *
 * @wordpress-plugin
 * Plugin Name: Task Manager
 * Version: 1.0.0
 * Description: A custom task management system demonstrating advanced WordPress plugin development
 * Author: Learning Project
 * License: GPL v2 or later
 * Text Domain: task-manager
 * Domain Path: /languages
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'TM_VERSION', '1.0.0' );
define( 'TM_PLUGIN_FILE', __FILE__ );
define( 'TM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TM_TEXT_DOMAIN', 'task-manager' );

/**
 * PSR-4 Autoloader for Task Manager classes
 *
 * Maps namespace to directory structure:
 * TaskManager\Core\Activator => includes/class-activator.php
 * TaskManager\Admin\AdminPages => admin/class-admin-pages.php
 */
spl_autoload_register(
	function ( $class ) {
		$prefix   = 'TaskManager\\';
		$base_dir = TM_PLUGIN_DIR . 'includes/';

		// Check if class uses our namespace
		if ( strpos( $class, $prefix ) !== 0 ) {
			return;
		}

		// Remove namespace prefix
		$relative_class = substr( $class, strlen( $prefix ) );

		// Convert namespace to path
		// TaskManager\Core\Activator => Core/Activator
		$parts = explode( '\\', $relative_class );

		// Determine subdirectory based on first part
		if ( 'Admin' === $parts[0] ) {
			$base_dir = TM_PLUGIN_DIR . 'admin/';
		} elseif ( 'Security' === $parts[0] ) {
			$base_dir = TM_PLUGIN_DIR . 'security/';
		}

		// Remove first part (namespace level)
		array_shift( $parts );

		// Build file path: Class => class-.php
		$file_name = 'class-' . strtolower( str_replace( '_', '-', array_pop( $parts ) ) ) . '.php';
		$file_path = $base_dir . implode( '/', array_map( 'strtolower', $parts ) ) . ( $parts ? '/' : '' ) . $file_name;

		// Require file if it exists
		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}
	}
);

// Load and initialize plugin
require_once TM_PLUGIN_DIR . 'includes/class-database.php';
require_once TM_PLUGIN_DIR . 'includes/class-task.php';

/**
 * Initialize plugin on plugins_loaded hook
 *
 * Ensures all WordPress functions and plugins are loaded before initialization
 */
add_action(
	'plugins_loaded',
	function () {
		// Initialize database
		TaskManager\Database::get_instance();
	}
);

/**
 * Plugin activation hook
 *
 * Creates database table, adds capabilities, initializes settings
 */
register_activation_hook(
	TM_PLUGIN_FILE,
	function () {
		require_once TM_PLUGIN_DIR . 'includes/class-activator.php';
		TaskManager\Core\Activator::activate();
	}
);

/**
 * Plugin deactivation hook
 *
 * Removes custom capabilities (table retained for data persistence)
 */
register_deactivation_hook(
	TM_PLUGIN_FILE,
	function () {
		require_once TM_PLUGIN_DIR . 'includes/class-activator.php';
		TaskManager\Core\Activator::deactivate();
	}
);

/**
 * Plugin uninstall hook
 *
 * Drops database table and removes all plugin data
 * Defined in separate uninstall.php file for security
 */
register_uninstall_hook( TM_PLUGIN_FILE, 'tm_uninstall' );

/**
 * Initialize admin interface
 *
 * Loads admin classes only on admin pages
 */
if ( is_admin() ) {
	add_action(
		'admin_menu',
		function () {
			require_once TM_PLUGIN_DIR . 'admin/class-admin-pages.php';
			TaskManager\Admin\AdminPages::register_menus();
		}
	);

	add_action(
		'admin_init',
		function () {
			require_once TM_PLUGIN_DIR . 'admin/class-settings.php';
			TaskManager\Admin\Settings::register();
		}
	);

	add_action(
		'admin_enqueue_scripts',
		function () {
			wp_enqueue_style(
				'task-manager-admin',
				TM_PLUGIN_URL . 'assets/css/admin-styles.css',
				[],
				TM_VERSION
			);
			wp_enqueue_script(
				'task-manager-admin',
				TM_PLUGIN_URL . 'assets/js/admin-scripts.js',
				[ 'jquery' ],
				TM_VERSION,
				true
			);
		}
	);
}
