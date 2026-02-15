<?php
/**
 * Migration 1.1.0 - Add completed_at column
 *
 * Adds a completed_at column to track when tasks are marked as done
 *
 * @package TaskManager\Migrations
 */

namespace TaskManager\Migrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Migration class for version 1.1.0
 *
 * Adds completed_at column to wp_tm_tasks table
 * Backfills completed_at for existing completed tasks
 * Creates index on completed_at for query optimization
 *
 * @since 1.1.0
 */
class Migration_110 extends Migration_Base {
	/**
	 * Apply migration to upgrade to 1.1.0
	 *
	 * @return bool True if successful
	 */
	public function up() {
		// Add completed_at column
		if ( ! $this->add_column( 'tm_tasks', 'completed_at', 'datetime NULL AFTER updated_at' ) ) {
			return false;
		}

		// Add index on completed_at for query optimization
		if ( ! $this->add_index( 'tm_tasks', 'completed_at', [ 'completed_at' ] ) ) {
			return false;
		}

		// Backfill completed_at for existing completed tasks
		if ( ! $this->backfill_completed_at() ) {
			return false;
		}

		return true;
	}

	/**
	 * Rollback migration to previous version
	 *
	 * @return bool True if successful
	 */
	public function down() {
		// Remove the completed_at column
		return $this->remove_column( 'tm_tasks', 'completed_at' );
	}

	/**
	 * Get target version for this migration
	 *
	 * @return string Version number
	 */
	public function version() {
		return '1.1.0';
	}

	/**
	 * Get migration description
	 *
	 * @return string Description of migration
	 */
	public function description() {
		return 'Add completed_at column to track task completion time';
	}

	/**
	 * Backfill completed_at for existing completed tasks
	 *
	 * Sets completed_at to updated_at for tasks with status='done'
	 *
	 * @return bool True if successful
	 */
	private function backfill_completed_at() {
		global $wpdb;

		$table = $wpdb->prefix . 'tm_tasks';

		// Update completed tasks to have completed_at set to their updated_at time
		$result = $wpdb->query(
			"UPDATE {$table}
			SET completed_at = updated_at
			WHERE status = 'done' AND completed_at IS NULL"
		);

		return false !== $result;
	}
}
