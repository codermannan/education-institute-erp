<?php

/* * ********************************************************************

 * ********************************************************************* */

class resultmanager_app extends application {

    function resultmanager_app() {
        $this->application("resultmanager", _($this->help_context = "Result Module"));

           $this->add_module(_("Result Management"));              
                $this->add_lapp_function(0, _("&Re-Admission"), "resultmanager/manage/student_result_processing.php", 'SS_SMS_STDNT_RSLT_PRCS', MENU_ENTRY);
                $this->add_lapp_function(0, _("&Student Pass List"), "resultmanager/manage/student_pass_list.php", 'SS_SMS_STDNT_PAS_LST', MENU_ENTRY);
 
	   $this->add_module(_("Result Report"));              
                $this->add_lapp_function(1, _("&Student Result View"), "resultmanager/report/students_result.php?", 'SS_SMS_STDNT_RSLT_VW', MENU_ENTRY);
                $this->add_lapp_function(1, _("&Student Result Status"), "resultmanager/report/result_status.php?", 'SS_SMS_STDNT_RESLT_STATS', MENU_ENTRY);    
	   
           $this->add_module(_("Evaluation Report")); 	
                $this->add_lapp_function(2, _("&Students Evaluation Report"), "resultmanager/report/student_evaluation_report_new.php?", 'SS_SMS_STDNT_RSLT_VW', MENU_ENTRY);
                $this->add_lapp_function(2, _("&Record of Course Work"), "resultmanager/report/student_course_work_report.php?", 'SS_SMS_REC_OF_COUR_WORK', MENU_ENTRY);

        $this->add_extensions();
        
    }

}

?>