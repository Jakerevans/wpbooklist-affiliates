<?php
/**
 * WPBookList Affiliate Tab Class
 *
 * @author   Jake Evans
 * @category ?????????
 * @package  ?????????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Affiliate_Tab', false ) ) :
/**
 * WPBookList_Affiliate_Tab Class.
 */
class WPBookList_Affiliate_Tab {

    public function __construct() {
        // This extension relies on the admin template file in the core WPBookList plugin.
        require_once(CLASS_DIR.'class-admin-ui-template.php');
        // Instantiate the class
        $this->template = new WPBookList_Admin_UI_Template;

        // Require the file that contains the actual code that will be output within the admin template
        require_once(AFFILIATE_CLASS_DIR.'class-affiliate-form.php');
        $this->form = new WPBookList_Affiliate_Form;

        $this->output_open_admin_container();
        $this->output_tab_content();
        $this->output_close_admin_container();
        $this->output_admin_template_advert();
    }

    private function output_open_admin_container(){
        # The two options that will be used by the admin template in the core WPBookList plugin to set the title and image
        $title = 'Affiliate IDs';
        $icon_url = AFFILIATE_ROOT_IMG_ICONS_URL.'affiliate.svg';
        echo $this->template->output_open_admin_container($title, $icon_url);
    }

    private function output_tab_content(){
        echo $this->form->output_affiliate_form();
    }

    private function output_close_admin_container(){
        echo $this->template->output_close_admin_container();
    }

    private function output_admin_template_advert(){
        echo $this->template->output_template_advert();
    }

}

endif;

// Instantiate the class
$am = new WPBookList_Affiliate_Tab;
?>