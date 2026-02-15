<?php
/**
 * Task Manager Uninstall Callback
 *
 * Handles complete cleanup when plugin is uninstalled
 * Drops database table and removes all plugin options
 *
 * This file is called when user clicks "Delete" on the plugins page
 */

// Exit if uninstall.php is not called by WordPress
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Define table name
$table_name = $wpdb->prefix . 'tm_tasks';

// Drop custom table
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name is safe
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

// Remove plugin options
delete_option( 'tm_settings' );

// Clear any transients (admin messages, form errors)
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		$wpdb->esc_like( 'tm_' ) . '%'
	)
);
