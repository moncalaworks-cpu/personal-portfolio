<?php
/**
 * Task Manager Admin Pages Class
 *
 * Handles admin menu registration and page routing
 * Demonstrates:
 * - add_menu_page() for main menu
 * - add_submenu_page() for submenus
 * - Capability checking
 * - Page routing and rendering
 *
 * @package TaskManager\Admin
 */

namespace TaskManager\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Pages class for Task Manager
 *
 * Manages admin menus, submenus, and page rendering
 */
class AdminPages {
	/**
	 * Register admin menus and submenus
	 *
	 * Called on admin_menu hook
	 * Creates main menu and submenus for Task Manager
	 */
	public static function register_menus() {
		// Check capability before registering
		if ( ! current_user_can( 'manage_tasks' ) ) {
			return;
		}

		// Add main menu page
		$main_page_hook = add_menu_page(
			__( 'Task Manager', TM_TEXT_DOMAIN ),          // Page title
			__( 'Task Manager', TM_TEXT_DOMAIN ),          // Menu title
			'manage_tasks',                                 // Capability
			'task-manager',                                 // Menu slug
			[ __CLASS__, 'render_dashboard' ],             // Callback
			'dashicons-tasks',                              // Icon
			25                                              // Position
		);

		// Add dashboard submenu
		add_submenu_page(
			'task-manager',
			__( 'Dashboard', TM_TEXT_DOMAIN ),
			__( 'Dashboard', TM_TEXT_DOMAIN ),
			'manage_tasks',
			'task-manager',
			[ __CLASS__, 'render_dashboard' ]
		);

		// Add tasks list submenu
		add_submenu_page(
			'task-manager',
			__( 'All Tasks', TM_TEXT_DOMAIN ),
			__( 'All Tasks', TM_TEXT_DOMAIN ),
			'manage_tasks',
			'task-manager-tasks',
			[ __CLASS__, 'render_tasks' ]
		);

		// Add add new task submenu
		add_submenu_page(
			'task-manager',
			__( 'Add New Task', TM_TEXT_DOMAIN ),
			__( 'Add New Task', TM_TEXT_DOMAIN ),
			'create_tasks',
			'task-manager-add',
			[ __CLASS__, 'render_add_task' ]
		);

		// Add settings submenu
		add_submenu_page(
			'task-manager',
			__( 'Task Manager Settings', TM_TEXT_DOMAIN ),
			__( 'Settings', TM_TEXT_DOMAIN ),
			'manage_tasks',
			'task-manager-settings',
			[ __CLASS__, 'render_settings' ]
		);

		// Enqueue scripts for Task Manager pages
		add_action( "load-{$main_page_hook}", [ __CLASS__, 'load_page' ] );
	}

	/**
	 * Load page callback
	 *
	 * Called when loading Task Manager pages
	 * Can be used for enqueuing page-specific scripts
	 */
	public static function load_page() {
		// Page-specific initialization can go here
	}

	/**
	 * Render dashboard page
	 *
	 * Shows overview with statistics and recent tasks
	 * Demonstrates WP admin styles and markup
	 */
	public static function render_dashboard() {
		// Check capability
		if ( ! current_user_can( 'manage_tasks' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', TM_TEXT_DOMAIN ) );
		}

		// Get statistics
		$db    = \TaskManager\Database::get_instance();
		$stats = $db->get_task_statistics();
		$recent = $db->get_recent_tasks( 5 );

		// Include dashboard template
		include TM_PLUGIN_DIR . 'admin/partials/dashboard.php';
	}

	/**
	 * Render tasks list page
	 *
	 * Shows all tasks with filtering and bulk actions
	 * Uses WP_List_Table for consistent UI
	 */
	public static function render_tasks() {
		// Check capability
		if ( ! current_user_can( 'manage_tasks' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', TM_TEXT_DOMAIN ) );
		}

		// Include task list template
		include TM_PLUGIN_DIR . 'admin/partials/task-list.php';
	}

	/**
	 * Render add/edit task page
	 *
	 * Shows form for creating or editing a task
	 * Handles form submission with nonce verification
	 */
	public static function render_add_task() {
		// Check capability
		if ( ! empty( $_GET['task_id'] ) ) {
			if ( ! current_user_can( 'edit_tasks' ) ) {
				wp_die( esc_html__( 'You do not have permission to edit tasks.', TM_TEXT_DOMAIN ) );
			}
		} else {
			if ( ! current_user_can( 'create_tasks' ) ) {
				wp_die( esc_html__( 'You do not have permission to create tasks.', TM_TEXT_DOMAIN ) );
			}
		}

		// Handle form submission
		if ( ! empty( $_POST ) ) {
			TaskForm::handle_submit();
		}

		// Get task if editing
		$task = null;
		if ( ! empty( $_GET['task_id'] ) ) {
			$db   = \TaskManager\Database::get_instance();
			$task = $db->get_task( absint( $_GET['task_id'] ) );

			if ( ! $task ) {
				wp_die( esc_html__( 'Task not found.', TM_TEXT_DOMAIN ) );
			}
		}

		// Include task form template
		include TM_PLUGIN_DIR . 'admin/partials/task-form.php';
	}

	/**
	 * Render settings page
	 *
	 * Shows plugin settings with Settings API
	 */
	public static function render_settings() {
		// Check capability
		if ( ! current_user_can( 'manage_tasks' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', TM_TEXT_DOMAIN ) );
		}

		// Include settings template
		include TM_PLUGIN_DIR . 'admin/partials/settings.php';
	}
}
