<?php
/**
 * Base Migration Class for Task Manager
 *
 * Provides abstract interface and helper methods for all database migrations
 *
 * @package TaskManager\Migrations
 */

namespace TaskManager\Migrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract base class for database migrations
 *
 * Each migration represents a discrete set of database schema changes.
 * Migrations are applied in order based on version number.
 *
 * @since 1.1.0
 */
abstract class Migration_Base {
	/**
	 * Apply migration (upgrade)
	 *
	 * Called when upgrading from previous version to this migration's version.
	 * Must implement the schema changes required for this version.
	 *
	 * @return bool True if migration succeeded, false otherwise
	 */
	abstract public function up();

	/**
	 * Rollback migration (downgrade)
	 *
	 * Called when rolling back from this version to previous version.
	 * Must reverse all schema changes made in up().
	 *
	 * @return bool True if rollback succeeded, false otherwise
	 */
	abstract public function down();

	/**
	 * Get target version for this migration
	 *
	 * @return string Version number (e.g., '1.1.0')
	 */
	abstract public function version();

	/**
	 * Get migration description
	 *
	 * @return string Human-readable description of what this migration does
	 */
	abstract public function description();

	/**
	 * Add a column to a table
	 *
	 * Helper method for column additions
	 *
	 * @param string $table_name Table name (without prefix)
	 * @param string $column_name Column name
	 * @param string $definition Column definition (type, constraints, etc.)
	 *
	 * @return bool True if successful
	 */
	protected function add_column( $table_name, $column_name, $definition ) {
		global $wpdb;

		$table = $wpdb->prefix . $table_name;

		// Check if column already exists
		$column_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = %s AND COLUMN_NAME = %s",
				$wpdb->dbname,
				$column_name
			)
		);

		if ( ! empty( $column_exists ) ) {
			return true; // Column already exists, consider it success
		}

		// Add the column
		$result = $wpdb->query( "ALTER TABLE {$table} ADD COLUMN {$column_name} {$definition}" );

		return false !== $result;
	}

	/**
	 * Remove a column from a table
	 *
	 * Helper method for column removal
	 *
	 * @param string $table_name Table name (without prefix)
	 * @param string $column_name Column name
	 *
	 * @return bool True if successful
	 */
	protected function remove_column( $table_name, $column_name ) {
		global $wpdb;

		$table = $wpdb->prefix . $table_name;

		// Check if column exists
		$column_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = %s AND COLUMN_NAME = %s",
				$wpdb->dbname,
				$column_name
			)
		);

		if ( empty( $column_exists ) ) {
			return true; // Column doesn't exist, consider it success
		}

		// Remove the column
		$result = $wpdb->query( "ALTER TABLE {$table} DROP COLUMN {$column_name}" );

		return false !== $result;
	}

	/**
	 * Add an index to a table
	 *
	 * Helper method for index creation
	 *
	 * @param string $table_name Table name (without prefix)
	 * @param string $index_name Index name
	 * @param array  $columns Column names to include in index
	 *
	 * @return bool True if successful
	 */
	protected function add_index( $table_name, $index_name, $columns ) {
		global $wpdb;

		$table = $wpdb->prefix . $table_name;
		$cols  = implode( ',', $columns );

		// Check if index already exists
		$index_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_NAME = %s AND INDEX_NAME = %s",
				$wpdb->dbname,
				$index_name
			)
		);

		if ( ! empty( $index_exists ) ) {
			return true; // Index already exists, consider it success
		}

		// Create the index
		$result = $wpdb->query( "ALTER TABLE {$table} ADD INDEX {$index_name} ({$cols})" );

		return false !== $result;
	}

	/**
	 * Remove an index from a table
	 *
	 * Helper method for index removal
	 *
	 * @param string $table_name Table name (without prefix)
	 * @param string $index_name Index name
	 *
	 * @return bool True if successful
	 */
	protected function remove_index( $table_name, $index_name ) {
		global $wpdb;

		$table = $wpdb->prefix . $table_name;

		// Check if index exists
		$index_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_NAME = %s AND INDEX_NAME = %s",
				$wpdb->dbname,
				$index_name
			)
		);

		if ( empty( $index_exists ) ) {
			return true; // Index doesn't exist, consider it success
		}

		// Remove the index
		$result = $wpdb->query( "ALTER TABLE {$table} DROP INDEX {$index_name}" );

		return false !== $result;
	}

	/**
	 * Execute a raw SQL query
	 *
	 * Helper method for custom SQL operations
	 *
	 * @param string $sql SQL query
	 *
	 * @return bool True if successful
	 */
	protected function execute( $sql ) {
		global $wpdb;

		$result = $wpdb->query( $sql );

		return false !== $result;
	}
}
