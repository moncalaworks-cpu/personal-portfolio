<?php
/**
 * Analytics Dashboard Admin Pages Class
 *
 * Handles admin menu registration and dashboard rendering
 *
 * @package AnalyticsDashboard\Admin
 */

namespace AnalyticsDashboard\Admin;

use AnalyticsDashboard\Database;
use AnalyticsDashboard\Cache;

/**
 * Admin pages class for dashboard registration
 */
class AdminPages {

	/**
	 * Register admin menus
	 *
	 * @return void
	 */
	public static function register_menus() {
		add_menu_page(
			esc_html__( 'Analytics', 'analytics-dashboard' ),
			esc_html__( 'Analytics', 'analytics-dashboard' ),
			'view_analytics',
			'analytics-dashboard',
			[ __CLASS__, 'render_dashboard' ],
			'dashicons-chart-line',
			3  // Position after Dashboard
		);
	}

	/**
	 * Render analytics dashboard
	 *
	 * @return void
	 */
	public static function render_dashboard() {
		// Verify capability
		if ( ! current_user_can( 'view_analytics' ) ) {
			wp_die( esc_html__( 'You do not have permission to view analytics.', 'analytics-dashboard' ) );
		}

		// Get date range from request
		$date_range = 'all';
		if ( isset( $_GET['range'] ) ) {
			$range      = sanitize_text_field( wp_unslash( $_GET['range'] ) );
			$date_range = in_array( $range, [ '7', '30', 'all' ], true ) ? $range : 'all';
		}

		// Try to get cached statistics
		$stats = Cache::get_statistics( $date_range );

		// If cache miss, fetch fresh statistics
		if ( false === $stats ) {
			$db    = Database::get_instance();
			$stats = $db->get_statistics( [ 'date_range' => $date_range ] );

			// Cache for next request
			Cache::set_statistics( $date_range, $stats );
		}

		// Include template
		include AD_PLUGIN_DIR . 'admin/partials/dashboard.php';
	}
}
