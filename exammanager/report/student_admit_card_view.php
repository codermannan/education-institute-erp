<?php
/**********************************************************************/
$page_security = 'SS_SMS_STDNT_ADMT_CRD';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/applicant_info_ui_lists.inc");
include_once($path_to_root . "/sms/includes/db/student_db.inc");

page(_($help_context = "Student Admit Card View"), true);

//if (!isset($_GET['Studentid'])) {
//    die("<BR>" . _("This page must be called with a Performa Invoice to review."));
//} else {
    $studentid = $_GET['Studentid'];
    $examname = $_GET['examname'];
    $cls = $_GET['class'];
    
///}

$sql = get_sql_for_admit_view($studentid);

$result = db_fetch(db_query($sql,"data could not be found"));



 /*-----------------main table start----------------------*/  
br();
start_table(TABLESTYLE1);
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
    end_row();
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['postal_address'],'colspan=3 align=center style="font-size:15px"');
    end_row();
    start_row();  
        labelheader_cell('Admit Card For '.$examname,'width=95%','colspan=4 style="font-size:18px"');
    end_row();
end_table();
  
  
br();
  start_table(TABLESTYLE_NOBORDER,'width=80%');
  start_row();
    label_cell('ADMIT CARD','colspan=2 align=center style="font-size:15px; font-weight:bold;"');
    
  end_row();
  end_table();
  
  br();
  start_table(TABLESTYLE_NOBORDER,'width=60%');
//   start_row('background-color:none');
//            labelheader_cell( 'Applicant Personal Info','colspan=8 style="text-align:left;"');
//         end_row();
  start_row();
             label_cell( 'Student ID','width=20%');
             label_cell( ':','width=10%');
             label_cell($result['student_id']); 
            
  end_row();
   start_row();
   
             label_cell('Student Name');
             label_cell( ':','width=10%');
             label_cell($result['name']);
            
  end_row();
  start_row();
             label_cell( 'Class');
             label_cell( ':','width=10%');
             label_cell($result['class_name']);
           
             
  end_row();
    start_row();
             label_cell( 'Exam Name');
             label_cell( ':','width=10%');
             label_cell($examname);
             
  end_row();
  end_table();
   
  br();
  
start_table(TABLESTYLE2,'width=70%');
    start_row();
    
        labelheader_cell("Subject Name");
        labelheader_cell("Exam Date");
        labelheader_cell("Shift");
        labelheader_cell("Room No");
        
     end_row();
     
$condition = "SELECT ss.subject_name,se.date,sh.shift,rs.room_no FROM " 
          . TB_PREF ."sms_stud_exam se
           LEFT JOIN " . TB_PREF . "sms_exam_name en ON en.id = se.exam_name
           LEFT JOIN " . TB_PREF ."sms_subject ss ON ss.id = se.subject_name
           LEFT JOIN " . TB_PREF ."sms_room_setup rs ON rs.id = se.room
           LEFT JOIN " . TB_PREF ."sms_shift sh ON sh.id = se.shift
           WHERE se.class_name =" . db_escape($_GET['class']).
           " AND en.exam_name=" . db_escape($_GET['examname']) ;
  
$res = db_query($condition);

while($pr=db_fetch($res))
{
    
    start_row();
    label_cell( $pr['subject_name'],'width=10% align=center');
    label_cell( sql2date($pr['date']),'width=10% align=center');
    label_cell( $pr['shift'],'width=10% align=center');
    label_cell( $pr['room_no'],'width=10% align=center');

    end_row();
}

end_table();
//-------------
  
 
  
  br(1);
  br(1);
  br(1);
   
end_page(true, false, false, ST_BOM, $style_no);