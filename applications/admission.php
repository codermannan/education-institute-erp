<?php

/* * ********************************************************************

 * ********************************************************************* */

class admission_app extends application {

    function admission_app() {
        $this->application("admission", _($this->help_context = "Addmission Module"));

            $this->add_module(_("Addmission Process"));
                
                $this->add_lapp_function(0, _("Admission Form"),"admission/manage/admission_form.php?", 'SS_SMS_ADMNSN_PRCS', MENU_INQUIRY);
				
		$this->add_lapp_function(0, _("&Applicant Status"), "admission/manage/applicant_status.php?", 'SS_SMS_APLCNT_STATS', MENU_ENTRY);
 
                $this->add_lapp_function(0, _("&Addmission Test Marks Entry"), "admission/manage/test_marks_entry.php?", 'SS_SMS_ADMSN_MRK_ENTRY', MENU_ENTRY);
                
                $this->add_lapp_function(0, _("&Applicant Short List"), "admission/manage/app_short_list.php?", 'SS_SMS_APLCNT_SHRT_LST', MENU_ENTRY);
                
                $this->add_lapp_function(0, _("&Spot Admission"), "admission/manage/directs_addmission.php?", 'SA_SALESORDER', MENU_ENTRY);
                
                $this->add_lapp_function(0, _("&Applicant Short List Approve"), "admission/manage/app_short_list_approve.php?", 'SS_SMS_APLCNT_SHRT_LST_APV', MENU_ENTRY);
             
           $this->add_module(_("Admission Report"));              
                $this->add_lapp_function(1, _("&Applicant List View"),"admission/report/candidate_list_view.php?", 'SS_SMS_APLCNT_LST_VW', MENU_INQUIRY);
		$this->add_lapp_function(1, _("&Test Result View"), "admission/report/test_result_view.php?", 'SS_SMS_TST_RSLT_VW', MENU_ENTRY);
				
		$this->add_module(_("Setup"));              
                $this->add_lapp_function(2, _("&Class Management"),"admission/setup/create_class.php?", 'SS_SMS_CRT_STDNT_CLS', MENU_INQUIRY);
		$this->add_lapp_function(2, _("&Section Management"), "admission/setup/section.php?", 'SS_SMS_STDNT_SECTN_MANGMNT', MENU_INQUIRY);
		$this->add_lapp_function(2, _("&Group Setup"), "admission/setup/stu_group.php?", 'SS_SMS_CRT_STDNT_CLS', MENU_INQUIRY);
		$this->add_lapp_function(2, _("&Student Category Setup"), "admission/setup/stu_catagory.php?", 'SS_SMS_CRT_STDNT_CLS', MENU_INQUIRY);
		$this->add_lapp_function(2, _("&Form Price Setup"), "admission/setup/admission_form_setting.php?", 'SS_SMS_STDNT_HEAD_NAME_SETNG', MENU_INQUIRY);
		$this->add_lapp_function(2, _("&Student GL Account Setup"), "admission/setup/student_gl_setup.php?", 'SS_SMS_XM_NAME_SETING', MENU_INQUIRY);
                $this->add_lapp_function(2, _("&Candidate Exam Schedule"), "admission/setup/exam_schedule.php?", 'SS_SMS_CNDIDT_XM_SDL', MENU_ENTRY);
		$this->add_lapp_function(2, _("&Required Students settings"), "admission/setup/required_students.php?", 'SS_SMS_REQ_STDNT_SETING', MENU_ENTRY);
                
                
        $this->add_extensions();
        
    }

}

?>