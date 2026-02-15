<?php
/**
 * Task Manager Database Class
 *
 * Handles all database operations using $wpdb with prepared statements
 * Demonstrates:
 * - Direct $wpdb queries with sanitization
 * - Prepared statements for SQL injection prevention
 * - Custom table operations
 * - Singleton pattern for database instance
 *
 * @package TaskManager
 */

namespace TaskManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Database class for Task Manager operations
 *
 * Uses singleton pattern to maintain single database connection
 * All queries use prepared statements for security
 */
class Database {
	/**
	 * Singleton instance
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * WordPress database object
	 *
	 * @var \wpdb
	 */
	private $wpdb;

	/**
	 * Custom table name (with prefix)
	 *
	 * @var string
	 */
	private $tasks_table;

	/**
	 * Constructor
	 *
	 * Private to enforce singleton pattern
	 */
	private function __construct() {
		global $wpdb;
		$this->wpdb        = $wpdb;
		$this->tasks_table = $wpdb->prefix . 'tm_tasks';
	}

	/**
	 * Get singleton instance
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get table name
	 *
	 * @return string Custom tasks table name with prefix
	 */
	public function get_table_name() {
		return $this->tasks_table;
	}

	/**
	 * Create a new task
	 *
	 * @param array $task_data Task data including title, description, status, priority, due_date, created_by.
	 * @return int|false Task ID on success, false on failure
	 */
	public function create_task( $task_data ) {
		// Sanitize input data
		require_once TM_PLUGIN_DIR . 'security/class-validator.php';
		$sanitized = Security\Validator::sanitize_task( $task_data );

		// Prepare insert data
		$insert_data = [
			'title'       => $sanitized['title'],
			'description' => $sanitized['description'],
			'status'      => $sanitized['status'],
			'priority'    => $sanitized['priority'],
			'due_date'    => $sanitized['due_date'],
			'created_by'  => $sanitized['created_by'],
			'created_at'  => current_time( 'mysql' ),
			'updated_at'  => current_time( 'mysql' ),
		];

		// Build prepared statement
		$result = $this->wpdb->insert(
			$this->tasks_table,
			$insert_data,
			[
				'%s', // title
				'%s', // description
				'%s', // status
				'%s', // priority
				'%s', // due_date
				'%d', // created_by
				'%s', // created_at
				'%s', // updated_at
			]
		);

		return $result ? $this->wpdb->insert_id : false;
	}

	/**
	 * Get tasks with optional filters
	 *
	 * @param array $args Query arguments:
	 *   - status (string|array): Filter by status
	 *   - priority (string|array): Filter by priority
	 *   - created_by (int): Filter by creator
	 *   - orderby (string): Column to order by (default: created_at)
	 *   - order (string): ASC or DESC (default: DESC)
	 *   - limit (int): Number of results
	 *   - offset (int): Query offset.
	 * @return array Array of Task objects
	 */
	public function get_tasks( $args = [] ) {
		// Default arguments
		$defaults = [
			'status'     => null,
			'priority'   => null,
			'created_by' => null,
			'orderby'    => 'created_at',
			'order'      => 'DESC',
			'limit'      => 20,
			'offset'     => 0,
		];

		$args = wp_parse_args( $args, $defaults );

		// Start building query
		$sql   = "SELECT * FROM {$this->tasks_table} WHERE 1=1";
		$where = [];

		// Add status filter
		if ( ! empty( $args['status'] ) ) {
			if ( is_array( $args['status'] ) ) {
				$placeholders = implode( ',', array_fill( 0, count( $args['status'] ), '%s' ) );
				$where[]      = $this->wpdb->prepare( "status IN ($placeholders)", $args['status'] );
			} else {
				$where[] = $this->wpdb->prepare( 'status = %s', $args['status'] );
			}
		}

		// Add priority filter
		if ( ! empty( $args['priority'] ) ) {
			if ( is_array( $args['priority'] ) ) {
				$placeholders = implode( ',', array_fill( 0, count( $args['priority'] ), '%s' ) );
				$where[]      = $this->wpdb->prepare( "priority IN ($placeholders)", $args['priority'] );
			} else {
				$where[] = $this->wpdb->prepare( 'priority = %s', $args['priority'] );
			}
		}

		// Add created_by filter
		if ( ! empty( $args['created_by'] ) ) {
			$where[] = $this->wpdb->prepare( 'created_by = %d', $args['created_by'] );
		}

		// Combine where clauses
		if ( ! empty( $where ) ) {
			$sql .= ' AND ' . implode( ' AND ', $where );
		}

		// Add ordering - validate to prevent SQL injection
		$valid_orderby = [ 'id', 'title', 'status', 'priority', 'due_date', 'created_at', 'updated_at' ];
		$orderby       = in_array( $args['orderby'], $valid_orderby, true ) ? $args['orderby'] : 'created_at';
		$order         = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';

		$sql .= " ORDER BY {$orderby} {$order}";

		// Add limit and offset
		$sql .= $this->wpdb->prepare( ' LIMIT %d OFFSET %d', $args['limit'], $args['offset'] );

		// Execute query
		$results = $this->wpdb->get_results( $sql, OBJECT );

		// Convert to Task objects
		$tasks = [];
		if ( ! empty( $results ) ) {
			foreach ( $results as $row ) {
				$tasks[] = new Task( $row );
			}
		}

		return $tasks;
	}

	/**
	 * Get single task by ID
	 *
	 * @param int $task_id Task ID to retrieve.
	 * @return Task|null Task object or null if not found
	 */
	public function get_task( $task_id ) {
		$sql = $this->wpdb->prepare(
			"SELECT * FROM {$this->tasks_table} WHERE id = %d",
			$task_id
		);

		$result = $this->wpdb->get_row( $sql, OBJECT );

		return $result ? new Task( $result ) : null;
	}

	/**
	 * Update a task
	 *
	 * @param int   $task_id Task ID to update.
	 * @param array $task_data Partial or complete task data.
	 * @return bool True on success, false on failure
	 */
	public function update_task( $task_id, $task_data ) {
		// Sanitize input data
		require_once TM_PLUGIN_DIR . 'security/class-validator.php';
		$sanitized = Security\Validator::sanitize_task( $task_data );

		// Prepare update data
		$update_data = [
			'title'       => $sanitized['title'],
			'description' => $sanitized['description'],
			'status'      => $sanitized['status'],
			'priority'    => $sanitized['priority'],
			'due_date'    => $sanitized['due_date'],
			'updated_at'  => current_time( 'mysql' ),
		];

		// Perform update
		$result = $this->wpdb->update(
			$this->tasks_table,
			$update_data,
			[ 'id' => $task_id ],
			[
				'%s', // title
				'%s', // description
				'%s', // status
				'%s', // priority
				'%s', // due_date
				'%s', // updated_at
			],
			[ '%d' ] // where format
		);

		return false !== $result;
	}

	/**
	 * Delete a task
	 *
	 * @param int $task_id Task ID to delete.
	 * @return bool True on success, false on failure
	 */
	public function delete_task( $task_id ) {
		$result = $this->wpdb->delete(
			$this->tasks_table,
			[ 'id' => $task_id ],
			[ '%d' ]
		);

		return false !== $result;
	}

	/**
	 * Count tasks by status
	 *
	 * @return array Array of status => count pairs
	 */
	public function get_task_statistics() {
		$sql = "SELECT status, COUNT(*) as count FROM {$this->tasks_table} GROUP BY status";

		$results = $this->wpdb->get_results( $sql, OBJECT );

		$stats = [
			'total'       => 0,
			'todo'        => 0,
			'in_progress' => 0,
			'done'        => 0,
		];

		if ( ! empty( $results ) ) {
			foreach ( $results as $row ) {
				$stats[ $row->status ] = (int) $row->count;
				$stats['total']       += (int) $row->count;
			}
		}

		return $stats;
	}

	/**
	 * Get recent tasks
	 *
	 * @param int $limit Number of recent tasks to retrieve.
	 * @return array Array of Task objects
	 */
	public function get_recent_tasks( $limit = 5 ) {
		return $this->get_tasks(
			[
				'orderby' => 'created_at',
				'order'   => 'DESC',
				'limit'   => $limit,
			]
		);
	}
}
