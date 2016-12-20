<?php
/**********************************************************************/
$page_security = 'SS_SMS_APLCNT_LST_VW';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/attendancemanager/includes/ui/attendance_ui_lists.inc");
include_once($path_to_root . "/attendancemanager/includes/db/attendance_db.inc");

page(_($help_context = "leave Form View"), true);

if (!isset($_GET['id'])) {
    die("<BR>" . _("This page must be called with a Performa Invoice to review."));
} else {
    $tblid = $_GET['id'];
}

$sql_ex = get_sql_for_leave_form_view($tblid);

$condition = array('student_id'=>$sql_ex['student_id']);
$field = array("CONCAT(first_name,' ',middle_name,' ',last_name) as name");
$classval = db_fetch(data_retrieve_condition("sms_students_details", $field, $condition));

 /*-----------------main table start----------------------*/  

 start_table(TABLESTYLE1);
    start_row();
      label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
        end_row();
        start_row();
  
        labelheader_cell('Student Leave Application Form','colspan=2 style="font-size:18px"');
          
        end_row();
end_table();
br();

start_form();
start_table(TABLESTYLE_NOBORDER, "width=70%");
start_row();
label_cell('Date : '.date('Y/m/d'));
end_row();
label_cell('<b>To </b>', 'height=25 valint=top align=left style="font-size:13px"');
label_row('<b>The Principle</b>', '','style="background-color:none"');
label_row('<Address><b>Dhaka</b></Address>', '','style="background-color:none"');
end_row();
end_table();
br(2);

start_table(TABLESTYLE_NOBORDER, "width=70%");
label_row('<b>Subject:</b> For Leave Application');
end_table();
br();
start_table(TABLESTYLE_NOBORDER, "width=70%");
label_row('Dear Sir/Madam,', '','style="background-color:none"');
label_cell('I, '.$classval['name'].', (Student ID:'.$sql_ex['student_id'].') request for leave of absence from '.$sql_ex['from_date'].' to '.$sql_ex['to_date'].' due to the reason of '.$sql_ex['reason'].'.', '','width=30%; style="background-color:none"');
br();
start_row();
label_cell('I understand that if approval is granted,i wil have to attend the remaining sessions of the term to be eligible for the examination.');
end_table();
br(1);
start_table(TABLESTYLE_NOBORDER, "width=70%");
label_row('Thank you');
br();
label_cell('<u>Signature :</u>');
end_row();
end_table();

br(2);
start_table(TABLESTYLE2,'width=70%');
    start_row();
    labelheader_cell('OFFICIAL USE ONLY','colspan=2 style="font-size:18px"');
    end_row();
    end_table();
    
     start_outer_table(TABLESTYLE2, "width=70%"); // outer table
    table_section(1, "50%");

        label_row('Date of submission :'.$sql_ex['sub_date'].'');
        label_row('Date of approval : '.$sql_ex['app_date'].'');
       


    table_section(2, "50%");  
      if($sql_ex['approve']==1){
         $ap='Approved'; 
      }
      else{
        $ap='Not Approved';  
      }
      
      label_row('Approved/Not approved: '.$ap.'');
    end_outer_table(1);
      
 br(1);
 br(1);
  br(1);
  
end_page(true, false, false, ST_BOM, $style_no);