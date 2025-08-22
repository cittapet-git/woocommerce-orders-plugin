<?php
/**
 * Uninstall script for My Real-Time WooCommerce Orders Plugin
 *
 * @package MyRealTimeOrders
 */

// If uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Clean up any options or transients
delete_option( 'my_real_time_orders_version' );
delete_transient( 'my_real_time_orders_cache' );

// Clean up any custom tables if they exist (future feature)
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}my_real_time_orders_log" );

// Remove any scheduled events
wp_clear_scheduled_hook( 'my_real_time_orders_cleanup' ); 