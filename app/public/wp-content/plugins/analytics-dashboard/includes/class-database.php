<?php
/**
 * Analytics Dashboard Database Class
 *
 * Handles all database queries for analytics statistics
 *
 * @package AnalyticsDashboard
 */

namespace AnalyticsDashboard;

/**
 * Database singleton class for statistics queries
 */
class Database {

	/**
	 * Singleton instance
	 *
	 * @var Database
	 */
	private static $instance;

	/**
	 * WordPress database object
	 *
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * Get singleton instance
	 *
	 * @return Database
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Get statistics for dashboard
	 *
	 * @param array $args Arguments for filtering.
	 *               - date_range (int|string): Days back ('7', '30', or 'all').
	 *
	 * @return array Statistics array with keys:
	 *               - total_posts (int)
	 *               - total_comments (int)
	 *               - total_users (int)
	 *               - total_pages (int)
	 *               - avg_engagement (float)
	 */
	public function get_statistics( $args = [] ) {
		$date_range = isset( $args['date_range'] ) ? sanitize_text_field( $args['date_range'] ) : 'all';

		$stats = [
			'total_posts'     => 0,
			'total_comments'  => 0,
			'total_users'     => 0,
			'total_pages'     => 0,
			'avg_engagement'  => 0.0,
		];

		// Get post count
		$stats['total_posts'] = $this->get_posts_count( $date_range );

		// Get page count
		$stats['total_pages'] = $this->get_pages_count( $date_range );

		// Get comment count
		$stats['total_comments'] = $this->get_comments_count( $date_range );

		// Get user count (all time, not affected by date range)
		$stats['total_users'] = $this->get_users_count();

		// Calculate engagement
		if ( $stats['total_posts'] > 0 ) {
			$stats['avg_engagement'] = $stats['total_comments'] / $stats['total_posts'];
		}

		return $stats;
	}

	/**
	 * Get published posts count
	 *
	 * @param string|int $date_range Date range ('7', '30', 'all').
	 *
	 * @return int Post count
	 */
	private function get_posts_count( $date_range = 'all' ) {
		$count_posts = wp_count_posts( 'post' );

		if ( 'all' === $date_range ) {
			return (int) $count_posts->publish;
		}

		// Count posts within date range
		$days  = (int) $date_range;
		$date  = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
		$sql   = "SELECT COUNT(*) FROM {$this->wpdb->posts}
		          WHERE post_type = 'post'
		          AND post_status = 'publish'
		          AND post_date >= %s";
		$count = (int) $this->wpdb->get_var( $this->wpdb->prepare( $sql, $date ) );

		return $count;
	}

	/**
	 * Get published pages count
	 *
	 * @param string|int $date_range Date range ('7', '30', 'all').
	 *
	 * @return int Page count
	 */
	private function get_pages_count( $date_range = 'all' ) {
		$count_posts = wp_count_posts( 'page' );

		if ( 'all' === $date_range ) {
			return (int) $count_posts->publish;
		}

		// Count pages within date range
		$days  = (int) $date_range;
		$date  = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
		$sql   = "SELECT COUNT(*) FROM {$this->wpdb->posts}
		          WHERE post_type = 'page'
		          AND post_status = 'publish'
		          AND post_date >= %s";
		$count = (int) $this->wpdb->get_var( $this->wpdb->prepare( $sql, $date ) );

		return $count;
	}

	/**
	 * Get approved comments count
	 *
	 * @param string|int $date_range Date range ('7', '30', 'all').
	 *
	 * @return int Comment count
	 */
	private function get_comments_count( $date_range = 'all' ) {
		$args = [
			'count'      => true,
			'status'     => 'approve',
			'post_type'  => 'post',
		];

		if ( 'all' !== $date_range ) {
			$days                  = (int) $date_range;
			$date                  = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
			$args['date_query']    = [
				'after'     => $date,
				'inclusive' => true,
			];
		}

		return (int) get_comments( $args );
	}

	/**
	 * Get registered users count
	 *
	 * @return int User count
	 */
	private function get_users_count() {
		$user_count = count_users();
		return (int) $user_count['total_users'];
	}
}
