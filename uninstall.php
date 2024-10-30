<?php 
if ( ! defined('ABSPATH') ) {
		die('You do not have sufficient permission to access it');
	}	
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit ();
global $wpdb;
$table_name = $wpdb->prefix . 'kt_redirect';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);
?>