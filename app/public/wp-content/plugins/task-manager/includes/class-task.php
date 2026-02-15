<?php
/**
 * Task Entity Class
 *
 * Represents a single task in the Task Manager
 * Handles task data structure and validation
 *
 * @package TaskManager
 */

namespace TaskManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Task entity class
 *
 * Represents a single task with properties and methods
 */
class Task {
	/**
	 * Task ID
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Task title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Task description
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Task status (todo, in_progress, done)
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Task priority (low, medium, high)
	 *
	 * @var string
	 */
	public $priority;

	/**
	 * Task due date
	 *
	 * @var string
	 */
	public $due_date;

	/**
	 * User ID who created the task
	 *
	 * @var int
	 */
	public $created_by;

	/**
	 * Task creation timestamp
	 *
	 * @var string
	 */
	public $created_at;

	/**
	 * Last update timestamp
	 *
	 * @var string
	 */
	public $updated_at;

	/**
	 * Constructor
	 *
	 * Can accept object or array with task data
	 *
	 * @param object|array $data Task data from database or form.
	 */
	public function __construct( $data = [] ) {
		$data = (array) $data;

		$this->id          = isset( $data['id'] ) ? (int) $data['id'] : 0;
		$this->title       = isset( $data['title'] ) ? (string) $data['title'] : '';
		$this->description = isset( $data['description'] ) ? (string) $data['description'] : '';
		$this->status      = isset( $data['status'] ) ? (string) $data['status'] : 'todo';
		$this->priority    = isset( $data['priority'] ) ? (string) $data['priority'] : 'medium';
		$this->due_date    = isset( $data['due_date'] ) ? (string) $data['due_date'] : '';
		$this->created_by  = isset( $data['created_by'] ) ? (int) $data['created_by'] : get_current_user_id();
		$this->created_at  = isset( $data['created_at'] ) ? (string) $data['created_at'] : current_time( 'mysql' );
		$this->updated_at  = isset( $data['updated_at'] ) ? (string) $data['updated_at'] : current_time( 'mysql' );
	}

	/**
	 * Convert task to array
	 *
	 * Useful for serialization and passing to database
	 *
	 * @return array Task data as associative array
	 */
	public function to_array() {
		return [
			'id'          => $this->id,
			'title'       => $this->title,
			'description' => $this->description,
			'status'      => $this->status,
			'priority'    => $this->priority,
			'due_date'    => $this->due_date,
			'created_by'  => $this->created_by,
			'created_at'  => $this->created_at,
			'updated_at'  => $this->updated_at,
		];
	}

	/**
	 * Get human-readable status label
	 *
	 * @return string Status label
	 */
	public function get_status_label() {
		$labels = [
			'todo'        => __( 'To Do', TM_TEXT_DOMAIN ),
			'in_progress' => __( 'In Progress', TM_TEXT_DOMAIN ),
			'done'        => __( 'Done', TM_TEXT_DOMAIN ),
		];

		return isset( $labels[ $this->status ] ) ? $labels[ $this->status ] : $this->status;
	}

	/**
	 * Get human-readable priority label
	 *
	 * @return string Priority label
	 */
	public function get_priority_label() {
		$labels = [
			'low'    => __( 'Low', TM_TEXT_DOMAIN ),
			'medium' => __( 'Medium', TM_TEXT_DOMAIN ),
			'high'   => __( 'High', TM_TEXT_DOMAIN ),
		];

		return isset( $labels[ $this->priority ] ) ? $labels[ $this->priority ] : $this->priority;
	}

	/**
	 * Get CSS class for status badge
	 *
	 * @return string CSS class name
	 */
	public function get_status_badge_class() {
		$classes = [
			'todo'        => 'status-todo',
			'in_progress' => 'status-in-progress',
			'done'        => 'status-done',
		];

		return isset( $classes[ $this->status ] ) ? $classes[ $this->status ] : 'status-unknown';
	}

	/**
	 * Get CSS class for priority badge
	 *
	 * @return string CSS class name
	 */
	public function get_priority_badge_class() {
		$classes = [
			'low'    => 'priority-low',
			'medium' => 'priority-medium',
			'high'   => 'priority-high',
		];

		return isset( $classes[ $this->priority ] ) ? $classes[ $this->priority ] : 'priority-unknown';
	}

	/**
	 * Get creator user object
	 *
	 * @return \WP_User User object
	 */
	public function get_creator() {
		return get_user_by( 'id', $this->created_by );
	}

	/**
	 * Get formatted due date
	 *
	 * @param string $format Date format string (default: WordPress date_format).
	 * @return string Formatted date
	 */
	public function get_formatted_due_date( $format = '' ) {
		if ( ! $this->due_date ) {
			return '';
		}

		if ( ! $format ) {
			$format = get_option( 'date_format' );
		}

		return date_format( date_create( $this->due_date ), $format );
	}

	/**
	 * Check if task is overdue
	 *
	 * @return bool True if due date has passed
	 */
	public function is_overdue() {
		if ( ! $this->due_date || 'done' === $this->status ) {
			return false;
		}

		$due = new \DateTime( $this->due_date );
		$now = new \DateTime();

		return $due < $now;
	}
}
