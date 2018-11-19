<?php
/**
 * WPBookList WPBookList_Affiliate_Form Tab Class
 *
 * @author   Jake Evans
 * @category ??????
 * @package  ??????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Affiliate_Form', false ) ) :
/**
 * WPBookList_Affiliate_Form Class.
 */
class WPBookList_Affiliate_Form {

	public static function output_affiliate_form(){

		// Perform check for previously-saved Amazon Authorization
		global $wpdb;
		$table_name = AFFILIATE_PREFIX . 'wpbooklist_jre_user_options';
		$opt_results = $wpdb->get_row("SELECT * FROM $table_name");

		if($opt_results->amazonaff == null || $opt_results->amazonaff == '' || $opt_results->amazonaff == 'wpbooklistid-20'){
			$amazonaff = 'Add Your Amazon Affiliate ID Here...';
		} else{
			$amazonaff = $opt_results->amazonaff;
		}

		if($opt_results->itunesaff == null || $opt_results->itunesaff == '' || $opt_results->itunesaff == '1010lnPx'){
			$itunesaff = 'Add Your iTunes Affiliate ID Here...';
		} else{
			$itunesaff = $opt_results->itunesaff;
		}

		$string1 = '<div id="wpbooklist-affiliate-container">
					<p>Got some Affiliate IDs? Then let <span class="wpbooklist-color-orange-italic">WPBookList</span> work for you by simply placing your Affiliate IDs in the appropriate spots below and clicking \'Save Affiliate IDs\'. Start making money with <span class="wpbooklist-color-orange-italic">WPBookList</span> today!</p>
					<table>
			          	<tbody>
			          		<tr>
			          			<td>
			          				<label for="wpbooklist-amazon-affiliate-input">Add your Amazon Affiliate ID: </label>
			          				<input type="text" class="wpbooklist-amazon-affiliate-input" id="wpbooklist-amazon-affiliate-library" value="'.$amazonaff.'" name="wpbooklist-amazon-affiliate-input"/>
		          				</td>
		          			</tr>
		          			<tr>
		          				<td>
		          					<label for="wpbooklist-itunes-affiliate-input">Add your iTunes Affiliate ID: </label>
			          				<input type="text"  class="wpbooklist-itunes-affiliate-input" id="wpbooklist-itunes-affiliate-library" value="'.$itunesaff.'" name="wpbooklist-itunes-affiliate-input"/>
		          				</td>
		          			</tr>
		          			<tr>
		          				<td>
		          					<button id="wpbooklist-affiliate-button" type="button">Save Affiliate IDs</button>
		          				</td>
	                    	</tr>
	                	</tbody>
			        </table>';

		$string2 = '</div>';

		return $string1.$string2;
	}
}

endif;