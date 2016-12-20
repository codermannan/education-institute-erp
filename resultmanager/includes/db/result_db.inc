<?php
function get_sql_for_add_promoted_student($id,$school_year, $stid, $roll_id, $class, $section, $grade, $cgpa, $res,$user){
        $dt = date2sql(Today());
        
        if($res == 'P'){
            
        $sqlu = "UPDATE " . TB_PREF . "sms_student SET 
                 promotion = 2 WHERE id = " . db_escape($id);
        //display_error($sqlu);
        db_query($sqlu, "could not update");

        $sqlp = "INSERT INTO " . TB_PREF . "sms_student_result(stbl_id, school_year, student_id, student_roll,class, section, gpa, cgpa, result, sp, added_by, added_date)
            values (" . db_escape($id) . "," . db_escape($school_year) . "," . db_escape($stid) . "," . db_escape($roll_id) . "," . db_escape($class) . "," . db_escape($section) . "," . db_escape($grade) . "," . db_escape($cgpa) . "," . db_escape($res) . ", " . db_escape(0) . "," . db_escape($user) . "," . db_escape($dt) . ")";
	
        db_query($sqlp, "student_result could not be inserted");
	//display_error($sqlp);
       
       }
}

function    get_sql_for_pass_promo_student($class,$section) {
   // display_error($class);
    $sql = "SELECT 
        sr.student_id,
        CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) AS name,  
        sc.class_name,
        ss.session_name,
        sr.gpa,
        sr.cgpa,
        sr.result,
        st.st_class,
        sr.id as tableid,
        sc.id,
        ss.id,
        sr.flag
        FROM " . TB_PREF . "sms_student_result sr
        LEFT JOIN " . TB_PREF . "sms_student st ON sr.stbl_id = st.id    
        LEFT JOIN " . TB_PREF . "sms_students_details sd ON st.applicant_id = sd.applicant_id
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON st.st_class = sc.id
        LEFT JOIN " . TB_PREF . "sms_session ss ON st.st_section = ss.id
        WHERE sc.id=" .  db_escape($class) . "AND ss.id=" .  db_escape($section);
    
    return $sql;
}

function get_student_for_result_processing($syear) {
    
  $sql = "SELECT st.*,
            CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name)AS name
            FROM " . TB_PREF . "sms_student st
            LEFT JOIN " . TB_PREF . "sms_students_details sd ON st.student_id = sd.student_id
            WHERE st.school_year = " . db_escape($syear) . "
            AND st.st_class = " . db_escape($_POST['class'])."
            AND st.promotion = 1";
  //display_error($sql);
    return $sql;
}

function get_applicant_data($applicant_id, $tbl_name, $wh) {

    $sql = "SELECT sd.*, sd.class, sc.class_name FROM " . TB_PREF . "$tbl_name sd
            LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON sd.class = sc.id
            WHERE sd.$wh=" . db_escape($applicant_id);
    //display_error($sql);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}

function get_sql_for_result($syear)
{
    $sql= "SELECT 
           sst.student_id,
           CONCAT(ssd.first_name,' ',ssd.middle_name,' ',ssd.last_name) as name,
           sst.st_class,
           sst.school_year
           FROM ". TB_PREF . "sms_student sst
           LEFT JOIN ". TB_PREF . "sms_students_details ssd ON sst.student_id = ssd.student_id";
    
    if (isset($_POST['SearchOrders'])){
          $sql .= " WHERE sst.st_class= " . db_escape($_POST['class']) ."
           AND sst.school_year=".db_escape($syear);
          
                   }
                   
    return $sql;
}
//-------------------------------------------

function get_sql_for_result_view($studentid){
    
    
 $sql = "SELECT *, CONCAT(first_name,' ',middle_name,' ',last_name) AS name,sp.father_name,sp.mother_name 
         FROM ".TB_PREF."sms_students_details sd
         LEFT JOIN ".TB_PREF."sms_stud_parent_details sp ON sp.applicant_id = sd.applicant_id
         WHERE sd.student_id =". db_escape($studentid);   
 
  $res = db_fetch(db_query($sql, 'Could not get student data.'));
    
    return $res;
    
}
//----------

function get_mark_for_result($class)
{
    $sql= "SELECT subject_name,credit,total_mark,id
           FROM " . TB_PREF . "sms_subject
           WHERE class=" .db_escape($class)."
           ";
    //display_error($sql);
    return $sql;
}
//---------------

function sql_for_result_status($year,$class)
{

    $sql= "SELECT sst.school_year, str.student_id, csc.class_name,str.result
           FROM " . TB_PREF . "sms_student sst
           LEFT JOIN " . TB_PREF . "sms_student_result str ON str.student_id = sst.student_id
           LEFT JOIN " . TB_PREF . "sms_create_stud_class csc ON csc.id = sst.st_class WHERE
           sst.school_year = " .  db_escape($year) . " AND csc.id = " . db_escape($class) .
            "AND sst.promotion=2 ";
    
    //display_error($sql);
    
    return $sql;
    
} 
