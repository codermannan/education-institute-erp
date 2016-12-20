<?php
//student db function

//used
function get_class_wise_student($class)
{
    
   $sql = " SELECT st.student_id, CONCAT( sd.first_name, ' ', sd.middle_name, ' ', sd.last_name ) AS name,
             sc.class_name
             FROM " . TB_PREF . "sms_student st
             LEFT JOIN " . TB_PREF . "sms_students_details sd ON st.student_id = sd.student_id
             LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON st.st_class = sc.id
             WHERE st.status =1 AND st.st_class=".  db_escape($class);
   //display_error($sql);
    return $sql;
    
}
//used
function get_stud_info($syear,$id)
{
    
    $sql = "SELECT CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name,st.student_id,st.roll_number,sc.class_name,ss.session_name,sd.blood_group
          FROM " . TB_PREF . "sms_student st LEFT JOIN " . TB_PREF . "sms_students_details sd ON sd.student_id = st.student_id 
          LEFT JOIN " . TB_PREF ."sms_create_stud_class sc ON sc.id = st.st_class 
          LEFT JOIN " . TB_PREF . "sms_session ss ON ss.id = st.st_section
          WHERE st.student_id=" .  db_escape($id) . "AND st.school_year = " . db_escape($syear) ;
    
    return $sql;
}