<?php

/* * ********************************************************************

 * ********************************************************************* */

class library_app extends application {

    function library_app() {
        $this->application("library", _($this->help_context = "Library Module"));
            	$this->add_rapp_function(0, _("&Location Setup"), "/library/setup/location_setup.php", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
                $this->add_rapp_function(0, _("&Book Type Setup"), "/library/setup/book_type_setup.php", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
                $this->add_rapp_function(0, _("&Author Setup"), "/library/setup/author_setup.php", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
                $this->add_rapp_function(0, _("&Publisher Setup"), "/library/setup/publisher_setup.php", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
                $this->add_rapp_function(0, _("&Library Configuration List"), "/library/setup/lib_config_list.php", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
                $this->add_lapp_function(0, _("&Book Entry"), "/library/manage/book_entry.php", 'SS_SMS_LIB_BENTRY', MENU_ENTRY);
                $this->add_lapp_function(0, _("&Book List"), "/library/manage/book_list.php", 'SS_SMS_LIB_BENTRY', MENU_ENTRY);               
                $this->add_lapp_function(0, _("&Book Request"), "/library/manage/book_request_page.php", 'SS_SMS_LIB_BREQ', MENU_ENTRY);
                $this->add_lapp_function(0, _("&Show Book Request"), "#", 'SS_SMS_CLS_SBJCT_SETP', MENU_ENTRY);
                $this->add_lapp_function(0, _("&Library Card"), "#", 'SS_SMS_CLS_SBJCT_SETP', MENU_ENTRY);
             
        $this->add_extensions();
        
    }

}

?>