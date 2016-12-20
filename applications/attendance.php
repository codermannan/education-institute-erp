<?php

/* * ********************************************************************

 * ********************************************************************* */

class attendancemanager_app extends application {

    function attendancemanager_app() {
        $this->application("attendancemanager", _($this->help_context = "Attendance Manager"));

           $this->add_module(_("Attendance Management"));              
                $this->add_rapp_function(0, _("&Make Attendance"), "attendancemanager/manage/take_attendence.php", 'SS_SMS_CLS_STDNT_ATNDNC', MENU_ENTRY);
			$this->add_lapp_function(0, _("&Student Leave Form"), "attendancemanager/manage/leave_form.php?", 'SS_SMS_DAY_WISE_SRDNT_VW', MENU_ENTRY);
 
		  $this->add_module(_("Attendance Report"));              
                $this->add_lapp_function(1, _("&Daily Attendance Report"), "attendancemanager/report/daily_attendance_report.php?", 'SS_SMS_DAY_WISE_SRDNT_VW', MENU_ENTRY);
		$this->add_lapp_function(1, _("&Monthly Attendance Report"), "attendancemanager/report/monthly_attendance_report.php?", 'SS_SMS_DAY_WISE_SRDNT_VW', MENU_ENTRY);
                
		$this->add_lapp_function(1, _("&Leave Approve"), "attendancemanager/report/view_leave_form.php?", 'SS_SMS_SCHL_YR_STNG', MENU_ENTRY);
				
		  $this->add_module(_("Setup"));    
				$this->add_lapp_function(2, _("&Holiday Setup"), "attendancemanager/setup/sms_holyday_setup.php?", 'SS_SMS_SCHL_YR_STNG', MENU_ENTRY);

        $this->add_extensions();
        
    }

}

?>