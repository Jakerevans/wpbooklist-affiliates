<?php


/*
 * Below is a bulkbookupload ajax function and callback, 
 * complete with console.logs and echos to verify functionality
 */

function wpbooklist_affiliate_action_javascript() { 
	?>
  	<script type="text/javascript" >
  	"use strict";
  	jQuery(document).ready(function($) {
  		$(document).on("click","#wpbooklist-affiliate-button", function(event){

  			var amazonAff = $('#wpbooklist-amazon-affiliate-library').val();
  			var itunesAff = $('#wpbooklist-itunes-affiliate-library').val();

		  	var data = {
				'action': 'wpbooklist_affiliate_action',
				'security': '<?php echo wp_create_nonce( "wpbooklist_affiliate_action_callback" ); ?>',
				'amazonaff':amazonAff,
				'itunesaff':itunesAff
			};
			console.log(data);

	     	var request = $.ajax({
			    url: ajaxurl,
			    type: "POST",
			    data:data,
			    timeout: 0,
			    success: function(response) {
			    	document.location.reload(true);
			    	console.log(response);
			    },
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
		            console.log(textStatus);
		            console.log(jqXHR);
				}
			});

			event.preventDefault ? event.preventDefault() : event.returnValue = false;
	  	});
	});
	</script>
	<?php
}

// Callback function for creating backups
function wpbooklist_affiliate_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_affiliate_action_callback', 'security' );
	$itunes_aff = filter_var($_POST['itunesaff'],FILTER_SANITIZE_STRING);
	$amazon_aff = filter_var($_POST['amazonaff'],FILTER_SANITIZE_STRING);

	if($itunes_aff == 'Add Your iTunes Affiliate ID Here...'){
		$itunes_aff = '1010lnPx';
	}

	if($amazon_aff == 'Add Your Amazon Affiliate ID Here...'){
		$amazon_aff = 'wpbooklistid-20';
	}

	$table_name_options = $wpdb->prefix . 'wpbooklist_jre_user_options';

	$data = array(
        'amazonaff' => $amazon_aff,
        'itunesaff' => $itunes_aff
        
	);
	$format = array( '%s', '%s'); 
	$where = array( 'ID' => 1 );
	$where_format = array( '%d' );
	$wpdb->update( $table_name_options, $data, $where, $format, $where_format );

	// Require the Transients file.
	require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
	$transients = new WPBookList_Transients();

	// Now delete all WPBL transients, to make sure we get the Affiliate ID into the Amazon Links.
	$transient_deleteall_result = $transients->delete_all_wpbl_transients();

	wp_die( $transient_deleteall_result );
}



?>