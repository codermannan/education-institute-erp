<?php

/* * ************************************************************************************************************** */
$page_security = 'SS_SMS_XM_MRKS_ENTRY';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/resultmanager/includes/db/result_db.inc");

$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(800, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

simple_page_mode(true);

if (isset($_GET['app_id'])) {
    $applicant_id = $_GET['app_id'];
}
//----------------------------------------------------------------------------------
//
$user = $_SESSION['wa_current_user']->username;

page(_($help_context = "Student Result Amendment"), false, false, "", $js);

//----------------------------------------------------------------------------------
if (isset($_POST['process'])) {
    
        
       if (strlen($_POST['amendment_note']) == '') 
	{
		$input_error = 1;
		display_error( _('Please write note.'));
		set_focus('amendment_note');
	}
        else {
            $condition = array('school_year'=>$_POST['syear'],'class'=>$_POST['class'],'section'=>$_POST['section']);
            $field = array('school_year','class','student_roll');
            $qr = data_retrieve_condition("sms_student_result", $field, $condition, 'student_roll DESC');
            $trow = db_fetch($qr);
            $studentroll = ($trow['student_roll'] + 1);
           
            $sql = "INSERT INTO " . TB_PREF . "sms_special_permission (student_id, original_result, recom_result, grace_subject, grace_mark, note, recommended_by, date)
            values (" . db_escape($_POST['sid']) . "," . db_escape($_POST['result']) . "," . db_escape('P') . "," . db_escape($_POST['sub']) . "," . db_escape($_POST['g_mark']) . "," . db_escape($_POST['amendment_note']) . ", " . db_escape($user) . "," . db_escape(date2sql($_POST['amend_date'])) . ")";
            //display_error($sql);
            db_query($sql, "student_result could not be inserted");
            
            $spid = db_insert_id();
            
            $sqlup= "UPDATE " . TB_PREF . "sms_student SET promotion = 2 WHERE id = " .  db_escape($_POST['sid']);
            db_query($sqlup, "could not update sms_student");
 
            
            $sqlp = "INSERT INTO " . TB_PREF . "sms_student_result(stbl_id, school_year, student_id, student_roll, class, section, gpa, cgpa, result, sp, added_by, added_date)
            values (" . db_escape($_POST['sid']) . "," . db_escape($_POST['syear']) . "," . db_escape($_POST['stid']) . "," . db_escape($studentroll) . "," . db_escape($_POST['class']) . "," . db_escape($_POST['section']) . "," . db_escape($_POST['grade']) . "," . db_escape($_POST['result']) . "," . db_escape('P') . "," . db_escape($spid) . ", " . db_escape($user) . "," . db_escape(date2sql($_POST['amend_date'])) . ")";
	
            db_query($sqlp, "student_result could not be inserted");
            //display_error($sqlp);

            $Ajax->activate('_page_body');
            $Mode = 'RESET';
            
             meta_forward($path_to_root . "/resultmanager/manage/student_result_processing.php?");
   
        }
} 

//---------------------------------------------------------------------------------- 
br();

start_form();

$result = get_applicant_data($applicant_id, 'sms_students_details', 'applicant_id');
$dt = explode('/',Today());
$studentid = substr($result['class_name'], 6).$dt[2].rand();

    div_start('details');
    start_outer_table(TABLESTYLE2);
    table_section(1);
    table_section_title(_("Student Result Amendment"));
    date_row(_("Date :"), 'amend_date');
    label_row(_("Student ID :"), $_GET['stid']);
    label_row(_("GRADE :"), $_GET['grade']);
    label_row(_("CGPA :"), $_GET['cgpa']);
    label_row(_("Lowest Sub. Mark :"), $_GET['sub']);
    text_row(_("Grace Mark:"), 'g_mark', '', 10, 50);
    textarea_row(_("Note :"), 'amendment_note', null, 30, 3);

    hidden('sid', $_GET['id']);
    hidden('grade', $_GET['grade']);
    hidden('result', $_GET['cgpa']);
    hidden('sub', $_GET['sub']);
    hidden('stid', $_GET['stid']);
    hidden('syear', $_GET['syear']);
    hidden('class', $_GET['class']);
    hidden('section', $_GET['section']);
    
    end_outer_table(1);
    div_end();


    submit_center_first('Update', _("Update"), '', null);
    submit_center_last('process', _("Process"), '', 'default');

    end_form();
    br();

end_page();
?>