<?php

// Adding the front-end ui css file for this extension
function wpbooklist_jre_affiliate_frontend_ui_style() {
    wp_register_style( 'wpbooklist-affiliate-frontend-ui', AFFILIATE_ROOT_CSS_URL.'affiliates-frontend-ui.css' );
    wp_enqueue_style('wpbooklist-affiliate-frontend-ui');
}

// Code for adding the general admin CSS file
function wpbooklist_jre_affiliate_admin_style() {
  if(current_user_can( 'administrator' )){
      wp_register_style( 'wpbooklist-affiliate-admin-ui', AFFILIATE_ROOT_CSS_URL.'affiliates-admin-ui.css');
      wp_enqueue_style('wpbooklist-affiliate-admin-ui');
  }
}

// Handles various aestetic functions for the back end
function wpbooklist_affiliate_various_aestetic_bits_back_end(){
  wp_enqueue_media();
  ?>
  <script type="text/javascript" >
  "use strict";
  jQuery(document).ready(function($) {

  	$('#wpbooklist-amazon-affiliate-library').on('click', function(){
  		$(this).val('');
  	})

  	$('#wpbooklist-itunes-affiliate-library').on('click', function(){
  		$(this).val('');
  	})
  });
  </script>
  <?php
}




?>