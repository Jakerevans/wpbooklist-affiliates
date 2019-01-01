<?php
/**
 * WordPress Book List Affiliates Extension
 *
 * @package     WordPress Book List Affiliates Extension
 * @author      Jake Evans
 * @copyright   2018 Jake Evans
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WPBookList Affiliates Extension
 * Plugin URI: https://www.jakerevans.com
 * Description: An Extension for WPBookList that allows the user to include their own Affiliate IDs
 * Version: 1.0.0
 * Author: Jake Evans
 * Text Domain: wpbooklist
 * Author URI: https://www.jakerevans.com
 */

/*
 * SETUP NOTES:
 *
 * Change all filename instances from affiliates to desired plugin name
 *
 * Modify Plugin Name
 *
 * Modify Description
 *
 * Modify Version Number in Block comment and in Constant
 *
 * Find & Replace these 3 strings:
 * affiliates
 * Affiliates
 * AFFILIATES
 *
 * Install Gulp & all Plugins listed in gulpfile.js
 *
 *
 *
 *
 *
 */




// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

/* REQUIRE STATEMENTS */
	require_once 'includes/class-affiliates-general-functions.php';
	require_once 'includes/class-affiliates-ajax-functions.php';
	require_once 'includes/classes/update/class-wpbooklist-affiliates-update.php';
/* END REQUIRE STATEMENTS */

/* CONSTANT DEFINITIONS */

// This Extension's Version Number.
define( 'WPBOOKLIST_AFFILIATES_VERSION_NUM', '1.0.0' );

// This is the URL our updater / license checker pings. This should be the URL of the site with EDD installed.
define( 'EDD_SL_STORE_URL_AFFILIATES', 'https://wpbooklist.com' );

// The id of your product in EDD.
define( 'EDD_SL_ITEM_ID_AFFILIATES', 46 );

// Root plugin folder directory.
define( 'AFFILIATES_ROOT_DIR', plugin_dir_path( __FILE__ ) );

// Root WordPress Plugin Directory.
define( 'AFFILIATES_ROOT_WP_PLUGINS_DIR', str_replace( '/wpbooklist-affiliates', '', plugin_dir_path( __FILE__ ) ) );

// Root plugin folder URL .
define( 'AFFILIATES_ROOT_URL', plugins_url() . '/wpbooklist-affiliates/' );

// Root Classes Directory.
define( 'AFFILIATES_CLASS_DIR', AFFILIATES_ROOT_DIR . 'includes/classes/' );

// Root Update Directory.
define( 'AFFILIATES_UPDATE_DIR', AFFILIATES_CLASS_DIR . 'update/' );

// Root REST Classes Directory.
define( 'AFFILIATES_CLASS_REST_DIR', AFFILIATES_ROOT_DIR . 'includes/classes/rest/' );

// Root Compatability Classes Directory.
define( 'AFFILIATES_CLASS_COMPAT_DIR', AFFILIATES_ROOT_DIR . 'includes/classes/compat/' );

// Root Transients Directory.
define( 'AFFILIATES_CLASS_TRANSIENTS_DIR', AFFILIATES_ROOT_DIR . 'includes/classes/transients/' );

// Root Image URL.
define( 'AFFILIATES_ROOT_IMG_URL', AFFILIATES_ROOT_URL . 'assets/img/' );

// Root Image Icons URL.
define( 'AFFILIATES_ROOT_IMG_ICONS_URL', AFFILIATES_ROOT_URL . 'assets/img/icons/' );

// Root CSS URL.
define( 'AFFILIATES_CSS_URL', AFFILIATES_ROOT_URL . 'assets/css/' );

// Root JS URL.
define( 'AFFILIATES_JS_URL', AFFILIATES_ROOT_URL . 'assets/js/' );

// Root UI directory.
define( 'AFFILIATES_ROOT_INCLUDES_UI', AFFILIATES_ROOT_DIR . 'includes/ui/' );

// Root UI Admin directory.
define( 'AFFILIATES_ROOT_INCLUDES_UI_ADMIN_DIR', AFFILIATES_ROOT_DIR . 'includes/ui/admin/' );

if ( ! defined( 'WPBOOKLIST_VERSION_NUM' ) ) {
	define( 'WPBOOKLIST_VERSION_NUM', '6.1.2' );
}

// Root WPBL Dir.
if ( ! defined( 'ROOT_WPBL_DIR' ) ) {
	define( 'ROOT_WPBL_DIR', AFFILIATES_ROOT_WP_PLUGINS_DIR . 'wpbooklist/' );
}

// Root WPBL Url.
if ( ! defined( 'ROOT_WPBL_URL' ) ) {
	define( 'ROOT_WPBL_URL', plugins_url() . '/wpbooklist/' );
}

// Root WPBL Classes Dir.
if ( ! defined( 'ROOT_WPBL_CLASSES_DIR' ) ) {
	define( 'ROOT_WPBL_CLASSES_DIR', ROOT_WPBL_DIR . 'includes/classes/' );
}

// Root WPBL Transients Dir.
if ( ! defined( 'ROOT_WPBL_TRANSIENTS_DIR' ) ) {
	define( 'ROOT_WPBL_TRANSIENTS_DIR', ROOT_WPBL_CLASSES_DIR . 'transients/' );
}

// Root WPBL Translations Dir.
if ( ! defined( 'ROOT_WPBL_TRANSLATIONS_DIR' ) ) {
	define( 'ROOT_WPBL_TRANSLATIONS_DIR', ROOT_WPBL_CLASSES_DIR . 'translations/' );
}

// Root WPBL Root Img Icons Dir.
if ( ! defined( 'ROOT_WPBL_IMG_ICONS_URL' ) ) {
	define( 'ROOT_WPBL_IMG_ICONS_URL', ROOT_WPBL_URL . 'assets/img/icons/' );
}

// Root WPBL Root Utilities Dir.
if ( ! defined( 'ROOT_WPBL_UTILITIES_DIR' ) ) {
	define( 'ROOT_WPBL_UTILITIES_DIR', ROOT_WPBL_CLASSES_DIR . 'utilities/' );
}


// Define the Uploads base directory.
$uploads     = wp_upload_dir();
$upload_path = $uploads['basedir'];
define( 'AFFILIATES_UPLOADS_BASE_DIR', $upload_path . '/' );

// Define the Uploads base URL.
$upload_url = $uploads['baseurl'];
define( 'AFFILIATES_UPLOADS_BASE_URL', $upload_url . '/' );

// Nonces array.
define( 'AFFILIATES_NONCES_ARRAY',
	wp_json_encode(array(
		'adminnonce1' => 'wpbooklist_affiliates_save_review_key_action_callback',
		'adminnonce2' => 'wpbooklist_affiliate_action_callback',
	))
);

/* END OF CONSTANT DEFINITIONS */

/* MISC. INCLUSIONS & DEFINITIONS */

	// Loading textdomain.
	load_plugin_textdomain( 'wpbooklist', false, AFFILIATES_ROOT_DIR . 'languages' );

/* END MISC. INCLUSIONS & DEFINITIONS */

/* CLASS INSTANTIATIONS */

	// Call the class found in wpbooklist-functions.php.
	$affiliates_general_functions = new Affiliates_General_Functions();

	// Call the class found in wpbooklist-functions.php.
	$affiliates_ajax_functions = new Affiliates_Ajax_Functions();

	// Include the Update Class.
	$affiliates_update_functions = new WPBookList_Affiliates_Update();


/* END CLASS INSTANTIATIONS */


/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// Displays the 'Enter Your License Key' message at the top of the dashboard if the user hasn't done so already.
	add_action( 'admin_notices', array( $affiliates_general_functions, 'wpbooklist_affiliates_top_dashboard_license_notification' ) );

	// Function that adds in the License Key Submission form on this Extension's entry on the plugins page.
	add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $affiliates_general_functions, 'wpbooklist_affiliates_pluginspage_nonce_entry' ) );

	// Function that loads up the menu page entry for this Extension.
	add_filter( 'wpbooklist_add_sub_menu', array( $affiliates_general_functions, 'wpbooklist_affiliates_submenu' ) );

	// Adding the function that will take our AFFILIATES_NONCES_ARRAY Constant from above and create actual nonces to be passed to Javascript functions.
	add_action( 'init', array( $affiliates_general_functions, 'wpbooklist_affiliates_create_nonces' ) );

	// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'plugins_loaded', array( $affiliates_general_functions, 'wpbooklist_affiliates_update_upgrade_function' ) );

	// Adding the admin js file.
	add_action( 'admin_enqueue_scripts', array( $affiliates_general_functions, 'wpbooklist_affiliates_admin_js' ) );

	// Adding the frontend js file.
	add_action( 'wp_enqueue_scripts', array( $affiliates_general_functions, 'wpbooklist_affiliates_frontend_js' ) );

	// Adding the admin css file for this extension.
	add_action( 'admin_enqueue_scripts', array( $affiliates_general_functions, 'wpbooklist_affiliates_admin_style' ) );

	// Adding the Front-End css file for this extension.
	add_action( 'wp_enqueue_scripts', array( $affiliates_general_functions, 'wpbooklist_affiliates_frontend_style' ) );

	// Function to add table names to the global $wpdb.
	add_action( 'admin_footer', array( $affiliates_general_functions, 'wpbooklist_affiliates_register_table_name' ) );

	// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'admin_footer', array( $affiliates_general_functions, 'wpbooklist_affiliates_admin_pointers_javascript' ) );

	// Creates tables upon activation.
	register_activation_hook( __FILE__, array( $affiliates_general_functions, 'wpbooklist_affiliates_create_tables' ) );

	// Runs once upon extension activation and adds it's version number to the 'extensionversions' column in the 'wpbooklist_jre_user_options' table of the core plugin.
	register_activation_hook( __FILE__, array( $affiliates_general_functions, 'wpbooklist_affiliates_record_extension_version' ) );

	// License verification function.
	add_filter( 'admin_footer', array( $affiliates_general_functions, 'wpbooklist_affiliates_verify_license' ) );

	// Displays the 'Enter Your License Key' message at the top of the dashboard if the user hasn't done so already.
	add_action( 'admin_notices', array( $affiliates_general_functions, 'wpbooklist_affiliates_top_dashboard_license_notification' ) );


global $wpdb;
$test_name = $wpdb->prefix . 'wpbooklist_affiliates_settings';
if ( $test_name === $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {
	$extension_settings = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_affiliates_settings' );
	if ( false !== stripos( $extension_settings->repw, 'aod' ) ) {
		add_filter( 'wpbooklist_add_tab_settings', array( $affiliates_general_functions, 'wpbooklist_affiliate_tab' ) );
	}
}

	// Function that adds in the License Key Submission form on this Extension's entry on the plugins page.
	add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $affiliates_general_functions, 'wpbooklist_affiliates_pluginspage_nonce_entry' ) );

	// Verifies that the core WPBookList plugin is installed and activated - otherwise, the Extension doesn't load and a message is displayed to the user.
	register_activation_hook( __FILE__, array( $affiliates_general_functions, 'wpbooklist_affiliates_core_plugin_required' ) );



/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// For receiving user feedback upon deactivation & deletion.
	add_action( 'wp_ajax_affiliates_exit_results_action', array( $affiliates_ajax_functions, 'affiliates_exit_results_action_callback' ) );

	// Callback function for handling the saving of the user's License Key.
	add_action( 'wp_ajax_wpbooklist_affiliates_save_review_key_action', array( $affiliates_ajax_functions, 'wpbooklist_affiliates_save_review_key_action_callback' ) );

	// Callback function for saving Affiliate IDs.
	add_action( 'wp_ajax_wpbooklist_affiliate_action', array( $affiliates_ajax_functions, 'wpbooklist_affiliate_action_callback' ) );

/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */






















