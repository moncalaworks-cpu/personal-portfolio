<?php
/**
 * Task Manager Dashboard Template
 *
 * Displays overview with statistics and recent tasks
 *
 * @package TaskManager\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Task Manager Dashboard', TM_TEXT_DOMAIN ); ?></h1>

	<!-- Statistics Cards -->
	<div class="tm-statistics" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
		<!-- Total Tasks Card -->
		<div class="postbox" style="padding: 20px;">
			<h2 class="hndle" style="margin-top: 0;"><?php esc_html_e( 'Total Tasks', TM_TEXT_DOMAIN ); ?></h2>
			<p style="font-size: 32px; font-weight: bold; margin: 10px 0;">
				<?php echo intval( $stats['total'] ); ?>
			</p>
		</div>

		<!-- To Do Card -->
		<div class="postbox" style="padding: 20px; background-color: #f9f1f6;">
			<h2 class="hndle" style="margin-top: 0; color: #d63384;"><?php esc_html_e( 'To Do', TM_TEXT_DOMAIN ); ?></h2>
			<p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #d63384;">
				<?php echo intval( $stats['todo'] ); ?>
			</p>
		</div>

		<!-- In Progress Card -->
		<div class="postbox" style="padding: 20px; background-color: #fff3cd;">
			<h2 class="hndle" style="margin-top: 0; color: #856404;"><?php esc_html_e( 'In Progress', TM_TEXT_DOMAIN ); ?></h2>
			<p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #856404;">
				<?php echo intval( $stats['in_progress'] ); ?>
			</p>
		</div>

		<!-- Done Card -->
		<div class="postbox" style="padding: 20px; background-color: #d4edda;">
			<h2 class="hndle" style="margin-top: 0; color: #155724;"><?php esc_html_e( 'Done', TM_TEXT_DOMAIN ); ?></h2>
			<p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #155724;">
				<?php echo intval( $stats['done'] ); ?>
			</p>
		</div>
	</div>

	<!-- Recent Tasks Section -->
	<div class="postbox" style="margin-top: 20px;">
		<h2 class="hndle"><?php esc_html_e( 'Recent Tasks', TM_TEXT_DOMAIN ); ?></h2>
		<div class="inside">
			<?php if ( ! empty( $recent ) ) : ?>
				<table class="widefat">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Title', TM_TEXT_DOMAIN ); ?></th>
							<th><?php esc_html_e( 'Status', TM_TEXT_DOMAIN ); ?></th>
							<th><?php esc_html_e( 'Priority', TM_TEXT_DOMAIN ); ?></th>
							<th><?php esc_html_e( 'Due Date', TM_TEXT_DOMAIN ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $recent as $task ) : ?>
							<tr>
								<td>
									<strong><?php echo esc_html( $task->title ); ?></strong>
								</td>
								<td>
									<span class="<?php echo esc_attr( $task->get_status_badge_class() ); ?>" style="padding: 5px 10px; border-radius: 3px; background-color: #f0f0f0;">
										<?php echo esc_html( $task->get_status_label() ); ?>
									</span>
								</td>
								<td>
									<span class="<?php echo esc_attr( $task->get_priority_badge_class() ); ?>" style="padding: 5px 10px; border-radius: 3px; background-color: #f0f0f0;">
										<?php echo esc_html( $task->get_priority_label() ); ?>
									</span>
								</td>
								<td>
									<?php
									if ( $task->due_date ) {
										echo esc_html( $task->get_formatted_due_date() );
										if ( $task->is_overdue() ) {
											echo ' <span style="color: red; font-weight: bold;">(' . esc_html__( 'Overdue', TM_TEXT_DOMAIN ) . ')</span>';
										}
									} else {
										echo '—';
									}
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<p><?php esc_html_e( 'No tasks yet. Create your first task!', TM_TEXT_DOMAIN ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<!-- Quick Actions -->
	<div class="postbox" style="margin-top: 20px;">
		<h2 class="hndle"><?php esc_html_e( 'Quick Actions', TM_TEXT_DOMAIN ); ?></h2>
		<div class="inside">
			<?php if ( current_user_can( 'create_tasks' ) ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=task-manager-add' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Add New Task', TM_TEXT_DOMAIN ); ?>
				</a>
			<?php endif; ?>

			<?php if ( current_user_can( 'manage_tasks' ) ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=task-manager-tasks' ) ); ?>" class="button">
					<?php esc_html_e( 'View All Tasks', TM_TEXT_DOMAIN ); ?>
				</a>

				<a href="<?php echo esc_url( admin_url( 'admin.php?page=task-manager-settings' ) ); ?>" class="button">
					<?php esc_html_e( 'Settings', TM_TEXT_DOMAIN ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
