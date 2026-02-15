<?php
/**
 * Task Manager Migrator Class
 *
 * Manages database migrations and version tracking
 *
 * @package TaskManager
 */

namespace TaskManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Migrator class for managing database migrations
 *
 * Tracks database schema version separately from plugin version.
 * Runs pending migrations on plugin activation.
 *
 * @since 1.1.0
 */
class Migrator {
	/**
	 * Database version option key
	 *
	 * @var string
	 */
	const DB_VERSION_OPTION = 'tm_db_version';

	/**
	 * Get current installed database version
	 *
	 * @return string Current database version
	 */
	public static function get_installed_version() {
		return get_option( self::DB_VERSION_OPTION, '1.0.0' );
	}

	/**
	 * Get current plugin version
	 *
	 * @return string Current plugin version
	 */
	public static function get_current_version() {
		return defined( 'TM_VERSION' ) ? TM_VERSION : '1.0.0';
	}

	/**
	 * Check if database needs upgrading
	 *
	 * @return bool True if installed version differs from current version
	 */
	public static function needs_upgrade() {
		$installed = self::get_installed_version();
		$current   = self::get_current_version();

		return version_compare( $installed, $current, '<' );
	}

	/**
	 * Run all pending migrations
	 *
	 * Executes migrations in order from current version to target version.
	 * Updates database version after each successful migration.
	 *
	 * @return array Status array with 'success' and 'messages' keys
	 */
	public static function run_migrations() {
		$installed_version = self::get_installed_version();
		$current_version   = self::get_current_version();
		$messages          = [];
		$success           = true;

		// Get all available migrations
		$migrations = self::get_pending_migrations();

		if ( empty( $migrations ) ) {
			return [
				'success'  => true,
				'messages' => [ 'Database is up to date.' ],
			];
		}

		// Run each pending migration in order
		foreach ( $migrations as $migration_class ) {
			try {
				$migration = new $migration_class();
				$version   = $migration->version();

				if ( ! $migration->up() ) {
					$messages[] = "Migration to {$version} failed";
					$success    = false;
					break;
				}

				// Update database version
				update_option( self::DB_VERSION_OPTION, $version );
				$messages[] = "Successfully migrated to {$version}";

			} catch ( Exception $e ) {
				$messages[] = "Migration error: " . $e->getMessage();
				$success    = false;
				break;
			}
		}

		return [
			'success'  => $success,
			'messages' => $messages,
		];
	}

	/**
	 * Get list of pending migrations
	 *
	 * Returns migration classes in order that should be applied.
	 *
	 * @return array Array of migration class names
	 */
	private static function get_pending_migrations() {
		$installed = self::get_installed_version();

		// All available migrations in order
		$all_migrations = [
			'1.1.0' => 'TaskManager\Migrations\Migration_110',
		];

		$pending = [];

		foreach ( $all_migrations as $version => $class ) {
			if ( version_compare( $installed, $version, '<' ) ) {
				$pending[] = $class;
			}
		}

		return $pending;
	}

	/**
	 * Get all available migrations with metadata
	 *
	 * Useful for displaying migration history or status
	 *
	 * @return array Array of migration metadata
	 */
	public static function get_available_migrations() {
		$migrations = [
			'1.1.0' => [
				'version'     => '1.1.0',
				'class'       => 'TaskManager\Migrations\Migration_110',
				'description' => 'Add completed_at column to track task completion time',
			],
		];

		return $migrations;
	}

	/**
	 * Get migration history
	 *
	 * Returns information about migrations that have been applied
	 *
	 * @return array Array of applied migrations
	 */
	public static function get_migration_history() {
		$installed_version = self::get_installed_version();
		$all_migrations    = self::get_available_migrations();
		$history           = [];

		foreach ( $all_migrations as $version => $info ) {
			if ( version_compare( $installed_version, $version, '>=' ) ) {
				$history[ $version ] = $info;
			}
		}

		return $history;
	}
}
