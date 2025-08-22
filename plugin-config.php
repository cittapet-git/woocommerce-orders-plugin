<?php
/**
 * Plugin Configuration File
 * 
 * This file contains all the configuration options for the plugin
 * 
 * @package MyRealTimeOrders
 * @version 1.2
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin Configuration Constants
define( 'MY_REAL_TIME_ORDERS_VERSION', '1.2' );
define( 'MY_REAL_TIME_ORDERS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MY_REAL_TIME_ORDERS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MY_REAL_TIME_ORDERS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Plugin Settings
define( 'MY_REAL_TIME_ORDERS_DEFAULT_LIMIT', 10 );
define( 'MY_REAL_TIME_ORDERS_MAX_LIMIT', 100 );
define( 'MY_REAL_TIME_ORDERS_REFRESH_INTERVAL', 5000 ); // 5 seconds
define( 'MY_REAL_TIME_ORDERS_DEBUG_MODE', false );

// Database Table Names
define( 'MY_REAL_TIME_ORDERS_LOG_TABLE', 'my_real_time_orders_log' );

// Capability Requirements
define( 'MY_REAL_TIME_ORDERS_CAPABILITY', 'read_shop_orders' );
define( 'MY_REAL_TIME_ORDERS_ADMIN_CAPABILITY', 'manage_options' );

// AJAX Actions
define( 'MY_REAL_TIME_ORDERS_AJAX_ACTION', 'get_latest_orders' );
define( 'MY_REAL_TIME_ORDERS_ADMIN_AJAX_ACTION', 'my_orders_admin_status' );

// Nonce Names
define( 'MY_REAL_TIME_ORDERS_NONCE_NAME', 'my_orders_nonce' );
define( 'MY_REAL_TIME_ORDERS_ADMIN_NONCE_NAME', 'my_orders_admin_nonce' );

// Order Statuses
define( 'MY_REAL_TIME_ORDERS_VALID_STATUSES', array(
    'pending',
    'processing', 
    'completed',
    'cancelled',
    'refunded',
    'on-hold',
    'failed'
) );

// Status Colors
define( 'MY_REAL_TIME_ORDERS_STATUS_COLORS', array(
    'pending' => '#ffba00',
    'processing' => '#73a724',
    'completed' => '#7ad03a',
    'cancelled' => '#a00',
    'refunded' => '#e2401c',
    'failed' => '#a00',
    'on-hold' => '#c6c1c1'
) );

// Plugin Features
define( 'MY_REAL_TIME_ORDERS_FEATURES', array(
    'real_time_updates' => true,
    'filters' => true,
    'auto_refresh' => true,
    'debug_mode' => true,
    'admin_panel' => true,
    'export_functionality' => false, // Future feature
    'notifications' => false, // Future feature
    'analytics' => false // Future feature
) );

// Performance Settings
define( 'MY_REAL_TIME_ORDERS_CACHE_ENABLED', true );
define( 'MY_REAL_TIME_ORDERS_CACHE_EXPIRY', 300 ); // 5 minutes
define( 'MY_REAL_TIME_ORDERS_MAX_ORDERS_PER_REQUEST', 50 );

// Security Settings
define( 'MY_REAL_TIME_ORDERS_RATE_LIMIT_ENABLED', true );
define( 'MY_REAL_TIME_ORDERS_RATE_LIMIT_REQUESTS', 60 ); // requests per minute
define( 'MY_REAL_TIME_ORDERS_RATE_LIMIT_WINDOW', 60 ); // 1 minute window

// Error Logging
define( 'MY_REAL_TIME_ORDERS_LOG_ENABLED', true );
define( 'MY_REAL_TIME_ORDERS_LOG_LEVEL', 'error' ); // error, warning, info, debug

// Development Mode
define( 'MY_REAL_TIME_ORDERS_DEV_MODE', false );

// Plugin URLs
define( 'MY_REAL_TIME_ORDERS_DOCS_URL', 'https://github.com/cittapet-git/woocommerce-real-time-orders-plugin' );
define( 'MY_REAL_TIME_ORDERS_SUPPORT_URL', 'https://github.com/cittapet-git/woocommerce-real-time-orders-plugin/issues' );
define( 'MY_REAL_TIME_ORDERS_CHANGELOG_URL', 'https://github.com/cittapet-git/woocommerce-real-time-orders-plugin/blob/main/CHANGELOG.md' );

// Hooks and Filters
define( 'MY_REAL_TIME_ORDERS_HOOKS', array(
    'init' => 'my_real_time_orders_init',
    'wp_enqueue_scripts' => 'my_real_time_orders_scripts',
    'admin_menu' => 'my_real_time_orders_admin_menu',
    'admin_init' => 'my_real_time_orders_check_woocommerce',
    'wp_ajax_get_latest_orders' => 'get_latest_orders_ajax_handler',
    'wp_ajax_nopriv_get_latest_orders' => 'get_latest_orders_ajax_handler'
) );

// Shortcode Configuration
define( 'MY_REAL_TIME_ORDERS_SHORTCODE', 'my_woocommerce_orders' );
define( 'MY_REAL_TIME_ORDERS_SHORTCODE_ATTS', array(
    'limit' => MY_REAL_TIME_ORDERS_DEFAULT_LIMIT,
    'status' => '',
    'show_filters' => 'true',
    'auto_refresh' => 'true',
    'debug' => 'false'
) );

// Plugin Dependencies
define( 'MY_REAL_TIME_ORDERS_DEPENDENCIES', array(
    'wordpress' => '5.0',
    'woocommerce' => '3.0',
    'php' => '7.4'
) );

// Plugin Requirements
define( 'MY_REAL_TIME_ORDERS_REQUIREMENTS', array(
    'php_extensions' => array( 'json', 'curl', 'mbstring' ),
    'wordpress_functions' => array( 'wp_enqueue_script', 'wp_localize_script', 'add_shortcode' ),
    'woocommerce_functions' => array( 'wc_get_orders', 'wc_get_order_status_name' )
) ); 