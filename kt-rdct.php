<?php
/*
Plugin Name: kt-redirect-URL
Plugin URI: https://www.realitypremedia.com
Description: This plugin used for redrecting the URL which are not mentioned / listed in the menus.
Version: 1.0.1
Licence: GPLv2
Author: Komalchand
Author URI: https://profiles.wordpress.org/ktbrothers
*/
if ( ! defined('ABSPATH') ) {
	die();
}

require_once( plugin_dir_path( __FILE__ ) . 'include/kt-rdct-class.php');
$objRdct = new ktrdctClass();
global $wpdb;
$table_name = $wpdb->prefix . "kt_redirect";


function add_ktrdct_plug() {
	global $wpdb;
	$table_name = $wpdb->prefix . "kt_redirect";
	$MSQL = "show tables like '$table_name'";
	if($wpdb->get_var($MSQL) != $table_name)
	{
	   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  rdct_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  rdct_title varchar(255) NULL,
		  rdct_dest_link varchar(255) NULL,
		  rdct_assign_posts text NULL,
		  rdct_menus VARCHAR(256) NOT NULL,
		  rdct_status TINYINT(2) NOT NULL,
		  PRIMARY KEY id (id)
		) ";

		require_once(ABSPATH . "wp-admin/includes/upgrade.php");
		dbDelta($sql);
	}
}	
register_activation_hook(__FILE__,'add_ktrdct_plug'); // Plugin Activation Hook


function rdct_Menu() /* Creating Menus */
{	add_menu_page(__('Redirect List'),'Redirect ALL', 8,'rdct_add&act=add', 'kt_rdct_form_display'); // Adding menu
	add_submenu_page('rdct/kt-rdct.php', 'Redirection Setting', 'Redirect Setting', 8, 'rdct_add', 'kt_rdct_form_display'); // Adding Sub menus	
}
add_action('admin_menu', 'rdct_Menu');

function kt_rdct_form_display() {
	require( plugin_dir_path( __FILE__ ) . 'include/kt-rdct-new.php');
}

function kt_rdct_inc_script_styles($hook){
	$plugin_url = plugin_dir_url(__FILE__);
	if(isset($_REQUEST["page"]) && ($_REQUEST["page"] == 'rdct_add') && ($hook == 'admin_page_rdct_add')): // Hook verified for files to be included this plugin page only
		wp_enqueue_script('bootstrap-js', $plugin_url. 'js/bootstrap.min.js');
		wp_enqueue_script('bootstrap-multiselect-js', $plugin_url. 'js/bootstrap-multiselect.js');
		wp_enqueue_script('jquery.dataTables-js', $plugin_url. 'js/jquery.dataTables.js');
		wp_enqueue_script('features-js', $plugin_url. 'js/rdct-features.js');
				
		wp_enqueue_style('bootstrap-min-css', $plugin_url. 'css/bootstrap.min.css');
		wp_enqueue_style('bootstrap-multiselect-css',  $plugin_url. 'css/bootstrap-multiselect.css');
		wp_enqueue_style('demo_table-css', $plugin_url. 'css/demo_table.css');
		wp_enqueue_style('features-css', $plugin_url. 'css/rdct-features.css');
	endif;	
}
add_action( 'admin_enqueue_scripts', 'kt_rdct_inc_script_styles' ); // Script & style included by specified admin hook

if(isset($_POST["add_rdct"])):
	if(!function_exists('wp_get_current_user')) {
		include(ABSPATH . "wp-includes/pluggable.php"); 
	}	
	if((int)$_POST["add_rdct"] == 1 && current_user_can('manage_options') && isset( $_POST['kt_rdct_add_tkn'] ) || wp_verify_nonce( $_POST['kt_rdct_add_tkn'], 'kt_add_rdct') && check_admin_referer('kt_rdct_add_tkn')) { 
		$objRdct->kt_rdct_addNew_redirect($table_name = $wpdb->prefix . "kt_redirect",$_POST); // Save setting, add_rdct = 1
	}
	else if((int)$_POST["add_rdct"] == 2 && current_user_can('manage_options') && isset( $_POST['kt_rdct_edit_tkn'] ) || wp_verify_nonce( $_POST['kt_rdct_edit_tkn'], 'kt_edit_rdct') && check_admin_referer('kt_rdct_edit_tkn')) {
		$objRdct->kt_rdct_updNew_redirect($table_name = $wpdb->prefix . "kt_redirect",$_POST); // Update setting, add_rdct = 2
	}
endif;
	
function kt_rdct_page_valid_test(){ // Redirection function for which plugin created
	global $wpdb;
	$getpost_SQL="select rdct_dest_link, rdct_menus, rdct_assign_posts from ".$wpdb->prefix . "kt_redirect where rdct_status=0 AND id<> ''";
	$result_post = $wpdb->get_results($getpost_SQL);
	//var_dump($result_post);
	if(sizeof($result_post)>0){
		foreach($result_post as $rdct_menu){
			$rdct_menu_arr = explode(",", $rdct_menu->rdct_menus);
			$rdct_post_arr = explode(",", $rdct_menu->rdct_assign_posts);
			$redirect_link = $rdct_menu->rdct_dest_link;
		}
		$cur_url = "//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		$postid = url_to_postid($cur_url);
		//echo "postid :".$postid;
		//$valid_page_id_arr[] = $postid;
		$menulist_arr = $rdct_menu_arr;
		for($mr=0; $mr<count($menulist_arr); $mr++){
			$menu_array = wp_get_nav_menu_items($menulist_arr[$mr]);
			foreach($menu_array as $pageres):
				$valid_page_id_arr[] = $pageres->object_id;
			endforeach;
		}	
		
		for($pr=0; $pr<count($rdct_post_arr); $pr++){
			$valid_page_id_arr[] = $rdct_post_arr[$pr];
		}		
		asort($valid_page_id_arr);	
		
		$total_pageId_list = array_values(array_unique($valid_page_id_arr));
		//print_r($total_pageId_list);
		if(in_array($postid, $total_pageId_list) || is_front_page() || is_home()){} else{ 
			if(isset($redirect_link) && $redirect_link!=''){
				wp_redirect( $redirect_link );
				exit;
			} else{
				wp_redirect( home_url() );
				exit;
			}				
		}
	}
}
add_action( 'template_redirect', 'kt_rdct_page_valid_test' ); // Redirection  Hook
?>