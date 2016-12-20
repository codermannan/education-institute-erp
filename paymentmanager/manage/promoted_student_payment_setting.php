<?php

/* * ************************************************************************************************************** */
$page_security = 'SS_SMS_STDNT_PAS_LST';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");
$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(800, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

simple_page_mode(true);

if (isset($_GET['studentid'])) {
    $stid = $_GET['studentid'];
    $class = $_GET['class'];
    $rid = $_GET['rid'];
}

//----------------------------------------------------------------------------------
//$user = $_SESSION['wa_current_user']->username;

page(_($help_context = "Applicant Payment Process"), false, false, "", $js);

//----------------------------------------------------------------------------------
         $dpar = date_parse(Today());
         $montharr = array('1'=>'2015-01-31','2'=>'2015-02-28','3'=>'2015-03-31','4'=>'2015-04-30','5'=>'2015-05-31','6'=>'2015-06-30','7'=>'2015-07-31','8'=>'2015-08-31','9'=>'2015-09-30','10'=>'2015-10-31','11'=>'2015-11-30','12'=>'2015-12-31');
        // $montharr = array('1'=>$dpar['year'].'-01-31','2'=>$dpar['year'].'-02-28','3'=>$dpar['year'].'-03-31','4'=>$dpar['year'].'-04-30','5'=>$dpar['year'].'-05-31','6'=>$dpar['year'].'-06-30','7'=>$dpar['year'].'-07-31','8'=>$dpar['year'].'-08-31','9'=>$dpar['year'].'-09-30','10'=>$dpar['year'].'-10-31','11'=>$dpar['year'].'-11-30','12'=>$dpar['year'].'-12-31');
         
         $sql = "Select * from " . TB_PREF . "sms_payment_head_setting where st_class=" . db_escape($class) . " AND (student_type = 2 OR student_type = 3)";
         
         $result = db_query($sql, 'Cant get data.');
         
         while($data = db_fetch($result)){
             
             if($data['no_of_payment'] == '1'){
                 
                $sql1 = "INSERT INTO " . TB_PREF . "sms_tbl_receivable (school_year,student_id,head_id,month,payment_date,due_date,realize)
                values (2," . db_escape($stid) . "," . db_escape($data['id']) . ",''," . db_escape(date2sql(Today()))  . ",'2015-01-31',0)";
               // display_error($sql1);
                 db_query($sql1, "could not insert");
             }
             if($data['no_of_payment'] == '12'){
                 foreach($montharr as $key=>$duedate){
                    $sql2 = "INSERT INTO " . TB_PREF . "sms_tbl_receivable (school_year,student_id,head_id,month,payment_date,due_date,realize)
                    values (2," . db_escape($stid) . "," . db_escape($data['id']) . "," . db_escape($key) . "," . db_escape(date2sql(Today())) . "," . db_escape($duedate) . ",0)";
                    //display_error($sql2);
                    db_query($sql2, "could not insert");
                 }
             
             }
             if($data['head_name'] == 'Examination Fee'){
             if($data['no_of_payment'] == '2'){
                $sqle = "Select start_date from " . TB_PREF . "sms_exam_name where class_name = " . db_escape($class);
                
                $res = db_query($sqle, 'Cant get data.');
                
                while($exam = db_fetch($res)){ 
                   $exd = substr($exam['start_date'], 4);
                   $ex = '2015'.$exd; 
                   $presentd = strtotime($ex);
                   $dudate = date('Y-m-d',strtotime('-1 day',$presentd));
                   $sql3 = "INSERT INTO " . TB_PREF . "sms_tbl_receivable (school_year,student_id,head_id,month,payment_date,due_date,realize)
                   values (2," . db_escape($stid) . "," . db_escape($data['id']) . ",''," . db_escape(date2sql(Today()))  . "," . db_escape($dudate) . ",0)";
                  //display_error($sql3);
                  db_query($sql3, "could not insert");
                }
               }
             }

         }
         
        $sqld = "UPDATE " . TB_PREF . "sms_student_result SET 
             flag = 1 WHERE id = " . db_escape($rid);
        db_query($sqld, "could not update");
        
        $Ajax->activate('_page_body');
        $Mode = 'RESET';
       
        meta_forward($path_to_root . "/resultmanager/manage/student_pass_list.php?");
 
end_page();
?>