<?php
/**
 * Task Manager Logger Class
 *
 * Provides structured logging for debugging and monitoring
 *
 * @package TaskManager
 */

namespace TaskManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Logger class for structured logging
 *
 * Respects WordPress WP_DEBUG and WP_DEBUG_LOG settings.
 * Logs to wp-content/debug.log when enabled.
 *
 * Log levels:
 * - ERROR (1): Critical failures
 * - WARNING (2): Non-critical issues
 * - INFO (3): Important events
 * - DEBUG (4): Detailed tracing
 *
 * @since 1.1.0
 */
class Logger {
	/**
	 * Log level constants
	 */
	const ERROR   = 1;
	const WARNING = 2;
	const INFO    = 3;
	const DEBUG   = 4;

	/**
	 * Log level names
	 *
	 * @var array
	 */
	private static $level_names = [
		1 => 'ERROR',
		2 => 'WARNING',
		3 => 'INFO',
		4 => 'DEBUG',
	];

	/**
	 * Log an error message
	 *
	 * Critical failures that need immediate attention
	 *
	 * @param string $message Error message
	 * @param array  $context Optional context data
	 */
	public static function error( $message, $context = [] ) {
		self::log( self::ERROR, $message, $context );
	}

	/**
	 * Log a warning message
	 *
	 * Non-critical issues that should be addressed
	 *
	 * @param string $message Warning message
	 * @param array  $context Optional context data
	 */
	public static function warning( $message, $context = [] ) {
		self::log( self::WARNING, $message, $context );
	}

	/**
	 * Log an info message
	 *
	 * Important events in plugin lifecycle
	 *
	 * @param string $message Info message
	 * @param array  $context Optional context data
	 */
	public static function info( $message, $context = [] ) {
		self::log( self::INFO, $message, $context );
	}

	/**
	 * Log a debug message
	 *
	 * Detailed tracing for development
	 *
	 * @param string $message Debug message
	 * @param array  $context Optional context data
	 */
	public static function debug( $message, $context = [] ) {
		self::log( self::DEBUG, $message, $context );
	}

	/**
	 * Log a message at specified level
	 *
	 * @param int    $level Log level (ERROR, WARNING, INFO, DEBUG)
	 * @param string $message Log message
	 * @param array  $context Context data to include
	 */
	private static function log( $level, $message, $context = [] ) {
		// Check if logging is enabled
		if ( ! self::should_log( $level ) ) {
			return;
		}

		// Format the log message
		$formatted = self::format_message( $level, $message, $context );

		// Log to debug.log if enabled
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( $formatted );
		}
	}

	/**
	 * Format a log message with context
	 *
	 * @param int    $level Log level
	 * @param string $message Log message
	 * @param array  $context Context data
	 *
	 * @return string Formatted log message
	 */
	private static function format_message( $level, $message, $context = [] ) {
		$level_name = self::$level_names[ $level ] ?? 'UNKNOWN';
		$timestamp  = wp_date( 'Y-m-d H:i:s' );
		$user_id    = get_current_user_id();

		// Build base message
		$log_message = "[{$timestamp}] [TASK-MANAGER] [{$level_name}] {$message}";

		// Add context if provided
		if ( ! empty( $context ) ) {
			// Remove sensitive data from context
			$safe_context = self::sanitize_context( $context );
			$log_message .= ' | ' . wp_json_encode( $safe_context );
		}

		// Add user info if logged in
		if ( $user_id > 0 ) {
			$user         = get_user_by( 'ID', $user_id );
			$user_login   = $user ? $user->user_login : 'unknown';
			$log_message .= " | User: {$user_login} ({$user_id})";
		}

		// Add caller info for debug level
		if ( self::DEBUG === $level ) {
			$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 5 );
			if ( isset( $trace[3] ) ) {
				$file = basename( $trace[3]['file'] ?? '' );
				$line = $trace[3]['line'] ?? 0;
				$func = $trace[3]['function'] ?? 'unknown';
				$log_message .= " | {$file}:{$line} in {$func}()";
			}
		}

		return $log_message;
	}

	/**
	 * Sanitize context data to remove sensitive information
	 *
	 * @param array $context Raw context data
	 *
	 * @return array Sanitized context data
	 */
	private static function sanitize_context( $context ) {
		$sensitive_keys = [
			'password',
			'token',
			'secret',
			'api_key',
			'auth',
			'key',
		];

		$sanitized = [];

		foreach ( $context as $key => $value ) {
			// Remove sensitive keys
			if ( in_array( strtolower( $key ), $sensitive_keys, true ) ) {
				$sanitized[ $key ] = '***REDACTED***';
				continue;
			}

			// Don't log arrays/objects, just type
			if ( is_object( $value ) ) {
				$sanitized[ $key ] = '[Object: ' . get_class( $value ) . ']';
			} elseif ( is_array( $value ) ) {
				$sanitized[ $key ] = '[Array]';
			} else {
				$sanitized[ $key ] = $value;
			}
		}

		return $sanitized;
	}

	/**
	 * Check if messages at this level should be logged
	 *
	 * @param int $level Log level
	 *
	 * @return bool True if logging is enabled for this level
	 */
	private static function should_log( $level ) {
		// Always log errors and warnings
		if ( self::ERROR === $level || self::WARNING === $level ) {
			return defined( 'WP_DEBUG' ) && WP_DEBUG;
		}

		// Log info and debug only if WP_DEBUG enabled
		if ( self::INFO === $level || self::DEBUG === $level ) {
			return defined( 'WP_DEBUG' ) && WP_DEBUG;
		}

		return false;
	}
}
