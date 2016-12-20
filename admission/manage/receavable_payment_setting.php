<?php

/* * ************************************************************************************************************** */
$page_security = 'SS_SMS_STDNT_PAYMNT_PRCS';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");

$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(800, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

simple_page_mode(true);

if (isset($_GET['applicantid'])) {
    $appid = $_GET['applicantid'];
    $class = $_GET['class'];
    $sid = $_GET['sid'];
    $syear = $_GET['syear'];
}

//----------------------------------------------------------------------------------
//$user = $_SESSION['wa_current_user']->username;

page(_($help_context = "Student Payment Process"), false, false, "", $js);

//----------------------------------------------------------------------------------
         $dpar = date_parse(Today());
         
//         $montharr = array('1'=>$dpar['year'].'-01-31','2'=>$dpar['year'].'-02-28','3'=>$dpar['year'].'-03-31','4'=>$dpar['year'].'-04-30','5'=>$dpar['year'].'-05-31','6'=>$dpar['year'].'-06-30','7'=>$dpar['year'].'-07-31','8'=>$dpar['year'].'-08-31','9'=>$dpar['year'].'-09-30','10'=>$dpar['year'].'-10-31','11'=>$dpar['year'].'-11-30','12'=>$dpar['year'].'-12-31');
//         $halfyearly = array('6'=>$dpar['year'].'-06-30','12'=>$dpar['year'].'-12-31');
//         $quarterly = array('4'=>$dpar['year'].'-04-30','8'=>$dpar['year'].'-08-31','12'=>$dpar['year'].'-12-31');
         
         $montharr = array('1'=>'2015-01-31','2'=>'2015-02-28','3'=>'2015-03-31','4'=>'2015-04-30','5'=>'2015-05-31','6'=>'2015-06-30','7'=>'2015-07-31','8'=>'2015-08-31','9'=>'2015-09-30','10'=>'2015-10-31','11'=>'2015-11-30','12'=>'2015-12-31');
         $halfyearly = array('6'=>'2015-06-30','12'=>'2015-12-31');
         $quarterly = array('4'=>'2015-04-30','8'=>'2015-08-31','12'=>'2015-12-31');
         
         $sql = "Select * from " . TB_PREF . "sms_payment_head_setting where st_class=" . db_escape($_GET['class']) . " AND (student_type = 1 OR student_type = 3)";
         
         $result = db_query($sql, 'Cant get data.');
         if(mysql_num_rows($result) == 0){
             
             $not = 1;
             meta_forward($path_to_root . "/admission/report/test_result_view.php?notification=".$not);
         }
         else{
             
         while($data = db_fetch($result)){
             
             if($data['no_of_payment'] == '1'){
                 
                $sql1 = "INSERT INTO " . TB_PREF . "sms_tbl_receivable (school_year,applicant_id,student_id,head_id,month,due_date,realize)
                values (" . db_escape($syear) . "," . db_escape($appid) . ",''," . db_escape($data['id']) . ",'',".db_escape($data['due_date']).",0)";
//                display_error($sql1);
                db_query($sql1, "could not insert");
             }
             if($data['no_of_payment'] == '2'){
                 foreach($halfyearly as $key=>$duedate){
                    $sql2 = "INSERT INTO " . TB_PREF . "sms_tbl_receivable (school_year,applicant_id,student_id,head_id,month,due_date,realize)
                    values (" . db_escape($syear) . "," . db_escape($appid) . ",''," . db_escape($data['id']) . "," . db_escape($key) . "," . db_escape($duedate) . ",0)";
//                    display_error($sql2);
                    db_query($sql2, "could not insert");
                 }
             
             }
             if($data['no_of_payment'] == '3'){
                 foreach($quarterly as $key=>$duedate){
                    $sql3 = "INSERT INTO " . TB_PREF . "sms_tbl_receivable (school_year,applicant_id,student_id,head_id,month,due_date,realize)
                    values (" . db_escape($syear) . "," . db_escape($appid) . ",''," . db_escape($data['id']) . "," . db_escape($key) . "," . db_escape($duedate) . ",0)";
//                    display_error($sql3);
                    db_query($sql3, "could not insert");
                 }
             
             }
             if($data['no_of_payment'] == '12'){
                 foreach($montharr as $key=>$duedate){
                    $sql4 = "INSERT INTO " . TB_PREF . "sms_tbl_receivable (school_year,applicant_id,student_id,head_id,month,due_date,realize)
                    values (" . db_escape($syear) . "," . db_escape($appid) . ",''," . db_escape($data['id']) . "," . db_escape($key) . "," . db_escape($duedate) . ",0)";
//                    display_error($sql4);
                    db_query($sql4, "could not insert");
                 }
             
             }

         }
         
        $sqld = "UPDATE " . TB_PREF . "sms_test_result SET 
             flag = 1 WHERE applicant_id = " . db_escape($appid);
        db_query($sqld, "could not update");
        
        $Ajax->activate('_page_body');
        $Mode = 'RESET';
       
        meta_forward($path_to_root . "/admission/report/test_result_view.php?");
         }
end_page();
?>