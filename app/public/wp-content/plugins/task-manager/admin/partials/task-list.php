<?php
/**
 * Task Manager Task List Template
 *
 * Displays list of all tasks with filtering
 *
 * @package TaskManager\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get database instance
$db = TaskManager\Database::get_instance();

// Get filter values from query string
$status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
$priority_filter = isset( $_GET['priority'] ) ? sanitize_text_field( $_GET['priority'] ) : '';
$paged = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;

// Get settings for tasks per page
$settings = TaskManager\Admin\Settings::get_all();
$per_page = $settings['tasks_per_page'];
$offset = ( $paged - 1 ) * $per_page;

// Build query arguments
$query_args = [
	'limit'  => $per_page,
	'offset' => $offset,
	'order'  => 'DESC',
	'orderby' => 'created_at',
];

if ( $status_filter ) {
	$query_args['status'] = $status_filter;
}

if ( $priority_filter ) {
	$query_args['priority'] = $priority_filter;
}

// Get tasks
$tasks = $db->get_tasks( $query_args );

// Get total count (without limit/offset)
$count_args = [];
if ( $status_filter ) {
	$count_args['status'] = $status_filter;
}
if ( $priority_filter ) {
	$count_args['priority'] = $priority_filter;
}
$count_args['limit'] = 9999;

$all_tasks = $db->get_tasks( $count_args );
$total_tasks = count( $all_tasks );
$total_pages = ceil( $total_tasks / $per_page );
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php TaskManager\Admin\TaskForm::display_notice(); ?>

	<!-- Filters -->
	<form method="get" class="wp-clearfix">
		<input type="hidden" name="page" value="task-manager-tasks" />

		<select name="status" id="filter-status">
			<option value=""><?php esc_html_e( 'All Statuses', TM_TEXT_DOMAIN ); ?></option>
			<option value="todo" <?php selected( $status_filter, 'todo' ); ?>><?php esc_html_e( 'To Do', TM_TEXT_DOMAIN ); ?></option>
			<option value="in_progress" <?php selected( $status_filter, 'in_progress' ); ?>><?php esc_html_e( 'In Progress', TM_TEXT_DOMAIN ); ?></option>
			<option value="done" <?php selected( $status_filter, 'done' ); ?>><?php esc_html_e( 'Done', TM_TEXT_DOMAIN ); ?></option>
		</select>

		<select name="priority" id="filter-priority">
			<option value=""><?php esc_html_e( 'All Priorities', TM_TEXT_DOMAIN ); ?></option>
			<option value="low" <?php selected( $priority_filter, 'low' ); ?>><?php esc_html_e( 'Low', TM_TEXT_DOMAIN ); ?></option>
			<option value="medium" <?php selected( $priority_filter, 'medium' ); ?>><?php esc_html_e( 'Medium', TM_TEXT_DOMAIN ); ?></option>
			<option value="high" <?php selected( $priority_filter, 'high' ); ?>><?php esc_html_e( 'High', TM_TEXT_DOMAIN ); ?></option>
		</select>

		<?php submit_button( __( 'Filter', TM_TEXT_DOMAIN ), 'button', 'filter_action', false ); ?>
	</form>

	<!-- Tasks Table -->
	<?php if ( ! empty( $tasks ) ) : ?>
		<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Title', TM_TEXT_DOMAIN ); ?></th>
					<th scope="col"><?php esc_html_e( 'Description', TM_TEXT_DOMAIN ); ?></th>
					<th scope="col"><?php esc_html_e( 'Status', TM_TEXT_DOMAIN ); ?></th>
					<th scope="col"><?php esc_html_e( 'Priority', TM_TEXT_DOMAIN ); ?></th>
					<th scope="col"><?php esc_html_e( 'Due Date', TM_TEXT_DOMAIN ); ?></th>
					<th scope="col"><?php esc_html_e( 'Created By', TM_TEXT_DOMAIN ); ?></th>
					<th scope="col"><?php esc_html_e( 'Actions', TM_TEXT_DOMAIN ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $tasks as $task ) : ?>
					<tr>
						<td>
							<strong><?php echo esc_html( $task->title ); ?></strong>
						</td>
						<td>
							<?php
							if ( $task->description ) {
								$description = wp_strip_all_tags( $task->description );
								$description = substr( $description, 0, 50 );
								echo esc_html( $description );
								if ( strlen( $task->description ) > 50 ) {
									echo '...';
								}
							} else {
								echo '—';
							}
							?>
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
						<td>
							<?php
							$creator = $task->get_creator();
							if ( $creator ) {
								echo esc_html( $creator->display_name );
							}
							?>
						</td>
						<td>
							<?php if ( current_user_can( 'edit_tasks' ) ) : ?>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=task-manager-add&task_id=' . $task->id ) ); ?>" class="button button-small">
									<?php esc_html_e( 'Edit', TM_TEXT_DOMAIN ); ?>
								</a>
							<?php endif; ?>

							<?php if ( current_user_can( 'delete_tasks' ) ) : ?>
								<a href="<?php echo esc_url( add_query_arg( 'action', 'delete', add_query_arg( 'task_id', $task->id ) ) ); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php echo esc_attr__( 'Are you sure you want to delete this task?', TM_TEXT_DOMAIN ); ?>');">
									<?php esc_html_e( 'Delete', TM_TEXT_DOMAIN ); ?>
								</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<!-- Pagination -->
		<?php if ( $total_pages > 1 ) : ?>
			<div class="tablenav bottom">
				<div class="tablenav-pages">
					<?php
					echo paginate_links(
						[
							'base'      => add_query_arg( 'paged', '%#%' ),
							'format'    => '',
							'prev_text' => '« ' . esc_html__( 'Previous', TM_TEXT_DOMAIN ),
							'next_text' => esc_html__( 'Next', TM_TEXT_DOMAIN ) . ' »',
							'total'     => $total_pages,
							'current'   => $paged,
						]
					);
					?>
				</div>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<p><?php esc_html_e( 'No tasks found.', TM_TEXT_DOMAIN ); ?></p>
	<?php endif; ?>
</div>
