<?php

/* * ************************************************************************************************************** */
$page_security = 'SS_SMS_STDNT_PAYMNT_PRCS';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");


$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(800, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

simple_page_mode(true);
page(_($help_context = "Scholarship Process"), false, false, "", $js);

if (isset($_GET['applicantid'])) {
    $appid = $_GET['applicantid'];
}

$syear = get_current_schoolyear();
$user = $_SESSION['wa_current_user']->username;
//----------------------------------------------------------------------------------
        $approved_date = date2sql(Today());
        
        $sqlbe = "UPDATE " . TB_PREF . "sms_student_scholarship SET 
                is_approved_cat = 1,approved_date =" . db_escape($approved_date) .",approved_by =".db_escape($user)." WHERE applicant_id = " . db_escape($appid);
        //display_error($sqlbe);
        db_query($sqlbe, "could not update");
        
        meta_forward($path_to_root . "/cpanel/setup/scholarship_approve_list.php?");
        
        
end_page();
?>