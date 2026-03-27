<?php
/**
 * Analytics Dashboard Template
 *
 * Displays KPI cards and date range selector
 *
 * @package AnalyticsDashboard\Admin
 * @var array $stats Statistics array with KPI data
 * @var string $date_range Current date range ('7', '30', 'all')
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Analytics Dashboard', 'analytics-dashboard' ); ?></h1>

	<!-- Date Range Selector -->
	<div style="margin-bottom: 20px; background: #fff; padding: 15px; border: 1px solid #ccc; border-radius: 4px;">
		<form method="get" style="display: flex; gap: 10px; align-items: center;">
			<input type="hidden" name="page" value="analytics-dashboard">

			<label for="ad-range" style="font-weight: 500;">
				<?php esc_html_e( 'Date Range:', 'analytics-dashboard' ); ?>
			</label>

			<select id="ad-range" name="range" style="padding: 8px 12px; font-size: 14px;">
				<option value="7" <?php selected( $date_range, '7' ); ?>>
					<?php esc_html_e( 'Last 7 Days', 'analytics-dashboard' ); ?>
				</option>
				<option value="30" <?php selected( $date_range, '30' ); ?>>
					<?php esc_html_e( 'Last 30 Days', 'analytics-dashboard' ); ?>
				</option>
				<option value="all" <?php selected( $date_range, 'all' ); ?>>
					<?php esc_html_e( 'All Time', 'analytics-dashboard' ); ?>
				</option>
			</select>

			<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Filter', 'analytics-dashboard' ); ?>">
		</form>
	</div>

	<!-- KPI Cards Grid -->
	<div class="analytics-dashboard-grid" style="
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
		gap: 20px;
		margin-top: 20px;
	">
		<!-- Total Posts Card -->
		<div class="postbox" style="padding: 20px; background: #fff; border: 1px solid #ccc; border-radius: 4px;">
			<h3 style="margin: 0 0 15px 0; color: #333; font-size: 14px; font-weight: 500; text-transform: uppercase;">
				<?php esc_html_e( 'Total Posts', 'analytics-dashboard' ); ?>
			</h3>
			<p style="
				font-size: 36px;
				font-weight: 700;
				margin: 0 0 10px 0;
				color: #0073aa;
			">
				<?php echo intval( $stats['total_posts'] ); ?>
			</p>
			<p style="margin: 0; color: #666; font-size: 12px;">
				<?php esc_html_e( 'Published articles', 'analytics-dashboard' ); ?>
			</p>
		</div>

		<!-- Total Comments Card -->
		<div class="postbox" style="padding: 20px; background: #fff; border: 1px solid #ccc; border-radius: 4px;">
			<h3 style="margin: 0 0 15px 0; color: #333; font-size: 14px; font-weight: 500; text-transform: uppercase;">
				<?php esc_html_e( 'Total Comments', 'analytics-dashboard' ); ?>
			</h3>
			<p style="
				font-size: 36px;
				font-weight: 700;
				margin: 0 0 10px 0;
				color: #23a1d1;
			">
				<?php echo intval( $stats['total_comments'] ); ?>
			</p>
			<p style="margin: 0; color: #666; font-size: 12px;">
				<?php esc_html_e( 'Approved comments', 'analytics-dashboard' ); ?>
			</p>
		</div>

		<!-- Total Users Card -->
		<div class="postbox" style="padding: 20px; background: #fff; border: 1px solid #ccc; border-radius: 4px;">
			<h3 style="margin: 0 0 15px 0; color: #333; font-size: 14px; font-weight: 500; text-transform: uppercase;">
				<?php esc_html_e( 'Total Users', 'analytics-dashboard' ); ?>
			</h3>
			<p style="
				font-size: 36px;
				font-weight: 700;
				margin: 0 0 10px 0;
				color: #33ba7c;
			">
				<?php echo intval( $stats['total_users'] ); ?>
			</p>
			<p style="margin: 0; color: #666; font-size: 12px;">
				<?php esc_html_e( 'Registered members', 'analytics-dashboard' ); ?>
			</p>
		</div>

		<!-- Total Pages Card -->
		<div class="postbox" style="padding: 20px; background: #fff; border: 1px solid #ccc; border-radius: 4px;">
			<h3 style="margin: 0 0 15px 0; color: #333; font-size: 14px; font-weight: 500; text-transform: uppercase;">
				<?php esc_html_e( 'Total Pages', 'analytics-dashboard' ); ?>
			</h3>
			<p style="
				font-size: 36px;
				font-weight: 700;
				margin: 0 0 10px 0;
				color: #d96b00;
			">
				<?php echo intval( $stats['total_pages'] ); ?>
			</p>
			<p style="margin: 0; color: #666; font-size: 12px;">
				<?php esc_html_e( 'Published pages', 'analytics-dashboard' ); ?>
			</p>
		</div>

		<!-- Average Engagement Card -->
		<div class="postbox" style="padding: 20px; background: #fff; border: 1px solid #ccc; border-radius: 4px;">
			<h3 style="margin: 0 0 15px 0; color: #333; font-size: 14px; font-weight: 500; text-transform: uppercase;">
				<?php esc_html_e( 'Avg Engagement', 'analytics-dashboard' ); ?>
			</h3>
			<p style="
				font-size: 36px;
				font-weight: 700;
				margin: 0 0 10px 0;
				color: #c51162;
			">
				<?php echo round( floatval( $stats['avg_engagement'] ), 2 ); ?>
			</p>
			<p style="margin: 0; color: #666; font-size: 12px;">
				<?php esc_html_e( 'comments per post', 'analytics-dashboard' ); ?>
			</p>
		</div>
	</div>

	<!-- Footer Info -->
	<div style="margin-top: 30px; padding: 15px; background: #e7f3ff; border-left: 4px solid #0073aa; border-radius: 4px;">
		<p style="margin: 0; color: #333; font-size: 13px;">
			<strong><?php esc_html_e( 'Info:', 'analytics-dashboard' ); ?></strong>
			<?php esc_html_e( 'Statistics are cached for 1 hour and updated automatically when content changes.', 'analytics-dashboard' ); ?>
		</p>
	</div>
</div>
