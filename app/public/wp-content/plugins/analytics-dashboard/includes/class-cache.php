<?php
/**
 * Analytics Dashboard Cache Class
 *
 * Handles caching of statistics using WordPress transients
 *
 * @package AnalyticsDashboard
 */

namespace AnalyticsDashboard;

/**
 * Cache class for statistics caching
 */
class Cache {

	/**
	 * Cache group prefix
	 *
	 * @const string
	 */
	const GROUP = 'analytics_dashboard';

	/**
	 * Statistics cache TTL (1 hour)
	 *
	 * @const int
	 */
	const STATS_TTL = HOUR_IN_SECONDS;

	/**
	 * Get cached statistics
	 *
	 * @param string $date_range Date range ('7', '30', 'all').
	 *
	 * @return array|false Cached statistics or false if cache miss
	 */
	public static function get_statistics( $date_range = 'all' ) {
		$cache_key = self::build_key( 'stats_' . sanitize_text_field( $date_range ) );
		return get_transient( $cache_key );
	}

	/**
	 * Set cached statistics
	 *
	 * @param string $date_range Date range ('7', '30', 'all').
	 * @param array  $stats      Statistics array.
	 *
	 * @return bool True if set, false otherwise
	 */
	public static function set_statistics( $date_range, $stats ) {
		$cache_key = self::build_key( 'stats_' . sanitize_text_field( $date_range ) );
		return set_transient( $cache_key, $stats, self::STATS_TTL );
	}

	/**
	 * Invalidate all cached statistics
	 *
	 * Called when posts, comments, or users change
	 *
	 * @return void
	 */
	public static function invalidate_all() {
		// Delete all cached statistics
		foreach ( [ '7', '30', 'all' ] as $range ) {
			$cache_key = self::build_key( 'stats_' . $range );
			delete_transient( $cache_key );
		}
	}

	/**
	 * Build cache key with group prefix
	 *
	 * @param string $key Cache key.
	 *
	 * @return string Prefixed cache key
	 */
	private static function build_key( $key ) {
		return self::GROUP . '_' . $key;
	}

	/**
	 * Get cache statistics
	 *
	 * Useful for debugging cache effectiveness
	 *
	 * @return array Cache info
	 */
	public static function get_cache_info() {
		global $wpdb;

		$prefix = self::GROUP . '%';
		$sql    = $wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->options}
			 WHERE option_name LIKE %s",
			$prefix
		);
		$count  = (int) $wpdb->get_var( $sql );

		return [
			'group'       => self::GROUP,
			'cached_keys' => $count,
			'ttl'         => self::STATS_TTL,
		];
	}
}
