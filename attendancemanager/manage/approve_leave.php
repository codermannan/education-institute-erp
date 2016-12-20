<?php

$page_security = 'SS_SMS_APLCNT_LST_VW';
$path_to_root = "../..";

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/attendancemanager/includes/ui/attendance_ui_lists.inc");
include_once($path_to_root . "/attendancemanager/includes/db/attendance_db.inc");
//----------------------------------------------------------------
if ($use_date_picker)
    $js .= get_js_date_picker();

page(_($help_context = "Leave Form"), false, false);

$Ajax->activate('_page_body');
//--------------------------------------
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $attnid = $_GET['attnid'];
}
        $sqld = "UPDATE " . TB_PREF . "sms_leave_form SET approve = 1 WHERE id = " . db_escape($id);
        db_query($sqld, "could not update sms_leave_form");
        
        $sql = "UPDATE " . TB_PREF . "sms_stud_class_attendence SET attendence= 1 WHERE id=" .$attnid;
        db_query($sql,'could not update sms_stud_class_attendence');

        $Ajax->activate('_page_body');
        $Mode = 'RESET';
       
       meta_forward($path_to_root . "/attendancemanager/report/view_leave_form.php?");
        
        
end_page();
?>

