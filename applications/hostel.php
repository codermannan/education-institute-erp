<?php

/* * ********************************************************************

 * ********************************************************************* */

class hostel_app extends application {

    function hostel_app() {
        $this->application("hostel", _($this->help_context = "Hostel Module"));
            	 $this->add_lapp_function(0, _("&Apply For Hostel"), "#", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
            	 $this->add_rapp_function(0, _("&Room Setting"), "#", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
            	 $this->add_rapp_function(0, _("&Bed Setting"), "#", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY); 
            	 $this->add_rapp_function(0, _("&Furniture Setting"), "#", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
            	 $this->add_rapp_function(0, _("&Master Setting"), "#", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
             
        $this->add_extensions();
        
    }

}

?>