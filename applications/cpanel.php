<?php

/* * ********************************************************************

 * ********************************************************************* */

class cpanel_app extends application {

    function cpanel_app() {
        $this->application("cpanel", _($this->help_context = "Cpanel Module"));

           $this->add_module(_("Setting")); 
               $this->add_lapp_function(0, _("&Scholarship Approve"), "cpanel/setup/scholarship_approve_list.php?", 'SS_SMS_GRADE_STNG', MENU_ENTRY);
               //rapp function 
               $this->add_rapp_function(0, _("&Grade Settings"), "cpanel/setup/grade_settings.php?", 'SS_SMS_GRADE_STNG', MENU_ENTRY);
	       $this->add_rapp_function(0, _("&Period Setup For Routine"), "cpanel/setup/period_set_up.php?", 'SS_SMS_CLS_RTN_SETP', MENU_ENTRY);
	       $this->add_rapp_function(0, _("&Class Routine Setup"), "cpanel/setup/routine_set_up.php?", 'SS_SMS_CLS_RTN_SETP', MENU_ENTRY);
               $this->add_rapp_function(0, _("&Class Duration Setup"), "cpanel/setup/class_duration_set_up.php?", 'SS_SMS_CLS_RTN_SETP', MENU_ENTRY);
	       $this->add_rapp_function(0, _("&Dash Board Setup"), "cpanel/setup/dashboard_set_up.php?", 'SS_SMS_DASH_BRD_SETP', MENU_ENTRY);

        $this->add_extensions();
        
    }

}

?>