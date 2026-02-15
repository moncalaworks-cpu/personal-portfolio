<?php
/**
 * Task Manager Cache Class
 *
 * Provides caching layer for performance optimization
 *
 * @package TaskManager
 */

namespace TaskManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cache class for performance optimization
 *
 * Uses WordPress transients for persistent caching.
 * Transients work on any WordPress installation.
 *
 * @since 1.1.0
 */
class Cache {
	/**
	 * Cache group for all task manager caches
	 *
	 * @var string
	 */
	const GROUP = 'task-manager';

	/**
	 * Get value from cache
	 *
	 * @param string $key Cache key
	 * @param string $group Optional cache group
	 *
	 * @return mixed Cached value or false if not found
	 */
	public static function get( $key, $group = self::GROUP ) {
		$cache_key = self::build_key( $key, $group );

		// Log cache access in debug mode
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$value = get_transient( $cache_key );

			if ( false === $value ) {
				Logger::debug( 'Cache miss', [ 'key' => $key, 'group' => $group ] );
			} else {
				Logger::debug( 'Cache hit', [ 'key' => $key, 'group' => $group ] );
			}

			return $value;
		}

		return get_transient( $cache_key );
	}

	/**
	 * Set value in cache
	 *
	 * @param string $key Cache key
	 * @param mixed  $value Value to cache
	 * @param int    $expiration Expiration time in seconds
	 * @param string $group Optional cache group
	 *
	 * @return bool True if set successfully
	 */
	public static function set( $key, $value, $expiration, $group = self::GROUP ) {
		$cache_key = self::build_key( $key, $group );

		Logger::debug( 'Cache set', [
			'key'        => $key,
			'group'      => $group,
			'expiration' => $expiration,
		] );

		return set_transient( $cache_key, $value, $expiration );
	}

	/**
	 * Delete value from cache
	 *
	 * @param string $key Cache key
	 * @param string $group Optional cache group
	 *
	 * @return bool True if deleted successfully
	 */
	public static function delete( $key, $group = self::GROUP ) {
		$cache_key = self::build_key( $key, $group );

		Logger::debug( 'Cache delete', [ 'key' => $key, 'group' => $group ] );

		return delete_transient( $cache_key );
	}

	/**
	 * Clear all cache for task manager
	 *
	 * Useful for manual cache clearing in admin
	 *
	 * @return bool True if successful
	 */
	public static function flush() {
		global $wpdb;

		// Delete all transients with our group prefix
		$cache_prefix = self::build_key( '', self::GROUP );
		$cache_prefix = substr( $cache_prefix, 0, -1 ); // Remove trailing underscore

		$result = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				'%' . $wpdb->esc_like( $cache_prefix ) . '%'
			)
		);

		Logger::info( 'Cache flushed', [ 'affected_rows' => $result ] );

		return false !== $result;
	}

	/**
	 * Invalidate a specific task's cache
	 *
	 * Called when task is created, updated, or deleted
	 *
	 * @param int $task_id Task ID
	 *
	 * @return bool True if successful
	 */
	public static function invalidate_task( $task_id ) {
		// Delete cache for individual task
		self::delete( 'task_' . $task_id );

		// Delete general statistics cache (will be recalculated)
		self::delete( 'statistics' );

		// Delete recent tasks cache
		self::delete( 'recent_tasks' );

		Logger::debug( 'Task cache invalidated', [ 'task_id' => $task_id ] );

		return true;
	}

	/**
	 * Get cached statistics
	 *
	 * Returns cached task statistics with 1 hour TTL
	 *
	 * @return array|false Cached statistics or false
	 */
	public static function get_statistics() {
		return self::get( 'statistics' );
	}

	/**
	 * Set cached statistics
	 *
	 * Caches for 1 hour (3600 seconds)
	 *
	 * @param array $statistics Statistics data
	 *
	 * @return bool True if set successfully
	 */
	public static function set_statistics( $statistics ) {
		return self::set( 'statistics', $statistics, HOUR_IN_SECONDS );
	}

	/**
	 * Get cached recent tasks
	 *
	 * Returns cached recent tasks list with 5 minute TTL
	 *
	 * @return array|false Cached tasks or false
	 */
	public static function get_recent_tasks() {
		return self::get( 'recent_tasks' );
	}

	/**
	 * Set cached recent tasks
	 *
	 * Caches for 5 minutes (300 seconds)
	 *
	 * @param array $tasks Task data
	 *
	 * @return bool True if set successfully
	 */
	public static function set_recent_tasks( $tasks ) {
		return self::set( 'recent_tasks', $tasks, 5 * MINUTE_IN_SECONDS );
	}

	/**
	 * Build cache key with group prefix
	 *
	 * @param string $key Cache key
	 * @param string $group Cache group
	 *
	 * @return string Full cache key
	 */
	private static function build_key( $key, $group ) {
		return $group . '_' . $key;
	}

	/**
	 * Get cache statistics
	 *
	 * Returns information about cached items
	 *
	 * @return array Cache statistics
	 */
	public static function get_cache_stats() {
		global $wpdb;

		$cache_prefix = self::build_key( '', self::GROUP );
		$cache_prefix = substr( $cache_prefix, 0, -1 );

		// Count cached items
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s",
				'%' . $wpdb->esc_like( $cache_prefix ) . '%'
			)
		);

		return [
			'cached_items' => (int) $count,
			'group'        => self::GROUP,
		];
	}
}
