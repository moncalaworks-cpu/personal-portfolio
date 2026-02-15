<?php
/**
 * Task Manager Settings Template
 *
 * Displays settings form using WordPress Settings API
 *
 * @package TaskManager\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form method="post" action="options.php">
		<?php
		// Output settings fields and nonce
		settings_fields( TaskManager\Admin\Settings::SETTINGS_GROUP );

		// Output settings sections
		do_settings_sections( TaskManager\Admin\Settings::PAGE_HOOK );

		// Output submit button
		submit_button();
		?>
	</form>
</div>
