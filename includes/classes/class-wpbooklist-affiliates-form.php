<?php
/**
 * WPBookList WPBookList_Affiliates_Form Submenu Class
 *
 * @author   Jake Evans
 * @category admin
 * @package  classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Affiliates_Form', false ) ) :
	/**
	 * WPBookList_Affiliates_Form Class.
	 */
	class WPBookList_Affiliates_Form {

		/**
		 * Class Constructor
		 */
		public function __construct() {

			// Get Translations.
			require_once ROOT_WPBL_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();
		}


		/**
		 * Outputs the actual HTML for the tab.
		 */
		public function output_affiliates_form() {

			global $wpdb;
			$table_name  = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$opt_results = $wpdb->get_row( "SELECT * FROM $table_name" );

			if ( null === $opt_results->amazonaff || '' === $opt_results->amazonaff || 'wpbooklistid-20' === $opt_results->amazonaff ) {
				$amazonaff = $this->trans->trans_630 . '...';
			} else {
				$amazonaff = $opt_results->amazonaff;
			}

			if ( null === $opt_results->itunesaff || '' === $opt_results->itunesaff || '1010lnPx' === $opt_results->itunesaff ) {
				$itunesaff = $this->trans->trans_631 . '...';
			} else {
				$itunesaff = $opt_results->itunesaff;
			}

			$string1 = '<div id="wpbooklist-affiliate-container">
						<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_632 . '? ' . $this->trans->trans_633 . ' <span class="wpbooklist-color-orange-italic">WPBookList</span> ' . $this->trans->trans_634 . ' <span class="wpbooklist-color-orange-italic">WPBookList</span> ' . $this->trans->trans_635 . '!</p>
						<table>
						  	<tbody>
						  		<tr>
						  			<td>
						  				<label for="wpbooklist-amazon-affiliate-input">' . $this->trans->trans_630 . ': </label>
						  				<input type="text" class="wpbooklist-amazon-affiliate-input" id="wpbooklist-amazon-affiliate-library" value="' . $amazonaff . '" name="wpbooklist-amazon-affiliate-input"/>
					  				</td>
					  			</tr>
					  			<tr>
					  				<td>
					  					<label for="wpbooklist-itunes-affiliate-input">' . $this->trans->trans_631 . ': </label>
						  				<input type="text"  class="wpbooklist-itunes-affiliate-input" id="wpbooklist-itunes-affiliate-library" value="' . $itunesaff . '" name="wpbooklist-itunes-affiliate-input"/>
					  				</td>
					  			</tr>
					  			<tr>
					  				<td>
					  					<button id="wpbooklist-affiliate-button" type="button">' . $this->trans->trans_636 . '</button>
					  				</td>
								</tr>
							</tbody>
						</table>';

			$string2 = '</div>';

			return $string1 . $string2;
		}
	}

endif;
