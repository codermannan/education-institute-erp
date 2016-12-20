<?php

/* * ********************************************************************

 * ********************************************************************* */

class exammanager_app extends application {

    function exammanager_app() {
        $this->application("exammanager", _($this->help_context = "Exam Module"));

           $this->add_module(_("Exam Management"));              

                $this->add_lapp_function(0, _("&Take Exam Attendance"), "exammanager/manage/exam_marks_attendence.php", 'SS_SMS_TAKE_XM_ATNDCNC', MENU_ENTRY);
				 $this->add_lapp_function(0, _("&Update Exam Attendance"), "exammanager/manage/update_exam_attendence.php", 'SS_SMS_TAKE_XM_ATNDCNC', MENU_ENTRY);
                $this->add_lapp_function(0, _("&Exam Marks Entry"), "exammanager/manage/exam_marks_entry.php", 'SS_SMS_XM_MRKS_ENTRY', MENU_ENTRY);
		$this->add_lapp_function(0, _("&Update Exam Marks"), "exammanager/manage/update_exam_marks.php", 'SS_SMS_XM_MRKS_ENTRY', MENU_ENTRY);
		$this->add_lapp_function(0, _("&Direct Exam Marks Entry"), "exammanager/manage/exam_marks_entry_other.php", 'SS_SMS_XM_MRKS_ENTRY', MENU_ENTRY);
 
		  $this->add_module(_("Exam Report"));              
                $this->add_lapp_function(1, _("&Student Admit Card"), "exammanager/report/student_admit_card.php?", 'SS_SMS_STDNT_ADMT_CRD', MENU_INQUIRY);
				
		   $this->add_module(_("Setup"));              
                $this->add_lapp_function(2, _("&Teacher Allocation"), "exammanager/setup/sub_wise_teacher.php", 'SS_SMS_SBJCT_TCHR', MENU_ENTRY);
				$this->add_lapp_function(2, _("&Exam Schedule"), "exammanager/setup/stud_exam_schedule.php", 'SS_SMS_STDNT_XM_SCHDL', MENU_ENTRY);
				$this->add_lapp_function(2, _("&Exam Guard Distribution"), "exammanager/setup/guard.php", 'SS_SMS_STDNT_XM_SCHDL', MENU_ENTRY);
				$this->add_rapp_function(2, _("&Class Wise Subject Setup"), "exammanager/setup/subject_setup.php", 'SS_SMS_CLS_SBJCT_SETP', MENU_INQUIRY);
				$this->add_rapp_function(2, _("&Exam Name Setting"), "exammanager/setup/exam_name.php?", 'SS_SMS_XM_NAME_SETING', MENU_INQUIRY);
				$this->add_rapp_function(2, _("&Exam Setting"), "exammanager/setup/exam_setting.php?", 'SS_SMS_XM_STNG', MENU_INQUIRY);
				$this->add_rapp_function(2, _("&Hall Setting"), "exammanager/setup/room_management.php?", 'SS_SMS_XM_ROM_STNG', MENU_INQUIRY);
				$this->add_rapp_function(2, _("&Shift Setting"), "exammanager/setup/shift_set_up.php?", 'SS_SMS_XM_ROM_STNG', MENU_INQUIRY);
            
           
        $this->add_extensions();
        
    }

}

?>