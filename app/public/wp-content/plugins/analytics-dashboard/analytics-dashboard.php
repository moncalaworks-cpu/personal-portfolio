<?php
/**
 * Analytics Dashboard Plugin
 *
 * Provides WordPress admin dashboard with analytics and KPI metrics
 *
 * @wordpress-plugin
 * Plugin Name: Analytics Dashboard
 * Version: 1.0.0
 * Description: Admin dashboard with analytics, KPIs, and engagement metrics
 * Author: Learning Project
 * License: GPL v2 or later
 * Text Domain: analytics-dashboard
 * Domain Path: /languages
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'AD_VERSION', '1.0.0' );
define( 'AD_PLUGIN_FILE', __FILE__ );
define( 'AD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'AD_TEXT_DOMAIN', 'analytics-dashboard' );

/**
 * PSR-4 Autoloader for Analytics Dashboard classes
 *
 * Maps namespace to directory structure:
 * AnalyticsDashboard\\Database => includes/class-database.php
 * AnalyticsDashboard\\Cache => includes/class-cache.php
 * AnalyticsDashboard\\Admin\\AdminPages => admin/class-admin-pages.php
 */
spl_autoload_register(
	function ( $class ) {
		$prefix   = 'AnalyticsDashboard\\';
		$base_dir = AD_PLUGIN_DIR . 'includes/';

		// Check if class uses our namespace
		if ( strpos( $class, $prefix ) !== 0 ) {
			return;
		}

		// Remove namespace prefix
		$relative_class = substr( $class, strlen( $prefix ) );

		// Convert namespace to path
		// AnalyticsDashboard\\Admin\\AdminPages => Admin/AdminPages
		$parts = explode( '\\', $relative_class );

		// Determine subdirectory based on first part
		if ( 'Admin' === $parts[0] ) {
			$base_dir = AD_PLUGIN_DIR . 'admin/';
		}

		// Remove first part (namespace level)
		array_shift( $parts );

		// Build file path: AdminPages => class-admin-pages.php
		$file_name = 'class-' . strtolower( str_replace( '_', '-', array_pop( $parts ) ) ) . '.php';
		$file_path = $base_dir . implode( '/', array_map( 'strtolower', $parts ) ) . ( $parts ? '/' : '' ) . $file_name;

		// Require file if it exists
		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}
	}
);

/**
 * Check plugin requirements
 *
 * Verifies that WordPress version meets minimum requirements
 */
function ad_check_requirements() {
	$min_wordpress = '5.8.0';

	global $wp_version;
	if ( version_compare( $wp_version, $min_wordpress, '<' ) ) {
		add_action(
			'admin_notices',
			function () use ( $min_wordpress ) {
				echo wp_kses_post(
					'<div class="notice notice-error"><p>' .
					sprintf(
						__( 'Analytics Dashboard plugin requires WordPress %s or higher. You are running WordPress %s.', 'analytics-dashboard' ),
						$min_wordpress,
						get_bloginfo( 'version' )
					) .
					'</p></div>'
				);
			}
		);
		return false;
	}

	return true;
}

// Check requirements on plugin load
if ( ! ad_check_requirements() ) {
	add_action(
		'admin_init',
		function () {
			deactivate_plugins( plugin_basename( AD_PLUGIN_FILE ) );
		}
	);
	return;
}

/**
 * Plugin activation hook
 *
 * Adds custom capabilities for analytics access
 */
register_activation_hook(
	AD_PLUGIN_FILE,
	function () {
		// Add capability to admin role
		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->add_cap( 'view_analytics' );
		}
	}
);

/**
 * Plugin deactivation hook
 *
 * Removes custom capabilities
 */
register_deactivation_hook(
	AD_PLUGIN_FILE,
	function () {
		// Remove capability from admin role
		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->remove_cap( 'view_analytics' );
		}
	}
);

/**
 * Initialize admin interface
 *
 * Loads admin classes only on admin pages
 */
if ( is_admin() ) {
	add_action(
		'admin_menu',
		function () {
			require_once AD_PLUGIN_DIR . 'admin/class-admin-pages.php';
			AnalyticsDashboard\Admin\AdminPages::register_menus();
		}
	);

	add_action(
		'admin_enqueue_scripts',
		function () {
			wp_enqueue_style(
				'analytics-dashboard-admin',
				AD_PLUGIN_URL . 'assets/css/admin-styles.css',
				[],
				AD_VERSION
			);
			wp_enqueue_script(
				'analytics-dashboard-admin',
				AD_PLUGIN_URL . 'assets/js/admin-scripts.js',
				[ 'jquery' ],
				AD_VERSION,
				true
			);
		}
	);
}

/**
 * Setup cache invalidation hooks
 *
 * Clears cached statistics when content changes
 */
add_action( 'save_post', [ 'AnalyticsDashboard\\Cache', 'invalidate_all' ] );
add_action( 'wp_insert_comment', [ 'AnalyticsDashboard\\Cache', 'invalidate_all' ] );
add_action( 'user_register', [ 'AnalyticsDashboard\\Cache', 'invalidate_all' ] );
