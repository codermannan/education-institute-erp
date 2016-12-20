<?php

/* * ********************************************************************
  
 * ********************************************************************* */
$page_security = 'SS_SMS_CLAS_WISE_STDNT';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");


    page(_($help_context = "Class Wise Student Report"), true);
//-------------------------------------------------------------------------------------------

start_table(TABLESTYLE1,'width=60%');
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
    end_row();
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['postal_address'],'colspan=3 align=center style="font-size:15px"');
    end_row();
    start_row();
        label_cell('<b>Class Student View</b>','align=center colspan3=10');
    end_row();
        
        
end_table();
br(2);
//---------------------------------------------------------------------------------------------
$class = $_GET['class'];

start_table(TABLESTYLE2,"width=60%");
         start_row();
             labelheader_cell( 'SL#','width=5%');
             labelheader_cell( 'Student ID','width=5%');
             labelheader_cell( 'Student Name','width=6% ');
             labelheader_cell( 'Class','width=7%'); 
         end_row();
  
   $sql = " SELECT st.student_id, CONCAT( sd.first_name, ' ', sd.middle_name, ' ', sd.last_name ) AS name,
             sc.class_name
             FROM " . TB_PREF . "sms_student st
             LEFT JOIN " . TB_PREF . "sms_students_details sd ON st.student_id = sd.student_id
             LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON st.st_class = sc.id
             WHERE st.st_class = " . db_escape($class)."
             AND st.status =1";

    $result = db_query($sql,"data could not be found");
 
 $sl=1;
  while ($rep = db_fetch($result))
    {
         start_row();
             label_cell( $sl ,'width=5% align=center');
             label_cell( $rep['student_id'],'width=10% align=center');
             label_cell( $rep['name'],'width=15% height=16');
             label_cell( $rep['class_name'],'width=7% align=center');  
         end_row();
           
         $sl++;
   // }
} 
end_table();

end_page(true);