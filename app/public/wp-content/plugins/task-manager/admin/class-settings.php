<?php
/**
 * Task Manager Settings Class
 *
 * Handles WordPress Settings API implementation
 * Demonstrates:
 * - register_setting() for settings registration
 * - add_settings_section() for organizing settings
 * - add_settings_field() for individual fields
 * - Settings sanitization callbacks
 * - Settings retrieval and display
 *
 * @package TaskManager\Admin
 */

namespace TaskManager\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class for Task Manager
 *
 * Manages plugin settings using WordPress Settings API
 */
class Settings {
	/**
	 * Settings group name
	 */
	const SETTINGS_GROUP = 'tm_settings_group';

	/**
	 * Settings option name
	 */
	const OPTION_NAME = 'tm_settings';

	/**
	 * Settings page hook
	 */
	const PAGE_HOOK = 'task_manager_settings_page';

	/**
	 * Register settings with WordPress Settings API
	 *
	 * Called on admin_init hook
	 * Registers settings, sections, and fields
	 */
	public static function register() {
		// Register settings
		register_setting(
			self::SETTINGS_GROUP,
			self::OPTION_NAME,
			[
				'type'              => 'array',
				'sanitize_callback' => [ __CLASS__, 'sanitize_settings' ],
				'show_in_rest'      => false,
			]
		);

		// Add settings section
		add_settings_section(
			'tm_display_section',
			__( 'Display Settings', TM_TEXT_DOMAIN ),
			[ __CLASS__, 'render_section' ],
			self::PAGE_HOOK
		);

		// Add individual settings fields
		add_settings_field(
			'tm_default_status',
			__( 'Default Task Status', TM_TEXT_DOMAIN ),
			[ __CLASS__, 'render_field_default_status' ],
			self::PAGE_HOOK,
			'tm_display_section'
		);

		add_settings_field(
			'tm_default_priority',
			__( 'Default Task Priority', TM_TEXT_DOMAIN ),
			[ __CLASS__, 'render_field_default_priority' ],
			self::PAGE_HOOK,
			'tm_display_section'
		);

		add_settings_field(
			'tm_tasks_per_page',
			__( 'Tasks Per Page', TM_TEXT_DOMAIN ),
			[ __CLASS__, 'render_field_tasks_per_page' ],
			self::PAGE_HOOK,
			'tm_display_section'
		);

		add_settings_field(
			'tm_show_completed',
			__( 'Show Completed Tasks', TM_TEXT_DOMAIN ),
			[ __CLASS__, 'render_field_show_completed' ],
			self::PAGE_HOOK,
			'tm_display_section'
		);
	}

	/**
	 * Sanitize settings on save
	 *
	 * Validation and sanitization callback for Settings API
	 *
	 * @param array $input Raw settings input from form.
	 * @return array Sanitized settings
	 */
	public static function sanitize_settings( $input ) {
		$sanitized = [];

		// Get existing settings as base
		$existing = get_option( self::OPTION_NAME, [] );

		// Sanitize default status
		if ( isset( $input['default_status'] ) ) {
			$valid_statuses                = [ 'todo', 'in_progress', 'done' ];
			$sanitized['default_status']   = in_array( $input['default_status'], $valid_statuses, true ) ? $input['default_status'] : 'todo';
		} else {
			$sanitized['default_status'] = $existing['default_status'] ?? 'todo';
		}

		// Sanitize default priority
		if ( isset( $input['default_priority'] ) ) {
			$valid_priorities              = [ 'low', 'medium', 'high' ];
			$sanitized['default_priority'] = in_array( $input['default_priority'], $valid_priorities, true ) ? $input['default_priority'] : 'medium';
		} else {
			$sanitized['default_priority'] = $existing['default_priority'] ?? 'medium';
		}

		// Sanitize tasks per page
		if ( isset( $input['tasks_per_page'] ) ) {
			$tasks_per_page = absint( $input['tasks_per_page'] );
			// Limit between 5 and 100
			$sanitized['tasks_per_page'] = max( 5, min( 100, $tasks_per_page ) );
		} else {
			$sanitized['tasks_per_page'] = $existing['tasks_per_page'] ?? 20;
		}

		// Sanitize show completed checkbox
		if ( isset( $input['show_completed'] ) ) {
			$sanitized['show_completed'] = (bool) $input['show_completed'];
		} else {
			$sanitized['show_completed'] = $existing['show_completed'] ?? true;
		}

		return $sanitized;
	}

	/**
	 * Render settings section
	 *
	 * Callback for add_settings_section
	 */
	public static function render_section() {
		echo '<p>' . esc_html__( 'Configure default task settings and display options.', TM_TEXT_DOMAIN ) . '</p>';
	}

	/**
	 * Render default status field
	 */
	public static function render_field_default_status() {
		$settings = get_option( self::OPTION_NAME, [] );
		$value    = $settings['default_status'] ?? 'todo';

		echo '<select id="tm_default_status" name="' . esc_attr( self::OPTION_NAME ) . '[default_status]">';
		echo '<option value="todo" ' . selected( $value, 'todo', false ) . '>' . esc_html__( 'To Do', TM_TEXT_DOMAIN ) . '</option>';
		echo '<option value="in_progress" ' . selected( $value, 'in_progress', false ) . '>' . esc_html__( 'In Progress', TM_TEXT_DOMAIN ) . '</option>';
		echo '<option value="done" ' . selected( $value, 'done', false ) . '>' . esc_html__( 'Done', TM_TEXT_DOMAIN ) . '</option>';
		echo '</select>';
	}

	/**
	 * Render default priority field
	 */
	public static function render_field_default_priority() {
		$settings = get_option( self::OPTION_NAME, [] );
		$value    = $settings['default_priority'] ?? 'medium';

		echo '<select id="tm_default_priority" name="' . esc_attr( self::OPTION_NAME ) . '[default_priority]">';
		echo '<option value="low" ' . selected( $value, 'low', false ) . '>' . esc_html__( 'Low', TM_TEXT_DOMAIN ) . '</option>';
		echo '<option value="medium" ' . selected( $value, 'medium', false ) . '>' . esc_html__( 'Medium', TM_TEXT_DOMAIN ) . '</option>';
		echo '<option value="high" ' . selected( $value, 'high', false ) . '>' . esc_html__( 'High', TM_TEXT_DOMAIN ) . '</option>';
		echo '</select>';
	}

	/**
	 * Render tasks per page field
	 */
	public static function render_field_tasks_per_page() {
		$settings = get_option( self::OPTION_NAME, [] );
		$value    = $settings['tasks_per_page'] ?? 20;

		echo '<input type="number" id="tm_tasks_per_page" name="' . esc_attr( self::OPTION_NAME ) . '[tasks_per_page]" value="' . esc_attr( $value ) . '" min="5" max="100" />';
		echo '<p class="description">' . esc_html__( 'Between 5 and 100 tasks per page', TM_TEXT_DOMAIN ) . '</p>';
	}

	/**
	 * Render show completed checkbox
	 */
	public static function render_field_show_completed() {
		$settings = get_option( self::OPTION_NAME, [] );
		$checked  = $settings['show_completed'] ?? true;

		echo '<input type="checkbox" id="tm_show_completed" name="' . esc_attr( self::OPTION_NAME ) . '[show_completed]" value="1" ' . checked( $checked, true, false ) . ' />';
		echo '<label for="tm_show_completed">' . esc_html__( 'Display completed tasks in the list view', TM_TEXT_DOMAIN ) . '</label>';
	}

	/**
	 * Get all settings
	 *
	 * @return array Settings array
	 */
	public static function get_all() {
		$defaults = [
			'default_status'   => 'todo',
			'default_priority' => 'medium',
			'tasks_per_page'   => 20,
			'show_completed'   => true,
		];

		return wp_parse_args( get_option( self::OPTION_NAME, [] ), $defaults );
	}

	/**
	 * Get single setting
	 *
	 * @param string $key Setting key.
	 * @param mixed  $default Default value if not found.
	 * @return mixed Setting value or default
	 */
	public static function get( $key, $default = null ) {
		$settings = self::get_all();
		return $settings[ $key ] ?? $default;
	}
}
