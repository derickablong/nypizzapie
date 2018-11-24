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
  category VARCHAR(100), 
  name TEXT,
  options TEXT,
  is_multiple INT(1) DEFAULT 0,  
  allow_half INT(1) DEFAULT 0,
  allow_quantity INT(1) DEFAULT 0,
  preselected INT(1) DEFAULT 0,
  date_updated datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
  status INT(1) DEFAULT 0,
  is_logic INT(1) DEFAULT 0,
  logic VARCHAR(100),
  PRIMARY KEY (id)
) $charset_collate;";

dbDelta( $sql );