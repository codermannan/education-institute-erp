<?php
// ---------------------for exam setting------------------------------
function add_exam_setting($school_year, $class, $subject, $exam_name, $child_exam, $amarks,$mmarks) {
    $sql = "INSERT INTO " . TB_PREF . "sms_exam_setting (school_year, class, subject, exam_name, child_exam_name,allocated_marks,min_marks) values (" . db_escape($school_year) .
            "," . db_escape($class) . "," . db_escape($subject) . "," . db_escape($exam_name) . "," . db_escape($child_exam) . "," . db_escape($amarks) . "," . db_escape($mmarks) . ")";
    //display_error($sql);
    db_query($sql, "could not insert into exam setting table");
}

function update_exam_setting($id, $class, $subject, $exam_name, $child_exam, $marks,$mmarks) {

    $sql = "UPDATE " . TB_PREF . "sms_exam_setting SET "
            . "class = " . db_escape($class)
            . " , " . "subject = " . db_escape($subject)
            . " , " . "exam_name = " . db_escape($exam_name)
            . " , " . "child_exam_name = " . db_escape($child_exam)
            . " , " . "allocated_marks = " . db_escape($marks)
            . " , " . "min_marks = " . db_escape($mmarks)
            . " WHERE id = " . db_escape($id);
    //display_error($sql);
    db_query($sql, "Exam setting could not be updated");
}


//.................................
function get_sql_exam_setting_data() {

    $sql = "SELECT
        sc.class_name,
        exn.exam_name as exname,
        en.exam_name as cexamname,
        es.allocated_marks,
        es.min_marks,
        sb.subject_name,
        es.id
        FROM " . TB_PREF . "sms_exam_setting es
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON es.class = sc.id
        LEFT JOIN " . TB_PREF . "sms_subject sb ON es.subject = sb.id
        LEFT JOIN " . TB_PREF . "sms_exam_name exn ON es.exam_name = exn.id
        LEFT JOIN " . TB_PREF . "sms_exam_name en ON es.child_exam_name = en.id";

    if ($_POST['searchclass'] != '') {
        $sql .= " WHERE es.class=" . db_escape($_POST['searchclass']);
    }

    return $sql;
}
// ---------------------for teacher allocation------------------------------
function add_teacher_allocation($class, $section , $subject, $teacher) {
    $sql = "INSERT INTO " . TB_PREF . "sms_teacher_allocation (class, section, subject, teacher) values (" . db_escape($class) . "," . db_escape($section) . "," .db_escape($subject) . "," . db_escape($teacher) . ")";
    db_query($sql, "could not insert into teacher allocation table");
}

function update_teacher_allocation($id, $class, $section , $subject, $teacher ) {

    $sql = "UPDATE " . TB_PREF . "sms_teacher_allocation SET "
            . "class = " . db_escape($class)
            . "," . "section = " . db_escape($section)
            . "," . "subject = " . db_escape($subject)
            . "," . "teacher = " . db_escape($teacher)
            . "WHERE id = " . db_escape($id);

    db_query($sql, "teacher allocation could not be updated");
}
function delete_data($id,$tbl)
{

    $sql= "DELETE from " . TB_PREF . "$tbl WHERE id=" .db_escape($id);
//        display_error($sql);
   return db_query($sql, 'cannot delete');
    
}
function get_sql_teacher_allocation_data($datasearch,$datavalue) {
   
    
    
      $sql = "SELECT
        he.name,
        sc.class_name,
        ss.session_name,
        sb.subject_name,
        sb.sub_code,
        ta.id
        FROM " . TB_PREF . "sms_teacher_allocation ta
        LEFT JOIN " . TB_PREF . "hcm_emp he ON ta.teacher = he.emp_code
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON ta.class = sc.id
        LEFT JOIN " . TB_PREF . "sms_session ss ON ta.section = ss.id
        LEFT JOIN " . TB_PREF . "sms_subject sb ON ta.subject = sb.id";
        
             
    
     if($datasearch){
               $sql .= "  WHERE ta.$datasearch =". db_escape($datavalue);
           }
       
    //display_error($sql);
    return $sql;

}
//--------------------------------------------------------------------

function add_stud_exam_schedule($name, $class, $subj, $date,$shift, $room, $status) {
//    $rep= date2sql($date);
    $sql = "INSERT INTO " . TB_PREF . "sms_stud_exam 
        (exam_name,class_name,subject_name,date,shift,room,status) values ("
         .db_escape($name) . "," . db_escape($class) . "," . db_escape($subj) . ","
         . db_escape($date) ."," . db_escape($shift) . "," . db_escape($room) . "," . db_escape($status) .")";
    display_error($sql);
    db_query($sql, 'insert failed');
}

function update_stud_exam_schedule($id, $ename, $class, $subj, $date, $shift, $room, $status) {
//   $rep= date2sql($date);
    $sql = "UPDATE " . TB_PREF . "sms_stud_exam  SET
       exam_name=" . db_escape($ename) . ","
            . "class_name=" . db_escape($class) . ","
            . "subject_name=" . db_escape($subj) . ","
            . "date=" . db_escape($date) . ","
            . "shift=" . db_escape($shift) . ","
            . "room=" . db_escape($room) . ","
            . "status=" . db_escape($status) . "
      where exam_id=" . db_escape($id);

    db_query($sql, 'update failed');
}

function get_schedule() {
    $sql = "SELECT scs.class_name,
          sen.exam_name,
          ss.subject_name,
          sse.date,
          rm.room_no,
          es.shift,
          sse.status,
          sse.exam_id
          FROM "      . TB_PREF . "sms_stud_exam sse
          LEFT JOIN " . TB_PREF . "sms_exam_name sen ON sse.exam_name = sen.id
          LEFT JOIN " . TB_PREF . "sms_create_stud_class scs ON sse.class_name = scs.id
          LEFT JOIN " . TB_PREF . "sms_subject ss ON sse.subject_name = ss.id
          LEFT JOIN " . TB_PREF . "sms_room_setup rm ON sse.room = rm.id
          LEFT JOIN " . TB_PREF . "sms_shift es ON es.id = sse.shift";
//    display_error($sql);
    if ($_POST['search_class'] != '') {
        $sql .= " WHERE sse.class_name=" . db_escape($_POST['search_class']);
    }
    //
    return $sql;
}

function delete_schedule($id) {
    $sql = "DELETE from " . TB_PREF . "sms_stud_exam WHERE exam_id=" . db_escape($id);

    db_query($sql, 'cannot delete');
}


//used to retrieve subject
// ---------------------for subject name------------------------------
function get_sqlp_db_data(){
    
    $sql = "SELECT sc.class_name, sj.subject_name, sj.sub_code, sj.total_mark,
            CASE 
                WHEN sj.assign_sub = '1' THEN 'Assign'
                WHEN sj.assign_sub = '0' THEN 'Not-Assign'
            ELSE sj.assign_sub
            END AS assign_sub, 
            sj.id 
            FROM ".TB_PREF."sms_subject sj
            LEFT JOIN ".TB_PREF."sms_create_stud_class sc ON sj.class=sc.id";
    
    if($_POST['class']!=''){
      $sql .=  " WHERE sj.class=".db_escape($_POST['class']);
    }
    return $sql;
};
//used to add subject
function add_subject($name,$sbcode,$total,$class,$credit,$assign_sub) {
    $sql = "INSERT INTO " . TB_PREF . "sms_subject (subject_name,sub_code,total_mark,class,credit,assign_sub)
        values (" . db_escape($name) . ",
        " . db_escape($sbcode) . ",
            " . db_escape($total) .",
        " . db_escape($class) .",
        " . db_escape($credit) .",
        " . db_escape($assign_sub) .")";
    //display_error($sql);
    db_query($sql, "could not insert into subject table");
    
}

function update_subject_class($id,$sub_name, $sbcode,$total,$class,$credit,$assign_sub) {

    $sql = "UPDATE " . TB_PREF . "sms_subject SET "
            . "subject_name = " . db_escape($sub_name) . ",
            ". "sub_code= " . db_escape($sbcode) . ",
              total_mark=" . db_escape($total) . ",
            class = " . db_escape($class) . ", credit=" . db_escape($credit) . ", assign_sub=" . db_escape($assign_sub) ."WHERE id=" . db_escape($id);
           
   
   db_query($sql, "subject could not be updated");
}
//used to delete subject
function delete_subject_class($id) {


    $sql = "DELETE from  " . TB_PREF . "sms_subject 
            WHERE id = " . db_escape($id);

    return db_query($sql, "Cant delete subject name.");
}

// ---------------------//used to add exam name subject-----------------------------
//........................xm................
function xm_name(){
    
    $sql = "SELECT en.exam_name,
                   en.parent, 
                   sc.class_name,
                   en.start_date,
                   en.end_date,
                   en.status,
                   en.id
            FROM " . TB_PREF . "sms_exam_name en
            LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON en.class_name = sc.id";
    
    if($_POST['xmclass']!=''){
      $sql .=  " WHERE en.class_name=".db_escape($_POST['xmclass']);
    }
    
    
    return $sql;
};
// ---------------------for exam name------------------------------
function add_exam_name($name,$class, $parent,$startdate, $enddate,$status) {
    if($parent == ''){
        $parent = 0;
    }
    $sql = "INSERT INTO " . TB_PREF . "sms_exam_name (exam_name,parent,class_name,start_date,end_date,status)
            values (" . db_escape($name) ."," . db_escape($parent) . "," . db_escape($class) . "," . db_escape(date2sql($startdate)) . "," . db_escape(date2sql($enddate)) . ",".  db_escape($status).")";
    db_query($sql, "could not insert into exam name table");
    //
    //display_error($sql);
}

function update_class_exam_name($id, $exam_name,$class, $parent,$startdate, $enddate,$status) {

    $sql = "UPDATE " . TB_PREF . "sms_exam_name SET "
            . " exam_name = " . db_escape($exam_name)
            . "," . " parent = " . db_escape($parent)
            . "," . " class_name = " . db_escape($class)
            . "," . " start_date = " . db_escape(date2sql($startdate))
            . "," . " end_date = " . db_escape(date2sql($enddate))
            . "," . " status = " . db_escape($status)
            . " WHERE id = " . db_escape($id);
    //display_error($sql);
    db_query($sql, "Exam could not be updated");
}

function delete_exam_name($id) {


    $sql = "DELETE from  " . TB_PREF . "sms_exam_name 
            WHERE id = " . db_escape($id);
    //display_error($id);

    return db_query($sql, "Cant delete exam name.");
}

//--------------------------------------------------------------
function get_sql_for_admit_card($class) {
    $sql = "SELECT ss.student_id,
     CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name,
     csc.class_name,
     sse.session_name,
     en.exam_name,ss.st_class FROM " . TB_PREF . "sms_student ss 
             LEFT JOIN " . TB_PREF . "sms_students_details sd ON ss.applicant_id = sd.applicant_id
             LEFT JOIN " . TB_PREF . "sms_create_stud_class csc ON ss.st_class = csc.id
             LEFT JOIN 0_sms_exam_name en ON ss.st_class = en.class_name AND en.parent = 0 AND en.status = 1
             LEFT JOIN " . TB_PREF . "sms_session sse ON ss.st_section = sse.id
             WHERE ss.st_class=" . db_escape($class)." AND ss.status = 1";
//    display_error($sql);
    return $sql;
}

function get_sql_for_admit_view($studentid)
{
     $sql = "SELECT ss.student_id, CONCAT( sd.first_name, ' ', sd.middle_name, ' ', sd.last_name ) AS name, cs.class_name, en.exam_name
               FROM " . TB_PREF .  "sms_student ss
               LEFT JOIN " . TB_PREF .  "sms_students_details sd ON ss.applicant_id = sd.applicant_id
               LEFT JOIN " . TB_PREF .  "sms_create_stud_class cs ON ss.st_class = cs.id
               LEFT JOIN " . TB_PREF .  "sms_exam_name en ON sd.id = en.id
               WHERE ss.student_id=" . db_escape($studentid);
    
    return $sql;                                                    
}

function get_sql_for_exam_schedule($id)
{
    
$sql= "SELECT date,time FROM" . TB_PREF . "sms_stud_exam WHERE id=" .db_escape($id) ;
}

//----------------------------------------------------------------------

function get_sql_for_students_mark_entry($syear, $class,$subject, $exam_name) {
   
    $sql = "SELECT
        sea.student_id, sea.school_year, sea.id,
        CONCAT(std.first_name,' ',std.middle_name,' ',std.last_name) AS name
        FROM " . TB_PREF . "sms_exam_attendence sea
        INNER JOIN " . TB_PREF . "sms_student st ON sea.student_id = st.student_id
        INNER JOIN " . TB_PREF . "sms_students_details std ON st.student_id = std.student_id
        WHERE sea.school_year = " . db_escape($syear)."
        AND sea.st_class = " . db_escape($class)."
        AND sea.subject = " . db_escape($subject)."
        AND sea.exam_name = " . db_escape($exam_name)."
        AND sea.status = 1";
    //display_error($sql);
    return $sql;
  
}

function get_student_for_update_marks($sub,$pexam,$cexam)
{
    $sql = "SELECT em.id,em.student_id,em.mark,CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name FROM " . TB_PREF . "sms_exam_mark_entry em 
            LEFT JOIN " . TB_PREF .  "sms_students_details sd ON sd.student_id = em.student_id
            WHERE em.subject=" . db_escape($sub). " AND em.exam_name=" . db_escape($pexam) . 
            " AND em.child_exam_name=" . db_escape($cexam);
    //display_error($sql);
    return $sql;
}
//----------------------------------------------------------------------

function get_sql_for_students_exam_attendance($class) {
   
    $sql = "SELECT
        st.student_id,st.id,
        CONCAT(std.first_name,' ',std.middle_name,' ',std.last_name) AS name 
        FROM " . TB_PREF . "sms_student st
        LEFT JOIN " . TB_PREF . "sms_students_details std ON st.applicant_id = std.applicant_id 
        WHERE st.student_id = std.student_id AND st.st_class = " . db_escape($class);  
    
           
        if($datasearch){
            $sql .= " AND sd.$datasearch LIKE '%$student%'";
        } 
       
        //display_error($sql);  
    return $sql;
  
}
//------------------------test marks------------------------------------

function add_exam_marks($ids,$syear,$exam_name,$child_exam_name,$class,$section,$subject,$stid,$marks,$grade,$gpa,$addedby) {
    
    $sql = "INSERT INTO " . TB_PREF . "sms_exam_mark_entry (school_year,exam_name,child_exam_name,st_class,section,subject,student_id,mark,grade,gpa,added_by,entry_date)
    values (".db_escape($syear).",".db_escape($exam_name).",".db_escape($child_exam_name).",".db_escape($class).",".db_escape($section).",
    ".db_escape($subject).",".db_escape($stid).",".db_escape($marks).",".db_escape($grade).",".db_escape($gpa).",".db_escape($addedby).",".db_escape(date2sql(Today())).")";
    
   
    db_query($sql, "could not insert exam mark");
    
    $sqld = "UPDATE " . TB_PREF . "sms_exam_attendence SET 
             flag = 1 WHERE id = " . db_escape($ids);
    db_query($sqld, "could not update");
   // display_error($ids);
    $sql_st = "UPDATE " . TB_PREF . "sms_student SET 
             promotion = 1 WHERE school_year = " . db_escape($syear). " AND student_id = " . db_escape($stid);
    db_query($sql_st, "could not update");
//    
}
//------------------------st attendance------------------------------------

function insert_exam_attendance($syear,$exam_name,$class,$subject,$stid,$attn,$reason) {
    
    $sql = "INSERT INTO " . TB_PREF . "sms_exam_attendence (school_year, exam_name, st_class, subject, student_id, status, reason, flag)
    values (".db_escape($syear).",".db_escape($exam_name).",".db_escape($class).",".db_escape($subject).",".db_escape($stid).",".db_escape($attn).",".db_escape($reason).",0)";
    //display_error($sql);
    db_query($sql, "could not insert attendence");

}


function add_room_setup($name,$no,$capacity)
{
    $sql= "INSERT INTO " . TB_PREF . "sms_room_setup(room_name,room_no,capacity) values (" . db_escape($name) . "," . db_escape($no) .  "," . db_escape($capacity) . ")";
    
    db_query($sql,'could not add');
}

function update_room_setup($id,$name,$no,$capacity)
{
    $sql= "UPDATE " . TB_PREF . "sms_room_setup SET room_name=" . db_escape($name) . 
            ", room_no=" . db_escape($no) . ", capacity=" . db_escape($capacity) . "WHERE id=" . db_escape($id);
    
    db_query($sql, 'update failed');
}

function delete_room_set_up($id)
{
    $sql= "delete from " . TB_PREF . "sms_room_setup
        where id= " .db_escape($id);
    db_query($sql, "cannot delete");
}

function add_grade_settings($syear,$class,$subject,$stype,$start,$end,$lgrade,$point)
{
    $sql= "INSERT INTO " . TB_PREF . "sms_grade_set_up(school_year,class,subject,grade_type,start_mark,end_mark,letter_grade,point) values (" 
            . db_escape($syear) . "," . db_escape($class) . "," . db_escape($subject) . "," . db_escape($stype) . "," . db_escape($start) . "," 
            . db_escape($end) . "," . db_escape($lgrade) . "," . db_escape($point) . ")";

    db_query($sql,'insert failed');
}

function update_grade_settings($id,$syear,$class,$subject,$gtype,$start,$end,$lgrade,$point)
{
    
    $sql= "UPDATE " . TB_PREF . "sms_grade_set_up SET
          school_year=" . db_escape($syear) . ", class=" . db_escape($hname) . ", subject=" . db_escape($subject) . ", grade_type=" .db_escape($gtype) . ", start_mark=" .db_escape($start) .
          ", end_mark=" .db_escape($end) . ", letter_grade=" .db_escape($lgrade) . ", point=" .db_escape($point) .  "WHERE id=" . db_escape($id);
    
    db_query($sql,'update successful');
}

function get_grade_settings()
{
   // display_error('class_name');
    $sql= "SELECT sy.school_year, gs.id,sy.begin,csc.class_name,ss.subject_name,gs.grade_type,gs.start_mark,gs.end_mark,gs.letter_grade,gs.point FROM " . TB_PREF . "sms_grade_set_up gs 
       LEFT JOIN " . TB_PREF . "sms_school_year sy ON sy.id = gs.school_year
       LEFT JOIN " . TB_PREF . "sms_create_stud_class csc ON csc.id = gs.class
       LEFT JOIN " . TB_PREF . "sms_subject ss ON ss.id = gs.subject" ;
    
    return $sql;
    
}

function get_student_data($applicant_id, $tbl_name, $wh) {

    $sql = "SELECT sd.*, sd.class, sc.class_name, sy.school_year FROM " . TB_PREF . "$tbl_name sd
            LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON sd.class = sc.id
            LEFT JOIN " . TB_PREF . "sms_school_year sy ON sd.school_year = sy.id
            WHERE sd.$wh=" . db_escape($applicant_id);
    //display_error($sql);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}

function get_student_info($id)
{
    
    $sql= "SELECT std.student_id,CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name)AS name,cs.class_name FROM "
          . TB_PREF . "sms_student std LEFT JOIN "
          . TB_PREF . "sms_create_stud_class cs ON cs.id = std.st_class LEFT JOIN "
          . TB_PREF . "sms_students_details sd ON sd.applicant_id = std.applicant_id
              WHERE std.student_id=" . db_escape($id);
    
    $rep= db_query($sql,'could not get data');
    
    return db_fetch($rep);
}

function get_students_marks_for_view($id)
{
    $sql= "SELECT em.mark,ss.subject_name,gs.letter_grade,gs.point FROM " . TB_PREF . "sms_grade_set_up gs
           LEFT JOIN " . TB_PREF . "sms_subject ss ON ss.id = gs.subject
           LEFT JOIN " . TB_PREF . "sms_exam_mark_entry em ON em.subject = gs.subject
           WHERE em.student_id =" .  db_escape($id);
    
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

function get_mark_for_evaluation($syear,$class,$sec)
{
    $sl= "SELECT roll_number FROM " . TB_PREF . "sms_student
         WHERE st_class=" . db_escape($class) . "AND school_year=" . db_escape($syear) . "AND st_section=" . db_escape($sec);
    
 db_query($sl,'can not get data');
}

function get_mark_for_result_total($id)
{
    $sql= "SELECT sum(em.gpa)
           FROM " . TB_PREF . "sms_subject ss
           LEFT JOIN " . TB_PREF . "sms_exam_mark_entry em ON  ss.id = em.subject
           WHERE student_id=" .db_escape($id);
  // display_error($sql);
   
    return  db_query($sql);
}

function get_sql_for_payment_report($class,$sdate,$edate)
{
      $from_date= date2sql($sdate);
      $to_date= date2sql($edate);
      
      $sql= "SELECT * FROM " . TB_PREF . "sms_transaction_details";
      
            if(isset($from_date) && isset($to_date)){
            $sql .= " WHERE trans_date >='$from_date' 
            AND trans_date<='$to_date'";
            }
            
      
      return $sql;
}








//function get_sql_for_atten_view($class,$section,$date)
//{
//    $sql= "";
//    
//}

function get_sql_for_view($syear, $class,$section,$fdt,$tdt)
{
//    display_error($fdt);
//    exit();
    $from=date2sql($fdt);
    $to=date2sql($tdt);
    $sql ="SELECT st.student_id FROM " . TB_PREF . "sms_stud_class_attendence ca
         LEFT JOIN " . TB_PREF . "sms_student st ON st.id = ca.student_id
          WHERE date BETWEEN " .  db_escape($from)." AND " .  db_escape($to);
    display_error($sql);
    
    return $sql;
    
}

function get_info_for_notice()
{
    $sql = "SELECT notice FROM " . TB_PREF . "sms_dashboard ";
    
    return $sql;
    
}


//used to update exam attendance
function get_student_update_info($stid,$sub){
    
  $sql= "SELECT exa.id, exa.student_id,exa.status,exa.reason,CONCAT(std.first_name,' ',std.middle_name,' ',std.last_name) as name FROM "
        . TB_PREF . "sms_exam_attendence exa LEFT JOIN " . TB_PREF . "sms_students_details std ON std.student_id = exa.student_id
        WHERE exa.subject = " . db_escape($sub) ." AND exa.student_id LIKE '%$stid%' AND exa.flag != 1";
  //display_error($sql);
  return $sql;
}
//used to update exam marks
function exam_marks_update($id, $mark, $sub, $pexam, $cexam) {
    $sql = "UPDATE " . TB_PREF . "sms_exam_mark_entry SET mark= " . db_escape($mark) . " WHERE  id= " . db_escape($id);
    //display_error($sql);
    db_query($sql);
}
//------------------------st attendance------------------------------------

function add_exam_attendance($syear, $exam_name, $class, $subject, $stid, $attn, $reason) {

    $sql = "INSERT INTO " . TB_PREF . "sms_exam_attendence (school_year, exam_name, st_class, subject, student_id, status, reason, flag)
    values (" . db_escape($syear) . "," . db_escape($exam_name) . "," . db_escape($class) . "," . db_escape($subject) . "," . db_escape($stid) . "," . db_escape($attn) . "," . db_escape($reason) . ",0)";
    //display_error($sql);
    db_query($sql, "could not insert attendence");
}
//used to update exam attendance
function update_student_attendance($id, $status, $reason) {

    $sql = "UPDATE " . TB_PREF . "sms_exam_attendence SET status=" . db_escape($status) .
            ", reason=" . db_escape($reason) . " WHERE id=" . db_escape($id);
    
//    display_error($sql);

    db_query($sql);
}

//------------------------test marks------------------------------------

function add_exam_marks_with_ct($ids, $syear, $exam_name, $child_exam_name, $class, $section, $subject, $stid, $marks, $grade, $gpa, $addedby) {

    $sql = "INSERT INTO " . TB_PREF . "sms_exam_mark_entry (school_year,exam_name,child_exam_name,st_class,section,subject,student_id,mark,grade,gpa,added_by,entry_date)
    values (" . db_escape($syear) . "," . db_escape($exam_name) . "," . db_escape($child_exam_name) . "," . db_escape($class) . "," . db_escape($section) . ",
    " . db_escape($subject) . "," . db_escape($stid) . "," . db_escape($marks) . "," . db_escape($grade) . "," . db_escape($gpa) . "," . db_escape($addedby) . "," . db_escape(date2sql(Today())) . ")";


    db_query($sql, "could not insert exam mark");

    $sqld = "UPDATE " . TB_PREF . "sms_exam_attendence SET 
             flag = 1 WHERE id = " . db_escape($ids);
    db_query($sqld, "could not update");
    // display_error($ids);
    $sql_st = "UPDATE " . TB_PREF . "sms_student SET 
             promotion = 1 WHERE school_year = " . db_escape($syear) . " AND student_id = " . db_escape($stid);
    db_query($sql_st, "could not update");
//    
}
//--------
function add_shift($shift, $start_time, $end_time) {
    $sql = "INSERT INTO " . TB_PREF . "sms_shift (shift,start_time,end_time) VALUES (" .db_escape($shift). "," . db_escape($start_time) . "," . db_escape($end_time) . ")";

 db_query($sql, 'can not add');
}

function update_shift($id,$shift,$start_time,$end_time) {
    $sql = "UPDATE " . TB_PREF . "sms_shift SET shift=" . db_escape($shift). ", start_time = " . db_escape($start_time) . ", end_time = " . db_escape($end_time) .
            " WHERE id=" . db_escape($id);

    $tr = db_query($sql, 'can not update');
}

//guard
function get_sql_for_guard() {
    $sql = "SELECT ss.shift,sg.date,rs.room_no,he.name,sg.id FROM " . TB_PREF . "sms_guard_set_up sg
            LEFT JOIN " . TB_PREF ."sms_shift ss ON ss.id = sg.shift LEFT JOIN "
            . TB_PREF ."sms_room_setup rs ON rs.id = sg.allocated_room LEFT JOIN "
            . TB_PREF . "hcm_emp he ON he.emp_code= sg.teacher";
      if ($_POST['shift1'] != '') {
        $sql .= " WHERE sg.shift=" . db_escape($_POST['shift1']);
    }

//    display_error($sql);
    
    return $sql;
}

function add_guard_set_up($shift, $date, $room, $teacher) {
    
//    $dt = sql2date($date);
    $sql = "INSERT INTO " . TB_PREF . "sms_guard_set_up (shift,date,allocated_room,teacher) VALUES ("
            . db_escape($shift) . ","
            . db_escape($date) . ","
            . db_escape($room) . ","
            . db_escape($teacher) . ")";
//    display_error($sql);
    db_query($sql, "insert fails");
}

function update_guard_set_up($id, $shift, $date, $room, $teacher) {
   
    $sql = "UPDATE " . TB_PREF . "sms_guard_set_up SET shift=" . db_escape($shift) .
            ", date=" . db_escape($date) . ", allocated_room=" . db_escape($room) . ", teacher=" . db_escape($teacher) .
            " WHERE id=" . db_escape($id);

  db_query($sql, 'update fails');
}
