<?php
/**
 * Task Manager Task Form Class
 *
 * Handles task form rendering and submission
 * Demonstrates:
 * - Form rendering with nonces
 * - Nonce verification
 * - Form submission handling
 * - Sanitization and validation
 *
 * @package TaskManager\Admin
 */

namespace TaskManager\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * TaskForm class for handling task forms
 */
class TaskForm {
	/**
	 * Handle form submission
	 *
	 * Verifies nonce, sanitizes data, and saves/updates task
	 * Called when form is posted
	 */
	public static function handle_submit() {
		// Verify nonce for security
		if ( ! isset( $_POST['tm_task_nonce'] ) || ! wp_verify_nonce( $_POST['tm_task_nonce'], 'tm_save_task' ) ) {
			wp_die( esc_html__( 'Security check failed. Please try again.', TM_TEXT_DOMAIN ) );
		}

		// Check user capability
		$is_edit = ! empty( $_POST['task_id'] );
		$cap     = $is_edit ? 'edit_tasks' : 'create_tasks';

		if ( ! current_user_can( $cap ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', TM_TEXT_DOMAIN ) );
		}

		// Sanitize form data
		require_once TM_PLUGIN_DIR . 'security/class-validator.php';
		$task_data = \TaskManager\Security\Validator::sanitize_form_submission( $_POST );

		// Validate task data
		$validation = \TaskManager\Security\Validator::validate_task_data( $task_data );

		if ( ! $validation['valid'] ) {
			// Store errors in transient for display
			set_transient(
				'tm_form_errors_' . get_current_user_id(),
				$validation['errors'],
				MINUTE_IN_SECONDS
			);

			// Redirect back with error notice
			wp_safe_remote_post(
				admin_url( 'admin.php?page=' . ( $is_edit ? 'task-manager-tasks' : 'task-manager-add' ) . '&form_error=1' ),
				[ 'blocking' => false ]
			);

			return;
		}

		// Create or update task
		$db = \TaskManager\Database::get_instance();

		if ( $is_edit ) {
			// Update existing task
			$task_id = absint( $_POST['task_id'] );
			$success = $db->update_task( $task_id, $task_data );
			$message = __( 'Task updated successfully!', TM_TEXT_DOMAIN );
			$type    = 'success';
		} else {
			// Create new task
			$task_id = $db->create_task( $task_data );
			$success = (bool) $task_id;
			$message = __( 'Task created successfully!', TM_TEXT_DOMAIN );
			$type    = 'success';
		}

		if ( $success ) {
			// Set success message
			set_transient(
				'tm_admin_message_' . get_current_user_id(),
				[ 'message' => $message, 'type' => $type ],
				10
			);

			// Redirect to task list
			wp_safe_remote_post(
				admin_url( 'admin.php?page=task-manager-tasks' ),
				[ 'blocking' => false ]
			);
		} else {
			// Set error message
			set_transient(
				'tm_admin_message_' . get_current_user_id(),
				[ 'message' => __( 'Error saving task. Please try again.', TM_TEXT_DOMAIN ), 'type' => 'error' ],
				10
			);
		}
	}

	/**
	 * Display admin notice
	 *
	 * Shows success or error messages after form submission
	 */
	public static function display_notice() {
		$message = get_transient( 'tm_admin_message_' . get_current_user_id() );

		if ( ! $message ) {
			return;
		}

		delete_transient( 'tm_admin_message_' . get_current_user_id() );

		$type = esc_attr( $message['type'] );
		$text = esc_html( $message['message'] );

		echo "<div class='notice notice-{$type} is-dismissible'><p>{$text}</p></div>";
	}

	/**
	 * Display form errors
	 *
	 * Shows validation errors from form submission
	 */
	public static function display_form_errors() {
		$errors = get_transient( 'tm_form_errors_' . get_current_user_id() );

		if ( ! $errors ) {
			return;
		}

		delete_transient( 'tm_form_errors_' . get_current_user_id() );

		echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Please correct the following errors:', TM_TEXT_DOMAIN ) . '</strong></p>';
		echo '<ul style="margin-left: 20px; list-style-type: disc;">';

		foreach ( $errors as $field => $error ) {
			echo '<li>' . esc_html( $error ) . '</li>';
		}

		echo '</ul></div>';
	}

	/**
	 * Get form fields for task
	 *
	 * Returns HTML for task form fields
	 *
	 * @param \TaskManager\Task $task Task object (null for new task).
	 * @return string Form HTML
	 */
	public static function get_form_html( $task = null ) {
		// Use current user as default creator
		$user_id = $task ? $task->created_by : get_current_user_id();

		$title       = $task ? $task->title : '';
		$description = $task ? $task->description : '';
		$status      = $task ? $task->status : 'todo';
		$priority    = $task ? $task->priority : 'medium';
		$due_date    = $task ? $task->due_date : '';

		$html = '<table class="form-table" role="presentation"><tbody>';

		// Title field
		$html .= '<tr>';
		$html .= '<th scope="row"><label for="task_title">' . esc_html__( 'Task Title', TM_TEXT_DOMAIN ) . ' <span class="required">*</span></label></th>';
		$html .= '<td><input type="text" id="task_title" name="task_title" class="regular-text" value="' . esc_attr( $title ) . '" required /></td>';
		$html .= '</tr>';

		// Description field
		$html .= '<tr>';
		$html .= '<th scope="row"><label for="task_description">' . esc_html__( 'Description', TM_TEXT_DOMAIN ) . '</label></th>';
		$html .= '<td>';
		$html .= '<textarea id="task_description" name="task_description" class="large-text" rows="5">' . esc_textarea( $description ) . '</textarea>';
		$html .= '</td>';
		$html .= '</tr>';

		// Status field
		$html .= '<tr>';
		$html .= '<th scope="row"><label for="task_status">' . esc_html__( 'Status', TM_TEXT_DOMAIN ) . '</label></th>';
		$html .= '<td>';
		$html .= '<select id="task_status" name="task_status">';
		$html .= '<option value="todo" ' . selected( $status, 'todo', false ) . '>' . esc_html__( 'To Do', TM_TEXT_DOMAIN ) . '</option>';
		$html .= '<option value="in_progress" ' . selected( $status, 'in_progress', false ) . '>' . esc_html__( 'In Progress', TM_TEXT_DOMAIN ) . '</option>';
		$html .= '<option value="done" ' . selected( $status, 'done', false ) . '>' . esc_html__( 'Done', TM_TEXT_DOMAIN ) . '</option>';
		$html .= '</select>';
		$html .= '</td>';
		$html .= '</tr>';

		// Priority field
		$html .= '<tr>';
		$html .= '<th scope="row"><label for="task_priority">' . esc_html__( 'Priority', TM_TEXT_DOMAIN ) . '</label></th>';
		$html .= '<td>';
		$html .= '<select id="task_priority" name="task_priority">';
		$html .= '<option value="low" ' . selected( $priority, 'low', false ) . '>' . esc_html__( 'Low', TM_TEXT_DOMAIN ) . '</option>';
		$html .= '<option value="medium" ' . selected( $priority, 'medium', false ) . '>' . esc_html__( 'Medium', TM_TEXT_DOMAIN ) . '</option>';
		$html .= '<option value="high" ' . selected( $priority, 'high', false ) . '>' . esc_html__( 'High', TM_TEXT_DOMAIN ) . '</option>';
		$html .= '</select>';
		$html .= '</td>';
		$html .= '</tr>';

		// Due date field
		$html .= '<tr>';
		$html .= '<th scope="row"><label for="task_due_date">' . esc_html__( 'Due Date', TM_TEXT_DOMAIN ) . '</label></th>';
		$html .= '<td>';
		$html .= '<input type="date" id="task_due_date" name="task_due_date" value="' . esc_attr( $due_date ) . '" />';
		$html .= '<p class="description">' . esc_html__( 'Format: YYYY-MM-DD', TM_TEXT_DOMAIN ) . '</p>';
		$html .= '</td>';
		$html .= '</tr>';

		$html .= '</tbody></table>';

		return $html;
	}
}
