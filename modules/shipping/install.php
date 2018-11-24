<?php
/**
 * Module: options
 * Desription: Install required table
 * 
 * @since  1.2
 */
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

global $wpdb;

$charset_collate = $wpdb->get_charset_collate();

$table_name = $wpdb->prefix . $this->table;

// $sql = "CREATE TABLE IF NOT EXISTS $table_name (
//   id mediumint(9) NOT NULL AUTO_INCREMENT,  
//   user_id mediumint(9),
//   orders TEXT,
//   subtotal VARCHAR(50),
//   discounts VARCHAR(10),
//   order_total VARCHAR(50),
//   date_ordered datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
//   notes TEXT,
//   cart_key TEXT,
//   checkout INT(1) DEFAULT 0,
//   PRIMARY KEY (id)
// ) $charset_collate;";

// dbDelta( $sql );