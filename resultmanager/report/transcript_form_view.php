<?php
/**********************************************************************/
$page_security = 'SS_SMS_APLCNT_LST_VW';
$path_to_root = "../..";

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/resultmanager/includes/db/result_db.inc");


page(_($help_context = "Trancript View"), true);


if (!isset($_GET['stid'])) {
    die("<BR>" . _("This page must be called with a student id to view."));
} else {
    
    $std_id = $_GET['stid'];
    $ex =    $_GET['ex'];
    $syear = $_GET['syear'];
    $class = $_GET['class'];
}
//--------------------------------------------------


$sql_ex = get_sql_for_result_view($std_id);

$prac = get_mark_for_result($class);
$mainresult = db_query($prac);
$tsub = mysql_num_rows($mainresult);

function findgrade($schyear, $st_class, $stid, $exn, $sub){
    $sqlr= "SELECT mark,grade,gpa
        FROM " . TB_PREF . "sms_exam_mark_entry
        WHERE school_year=".db_escape($schyear)."
        AND st_class=" .db_escape($st_class)."
        AND student_id=".db_escape($stid)."
        AND exam_name=".db_escape($exn)."
        AND child_exam_name=".db_escape(0)."
        AND subject=".db_escape($sub);

    $res = db_query($sqlr);
    return $res;
}
function finalresult($g){
    $sql = "SELECT letter_grade,cpoint FROM " . TB_PREF . "sms_grade_set_up"; 
    $sql .= " WHERE start_point <= '$g' AND end_point >= '$g'" ;
    
    $query = db_query($sql);
    if(db_num_rows($query)==0){
        return '<span style="color:#FF0000;">Please setup your grade</span>';
    }else{
       $result = db_fetch($query);
       return $result['letter_grade'];
    }
}

 /*-----------------main table start----------------------*/  
br();


start_table(TABLESTYLE1);
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
    end_row();
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['postal_address'],'colspan=3 align=center style="font-size:15px"');
    end_row();
    end_table();
        br(1);
start_table(TABLESTYLE1);
   start_row();
   
        labelheader_cell('<u>ACADEMIC TRANSCRIPT</u>','colspan=4 style="font-size:18px"');
    end_row();
 end_table();
br(2);
         
  start_table(TABLESTYLE_NOBORDER,'width=80%');
  
 
  start_row();
             label_cell( 'Serial No. DBS ','width=20%');

//             label_cell( $_GET['Applicantid']);
  end_row();
  start_row();
             label_cell( 'DBCS ','width=20%');

//             label_cell( $_GET['Applicantid']);
  end_row();
    end_table();
     br(1);
   
  start_table(TABLESTYLE_NOBORDER,'width=80%');
  start_row();
             label_cell( 'Student ID','width=20%');
             label_cell( ':');
             label_cell($std_id,'width=30%');
  end_row();
  start_row();
             label_cell( 'Name Of Student','width=20%');
             label_cell( ':');
             label_cell( $sql_ex['name'],'width=30%');
  end_row();
 
start_row();
   
             label_cell('Father Name');
             label_cell( ':','width=10%');
             label_cell($sql_ex['father_name']);
  end_row();
  start_row();
   
             label_cell( 'Mother Name');
             label_cell( ':','width=10%');
             label_cell($sql_ex['mother_name']);
  end_row();
  start_row();
             label_cell( 'Name Of Institution');
             label_cell( ':','width=10%');
//             label_cell($sql_ex['class_name']);
             
  end_row();
   start_row();
             label_cell( 'Name Of Centre');
             label_cell( ':','width=10%');
//             label_cell($sql_ex['class_name']);
             
  end_row();
   start_row();
             label_cell( 'Class');
             label_cell( ':','width=10%');
             label_cell($sql_ex['class_name']);
             label_cell( 'Roll No.');
             label_cell( ':','width=10%');
             label_cell($sql_ex['roll_number']);
             
   end_row();
   start_row();
             label_cell( 'Section Name','width=20%'); 
             label_cell( ':','width=10%');
             label_cell( $sql_ex['session_name'],'width=20%');
             label_cell( 'Registration No.','width=20%'); 
             label_cell( ':','width=10%');
             
   end_row();
    start_row();
             label_cell( 'Group');
             label_cell( ':','width=10%');
             label_cell($sql_ex['group_name']);
             label_cell( 'Type Of Student','width=20%'); 
             label_cell( ':','width=10%');
             label_cell('Regular');
  end_row();
   start_row();
             label_cell( 'Date Of Birth');
             label_cell( ':','width=10%');
             label_cell(sql2date($sql_ex['dob']));
             
  end_row();
  end_table();
   
  br();
  
  //$sch = get_sql_for_exam_schedule();
  $sl = 1;
  start_table(TABLESTYLE2,'width=80%');
   start_row('background-color:none');
            labelheader_cell( 'Sl.No.','align=center');
            labelheader_cell( 'Name Of Subjects','align=center');
            labelheader_cell( 'Mark','align=center');
            labelheader_cell( 'Letter Grade','align=center');
            labelheader_cell( 'Grade Point','align=center');
            labelheader_cell( 'GPA<br/>(without additional subject)','align=center');
            labelheader_cell( 'GPA','align=center');
            labelheader_cell( 'Letter Grade','align=center');
   end_row();
  $fl = 1;
  while($mrow = db_fetch($mainresult)){
  $fgrade = db_fetch(findgrade($syear, $class, $std_id, $ex, $mrow['id']));
  $gpa += $fgrade['gpa'];
  $g = number_format2(($gpa / $tsub),2);
  start_row();
             label_cell( $sl,'align=center');
             label_cell($mrow['subject_name'],'align=center');
             label_cell($fgrade['mark'],'align=center');
             label_cell($fgrade['grade'],'align=center');
             label_cell($fgrade['gpa'],'align=center');
             label_cell( '','align=center');
             if($fl == 1){  
             label_cell('','align=center rowspan=3');
             label_cell('','align=center rowspan=3');
             $fl++;
             } 
   $totalmark += $fgrade['mark'];          
  $sl++;
  } 
  $finalresult =  finalresult($g);
  end_row();
 
//  start_row();
//             label_cell( '<b>Additional Subject</b> : ','align=left colspan=8');
//             
//  end_row();
  start_row();
             label_cell('<b>Total</b>','align=center colspan = 2');
             label_cell('<b>'.$totalmark.'</b>','align=center');
             label_cell( '','align=center');
             label_cell( '','align=center');
             label_cell( '','align=center');
             label_cell( '<b>'.$g.'</b>','align=center');
             label_cell( '<b>'.$finalresult.'</b>','align=center');
  end_row();
  end_table();
  br(3);
  start_table(TABLESTYLE_NOBORDER,'width=80%');
  start_row();
    label_cell( '--------------------------------<br/>Date of Publication of Result','width=75%');
   
//             label_cell($sql_ex['class_name']);
    label_cell( '--------------------------------<br/>Controller of Examinations'); 
  
  end_row();
  end_table();
  
  
  br(1);
  br(1);
  br(1);
   
end_page(true, false, false);