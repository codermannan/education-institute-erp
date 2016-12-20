<?php
$path_to_root = "../..";
include_once($path_to_root . "/sms/includes/ui/applicant_info_ui_lists.inc");

function add_applicant($NewProID, $f_name, $m_name, $l_name, $class_id, $group_id, $dob, 
        $gender, $blood_group, $pre_address, $per_address, $city, $country, 
        $birth_place, $phone, $mobile, $email, $nationality, $mother_tongue,
        $religion, $hobby, $exca, $pic, $frm_price, $discount,$user) {
    $dateob = date2sql($dob);
    $syear = get_current_schoolyear();
    $sql = "INSERT INTO " . TB_PREF . "sms_students_details (`applicant_id`, `first_name`, `middle_name`, `last_name`, `class`, `group`, `dob`, `gender`, `blood_group`, `pre_address`, `per_address`, `city`, `country`, `birth_place`, `phone`, `mobile`, `email`, `nationality`, `mother_tongue`, `religion`, `hobby`, `extra_curriculum`, `photo_upload`,`school_year`) 
                VALUES (" . db_escape($NewProID) . "," . db_escape($f_name) . ","
            . db_escape($m_name) . ", " . db_escape($l_name) . ", "
            . db_escape($class_id) . ", " . db_escape($group_id) . ","
            . db_escape($dateob) . ", " . db_escape($gender) . ", "
            . db_escape($blood_group) . ", " . db_escape($pre_address) . ","
            . db_escape($per_address) . ", " . db_escape($city) . ", "
            . db_escape($country) . ", " . db_escape($birth_place) . ","
            . db_escape($phone) . ", " . db_escape($mobile) . ", "
            . db_escape($email) . ", " . db_escape($nationality) . ", "
            . db_escape($mother_tongue) . ", " . db_escape($religion) . ", "
            . db_escape($hobby) . ", " . db_escape($exca) . ","
            . db_escape($pic) . "," . db_escape($syear) .
            ")";
    
    db_query($sql, "The Applicant information could not be added");
    
    $requ = (($frm_price * $discount)/100);
    $restamnt = ($frm_price - $requ);
    $td=date2sql(Today());
    
    $sqlp = "INSERT INTO " . TB_PREF . "sms_app_payment (school_year,class,applicant_id,form_price,discount,rest_amnt,sale_date,receive_by)
        values (" . db_escape($syear) . "," . db_escape($class_id) . "," . db_escape($NewProID) . "," . db_escape($frm_price) . "," . db_escape($dicount) . "," . db_escape($restamnt) . "," . db_escape($td) . "," . db_escape($user) . ")";
   
    db_query($sqlp, "could not insert into app payment table");
}

//........................xm................
function xm_name(){
    
    $sql = "SELECT en.exam_name, en.parent, sc.class_name, en.allocated_marks,en.id
FROM " . TB_PREF . "sms_exam_name en
LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON en.class_name = sc.id";
    if($_POST['xmclass']!=''){
      $sql .=  " WHERE en.class_name=".db_escape($_POST['xmclass']);
    }
    
    
    return $sql;
};
//.........................................
//-------update parent_info-----/
function update_applicant($re_id, $appl_id ,$f_name, $m_name, $l_name, $class_id, $group_id, $dob, $gender, $blood_group, $pre_address, $per_address, $city, $country, $birth_place, $phone, $mobile, $email, $nationality, $mother_tongue, $religion, $hobby, $exca, $pic, $frm_price, $discount) {
    $daob = date2sql($dob);

    $sql = "UPDATE " . TB_PREF . "sms_students_details SET 
        
        first_name=" . db_escape($f_name) . ", 	
        middle_name=" . db_escape($m_name) . ",
        last_name=" . db_escape($l_name) . ",
        class=" . db_escape($class_id) . ",
        `group` =" . db_escape($group_id) . ", 
	dob=" . db_escape($daob) . ", 
	gender=" . db_escape($gender) . ", 
	blood_group=" . db_escape($blood_group) . ", 
	pre_address=" . db_escape($pre_address) . ", 
	per_address=" . db_escape($per_address) . ", 
	city=" . db_escape($city) . ",
        country=" . db_escape($country) . ",
        birth_place =" . db_escape($birth_place) . ",
        phone=" . db_escape($phone) . ",
	mobile=" . db_escape($mobile) . ", 
	email=" . db_escape($email) . ", 
	nationality=" . db_escape($nationality) . ", 
	mother_tongue = " . db_escape($mother_tongue) . ", 
	religion =" . db_escape($religion) . ",
        hobby =" . db_escape($hobby) . ",  
        extra_curriculum =" . db_escape($exca) . ",
        photo_upload =" . db_escape($pic) . "
        
        WHERE id = " . db_escape($re_id);

    db_query($sql, "Applicants information not be updated");
    
    $requ = (($frm_price * $discount)/100);
    $restamnt = ($frm_price - $requ);
    
    $sqlp = "UPDATE " . TB_PREF . "sms_app_payment SET
            form_price  =" . db_escape($frm_price) . ", 	
            discount    =" . db_escape($discount) . ",
            rest_amnt   =" . db_escape($restamnt) . "
            WHERE applicant_id = " . db_escape($appl_id);

    db_query($sqlp, "could not insert into app payment table");
}

//-------add_applicant_parent_info-----/
function add_applicant_parent_info($NewAppID, $father_name, $mother_name, $father_occp, $mother_occp, $ppre_address, $pper_address, $income_source, $pcity, $pcountry, $pphone, $pmobile, $pemail) {

    $sql = "INSERT INTO " . TB_PREF . "sms_stud_parent_details (`applicant_id`, `father_name`, `mother_name`, `f_occupation`, `m_occupation`, `email`, `persent_add`, `permanent_add`, `income_source`, `city`, `country`, `mobile`, `alternate_phone`) 
                VALUES (" . db_escape($NewAppID) . "," . db_escape($father_name) . ","
            . db_escape($mother_name) . ", " . db_escape($father_occp) . ", "
            . db_escape($mother_occp) . ", " . db_escape($pemail) . ","
            . db_escape($ppre_address) . ", " . db_escape($pper_address) . ", "
            . db_escape($income_source) . ", " . db_escape($pcity) . ","
            . db_escape($pcountry) . ", " . db_escape($pmobile) . ", "
            . db_escape($pphone) . ")";

    
   db_query($sql, "The Applicant information could not be added");
}

//-------update parent_info-----/
function update_parent_info($NewAppID, $father_name, $mother_name, $father_occp, $mother_occp, $ppre_address, $pper_address, $income_source, $pcity, $pcountry, $pphone, $pmobile, $pemail) {

    $sql = "UPDATE " . TB_PREF . "sms_stud_parent_details SET 
        
        father_name=" . db_escape($father_name) . ", 	
        mother_name=" . db_escape($mother_name) . ",
        f_occupation=" . db_escape($father_occp) . ",
        m_occupation=" . db_escape($mother_occp) . ",
        email =" . db_escape($pemail) . ", 
	persent_add=" . db_escape($ppre_address) . ", 
	permanent_add=" . db_escape($pper_address) . ", 
	income_source=" . db_escape($income_source) . ", 
	city=" . db_escape($pcity) . ", 
	country=" . db_escape($pcountry) . ", 
	mobile=" . db_escape($pmobile) . ",
        alternate_phone=" . db_escape($pphone) . "
        
        WHERE applicant_id = " . db_escape($NewAppID);
    //display_error($sql);
    db_query($sql, "Applicants parent information not be updated");
}

//-------add_previous education-----/
function add_applicant_pre_education($NewAppID, $inst_name, $class_id, $group_id, $edu_year, $grade, $cgpa, $outof, $folupdate, $smsalert, $email_notification) {
    $follupdate = date2sql($folupdate);

    $sql = "INSERT INTO " . TB_PREF . "sms_stud_edu_details (`applicant_id`, `institute_name`, `class`, `st_group`, `year`, `grade`, `cgpa`, `out_of`, `folupdate`, `sms_alert`, `email_notification`) 
                VALUES (" . db_escape($NewAppID) . "," . db_escape($inst_name) . ","
            . db_escape($class_id) . ", " . db_escape($group_id) . ", "
            . db_escape($edu_year) . ", " . db_escape($grade) . ","
            . db_escape($cgpa) . ", " . db_escape($outof) . ", "
            . db_escape($follupdate) . ", " . db_escape($smsalert) . ","
            . db_escape($email_notification) . ")";

    //display_error($sql);
    db_query($sql, "The Applicant education could not be added");
}

//-------update education_info-----/
function update_pre_education($NewAppID, $inst_name, $class_id, $group_id, $edu_year, $grade, $cgpa, $outof, $folupdate, $smsalert, $email_notification) {

    $sql = "UPDATE " . TB_PREF . "sms_stud_edu_details SET 
        
        institute_name=" . db_escape($inst_name) . ",
        class=" . db_escape($class_id) . ",
        st_group=" . db_escape($group_id) . ",
        year=" . db_escape($edu_year) . ", 
	grade=" . db_escape($grade) . ", 
	cgpa=" . db_escape($cgpa) . ", 
	out_of=" . db_escape($outof) . ", 
	folupdate=" . db_escape($folupdate) . ", 
	sms_alert=" . db_escape($smsalert) . ",
        email_notification=" . db_escape($email_notification) . "
        
        WHERE applicant_id = " . db_escape($NewAppID);

    db_query($sql, "Applicants previous education history not be updated");
}

function get_applicant_data($applicant_id, $tbl_name, $wh) {

    $sql = "SELECT sd.*, sd.class, sc.class_name FROM " . TB_PREF . "$tbl_name sd
            LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON sd.class = sc.id
            WHERE sd.$wh=" . db_escape($applicant_id);
    //display_error($sql);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}

function get_app_data($applicant_id, $tbl_name, $wh) {

    $sql = "SELECT  * FROM " . TB_PREF . "$tbl_name
            WHERE $wh=" . db_escape($applicant_id);
    //display_error($sql);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}


// ---------------------for exam name------------------------------
function add_exam_name($name,$class, $parent, $marks) {
    if($parent == ''){
        $parent = 0;
    }
    $sql = "INSERT INTO " . TB_PREF . "sms_exam_name (exam_name,parent,class_name,allocated_marks) values (" . db_escape($name) ."," . db_escape($parent) . "," . db_escape($class) . "," . db_escape($marks) . ")";
    db_query($sql, "could not insert into exam name table");
    //display_error($sql);
}

function update_exam_name($id, $exam_name,$class, $parent,$exam_description) {

    $sql = "UPDATE " . TB_PREF . "sms_exam_name SET "
            . "exam_name = " . db_escape($exam_name)
            . "," . "parent = " . db_escape($parent)
            . "," . "class_name = " . db_escape($class)
            . "," . "allocated_marks = " . db_escape($exam_description)
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

// ---------------------for subject name------------------------------
function get_sqlp_db_data(){
    
    $sql = "SELECT sc.class_name,sj.subject_name,sj.sub_code,sj.mcq,written,sj.practical,sj.total_mark,sj.credit,sj.id FROM ".TB_PREF."sms_subject sj
    LEFT JOIN ".TB_PREF."sms_create_stud_class sc ON sj.class=sc.id";
    if($_POST['class']!=''){
      $sql .=  " WHERE sj.class=".db_escape($_POST['class']);
    }
    return $sql;
};
function add_subject($name,$sbcode,$mcq,$written,$practical,$total, $class,$credit) {
    $sql = "INSERT INTO " . TB_PREF . "sms_subject (subject_name,sub_code,mcq, written,practical,total_mark,class,credit)
        values (" . db_escape($name) . ",
        " . db_escape($sbcode) . ",
        " . db_escape($mcq) . ",
        " . db_escape($written) . ",
        " . db_escape($practical) . ",
        " . db_escape($total) . ",
        ". db_escape($class) . ",
        " . db_escape($credit) . ")";
    //display_error($sql);
    db_query($sql, "could not insert into subject table");
    
}

function update_subject_class($id,$sub_name, $sbcode, $mcq,$written,$practical,$total, $class,$credit) {

    $sql = "UPDATE " . TB_PREF . "sms_subject SET "
            . "subject_name = " . db_escape($sub_name) . ",
            ". "sub_code= " . db_escape($sbcode) . ",    
            ". "mcq= " . db_escape($mcq) . ",
            ". "written= " . db_escape($written) . ",
            ". "practical= " . db_escape($practical) . ",
            ". "total_mark= " . db_escape($total) . ",    
            " ."class = " . db_escape($class).",
            ". "credit= " . db_escape($credit)     
            . "WHERE id = " . db_escape($id);
    //display_error($sql);
    db_query($sql, "subject could not be updated");
}
//

//............................
//function get_sqlp_db_data(){
//    
//    $sql = "SELECT sc.class_name,sj.subject_name,sj.mcq,written,sj.practical,sj.total_mark,sj.credit,sj.id FROM ".TB_PREF."sms_subject sj
//    LEFT JOIN ".TB_PREF."sms_create_stud_class sc ON sj.class=sc.id";
//    
//    return $sql;
//};
//...........................
function delete_subject_class($id) {


    $sql = "DELETE from  " . TB_PREF . "sms_subject 
            WHERE id = " . db_escape($id);

    return db_query($sql, "Cant delete subject name.");
}


//------------------------------------------------------------------------

function add_student_group($name, $class) {
    //display_error(db_escape($description));
    $sql = "INSERT INTO " . TB_PREF . "sms_candidate_group (group_name,group_covered_class) values (" . db_escape($name) . "," . db_escape($class) . ")";
    db_query($sql, "could not insert into group table");
}

function update_student_group($id, $group_name, $class_name) {

    $sql = "UPDATE " . TB_PREF . "sms_candidate_group SET "
            . "group_name = " . db_escape($group_name)
            . "," . "group_covered_class = " . db_escape($class_name)
            . "WHERE id = " . db_escape($id);

    db_query($sql, "Group could not be updated");
}

function getgroupBYid($id) {

    $sql = "Select * from " . TB_PREF . "sms_candidate_group where id=" . db_escape($id);

    $res = db_fetch(db_query($sql, 'Cant get group .'));

    return $res;
}

function delete_selected_group($id) {


    $sql = "DELETE from  " . TB_PREF . "sms_candidate_group 
            WHERE id = " . db_escape($id);

    return db_query($sql, "Cant delete student group.");
}

//---------------------------------------------------------------------
//---------------------------exam schedule------------------------------

function add_exam_schedule($date, $venue, $time, $status) {
    $ex_date = date2sql($date);

    $sql = "INSERT INTO " . TB_PREF . "sms_exam_schedule (exam_date,exam_venue,exam_time,close) values (" . db_escape($ex_date) . "," . db_escape($venue) . "," . db_escape($time) . "," . db_escape($status) . ")";
    db_query($sql, "could not insert into schedule table");
}

function update_exam_schedule($id, $date, $venue, $time, $status) {
   
    $ex_date = date2sql($date);
    $sql = "UPDATE " . TB_PREF . "sms_exam_schedule SET "
            . "exam_date = " . db_escape($ex_date)
            . "," . "exam_venue = " . db_escape($venue) .
            "," . "exam_time = " . db_escape($time) .
             "," . "close = " . db_escape($status)
            . "WHERE id = " . db_escape($id);
    //display_error($sql);

    db_query($sql, "Schedule update failed");
}

function scrh_exam_scdle($pdt){
    
    $sql = "SELECT exam_date,exam_venue,exam_time,close,id FROM ".TB_PREF."sms_exam_schedule";
    
    if($pdt!=''){
      $sql .=  " WHERE exam_date=".db_escape(date2sql($pdt));
    }
    
    return $sql;
};

function delete_selected_schedule($id) {


    $sql = "DELETE from  " . TB_PREF . "sms_exam_schedule 
            WHERE id = " . db_escape($id);

    return db_query($sql, "Cant delete selected schedule.");
}

function getschedule($myid) {

    $sql = "Select * from " . TB_PREF . "sms_exam_schedule where id=" . db_escape($myid);
    $res = db_fetch(db_query($sql, 'Cant get schedule .'));

    return $res;
}

//--------------------------------------------------------------------------

function add_applicant_status($status,$applicant_id,$atten,$class) {
//display_error($applicant_id);
    $sql = "INSERT INTO " . TB_PREF . "sms_applicant_atten_status(status,applicant_id,applicant_attendance,class) values (" . db_escape($status) . "," . db_escape($applicant_id) . "," . db_escape($atten) . "," . db_escape($class) . ")";
     $re  =db_query($sql, "insert fails");
     
$sq2 = "UPDATE " . TB_PREF . "sms_students_details SET atten= '1' WHERE applicant_id=" . db_escape($applicant_id) ;


$re  =db_query($sq2, "insert fails");
}  
//}
//--------------------------------------------------------
//function update_applicant_status($applicant_id, $atten) {
//
//    $sql = "UPDATE " . TB_PREF . "sms_applicant_atten_status SET 
//             applicant_attendance = " . db_escape($atten) . "
//            WHERE id = " . db_escape($applicant_id) . "AND flag != '0'";
//    //display_error($sql);
//
//    db_query($sql, "Cannot update applicant status");
//}

function get_applicant_status($id)
{
    
 $sql = "Select * from " . TB_PREF . "sms_applicant_atten_status where id=" . db_escape($id);
 //display_error(db_query($sql)); 
    $res = db_fetch(db_query($sql, 'Cant get status.'));

    return $res;   
}

function delete_applicant_status($id) {


    $sql = "DELETE from  " . TB_PREF . "sms_applicant_status 
            WHERE id = " . db_escape($id);

    return db_query($sql, "Cant delete selected applicant.");
}

//-----------------------------test subject--------------------------------

function add_test_subject($sub_name, $class) {
    //display_error(db_escape($description));
    $sql = "INSERT INTO " . TB_PREF . "sms_admission_subject (subject_name, class) values (" . db_escape($sub_name) . "," . db_escape($class) . ")";
    db_query($sql, "could not insert into subject table");
}

function update_test_subject($sub_id, $sub_name, $class) {

    $sql = "UPDATE " . TB_PREF . "sms_admission_subject SET 
             subject_name = " . db_escape($sub_name) . ",
             class = " . db_escape($class) .
            "WHERE subject_id = " . db_escape($id);
    //display_error($sql);

    db_query($sql, "Cannot update test subject");
}

function getsubjectBYid($id) {

    $sql = "Select * from " . TB_PREF . "sms_admission_subject where subject_id =" . db_escape($id);
   
    $res = db_fetch(db_query($sql, 'Can not get subject.'));

    return $res;
}

//----------------------------------------------------------------
function getClasswiseStudent() {

    $sql = "Select applicant_id, first_name from " . TB_PREF . "sms_students_details";

    return $sql;
}

//------------------------test marks------------------------------------

function add_test_marks($app_id,$marks,$status) {

    $sql = "INSERT INTO " . TB_PREF . "sms_admission_marks (applicant_id,obtain_marks,status) values (".db_escape($app_id)."," .db_escape($marks) . "," . db_escape($status).")";
   db_query($sql, "could not insert test marks");
    //display_error($sql);
//    $sqld = "UPDATE " . TB_PREF . "sms_students_details SET 
//             flag = 1 WHERE applicant_id = " . db_escape($app_id);
//    db_query($sqld, "could not update");
//    
//    $sql3 = "UPDATE " . TB_PREF . "sms_students_details SET 
//             atten = 2 WHERE applicant_id = " . db_escape($app_id);
//    db_query($sql3, "could not update");
    
    $sqld = "UPDATE " . TB_PREF . "sms_students_details SET flag = 1,
             atten= 2 WHERE applicant_id = " . db_escape($app_id) ;
 //display_error($sqld);
    
   db_query($sqld, "could not update");
}

//------------------------------------------------------------


function delete_selected_marks($id) {


    $sql = "DELETE from  " . TB_PREF . "sms_admission_marks 
            WHERE id = " . db_escape($id);

    return db_query($sql, "Cant delete selected record.");
}

//----------------------------------------------------------------------

function get_sql_for_list_view($class,$applicant, $datasearch) {
    
    $sql = "SELECT 
        sd.applicant_id, 
        sd.photo_upload,
        CONCAT(sd.first_name, ' ',sd.middle_name, ' ', sd.last_name)as name,
        sd.mobile,
        sd.per_address
        
        FROM " . TB_PREF . "sms_students_details sd where sd.id";
    
          if($class){
               $sql .= " AND sd.class=".db_escape($class);
           }
          
           
        if($datasearch){
            $sql .= " AND sd.$datasearch LIKE '%$applicant%'";
        } 
       
    
    return $sql;
}

//----------------------------------------------------------------------

function get_sql_for_mark_entry() {
   
    $sql = "SELECT
        applicant_id,
        CONCAT(first_name,' ',middle_name,' ',last_name) AS name,
        id
        FROM " . TB_PREF . "sms_students_details WHERE class=" . db_escape($_POST['app_class']). "AND atten= 1";
    
    //display_error($sql);

    return $sql;
    
    
}

//----------------------------------------------------------------------

function get_sql_student_wise_class() {
   
    $sql = "SELECT
        st.student_id,
        CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) AS name,
        sc.class_name
        FROM " . TB_PREF . "sms_student st
        LEFT JOIN " . TB_PREF . "sms_students_details sd ON st.applicant_id  = sd.applicant_id
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON st.st_class = sc.id
        WHERE st.st_class=" . db_escape($_POST['app_class'])."AND st.status = 1";
    
    return $sql;
}

//----------------------------------------------------------------------

function get_sql_for_test_result_update($app_id,$am_res) {
    
    $sql = "UPDATE " . TB_PREF . "sms_test_result SET 
             result = " . db_escape($am_res) .
            " WHERE applicant_id = " . db_escape($app_id);
    //display_error($sql);

    db_query($sql, "Cannot update test subject");
}

//----------------------------------------------------------------------

function get_sql_short_list_data() {
  
    $sql = "SELECT
        sd.applicant_id,CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name,
        sam.obtain_marks,
        sd.id
        FROM " . TB_PREF . "sms_students_details sd
        LEFT JOIN " . TB_PREF . "sms_admission_marks sam ON sd.applicant_id = sam.applicant_id
        LEFT JOIN " . TB_PREF . "sms_test_result tr ON sd.applicant_id = tr.applicant_id
        WHERE sd.class=" . db_escape($_POST['app_class'])."
        AND sd.flag = 1 AND tr.applicant_id IS NULL
        AND (sam.obtain_marks>=". db_escape($_POST['start_mark'])." OR sam.obtain_marks<=". db_escape($_POST['end_mark']).")";
    
    //display_error($sql);

    return $sql;
}

//----------------------------------------------------------------------

function get_sql_short_list_data_st() {
    
    $sqlr = "SELECT required_seat FROM " . TB_PREF . "sms_seat_required WHERE st_class = ".db_escape($_POST['app_class']);
    
    $res = db_fetch(db_query($sqlr, 'Could not get required_seat.'));
   
    if($res['required_seat'] == ''){
        return 1;
    }
    else{
    $sql = "SELECT
        sd.applicant_id,sd.first_name,
        sam.obtain_marks,
        sd.id
        FROM " . TB_PREF . "sms_students_details sd
        LEFT JOIN " . TB_PREF . "sms_admission_marks sam ON sd.applicant_id = sam.applicant_id
        LEFT JOIN " . TB_PREF . "sms_test_result tr ON sd.applicant_id = tr.applicant_id
        WHERE sd.class=" . db_escape($_POST['app_class']). " AND sd.flag = 1 AND tr.applicant_id IS NULL
        ORDER BY sam.obtain_marks DESC
        LIMIT 0 ,". $res['required_seat'];
    //display_error($sql);
    return $sql;
    }
}

//----------------------------------------------------------------------

//----------------------------------------------------------------
function get_sql_for_exam_schedule() {

    $sql = "SELECT * FROM " . TB_PREF . "sms_exam_schedule WHERE close= '1'";
    
    $res = db_fetch(db_query($sql, 'Could not get schedule data.'));
    
    return $res;
    
}
//--------------------------------------------------------

function student_registration($appid,$stid,$admitted_class,$admission_date,$admission_fees,$fees_discount,$payment_method,$admission_note) {
    
    $addate = date2sql($admission_date);
    
    $requ = (($admission_fees * $fees_discount)/100);
    $restamnt = ($admission_fees - $requ);
    
    $sql = "INSERT INTO " . TB_PREF . "sms_admission_fees (applicant_id,student_id,admission_date,admission_fess,fees_discount,payment_method,note,status)
        values (" . db_escape($appid) . "," . db_escape($stid) . "," . db_escape($addate) . "," . db_escape($admission_fees) . "," . db_escape($restamnt) . "," . db_escape($payment_method) . "," . db_escape($admission_note) . ",1)";
    
    db_query($sql, "could not insert into admission fees");
    
    $sqlpm = "INSERT INTO " . TB_PREF . "sms_student (applicant_id, student_id, st_class, admission_date, status)
        values (" . db_escape($appid) . "," . db_escape($stid) . "," . db_escape($admitted_class) . "," . db_escape($addate) . ",1)";
    
    db_query($sqlpm, "could not insert into student table");

}

//-----------------------------------------------------------------------
function get_holy__data() {
   
    $sql = "SELECT
        he.name,
        sc.class_name,
        ss.session_name,
        sb.subject_name,
        ta.id
        FROM " . TB_PREF . "sms_teacher_allocation ta
        LEFT JOIN " . TB_PREF . "hcm_emp he ON ta.teacher = he.emp_code
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON ta.class = sc.id
        LEFT JOIN " . TB_PREF . "sms_session ss ON ta.section = ss.id
        LEFT JOIN " . TB_PREF . "sms_subject sb ON ta.subject = sb.id";
        
    
    return $sql;
}

//.....--------------------------------------------------------



