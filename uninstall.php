<?php
/**
 * Clean up the options 
 * and database tables
 * @package  nypizza
 */

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

global $wpdb;

// $table_options = "{$wpdb->prefix}pizza_options";
// $wpdb->query( "DROP TABLE IF EXISTS {$table_options}" );

// $table_orders = "{$wpdb->prefix}pizza_order";
// $wpdb->query( "DROP TABLE IF EXISTS {$table_orders}" );