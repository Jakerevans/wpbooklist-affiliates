<?php
/**
 * Class Affiliates_General_Functions - class-affiliates-general-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Affiliates_General_Functions', false ) ) :
	/**
	 * Affiliates_General_Functions class. Here we'll do things like enqueue scripts/css, set up menus, etc.
	 */
	class Affiliates_General_Functions {


		/**
		 * Verifies that the core WPBookList plugin is installed and activated - otherwise, the Extension doesn't load and a message is displayed to the user.
		 */
		public function wpbooklist_affiliates_core_plugin_required() {

			// Require core WPBookList Plugin.
			if ( ! is_plugin_active( 'wpbooklist/wpbooklist.php' ) && current_user_can( 'activate_plugins' ) ) {

				// Stop activation redirect and show error.
				wp_die( 'Whoops! This WPBookList Extension requires the Core WPBookList Plugin to be installed and activated! <br><a target="_blank" href="https://wordpress.org/plugins/wpbooklist/">Download WPBookList Here!</a><br><br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>' );
			}
		}

		/**
		 * Function that utilizes the filter in the core WPBookList plugin, resulting in a new tab.
		 *
		 * @param  array $tabs Array of WPBookList Tabs.
		 */
		public function wpbooklist_affiliate_tab( $tabs ) {
			$extra_tab = array(
				'affiliates' => __( 'Affiliate IDs', 'wpbooklist' ),
			);

			// Combine the two arrays.
			$tabs = array_merge( $tabs, $extra_tab );
			return $tabs;
		}

		/**
		 * Verifies the crown of the rose.
		 *
		 * @param  array $plugins List of plugins to activate & load.
		 */
		public function wpbooklist_affiliates_smell_rose() {

			global $wpdb;

			// Get license key from plugin options, if it's already been saved. If it has, don't display anything.
			$this->extension_settings = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_affiliates_settings' );

			// If the License Key just hasn't been entered yet...
			if ( null === $this->extension_settings->repw || '' === $this->extension_settings->repw ) {
				return;
			} else {

				if ( false !== stripos( $this->extension_settings->repw, '---' ) ) {

					$temp = explode( '---', $this->extension_settings->repw );

					if ( 'aod' === $temp[1] ) {

						// Get the date.
						require_once ROOT_WPBL_UTILITIES_DIR . 'class-wpbooklist-utilities-date.php';
						$utilities_date = new WPBookList_Utilities_Date();
						$this->date     = $utilities_date->wpbooklist_get_date_via_current_time( 'timestamp' );

						if ( 604800 < ( $this->date - (int) $temp[2] ) ) {

							$checker_good_flag = false;

							$san_check = wp_remote_get( 'https://wpbooklist.com/?edd_action=activate_license&item_id=' . EDD_SL_ITEM_ID_AFFILIATES . '&license=' . $temp[0] . '&url=' . get_site_url() );

							// Check the response code.
							$response_code    = wp_remote_retrieve_response_code( $san_check );
							$response_message = wp_remote_retrieve_response_message( $san_check );

							if ( 200 !== $response_code && ! empty( $response_message ) ) {
								return new WP_Error( $response_code, $response_message );
							} elseif ( 200 !== $response_code ) {
								$this->apireport = $this->apireport . 'Unknown error occurred with wp_remote_get() trying to build Books-a-Million link in the create_buy_links() function ';
								return new WP_Error( $response_code, 'Unknown error occurred with wp_remote_get() trying to build Books-a-Million link in the create_buy_links() function' );
							} else {
								$san_check = wp_remote_retrieve_body( $san_check );
								$san_check = json_decode( $san_check, true );

								if ( 'valid' === $san_check['license'] && $san_check['success'] ) {

									$this->date = $utilities_date->wpbooklist_get_date_via_current_time( 'timestamp' );

									$data         = array(
										'repw' => $temp[0] . '---aod---' . $this->date,
									);
									$format       = array( '%s' );
									$where        = array( 'ID' => 1 );
									$where_format = array( '%d' );
									$save_result = $wpdb->update( $wpdb->prefix . 'wpbooklist_affiliates_settings', $data, $where, $format, $where_format );

									$checker_good_flag = true;
								} else {
									$data         = array(
										'repw' => '',
									);
									$format       = array( '%s' );
									$where        = array( 'ID' => 1 );
									$where_format = array( '%d' );
									$save_result = $wpdb->update( $wpdb->prefix . 'wpbooklist_affiliates_settings', $data, $where, $format, $where_format );
								}
							}

							if ( ! $checker_good_flag ) {
								deactivate_plugins( AFFILIATES_ROOT_DIR . 'wpbooklist-affiliates.php' );
								return;
							}
						} else {
							return;
						}
					} else {

						$checker_good_flag = false;

						$san_check = wp_remote_get( 'https://wpbooklist.com/?edd_action=activate_license&item_id=' . EDD_SL_ITEM_ID_AFFILIATES . '&license=' . $this->extension_settings->repw . '&url=' . get_site_url() );

						// Check the response code.
						$response_code    = wp_remote_retrieve_response_code( $san_check );
						$response_message = wp_remote_retrieve_response_message( $san_check );

						if ( 200 !== $response_code && ! empty( $response_message ) ) {
							return new WP_Error( $response_code, $response_message );
						} elseif ( 200 !== $response_code ) {
							$this->apireport = $this->apireport . 'Unknown error occurred with wp_remote_get() trying to build Books-a-Million link in the create_buy_links() function ';
							return new WP_Error( $response_code, 'Unknown error occurred with wp_remote_get() trying to build Books-a-Million link in the create_buy_links() function' );
						} else {
							$san_check = wp_remote_retrieve_body( $san_check );
							$san_check = json_decode( $san_check, true );

							if ( 'valid' === $san_check['license'] && $san_check['success'] ) {

								// Get the date.
								require_once ROOT_WPBL_UTILITIES_DIR . 'class-wpbooklist-utilities-date.php';
								$utilities_date = new WPBookList_Utilities_Date();
								$this->date     = $utilities_date->wpbooklist_get_date_via_current_time( 'timestamp' );

								$data         = array(
									$san_checkthis->extension_settings->repw . '---aod---' . $this->date,
								);
								$format       = array( '%s' );
								$where        = array( 'ID' => 1 );
								$where_format = array( '%d' );
								$save_result = $wpdb->update( $wpdb->prefix . 'wpbooklist_affiliates_settings', $data, $where, $format, $where_format );

								$checker_good_flag = true;

							} else {
								$data         = array(
									'repw' => '',
								);
								$format       = array( '%s' );
								$where        = array( 'ID' => 1 );
								$where_format = array( '%d' );
								$save_result = $wpdb->update( $wpdb->prefix . 'wpbooklist_affiliates_settings', $data, $where, $format, $where_format );
							}
						}

						if ( ! $checker_good_flag ) {
							deactivate_plugins( AFFILIATES_ROOT_DIR . 'wpbooklist-affiliates.php' );
							return;
						}
					}
				} else {

					$checker_good_flag = false;

					$san_check = wp_remote_get( 'https://wpbooklist.com/?edd_action=activate_license&item_id=' . EDD_SL_ITEM_ID_AFFILIATES . '&license=' . $this->extension_settings->repw . '&url=' . get_site_url() );

					// Check the response code.
					$response_code    = wp_remote_retrieve_response_code( $san_check );
					$response_message = wp_remote_retrieve_response_message( $san_check );

					if ( 200 !== $response_code && ! empty( $response_message ) ) {
						return new WP_Error( $response_code, $response_message );
					} elseif ( 200 !== $response_code ) {
						$this->apireport = $this->apireport . 'Unknown error occurred with wp_remote_get() trying to build Books-a-Million link in the create_buy_links() function ';
						return new WP_Error( $response_code, 'Unknown error occurred with wp_remote_get() trying to build Books-a-Million link in the create_buy_links() function' );
					} else {
						$san_check = wp_remote_retrieve_body( $san_check );
						$san_check = json_decode( $san_check, true );

						if ( 'valid' === $san_check['license'] && $san_check['success'] ) {

							// Get the date.
							require_once ROOT_WPBL_UTILITIES_DIR . 'class-wpbooklist-utilities-date.php';
							$utilities_date = new WPBookList_Utilities_Date();
							$this->date     = $utilities_date->wpbooklist_get_date_via_current_time( 'timestamp' );

							$data         = array(
								'repw' => $this->extension_settings->repw . '---aod---' . $this->date,
							);
							$format       = array( '%s' );
							$where        = array( 'ID' => 1 );
							$where_format = array( '%d' );
							$save_result = $wpdb->update( $wpdb->prefix . 'wpbooklist_affiliates_settings', $data, $where, $format, $where_format );

							$checker_good_flag = true;

						} else {
							$data         = array(
								'repw' => '',
							);
							$format       = array( '%s' );
							$where        = array( 'ID' => 1 );
							$where_format = array( '%d' );
							$save_result = $wpdb->update( $wpdb->prefix . 'wpbooklist_affiliates_settings', $data, $where, $format, $where_format );
						}
					}

					if ( ! $checker_good_flag ) {
						deactivate_plugins( AFFILIATES_ROOT_DIR . 'wpbooklist-affiliates.php' );

						if ( isset( $_SERVER['REQUEST_URI'] ) ) {
							//header( 'Location: ' . filter_var( wp_unslash( $_SERVER['REQUEST_URI'] ), FILTER_SANITIZE_STRING ) );
						}

						return;
					}
				}
			}
		}

		/**
		 * Adds in the 'Enter License Key' text input and submit button.
		 *
		 * @param  array $links List of existing plugin action links.
		 * @return array List of modified plugin action links.
		 */
		public function wpbooklist_affiliates_pluginspage_nonce_entry( $links ) {

			global $wpdb;

			require_once ROOT_WPBL_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$trans = new WPBookList_Translations();

			$this->extension_settings = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_affiliates_settings' );

			if ( null === $this->extension_settings->repw || '' === $this->extension_settings->repw ) {
				$value = $trans->trans_613;
			} else {

				if ( false !== stripos( $this->extension_settings->repw, '---' ) ) {
					$temp  = explode( '---', $this->extension_settings->repw );
					$value = $temp[0];
				} else {
					$value = $this->extension_settings->repw;
				}
			}

			$form_html = '
				<form>
					<input id="wpbooklist-extension-genreric-key-plugins-page-input-affiliates" class="wpbooklist-extension-genreric-key-plugins-page-input" type="text" placeholder="' . $trans->trans_613 . '" value="' . $value . '"></input>
					<button id="wpbooklist-extension-genreric-key-plugins-page-button-affiliates" class="wpbooklist-extension-genreric-key-plugins-page-button">' . $trans->trans_614 . '</button>
				</form>';

			array_push( $links, $form_html );

			return $links;

		}

		/**
		 * Displays the 'Enter Your License Key' message at the top of the dashboard if the user hasn't done so already.
		 */
		public function wpbooklist_affiliates_top_dashboard_license_notification() {

			global $wpdb;

			// Get license key from plugin options, if it's already been saved. If it has, don't display anything.
			$this->extension_settings = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_affiliates_settings' );

			if ( null === $this->extension_settings->repw || '' === $this->extension_settings->repw ) {

				require_once ROOT_WPBL_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
				$trans = new WPBookList_Translations();

				echo '
				<div class="notice notice-success is-dismissible">
					<form class="wpbooklist-extension-genreric-key-dashboard-form" id="wpbooklist-extension-genreric-key-dashboard-form-affiliates">
						<p class="wpbooklist-extension-genreric-key-dashboard-title">' . $trans->trans_617 . '</p>
						<input id="wpbooklist-extension-genreric-key-dashboard-input-affiliates" class="wpbooklist-extension-genreric-key-dashboard-input" type="text" placeholder="' . $trans->trans_613 . '" value="' . $trans->trans_613 . '"></input>
						<button data-ext="affiliates" id="wpbooklist-extension-genreric-key-dashboard-button-affiliates" class="wpbooklist-extension-genreric-key-dashboard-button">' . $trans->trans_616 . '</button>
					</form>
				</div>';
			}
		}

		/**
		 *  Here we take the Constant defined in wpbooklist.php that holds the values that all our nonces will be created from, we create the actual nonces using wp_create_nonce, and the we define our new, final nonces Constant, called WPBOOKLIST_FINAL_NONCES_ARRAY.
		 */
		public function wpbooklist_affiliates_create_nonces() {

			$temp_array = array();
			foreach ( json_decode( AFFILIATES_NONCES_ARRAY ) as $key => $noncetext ) {
				$nonce              = wp_create_nonce( $noncetext );
				$temp_array[ $key ] = $nonce;
			}

			// Defining our final nonce array.
			define( 'AFFILIATES_FINAL_NONCES_ARRAY', wp_json_encode( $temp_array ) );

		}

		/**
		 *  Runs once upon extension activation and adds it's version number to the 'extensionversions' column in the 'wpbooklist_jre_user_options' table of the core plugin.
		 */
		public function wpbooklist_affiliates_record_extension_version() {
			global $wpdb;
			$existing_string = $wpdb->get_row( 'SELECT * from ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );

			// Check to see if Extension is already registered.
			if ( false !== strpos( $existing_string->extensionversions, 'affiliates' ) ) {
				$split_string = explode( 'affiliates', $existing_string->extensionversions );
				$first_part   = $split_string[0];
				$last_part    = substr( $split_string[1], 5 );
				$new_string   = $first_part . 'affiliates' . WPBOOKLIST_AFFILIATES_VERSION_NUM . $last_part;
			} else {
				$new_string = $existing_string->extensionversions . 'affiliates' . WPBOOKLIST_AFFILIATES_VERSION_NUM;
			}

			$data         = array(
				'extensionversions' => $new_string,
			);
			$format       = array( '%s' );
			$where        = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$wpdb->update( $wpdb->prefix . 'wpbooklist_jre_user_options', $data, $where, $format, $where_format );

		}

		/**
		 *  Function to run the compatability code in the Compat class for upgrades/updates, if stored version number doesn't match the defined global in wpbooklist-affiliates.php
		 */
		public function wpbooklist_affiliates_update_upgrade_function() {

			// Get current version #.
			global $wpdb;
			$existing_string = $wpdb->get_row( 'SELECT * from ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );

			// Check to see if Extension is already registered and matches this version.
			if ( false !== strpos( $existing_string->extensionversions, 'affiliates' ) ) {
				$split_string = explode( 'affiliates', $existing_string->extensionversions );
				$version      = substr( $split_string[1], 0, 5 );

				// If version number does not match the current version number found in wpbooklist.php, call the Compat class and run upgrade functions.
				if ( WPBOOKLIST_AFFILIATES_VERSION_NUM !== $version ) {
					require_once AFFILIATES_CLASS_COMPAT_DIR . 'class-affiliates-compat-functions.php';
					$compat_class = new Affiliates_Compat_Functions();
				}
			}
		}

		/**
		 * Adding the admin js file
		 */
		public function wpbooklist_affiliates_admin_js() {

			wp_register_script( 'wpbooklist_affiliates_adminjs', AFFILIATES_JS_URL . 'wpbooklist_affiliates_admin.min.js', array( 'jquery' ), WPBOOKLIST_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once ROOT_WPBL_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$trans = new WPBookList_Translations();

			// Localize the script with the appropriate translation array from the Translations class.
			$translation_array1 = $trans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( AFFILIATES_FINAL_NONCES_ARRAY, true ) );

			// Adding some other individual values we may need.
			$final_array_of_php_values['AFFILIATES_ROOT_IMG_ICONS_URL'] = AFFILIATES_ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['AFFILIATES_ROOT_IMG_URL']       = AFFILIATES_ROOT_IMG_URL;
			$final_array_of_php_values['FOR_TAB_HIGHLIGHT']                         = admin_url() . 'admin.php';
			$final_array_of_php_values['SAVED_ATTACHEMENT_ID']                      = get_option( 'media_selector_attachment_id', 0 );

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wphealthtracker_php_variables' object (like wphealthtracker_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist_affiliates_adminjs', 'wpbooklistAffiliatesPhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'wpbooklist_affiliates_adminjs' );

			return $final_array_of_php_values;

		}

		/**
		 * Adding the frontend js file
		 */
		public function wpbooklist_affiliates_frontend_js() {

			wp_register_script( 'wpbooklist_affiliates_frontendjs', AFFILIATES_JS_URL . 'wpbooklist_affiliates_frontend.min.js', array( 'jquery' ), WPBOOKLIST_AFFILIATES_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once ROOT_WPBL_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$trans = new WPBookList_Translations();

			// Localize the script with the appropriate translation array from the Translations class.
			$translation_array1 = $trans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( AFFILIATES_FINAL_NONCES_ARRAY, true ) );

			// Adding some other individual values we may need.
			$final_array_of_php_values['AFFILIATES_ROOT_IMG_ICONS_URL'] = AFFILIATES_ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['AFFILIATES_ROOT_IMG_URL']       = AFFILIATES_ROOT_IMG_URL;

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wphealthtracker_php_variables' object (like wphealthtracker_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist_affiliates_frontendjs', 'wpbooklistAffiliatesPhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'wpbooklist_affiliates_frontendjs' );

			return $final_array_of_php_values;

		}

		/**
		 * Adding the admin css file
		 */
		public function wpbooklist_affiliates_admin_style() {

			wp_register_style( 'wpbooklist_affiliates_adminui', AFFILIATES_CSS_URL . 'wpbooklist-affiliates-main-admin.css', null, WPBOOKLIST_AFFILIATES_VERSION_NUM );
			wp_enqueue_style( 'wpbooklist_affiliates_adminui' );

		}

		/**
		 * Adding the frontend css file
		 */
		public function wpbooklist_affiliates_frontend_style() {

			wp_register_style( 'wpbooklist_affiliates_frontendui', AFFILIATES_CSS_URL . 'wpbooklist-affiliates-main-frontend.css', null, WPBOOKLIST_AFFILIATES_VERSION_NUM );
			wp_enqueue_style( 'wpbooklist_affiliates_frontendui' );

		}

		/**
		 *  Function to add table names to the global $wpdb.
		 */
		public function wpbooklist_affiliates_register_table_name() {
			global $wpdb;
			$wpdb->wpbooklist_affiliates_settings = "{$wpdb->prefix}wpbooklist_affiliates_settings";
		}

		/**
		 *  Function that calls the Style and Scripts needed for displaying of admin pointer messages.
		 */
		public function wpbooklist_affiliates_admin_pointers_javascript() {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_script( 'utils' );
		}

		/**
		 *  Runs once upon plugin activation and creates the table that holds info on WPBookList Pages & Posts.
		 */
		public function wpbooklist_affiliates_create_tables() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_affiliates_register_table_name();

			$sql_create_table1 = "CREATE TABLE {$wpdb->wpbooklist_affiliates_settings}
			(
				ID bigint(190) auto_increment,
				repw varchar(255),
				PRIMARY KEY  (ID),
				KEY repw (repw)
			) $charset_collate; ";

			// If table doesn't exist, create table and add initial data to it.
			$test_name = $wpdb->prefix . 'wpbooklist_affiliates_settings';
			if ( $test_name !== $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {
				dbDelta( $sql_create_table1 );
				$table_name = $wpdb->prefix . 'wpbooklist_affiliates_settings';
				$wpdb->insert( $table_name, array( 'ID' => 1, ) );
			}
		}
	}
endif;
