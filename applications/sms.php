<?php

/* * ********************************************************************

 * ********************************************************************* */

class sms_app extends application {

    function sms_app() {
        $this->application("sms", _($this->help_context = "SMS Module"));

                $this->add_lapp_function(11, _("&Send SMS"), "#", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
                $this->add_lapp_function(11, _("&SMS Alert"), "#", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
                $this->add_rapp_function(11, _("&Select Group"), "#", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
                $this->add_rapp_function(11, _("&Create Batch "), "#", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
             
        $this->add_extensions();
        
    }

}

?>