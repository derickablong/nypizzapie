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

$sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  user_id mediumint(9), 
  user_name VARCHAR(100),
  user_email VARCHAR(100),
  pin TEXT,  
  PRIMARY KEY (id)
) $charset_collate;";

dbDelta( $sql );