<?php

function add_applicant($syear,$NewProID, $f_name, $m_name, $l_name, $class_id, $group_id, $dob, 
        $gender, $blood_group, $pre_address, $per_address, $city, $country,$siblings, 
        $birth_place,$certificate_no, $phone, $mobile, $email, $nationality, $mother_tongue,
        $religion, $hobby, $exca, $pic, $frm_price, $discount,$studentcat,$user) {
    $dateob = date2sql($dob);
    
    $sql = "INSERT INTO " . TB_PREF . "sms_students_details (`school_year`,`applicant_id`, `first_name`, `middle_name`, `last_name`, `class`, `group`, `dob`, `gender`, `blood_group`, `pre_address`, `per_address`, `city`, `country`,`siblings`, `birth_place`,`certificate_no`, `phone`, `mobile`, `email`, `nationality`, `mother_tongue`, `religion`, `hobby`, `extra_curriculum`, `photo_upload`) 
                VALUES (" . db_escape($syear) . "," . db_escape($NewProID) . "," . db_escape($f_name) . ","
            . db_escape($m_name) . ", " . db_escape($l_name) . ", "
            . db_escape($class_id) . ", " . db_escape($group_id) . ","
            . db_escape($dateob) . ", " . db_escape($gender) . ", "
            . db_escape($blood_group) . ", " . db_escape($pre_address) . ","
            . db_escape($per_address) . ", " . db_escape($city) . ", "
            . db_escape($country) . ", ". db_escape($siblings) . ", " . db_escape($birth_place) . ","
            . db_escape($certificate_no) . "," . db_escape($phone) . ", "
            . db_escape($mobile) . ", ". db_escape($email) . ", "
            . db_escape($nationality) . ", " . db_escape($mother_tongue) . ", "
            . db_escape($religion) . ", ". db_escape($hobby) . ", "
            . db_escape($exca) . ",". db_escape($pic). ")";
//       display_error($sql);
    
    db_query($sql, "The Applicant information could not be added");
 
    
    //for application form payment
    $requ = (($frm_price * $discount)/100);
    $restamnt = ($frm_price - $requ);
    $td=date2sql(Today());
    
    $sqlp = "INSERT INTO " . TB_PREF . "sms_app_payment (school_year,class,applicant_id,form_price,discount,rest_amnt,sale_date,receive_by)
        values (" . db_escape($syear) . "," . db_escape($class_id) . "," . db_escape($NewProID) . "," . db_escape($frm_price) . "," . db_escape($dicount) . "," . db_escape($restamnt) . "," . db_escape($td) . "," . db_escape($user) . ")";
   
    db_query($sqlp, "could not insert into app payment table");
    
    //for scholarship payment
    
    $sqls = "INSERT INTO " . TB_PREF . "sms_student_scholarship (school_year,class,applicant_id,student_cat)
        values (" . db_escape($syear) . "," . db_escape($class_id) . "," . db_escape($NewProID) . "," . db_escape($studentcat) . ")";
   
    db_query($sqls, "could not insert into app payment table");
}
//............search.........../

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
//admission
function get_app_data($applicant_id, $tbl_name, $wh) {

    $sql = "SELECT  * FROM " . TB_PREF . "$tbl_name
            WHERE $wh=" . db_escape($applicant_id);
    //display_error($sql);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}

// ---------------------for class------------------------------
function add_student_class($name, $school_set) {
    $sql = "INSERT INTO " . TB_PREF . "sms_create_stud_class (class_name, school_set) values (" . db_escape($name) . "," . db_escape($school_set) . ")";
    db_query($sql, "could not insert into class table");
}

function update_student_class($id, $name, $school_set) {

    $sql = "UPDATE " . TB_PREF . "sms_create_stud_class SET "
            . "class_name = " . db_escape($name)
            . "," . "school_set = " . db_escape($school_set)
            . "WHERE id = " . db_escape($id);

    db_query($sql, "Class could not be updated");
}
function srch_create_class(){
     $sql = "SELECT class_name,total_student,school_set,hierarchy,id FROM ".TB_PREF."sms_create_stud_class"; 
  if($_POST['class']!=''){
      $sql .=  " WHERE id=".db_escape($_POST['class']);
    }
    //display_error($_POST['class']);
    return $sql;
     //
};

// used---------------------for------------------------------
function delete_selected_class($id) {

    
    $sql = "DELETE from  " . TB_PREF . "sms_create_stud_class 
            WHERE id = " . db_escape($id);

    return db_query($sql, "Cant delete estudent class.");
}

//------------------------------------------------------------------------

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


//----------------------------------------------------------------
function getClasswiseStudent() {

    $sql = "Select applicant_id, first_name from " . TB_PREF . "sms_students_details";

    return $sql;
}

//------------------------test marks------------------------------------
//used
function add_test_marks($app_id,$marks,$status) {

    $sql = "INSERT INTO " . TB_PREF . "sms_admission_marks (applicant_id,obtain_marks,status) values (".db_escape($app_id)."," .db_escape($marks) . "," . db_escape($status).")";
   db_query($sql, "could not insert test marks");
    
    $sqld = "UPDATE " . TB_PREF . "sms_students_details SET flag = 1,
             atten= 2 WHERE applicant_id = " . db_escape($app_id) ;
 //display_error($sqld);
    
   db_query($sqld, "could not update");
}

//admission------------------------------------------------------------

function process_short_list($app_id, $pass) {

    $sql = "INSERT INTO " . TB_PREF . "sms_test_result (applicant_id, result, status) values (".db_escape($app_id).",".db_escape($pass).",'0')";
    db_query($sql, "could not insert test marks"); 
}

//------------------------test marks------------------------------------

function approve_short_list($app_id,$user,$time) {
    
    $sql = "UPDATE " . TB_PREF . "sms_test_result SET 
             status = 3 , user=" . db_escape($user) . ", time=" . db_escape($time) .
            "WHERE applicant_id = " . db_escape($app_id);
    db_query($sql, "could not update");
}

function delete_selected_marks($id) {


    $sql = "DELETE from  " . TB_PREF . "sms_admission_marks 
            WHERE id = " . db_escape($id);

    return db_query($sql, "Cant delete selected record.");
}

//----------------------------------------------------------------------
//used
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

function get_sql_for_test_result_edit() {

    $sql = "SELECT
        '',
        sd.applicant_id,
        CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) AS name,
        m.obtain_marks,
        tr.result,
        tr.id
        FROM " . TB_PREF . "sms_test_result tr
        LEFT JOIN " . TB_PREF . "sms_students_details sd ON tr.applicant_id  = sd.applicant_id
        LEFT JOIN " . TB_PREF . "sms_admission_marks m ON tr.applicant_id = m.applicant_id
        WHERE tr.status = 0";
//display_error($sql);
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

function get_sql_for_stform_view($applicant_id) {
  
    $sql = "SELECT 
        CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) AS name,
        sd.dob,sd.gender,sd.photo_upload,sd.pre_address,sd.per_address,sd.birth_place, 
        sd.city as ct,con.country_name as coun,sd.phone,sd.mobile as mob,sd.email as mail,na.nationality,sd.mother_tongue,sd.hobby,
        sd.extra_curriculum,sd.siblings,sd.certificate_no, 
        sc.class_name,
        sp.father_name, sp.mother_name,sp.f_occupation,sp.m_occupation,sp.email,
        sp.city,sp.mobile,sp.alternate_phone,sp.f_education ,sp.m_education,
        ed.institute_name,edc.class_name as edclass,ed.year,ed.grade,ed.cgpa,ed.out_of,ed.folupdate,ed.sms_alert,ed.email_notification,
        sc.class_name,
        ss.applicant_id,
        ss.student_cat,
        ssc.cat_name,
        sg.group_name,
        rel.description as religion
        FROM " . TB_PREF . "sms_students_details sd
        LEFT JOIN " . TB_PREF . "sms_stud_parent_details sp ON sd.applicant_id = sp.applicant_id
        LEFT JOIN " . TB_PREF . "sms_student_scholarship ss ON sd.applicant_id = ss.applicant_id
        LEFT JOIN " . TB_PREF . "sms_student_category ssc ON ss.student_cat = ssc.id
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON sd.class = sc.id
        LEFT JOIN " . TB_PREF . "sms_applicant_group sg ON sd.group = sg.id
        LEFT JOIN " . TB_PREF . "sms_stud_edu_details ed ON sd.applicant_id = ed.applicant_id
        LEFT JOIN " . TB_PREF . "sms_create_stud_class edc ON ed.class = edc.id
        LEFT JOIN " . TB_PREF . "nationality na ON sd.nationality = na.id
        LEFT JOIN " . TB_PREF . "religion rel ON sd.religion = rel.id
        LEFT JOIN " . TB_PREF . "country con ON con.id = sd.country
        WHERE sd.applicant_id =" . db_escape($applicant_id);
   
   
   $res = db_fetch(db_query($sql, 'Could not get student data.'));
  
    return $res;
}

//----------------------------------------------------------------
function get_sql_for_exam_schedule() {

    $sql = "SELECT * FROM " . TB_PREF . "sms_exam_schedule WHERE close= '1'";
    
    $res = db_fetch(db_query($sql, 'Could not get schedule data.'));
    
    return $res;
    
}

//------------------------------------------------------------------------

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



function get_sql_for_payment_view($applicant_id)
{
    
    $sql= " SELECT CONCAT( sd.first_name, ' ', sd.middle_name, ' ', sd.last_name ) AS name, sd.photo_upload, 
            sp.father_name, sp.mother_name, 
            cs.class_name,
            ag.group_name
            FROM 0_sms_students_details sd
            LEFT JOIN 0_sms_stud_parent_details sp ON sp.applicant_id = sd.applicant_id
            LEFT JOIN 0_sms_create_stud_class cs ON cs.id = sd.class
            LEFT JOIN 0_sms_applicant_group ag ON ag.id = sd.group
            WHERE sd.student_id = " .  db_escape($applicant_id);
    
    return $sql;
}

function get_sql_for_applicant_attendance($cls,$atten)
{
    $sql = "SELECT applicant_id,id FROM " . TB_PREF . "sms_students_details WHERE class=" .  db_escape($cls) ." AND atten = '0'";
    
    return $sql;
    
}


//----------------------------------------------------------------

function add_section($class,$section)
{
    $sql= "INSERT INTO " . TB_PREF . "sms_session(class,session_name) values(" . db_escape($class) . "," .db_escape($section) . ")";
    
    db_query($sql, "added successfully");
    
}

function update_section($id,$class,$section)
{
    $sql= "UPDATE " . TB_PREF . "sms_session SET
        class=" . db_escape($class) . "," . 
            "session_name= " . db_escape($section) . 
            " where id= " .  db_escape($id);
    
    db_query($sql, "update complete");
            
    
}

function delete_section($id)
{
    $sql= "DELETE FROM " . TB_PREF . "sms_session WHERE id=" .db_escape($id);
    
    db_query($sql, 'cannot delete');
    
}

//----------------------------------------------------------------------
//used
function get_sql_for_test_final_result($class,$applicant, $datasearch) {
    
    $sql = "SELECT 
        tr.applicant_id,
        sd.student_id,
        CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name,
        sc.class_name,
        sd.mobile,
        ad.obtain_marks,
        tr.result,
        tr.flag,
        sc.id,
        sd.school_year
        FROM " . TB_PREF . "sms_test_result tr
        LEFT JOIN " . TB_PREF . "sms_students_details sd ON tr.applicant_id = sd.applicant_id
        LEFT JOIN " . TB_PREF . "sms_admission_marks ad ON tr.applicant_id = ad.applicant_id
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON sd.class = sc.id  where tr.id  AND sd.class=" .db_escape($class); 
   //display_error($sql);
  
           
        if($datasearch){
            $sql .= " AND sd.$datasearch LIKE '%$applicant%'";
        } 
 
return $sql;
}
//----------------
function view_group(){
    $sql = "SELECT id, group_name, description
            FROM " .TB_PREF."sms_applicant_group";
    
    return $sql;
    
}


function add_group($group_name,$group_description){
    
    $sql = "INSERT INTO " . TB_PREF . "sms_applicant_group(group_name,description) values (" . db_escape($group_name) . ",
        " . db_escape($group_description).")";

    db_query($sql, "could not insert into table");
}


function update_group($id,$group_name,$group_description){
        $sql = "UPDATE " . TB_PREF . "sms_applicant_group SET "
            . "group_name = " . db_escape($group_name)
            . " , " . "description = " . db_escape($group_description)
            . " WHERE id = " . db_escape($id);
  db_query($sql, "data  not be updated");
    
}
function delete_group($id){
    $sql= "delete from " . TB_PREF . "sms_applicant_group
        where id= " .db_escape($id);
    db_query($sql, "cannot delete");
}
//---------------------------category---------//
function view_category(){
    $sql = "SELECT @num:=@num+1, cat_name, cat_description, ratio,id 
            FROM (SELECT @num:=0) as n, ".TB_PREF."sms_student_category";
    return ($sql);
    
}

function add_category($cat_name,$cat_description,$ratio){
    
    $sql = "INSERT INTO " . TB_PREF . "sms_student_category (cat_name, cat_description, ratio) values (" . db_escape($cat_name) . ",
        " . db_escape($cat_description) .",". db_escape($ratio) .")";

    db_query($sql, "could not insert into table");
}

function update_category($id,$cat_name,$cat_description,$ratio){
        $sql = "UPDATE " . TB_PREF . "sms_student_category SET "
            . "cat_name = " . db_escape($cat_name)
            . " , " . "cat_description = " . db_escape($cat_description)
            . " , " . "ratio = " . db_escape($ratio)
            . " WHERE id = " . db_escape($id);
  db_query($sql, "Artical  not be updated");
    
}
function dynamic_delete($table,$id){
    $sql= "DELETE FROM " . TB_PREF . "$table
        where id= " .db_escape($id);

    db_query($sql, "cannot delete");
}
//-------------
//-----------------------------------------------------------------------------------------
function update_price_settings($id, $syear, $hname ) {
 
    $sql = "UPDATE " . TB_PREF . "sms_form_price_setting SET
          school_year=" . db_escape($syear) . ", price=" . db_escape($hname) .
            " WHERE id=" . db_escape($id);
    //display_error($sql);
    db_query($sql, 'update successful');
}


function add_price_settings($syear, $price) {

    $sql = "INSERT INTO " . TB_PREF . "sms_form_price_setting(school_year,price) values (" . db_escape($syear) . "," . db_escape($price) . ")";
//    display_error($sql);
    db_query($sql, 'insert failed');
}

function sms_price_setting() {
    $sql = "SELECT * FROM " . TB_PREF . "sms_form_price_setting";
    return $sql;
}

function delete_price($id){
    $sql= "delete from " . TB_PREF . "sms_form_price_setting where id= " .db_escape($id);
    db_query($sql, "cannot delete");
}
//for spot admission
function get_sql_admission_drirect_list() {
  
    $sql = "SELECT
        sd.applicant_id,CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name,
        sam.obtain_marks,
        sd.id
        FROM " . TB_PREF . "sms_students_details sd
        LEFT JOIN " . TB_PREF . "sms_admission_marks sam ON sd.applicant_id = sam.applicant_id
        LEFT JOIN " . TB_PREF . "sms_test_result tr ON sd.applicant_id = tr.applicant_id
    
        WHERE sd.class=" . db_escape($_POST['class'])."
        AND sd.flag = 0 AND tr.applicant_id IS NULL";
   
    return $sql;
}

function process_sport_short_list($app_id, $pass) {

    $sql = "INSERT INTO " . TB_PREF . "sms_test_result (applicant_id, result, status) values (".db_escape($app_id).",".db_escape($pass).",'0')";
    db_query($sql, "could not insert test marks");
    
    $sqlu = "UPDATE " . TB_PREF . "sms_students_details SET 
             flag  = 1 , atten = 1 WHERE applicant_id = " . db_escape($app_id);
    db_query($sqlu, "could not update");
    
    //display_error($sql);
}
