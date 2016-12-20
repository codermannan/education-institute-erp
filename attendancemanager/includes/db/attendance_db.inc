<?php
include_once($path_to_root . "/attendancemanager/includes/ui/attendance_ui_lists.inc");
// used
function get_sql_holy_data() {
   
    $sql = "SELECT hs.*,mon.full_name FROM " . TB_PREF . "sms_holiday_set_up hs
            LEFT JOIN " . TB_PREF . "hcm_salary_month mon ON hs.month = mon.mon_id";
        
    return $sql;
}


function delete_data($id)
{
    $sql= "delete from " . TB_PREF . "sms_holiday_set_up
        where id= " .db_escape($id);
    db_query($sql, "cannot delete");
}

function add_holy_data($year, $month,$st_date,$end_date,$reason)
{
    $st_dt=sql2date($st_date);
    $en_dt=sql2date($end_date);
    $days= date_range($st_dt, $en_dt);
    $count_days = count($days);
  
    $sql= "INSERT INTO " . TB_PREF . "sms_holiday_set_up (school_year,month,start_date,end_date,reason,no_of_days) VALUES ("
          . db_escape($year). ","
          . db_escape($month). ","
          . db_escape($st_dt). ","
          . db_escape($en_dt). ","
          . db_escape($reason).","
          . db_escape($count_days).  ")";
    
  db_query($sql,'cannot insert');
}

function update_holy_data($id , $month,$st_date,$end_date,$reason,$days ) {
 
$st_dt=sql2date($st_date);
    $en_dt=sql2date($end_date);
    $days= date_range($st_dt, $en_dt);
    $count_days = count($days);

    $sql = "UPDATE " . TB_PREF . "sms_holiday_set_up SET "
            . "month = " . db_escape($month)
            . "," . "start_date = " . db_escape($st_dt)
            . "," . "end_date = " . db_escape($en_dt)
            .","."reason=".db_escape($reason)
            .","."no_of_days=".db_escape($count_days)
            . " WHERE id = " . db_escape($id);
    db_query($sql, "teacher allocation could not be updated");
}


function get_sql_for_absent_view($studentid){
    
      
 $sql = "SELECT lf.*,sc.class_name 
         FROM ".TB_PREF."sms_leave_form lf
         LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON lf.class = sc.id
         WHERE student_id =". db_escape($studentid);   
 
  $res = db_fetch(db_query($sql, 'Could not get student data.'));
    
    return $res;  
    
}
//used
function add_form($cls,$sec,$stid,$ltype,$reason,$fdate,$tdate){
    $dt1= date2sql($fdate);
    $dt2= date2sql($tdate);
 
    $sq = "INSERT INTO " . TB_PREF ."sms_leave_form (class,section,student_id,leave_type,reason,from_date,to_date) values("
            . db_escape($cls) . "," . db_escape($sec) . ",". db_escape($stid) . ","
            . db_escape($ltype) . "," . db_escape($reason) . ",". db_escape($dt1) . ",". db_escape($dt2) .")";
  
db_query($sq,'could not insert');
}
//used
function update_form($stid){
  
     $sql = "UPDATE " . TB_PREF . "sms_leave_form SET approve=1 WHERE student_id=" . db_escape($stid) ;
 
  return db_query($sql, "could not update the exam setting table");
}
//used
function add_attendance($sid,$syear,$class,$section,$attendence){
   $dt= date2sql(Today()); 
    if($attendence != 1 && $attendence != '')
    {
    
    
    $sql= "INSERT INTO " . TB_PREF . "sms_stud_class_attendence(student_id,school_year,sclass,section,attendence,date)
          VALUES(" .  db_escape($sid) . "," .db_escape($syear) . "," .  db_escape($class) . "," .  db_escape($section) .",
          " . db_escape($attendence). "," . db_escape($dt). ")";
    
   // display_error($sql);
    db_query($sql,'insert failed');
    } 
}
//used
function take_student_attendance($syear, $class, $section){
   $dt= date2sql(Today()); 

   $sql = "SELECT ss.id, ss.student_id, ss.roll_number, CONCAT( std.first_name, ' ', std.middle_name, ' ', std.last_name ) AS name, ss.st_class, ss.st_section
FROM 0_sms_student ss
INNER JOIN 0_sms_students_details std ON ss.applicant_id = std.applicant_id
WHERE ss.school_year ="  . db_escape($syear)  . 
"AND ss.st_class = " .  db_escape($class)  .
"AND ss.st_section =" . db_escape($section).
"ORDER BY ss.roll_number ASC";
  // display_error($sql);
    return $sql;
    
}
//used
function get_sql_for_leave_form_view($tblid){
    
    
 $sql = "SELECT * FROM ".TB_PREF."sms_leave_form
    WHERE id =". db_escape($tblid);   
 
 $res = db_fetch(db_query($sql, 'Could not get student data.'));
    
    return $res;
    
}