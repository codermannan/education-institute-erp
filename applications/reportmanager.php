<?php

/* * ********************************************************************

 * ********************************************************************* */

class reportmanager_app extends application {

    function reportmanager_app() {
        $this->application("reportmanager", _($this->help_context = "Report Module"));
           $this->add_module(_("Report")); 
           $this->add_lapp_function(0, _("&Class Wise Student"), "reportmanager/report/class_wise_student.php?", 'SS_SMS_CLAS_WISE_STDNT', MENU_ENTRY);
		   $this->add_rapp_function(0, _("&Student ID Card Generate"), "reportmanager/report/stud_id_card.php?", 'SS_SMS_TST_RSLT_VW', MENU_ENTRY);
		   $this->add_lapp_function(0, _("&Exam Schedule View"), "reportmanager/report/schedule_view.php?", 'SS_SMS_DAY_WISE_SRDNT_VW', MENU_ENTRY);
		   $this->add_lapp_function(0, _("&Class Routine View"), "reportmanager/report/routine_view.php?", 'SS_SMS_CLS_RTN_VIEW', MENU_ENTRY);
		   $this->add_lapp_function(0, _("&Dash Board View"), "reportmanager/report/dashboard_view.php?", 'SS_SMS_CLS_RTN_VIEW', MENU_ENTRY);
		   
        $this->add_extensions();
        
    }

}

?>