<?php
/**
 * Task Manager Validator Class
 *
 * Handles input validation and sanitization
 * Demonstrates:
 * - Input sanitization best practices
 * - Data validation
 * - Security checks
 *
 * @package TaskManager\Security
 */

namespace TaskManager\Security;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validator class for input sanitization and validation
 */
class Validator {
	/**
	 * Valid task statuses
	 *
	 * @var array
	 */
	const VALID_STATUSES = [ 'todo', 'in_progress', 'done' ];

	/**
	 * Valid task priorities
	 *
	 * @var array
	 */
	const VALID_PRIORITIES = [ 'low', 'medium', 'high' ];

	/**
	 * Sanitize task data
	 *
	 * Validates and sanitizes all task fields to prevent security issues
	 *
	 * @param array $data Raw task data from form or request.
	 * @return array Sanitized task data
	 */
	public static function sanitize_task( $data ) {
		$data = (array) $data;

		return [
			'title'       => self::sanitize_title( $data['title'] ?? '' ),
			'description' => self::sanitize_description( $data['description'] ?? '' ),
			'status'      => self::sanitize_status( $data['status'] ?? 'todo' ),
			'priority'    => self::sanitize_priority( $data['priority'] ?? 'medium' ),
			'due_date'    => self::sanitize_date( $data['due_date'] ?? '' ),
			'created_by'  => self::sanitize_user_id( $data['created_by'] ?? 0 ),
		];
	}

	/**
	 * Sanitize task title
	 *
	 * Removes HTML tags and trims whitespace
	 *
	 * @param string $title Raw title from form.
	 * @return string Sanitized title
	 */
	public static function sanitize_title( $title ) {
		// Remove HTML tags
		$title = wp_strip_all_tags( $title );

		// Trim whitespace
		$title = trim( $title );

		// Limit length
		$title = substr( $title, 0, 255 );

		// Ensure not empty
		if ( empty( $title ) ) {
			$title = __( 'Untitled Task', TM_TEXT_DOMAIN );
		}

		return $title;
	}

	/**
	 * Sanitize task description
	 *
	 * Allows safe HTML (p, strong, em, br, etc.)
	 *
	 * @param string $description Raw description from form.
	 * @return string Sanitized description
	 */
	public static function sanitize_description( $description ) {
		// Allow safe HTML tags
		$allowed_html = [
			'p'      => [],
			'br'     => [],
			'strong' => [],
			'em'     => [],
			'b'      => [],
			'i'      => [],
			'u'      => [],
			'ul'     => [],
			'ol'     => [],
			'li'     => [],
			'a'      => [ 'href' => true, 'title' => true ],
		];

		return wp_kses_post( $description );
	}

	/**
	 * Sanitize task status
	 *
	 * Validates against allowed statuses
	 *
	 * @param string $status Raw status from form.
	 * @return string Valid status
	 */
	public static function sanitize_status( $status ) {
		$status = strtolower( trim( $status ) );

		if ( ! in_array( $status, self::VALID_STATUSES, true ) ) {
			$status = 'todo';
		}

		return $status;
	}

	/**
	 * Sanitize task priority
	 *
	 * Validates against allowed priorities
	 *
	 * @param string $priority Raw priority from form.
	 * @return string Valid priority
	 */
	public static function sanitize_priority( $priority ) {
		$priority = strtolower( trim( $priority ) );

		if ( ! in_array( $priority, self::VALID_PRIORITIES, true ) ) {
			$priority = 'medium';
		}

		return $priority;
	}

	/**
	 * Sanitize date value
	 *
	 * Validates date format (YYYY-MM-DD)
	 *
	 * @param string $date Raw date from form.
	 * @return string Valid date or empty string
	 */
	public static function sanitize_date( $date ) {
		$date = trim( $date );

		if ( empty( $date ) ) {
			return '';
		}

		// Validate date format YYYY-MM-DD
		$date_obj = \DateTime::createFromFormat( 'Y-m-d', $date );

		if ( ! $date_obj ) {
			return '';
		}

		return $date_obj->format( 'Y-m-d' );
	}

	/**
	 * Sanitize user ID
	 *
	 * Ensures user exists and is valid
	 *
	 * @param int $user_id Raw user ID from form.
	 * @return int Valid user ID or current user ID
	 */
	public static function sanitize_user_id( $user_id ) {
		$user_id = absint( $user_id );

		if ( ! $user_id || ! get_user_by( 'id', $user_id ) ) {
			$user_id = get_current_user_id();
		}

		return $user_id;
	}

	/**
	 * Validate task data has required fields
	 *
	 * @param array $data Task data to validate.
	 * @return array Array with 'valid' bool and 'errors' array
	 */
	public static function validate_task_data( $data ) {
		$errors = [];

		// Check title is not empty
		if ( empty( $data['title'] ?? '' ) ) {
			$errors['title'] = __( 'Task title is required', TM_TEXT_DOMAIN );
		}

		// Check title length
		if ( strlen( $data['title'] ?? '' ) > 255 ) {
			$errors['title'] = __( 'Task title must be less than 255 characters', TM_TEXT_DOMAIN );
		}

		// Validate status if provided
		if ( ! empty( $data['status'] ) && ! in_array( $data['status'], self::VALID_STATUSES, true ) ) {
			$errors['status'] = __( 'Invalid task status', TM_TEXT_DOMAIN );
		}

		// Validate priority if provided
		if ( ! empty( $data['priority'] ) && ! in_array( $data['priority'], self::VALID_PRIORITIES, true ) ) {
			$errors['priority'] = __( 'Invalid task priority', TM_TEXT_DOMAIN );
		}

		// Validate date if provided
		if ( ! empty( $data['due_date'] ) ) {
			$date_obj = \DateTime::createFromFormat( 'Y-m-d', $data['due_date'] );
			if ( ! $date_obj ) {
				$errors['due_date'] = __( 'Invalid date format, use YYYY-MM-DD', TM_TEXT_DOMAIN );
			}
		}

		return [
			'valid'  => empty( $errors ),
			'errors' => $errors,
		];
	}

	/**
	 * Sanitize and validate GET/POST parameters
	 *
	 * Useful for sanitizing form submissions
	 *
	 * @param array $data Raw $_POST or $_GET data.
	 * @return array Cleaned and validated data
	 */
	public static function sanitize_form_submission( $data ) {
		$sanitized = [];

		// Sanitize each field if present
		if ( isset( $data['task_title'] ) ) {
			$sanitized['title'] = self::sanitize_title( $data['task_title'] );
		}

		if ( isset( $data['task_description'] ) ) {
			$sanitized['description'] = self::sanitize_description( $data['task_description'] );
		}

		if ( isset( $data['task_status'] ) ) {
			$sanitized['status'] = self::sanitize_status( $data['task_status'] );
		}

		if ( isset( $data['task_priority'] ) ) {
			$sanitized['priority'] = self::sanitize_priority( $data['task_priority'] );
		}

		if ( isset( $data['task_due_date'] ) ) {
			$sanitized['due_date'] = self::sanitize_date( $data['task_due_date'] );
		}

		// Add current user as creator
		$sanitized['created_by'] = get_current_user_id();

		return $sanitized;
	}
}
