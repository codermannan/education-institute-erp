<?php
//student db function

//used.........................................................period setup
function get_period_data()
{
    $sql = "SELECT std.class_name,ss.session_name,sp.no_of_period,sp.id FROM " . TB_PREF . "sms_period_set_up sp 
        LEFT JOIN " . TB_PREF . "sms_create_stud_class std ON std.id = sp.class_name 
        LEFT JOIN " . TB_PREF . "sms_session ss ON ss.id = sp.section";
    
    return $sql ;
}

//used
function add_period_setting($class , $section, $period) {
    
    $sql = "INSERT INTO " . TB_PREF . "sms_period_set_up (class_name, section, no_of_period) values (" . db_escape($class) . "," . db_escape($section) . "," . db_escape($period) . ")";
   
    return db_query($sql, "could not insert into exam setting table");

}
//used
function update_period_settings($id,$cls,$sec,$period)
{
    $sql = "UPDATE " . TB_PREF . "sms_period_set_up SET class_name=" . db_escape($cls) . ", section=" . db_escape($sec) . ", no_of_period=" . db_escape($period) .
           "WHERE id=" . db_escape($id);
 
  return db_query($sql, "could not update the exam setting table");
  
}
//used
function delete_data($id,$tbl)
{

    $sql= "DELETE from " . TB_PREF . "$tbl WHERE id=" .db_escape($id);
//        display_error($sql);
   return db_query($sql, 'cannot delete');
    
}
//used
function update_routine_set_up($id,$cls,$day,$sub,$teacher,$per)
{
    $sql ="UPDATE " .TB_PREF . "sms_class_routine SET class=" .  db_escape($cls) . ",
        week_day = " .  db_escape($day) . ",
        subject = " .  db_escape($sub) . ",
        teacher = " .  db_escape($teacher) . ",
        period = "  . db_escape($per) . " 
        WHERE id = " . db_escape($id);
    display_error($sql);
    $tr = db_query($sql,"can not update");
          
    
}
//used
function add_routine_set_up($cls,$day,$sub,$teacher,$per)
{
    $sql = "INSERT INTO " .TB_PREF . "sms_class_routine (class,week_day,subject,teacher,period) VALUES ("
            . db_escape($cls) . "," . db_escape($day) . ","  .  db_escape($sub) . "," . db_escape($teacher) ."," . db_escape($per) .")";
    
    $res =db_query($sql,'data can not add');
    
}
//used
function get_sql_for_routine_set_up($id)
{
    $sql= "SELECT * FROM " . TB_PREF . "sms_class_routine 
                          WHERE id=" . db_escape($id);
    //display_error($sql);
    return $sql;
}
//---------------------------routine setup...........
function routine_setup(){
    
    $sql1 = "SELECT
           sc.class_name,
           hw.day ,
           ss.subject_name,
           he.name,
           cd.period
           FROM ". TB_PREF . "sms_class_routine sr
           LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON sc.id = sr.class
           LEFT JOIN " . TB_PREF . "hcm_weekdays1 hw ON hw.day_id = sr.week_day
           LEFT JOIN " . TB_PREF . "sms_subject ss ON ss.id = sr.subject
           LEFT JOIN " . TB_PREF . "hcm_emp he ON  he.emp_code=sr.teacher 
           LEFT JOIN " . TB_PREF . "sms_class_duration cd ON cd.period = sr.period";
    return $sql1;
};

//used
function add_time_duration($scl,$cls,$sectn,$subjt,$sub,$start_time,$end_time)
{
    $sql= "INSERT INTO " . TB_PREF . "sms_class_duration (school_year,class,section,subject,period,start_time,end_time) VALUES (" . db_escape($scl) . "," . db_escape($cls) . "," . db_escape($sectn) . "," . db_escape($subjt) . "," . db_escape($sub) . "," . db_escape($start_time) . "," .  db_escape($end_time) . ")";
//display_error($sql);
  $tb= db_query($sql,'can not add');
}
//used
function update_time_duration($id,$cls,$sectn,$subjt,$sub,$start_time,$end_time)
{
    $sql= "UPDATE " . TB_PREF . "sms_class_duration SET class=" .  db_escape($cls) . ", section=" .  db_escape($sectn) . ",subject=" .  db_escape($subjt) . ",period= " . db_escape($sub) . ", start_time = " .  db_escape($start_time) . ", end_time = " .  db_escape($end_time) .
                      "WHERE id=" . db_escape($id);
   
   $tr= db_query($sql,'can not update');
}
//used
function get_sql_for_duration($id)
{
    $sql ="SELECT * FROM " . TB_PREF . "sms_class_duration WHERE id= " . db_escape($id);
    
    return $sql;
}
//For scholarship list--------------------------
function get_sql_for_scholarship_view($class,$applicant, $datasearch) {
    
    $sql = "SELECT 
        sch.applicant_id, 
        sd.photo_upload,
        CONCAT(sd.first_name, ' ',sd.middle_name, ' ', sd.last_name)as name,
        sd.mobile,
        sd.per_address,
        sc.cat_description,
        sch.is_approved_cat
        FROM " . TB_PREF . "sms_student_scholarship sch
        INNER JOIN " . TB_PREF . "sms_student_category sc ON sch.student_cat = sc.id
        INNER JOIN " . TB_PREF . "sms_students_details sd ON sch.applicant_id = sd.applicant_id
        WHERE sch.student_cat!=1";
    
       if($class){
           $sql .= " AND sch.class=".db_escape($class);
       }

        if($datasearch){
            $sql .= " AND sd.$datasearch LIKE '%$applicant%'";
        } 
       
      $sql .= " ORDER BY sch.id DESC";
      //display_error($sql);
    return $sql;
}
//------------------For grade setup--------------//
function get_grade_settings() {
    $sql = "SELECT * FROM " . TB_PREF . "sms_grade_set_up";
     return $sql;
}

function update_grade_settings($id, $start, $end, $lgrade, $point,$stpoint,$epoint) {

    $sql = "UPDATE " . TB_PREF . "sms_grade_set_up SET start_mark=" . db_escape($start) .
            ", end_mark=" . db_escape($end) . ", letter_grade=" . db_escape($lgrade) . ", cpoint=" . db_escape($point) . ",start_point=" . db_escape($stpoint) . ",end_point=" . db_escape($epoint) . "WHERE id=" . db_escape($id);
    db_query($sql, 'update successful');
}
function add_grade_settings($start, $end, $lgrade, $point,$spoint,$epoint) {
    $sql = "INSERT INTO " . TB_PREF . "sms_grade_set_up(start_mark,end_mark,letter_grade,cpoint,start_point,end_point) 
            values (" . db_escape($start) . ",". db_escape($end) . "," . db_escape($lgrade) . "," . db_escape($point) . "," . db_escape($spoint) . "," . db_escape($epoint) . ")";

    db_query($sql, 'insert failed');
}
//--------------------