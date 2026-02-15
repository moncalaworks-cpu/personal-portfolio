<?php
/**
 * Task Manager Task Form Template
 *
 * Form for creating and editing tasks
 * Demonstrates:
 * - Form rendering with nonces
 * - WP editor for content
 * - Form submission handling
 *
 * @package TaskManager\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php TaskManager\Admin\TaskForm::display_notice(); ?>
	<?php TaskManager\Admin\TaskForm::display_form_errors(); ?>

	<form method="post" action="">
		<?php
		// Add nonce field for security
		wp_nonce_field( 'tm_save_task', 'tm_task_nonce' );
		?>

		<?php
		// Render form fields
		echo TaskManager\Admin\TaskForm::get_form_html( $task ?? null );
		?>

		<?php if ( $task ) : ?>
			<input type="hidden" name="task_id" value="<?php echo intval( $task->id ); ?>" />
		<?php endif; ?>

		<div class="submit">
			<?php submit_button( __( 'Save Task', TM_TEXT_DOMAIN ), 'primary', 'submit', true ); ?>

			<?php if ( ! $task && current_user_can( 'manage_tasks' ) ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=task-manager-tasks' ) ); ?>" class="button">
					<?php esc_html_e( 'Cancel', TM_TEXT_DOMAIN ); ?>
				</a>
			<?php endif; ?>
		</div>
	</form>
</div>
