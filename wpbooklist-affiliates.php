<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: WPBookList Affiliate IDs Extension
Plugin URI: https://www.jakerevans.com
Description: An Extension for WPBookList that allows the user to include their own Affiliate IDs
Version: 1.0.1
Author: Jake Evans - Forward Creation
Author URI: https://www.jakerevans.com
License: GPL2
*/ 

/*
CHANGELOG
= 1.0.1 =
	1. Fixed issue that prevented Affiliate IDs from being saved
	2. Introduced code to reload page upon saving of affiliate IDs
*/

global $wpdb;
require_once('includes/affiliate-functions.php');
require_once('includes/affiliate-ajaxfunctions.php');

// Root plugin folder URL of this extension
define('AFFILIATE_ROOT_URL', plugins_url().'/wpbooklist-affiliates/');

// Grabbing database prefix
define('AFFILIATE_PREFIX', $wpdb->prefix);

// Root plugin folder directory for this extension
define('AFFILIATE_ROOT_DIR', plugin_dir_path(__FILE__));

// Root Image Icons URL of this extension
define('AFFILIATE_ROOT_IMG_ICONS_URL', AFFILIATE_ROOT_URL.'assets/img/');

// Root Classes Directory for this extension
define('AFFILIATE_CLASS_DIR', AFFILIATE_ROOT_DIR.'includes/classes/');

// Root CSS URL for this extension
define('AFFILIATE_ROOT_CSS_URL', AFFILIATE_ROOT_URL.'assets/css/');

// Adding the front-end ui css file for this extension
add_action('wp_enqueue_scripts', 'wpbooklist_jre_affiliate_frontend_ui_style');

// Adding the admin css file for this extension
add_action('admin_enqueue_scripts', 'wpbooklist_jre_affiliate_admin_style' );

// Handles various aestetic functions for the back end
add_action( 'admin_footer', 'wpbooklist_affiliate_various_aestetic_bits_back_end' );

// For saving affiliate IDs
add_action( 'admin_footer', 'wpbooklist_affiliate_action_javascript' );
add_action( 'wp_ajax_wpbooklist_affiliate_action', 'wpbooklist_affiliate_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_affiliate_action', 'wpbooklist_affiliate_action_callback' );


/*
 * Function that utilizes the filter in the core WPBookList plugin, resulting in a new tab. Possible options for the first argument in the 'Add_filter' function below are:
 *  - 'wpbooklist_add_tab_books'
 *  - 'wpbooklist_add_tab_display'
 *
 *
 *
 * The instance of "Affiliate" in the $extra_tab array can be replaced with whatever you want - but the 'affiliate' instance MUST be your one-word descriptor.
*/
add_filter('wpbooklist_add_tab_settings', 'wpbooklist_affiliate_tab');
function wpbooklist_affiliate_tab($tabs) {
 	$extra_tab = array(
		'affiliates'  => __("Affiliate IDs", 'plugin-textdomain'),
	);
 
	// combine the two arrays
	$tabs = array_merge($tabs, $extra_tab);
	return $tabs;
}

?>