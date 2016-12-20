<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$page_security = 'SS_SMS_APLCNT_LST_VW';
$path_to_root = "../..";

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/attendancemanager/includes/ui/attendance_ui_lists.inc");
include_once($path_to_root . "/attendancemanager/includes/db/attendance_db.inc");


page(_($help_context = "Application Form View"), true);


if (!isset($_GET['studentid'])) {
    die("<BR>" . _("This page must be called with a Performa Invoice to review."));
} else {
    $studentid = $_GET['studentid'];
}
//--------------------------------------------------


$sql_ex = get_sql_for_absent_view($studentid);

 /*-----------------main table start----------------------*/  
//br();


 start_table(TABLESTYLE1);
 start_row();
               label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
  end_row();
  end_table();
  br(1);
         
  start_table(TABLESTYLE1);
  start_row();
   
              label_cell('<b>Monthly Absent Report</b>','align=center');
  end_row();
  end_table();
  br(2);
         
  start_table(TABLESTYLE_NOBORDER,'width=80%');

  start_row();
             label_cell( 'Student ID','width=10%');
             label_cell( ':','width=1%');
             label_cell($sql_ex['student_id']);
  end_row();
  start_row();
   
             label_cell('Class','width=10%');
             label_cell( ':','width=1%');
             label_cell($sql_ex['class_name']);
  end_row();
  start_row();
   
             label_cell('Group','width=1%');
             label_cell( ':','width=10%');
//             label_cell($sql_ex['father_name']);
  end_row();
  end_table();
    br(3);
    
   start_table(TABLESTYLE2,'width=80%');
   start_row('background-color:none');
            labelheader_cell( 'From Date','align=center');
            labelheader_cell( 'To Date','align=center');
            labelheader_cell( 'No of days','align=center');
            labelheader_cell( 'Reason','align=center');
            labelheader_cell( 'Leave Status','align=center');
            labelheader_cell( 'Approve Status','align=center');
   end_row();
   start_row();
         label_cell(sql2date($sql_ex['from_date']),'align=center');
         label_cell(sql2date($sql_ex['to_date']),'align=center');
         $no_o_d = ((sql2date($sql_ex['to_date'])) - (sql2date($sql_ex['from_date'])));
//         $nd=(($sql_ex['from_date'])-($sql_ex['to_date']));
         number_cell($no_o_d,'','align=center');
         label_cell($sql_ex['reason'],'align=center');
         
              if($sql_ex['leave_type']==1){
                   $ap='Present'; 
                 }
                else{
                   $ap='Absent';  
               }
      
        label_cell($ap,'align=center');
         
         
            if($sql_ex['approve']==1){
                 $ap='Approved'; 
             }
            else{
                 $ap='Not Approved';  
             }
             
        label_cell($ap,'align=center');

 end_row();
  
 end_table();
  
  br(1);
  br(1);
  br(1);
   
if (!@$_GET['popup']) {

    end_page();
}
  

?>
