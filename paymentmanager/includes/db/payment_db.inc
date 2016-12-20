<?php
include_once($path_to_root . "/paymentmanager/setup/setting.php");

function add_head_settings($syear,$hname, $payment,$stype,$class,$amount,$due_date)
{
    $sql= "INSERT INTO " . TB_PREF . "sms_payment_head_setting(school_year,head_name,no_of_payment,student_type,st_class,amount,due_date) values (" . db_escape($syear) . "," . db_escape($hname) . "," . db_escape($payment) . "," . db_escape($stype) . "," . db_escape($class) . "," . db_escape($amount) . "," . db_escape($due_date) . ")";
    db_query($sql,'insert failed');
}

function update_head_settings($id,$syear,$hname,$payment,$stype,$class,$amount,$due_date)
{
    $sql= "UPDATE " . TB_PREF . "sms_payment_head_setting SET
          school_year=" . db_escape($syear) . ", head_name=" . db_escape($hname) . ", no_of_payment=" . db_escape($payment) . ",student_type=" .db_escape($stype) . ", st_class=" .db_escape($class) . ", amount=" .db_escape($amount) .", due_date=" .db_escape($due_date).
              " WHERE id=" . db_escape($id);
    
    db_query($sql,'update successful');
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

function add_school_student_payment(&$print_obj, $studentroll) {
    begin_transaction();
    /* Insert to print_requisition_master  record */
    $result = get_student_data($print_obj->app_id, 'sms_students_details', 'applicant_id');
    $studentid = date(Y) . '-' . substr($result['class_name'], 6) . '-' . substr(time(), 6);

    $con = array('class' => $result['class']);
    $field = array('id');
    $sec = db_fetch(data_retrieve_condition('sms_session', $field, $con));

    $receivedby = $_SESSION['wa_current_user']->username;

    $sql = "INSERT INTO " . TB_PREF . "sms_transaction_details (school_year,applicant_id,student_id,trans_date,payment_method,cheque_no, 
     	cheque_date,bank_name,branch_name,total_amount,comments,received_by,flag) VALUES(";
    $sql .= db_escape($print_obj->syear) . "," .
            db_escape($print_obj->app_id) . ", " .
            db_escape($studentid) . ", '" .
            date2sql($print_obj->add_dt) . "', " .
            db_escape($print_obj->payment_method) . "," .
            db_escape($print_obj->cheque_no) . ", '" .
            date2sql($print_obj->cheque_date) . "', " .
            db_escape($print_obj->bank_name) . "," .
            db_escape($print_obj->branch_name) . "," .
            db_escape($tamount) . "," .
            db_escape($print_obj->comments) . "," .
            db_escape($receivedby) . "," .
            1 . ")";
    db_query($sql, "The transaction details could not be inserted");

    $last_id = db_insert_id();
    $prtn = "trid=".$last_id."&stid=".$studentid;
     //display_error($prtn);
    /* Insert the print_requisition_details records*/
    foreach ($print_obj->line_items as $line_no => $print_line) {
        
        if($print_line->disstatus=='1'){
                 $dishw = ($print_line->amount * $print_line->ratio)/100;
                 $totalamnt = ($print_line->amount - $dishw);
                 $distotal += $dishw;
         }else{
                  $dishw = 0;
                  $totalamnt = $print_line->amount; 
                  
        }
        
        $sql1 = "INSERT INTO " . TB_PREF . "sms_transaction (trand_id, head_id, amount) VALUES (";
        $sql1 .= db_escape($last_id) . ", " .
                db_escape($print_line->id) . "," .
                db_escape($totalamnt) . ")";
        
        db_query($sql1, "One of the sms transaction could not be inserted");

        $sql2 = "UPDATE " . TB_PREF . "sms_tbl_receivable SET student_id =" . db_escape($studentid) . ", payment_date =" . db_escape(date2sql(Today())) . ", discount=" . db_escape($dishw) . ",realize =" . db_escape($totalamnt) . ",  flag = 1
                WHERE id= " . db_escape($print_line->id);
        
        db_query($sql2, "could not update sms tbl receivable");

        $tamount += $totalamnt;
        $sbtotal += $print_line->amount;
        
    }
    $amnttotal = ($tamount - $print_obj->spdiscount);
    
    $sql3 = "UPDATE " . TB_PREF . "sms_transaction_details SET 
             discount_total =" . db_escape($distotal) .", sp_discount =" . db_escape($print_obj->spdiscount) .", total_amount =" . db_escape($amnttotal) .
            " WHERE id= " . db_escape($last_id);
//    display_error($sbtotal);
    db_query($sql3, "could not update sms_transaction_details");
    
    $gldata = db_fetch(db_query("SELECT * FROM ".TB_PREF."sms_student_gl_setup"));
    
    $sqlpm = "INSERT INTO " . TB_PREF . "sms_student (school_year, applicant_id, student_id, roll_number, st_class, st_section, admission_date, status, sales_account, sales_discount_account, receivables_account, payment_discount_account)
        values (" . db_escape($print_obj->syear) . "," . db_escape($print_obj->app_id) . "," . db_escape($studentid) . "," . db_escape($studentroll) . "," . db_escape($result['class']) . ", " . db_escape($print_obj->psection) . "," . db_escape(date2sql($print_obj->add_dt)) . ",1," . db_escape($gldata['sales_account']) . ", " . db_escape($gldata['sales_discount_account']) . "," . db_escape($gldata['receivables_account']) . ", " . db_escape($gldata['payment_discount_account']).")";
    
    db_query($sqlpm, "could not insert into student table");
    
    //For entry in gl accounts
    $glsttbl = db_fetch(db_query("SELECT sales_account, sales_discount_account, receivables_account, payment_discount_account FROM ".TB_PREF."sms_student WHERE student_id=".db_escape($studentid)));
    $type_no = get_gl_type_no(ST_APPPAYMENT);
    $reference = get_gl_type_no(ST_APPPAYMENT);
    global $Refs;        
           //For slae purpose  [
           $glsqltotalcr = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_account']) . "," . db_escape('applicant payment credit amount') . ", " . db_escape(-$sbtotal) . ")";
//            display_error($glsqltotalcr);
           db_query($glsqltotalcr, "could not update");
            
            add_audit_trail(ST_APPPAYMENT, $type_no, Today());
            add_comments(ST_APPPAYMENT, $type_no, Today(), $studentid);
            
           //..........
           if($distotal!=''){
           
           $glsqldistotal = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_discount_account']) . "," . db_escape('applicant discount total debit amount') . ", " . db_escape($distotal) . ")";
//           display_error($glsqldistotal);
           db_query($glsqldistotal, "could not update");
           
           add_audit_trail(ST_APPPAYMENT, $type_no, Today());
           add_comments(ST_APPPAYMENT, $type_no, Today(), $studentid);
           
           }
           //..........
           
           if($print_obj->spdiscount!=''){
           
           $glsqlspdiscount = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_discount_account']) . "," . db_escape('applicant special discount debit amount') . ", " . db_escape($print_obj->spdiscount) . ")";
//          display_error($glsqlspdiscount);
           db_query($glsqlspdiscount, "could not update");
          
            add_audit_trail(ST_APPPAYMENT, $type_no, Today());
            add_comments(ST_APPPAYMENT, $type_no, Today(), $studentid);
           //..........
           }
           
           $glsqltotaldr = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
            values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['receivables_account']) . "," . db_escape('applicant total payment receivables debit amount') . ", " . db_escape($amnttotal) . ")";
//           display_error($glsqltotaldr);
           db_query($glsqltotaldr, "could not update");
           
            add_audit_trail(ST_APPPAYMENT, $type_no, Today());
            add_comments(ST_APPPAYMENT, $type_no, Today(), $studentid);
           //For slae purpose ]
           
           //For Payment Receive purpose [
           $type_no = $type_no+1;
           $glaccreccr = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
            values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['receivables_account']) . "," . db_escape('applicant total account receivables credit amount') . ", " . db_escape(-$amnttotal) . ")";
//           display_error($glaccreccr);
           db_query($glaccreccr, "could not update");
           
           add_audit_trail(ST_APPPAYMENT, $type_no, Today());
           add_comments(ST_APPPAYMENT, $type_no, Today(), $studentid); 
           
           if($print_obj->payment_method==1){
           $from_account = $print_obj->bank_account;
           $bank_gl_account = get_bank_gl_account($from_account);
           }elseif($print_obj->payment_method==2){
           $from_account = $print_obj->bank_name;
           $bank_gl_account = get_bank_gl_account($from_account);
           }
           
           $sqltc = "INSERT INTO ".TB_PREF."gl_trans_cheque(type, type_no, tran_date,account,bank_act,amount,cheque_no,cheque_date,reference,memo_)VALUES (".db_escape(ST_APPPAYMENT).", ".db_escape($type_no).", " . db_escape(date2sql(Today())) . ",".db_escape($bank_gl_account).",".db_escape($from_account).",".db_escape($amnttotal).",".db_escape($print_obj->cheque_no).",".db_escape($print_obj->cheque_date).",
            ".db_escape($reference).",".db_escape('applicant payment bank/cash debit amount').")";
//           display_error($sqltc);
           db_query($sqltc,"The GL could not be inserted");
                      
           $glbankdr = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_account']) . "," . db_escape('applicant payment debit amount') . ", " . db_escape($amnttotal) . ")";
//            display_error($glbankdr);
           db_query($glbankdr, "could not update");
           
            add_audit_trail(ST_APPPAYMENT, $type_no, Today());
            add_comments(ST_APPPAYMENT, $type_no, Today(), $studentid);
           //end For Payment Receive purpose ]
            
            $id = get_gl_ref_no(ST_APPPAYMENT);
            $Refs->save(ST_APPPAYMENT, $id, $reference);     
    //end gl entry      
           
    $sqlu = "UPDATE " . TB_PREF . "sms_test_result SET 
             flag = 2 WHERE applicant_id = " . db_escape($print_obj->app_id);
    db_query($sqlu, "could not update");
    
    //to update student details
    $sqstdetails = "UPDATE " . TB_PREF . "sms_students_details SET 
             student_id = " . db_escape($studentid) . " WHERE applicant_id = " . db_escape($print_obj->app_id);
    db_query($sqstdetails, "could not update");
    
    //to update sms_student_scholarship
    $sqlforscholarship = "UPDATE " . TB_PREF . "sms_student_scholarship SET 
             student_id = " . db_escape($studentid) . " WHERE applicant_id = " . db_escape($print_obj->app_id);
    db_query($sqlforscholarship, "could not update");
    
     $sql_user = "SELECT * from " . TB_PREF . "users where user_id=" . db_escape($studentid);
     $user = db_query($sql_user, "Can't make query in hcm table.");
     $numbr = db_num_rows($user);

    if ($numbr == 0){
        add_user($studentid, $result['f_name'], md5(12345), $result['mobile_no'], $result['email'], 56, 'C', '', '', '');
    }else{
        display_notification("User Already Exits");
    }
    
    commit_transaction();
    
    return $prtn;
}

function add_promotion_student_payment(&$print_obj, $user) {
    begin_transaction();

    $tamount = 0;
    $receivedby = $user;
    $newsyear = get_next_schoolyear();
//
    $sql = "INSERT INTO " . TB_PREF . "sms_transaction_details (school_year,student_id,trans_date,payment_method,cheque_no, 
     	cheque_date,bank_name,branch_name,total_amount,comments,received_by,flag) VALUES(";
    $sql .= db_escape($print_obj->syear) . "," .
            db_escape($print_obj->stid) . ", '" .
            date2sql($print_obj->add_dt) . "', " .
            db_escape($print_obj->payment_method) . "," .
            db_escape($print_obj->cheque_no) . ", '" .
            date2sql($print_obj->cheque_date) . "', " .
            db_escape($print_obj->bank_name) . "," .
            db_escape($print_obj->branch_name) . "," .
            db_escape($tamount) . "," .
            db_escape($print_obj->comments) . "," .
            db_escape($receivedby) . "," .
            1 . ")";

    db_query($sql, "The transaction details could not be inserted");
//    display_error($sql); 
    $last_id = db_insert_id();
    $prtn = "trid=".$last_id."&stid=".$print_obj->stid;
////    /* Insert the print_requisition_details records */
    foreach ($print_obj->line_items as $line_no => $print_line) {
        
        if($print_line->disstatus=='1'){
                 $dishw = ($print_line->amount * $print_line->ratio)/100;
                 $totalamnt = ($print_line->amount - $dishw);
                 $distotal += $dishw;
         }else{
                  $dishw = 0;
                  $totalamnt = $print_line->amount; 
                  
        }
        
        $sql1 = "INSERT INTO " . TB_PREF . "sms_transaction (trand_id, head_id, amount) VALUES (";
        $sql1 .= db_escape($last_id) . ", " .
                 db_escape($print_line->id) . "," .
                 db_escape($totalamnt) . ")";
//       display_error($sql1);
        db_query($sql1, "One of the sms transaction could not be inserted");

        $sql2 = "UPDATE " . TB_PREF . "sms_tbl_receivable SET student_id =" . db_escape($print_obj->stid) . ", discount=" . db_escape($dishw) . ",realize =" . db_escape($totalamnt) . ",  flag = 1
                WHERE id= " . db_escape($print_line->id);

        db_query($sql2, "could not update sms tbl receivable");
//        display_error($sql2);
        $tamount += $totalamnt;
    }
    $amnttotal = ($tamount - $print_obj->spdiscount);
//    
    $sql3 = "UPDATE " . TB_PREF . "sms_transaction_details SET 
             discount_total =" . db_escape($distotal) .", sp_discount =" . db_escape($print_obj->spdiscount) .", total_amount =" . db_escape($amnttotal) .
            " WHERE id= " . db_escape($last_id);
//  display_error($sql3);
    db_query($sql3, "could not update sms_transaction_details");
    
    $gldata = db_fetch(db_query("SELECT * FROM ".TB_PREF."sms_student_gl_setup"));
   
    $sqlpm = "INSERT INTO " . TB_PREF . "sms_student (school_year, student_id, roll_number, st_class, st_section, admission_date, status, sales_account, sales_discount_account, receivables_account, payment_discount_account)
        values (" . db_escape($newsyear) . "," . db_escape($print_obj->stid) . "," . db_escape($print_obj->rollnumber) . "," . db_escape($print_obj->pclass) . ", " . db_escape($print_obj->psection) . "," . db_escape(date2sql($print_obj->add_dt)) . ",1," . db_escape($gldata['sales_account']) . ", " . db_escape($gldata['sales_discount_account']) . "," . db_escape($gldata['receivables_account']) . ", " . db_escape($gldata['payment_discount_account']).")";
    
    db_query($sqlpm, "could not insert into student table");
    
    //For entry in gl accounts
    $glsttbl = db_fetch(db_query("SELECT sales_account, sales_discount_account, receivables_account, payment_discount_account FROM ".TB_PREF."sms_student WHERE student_id=".db_escape($print_obj->stid)));
    $type_no = get_gl_type_no(ST_APPPAYMENT);
    
    foreach ($print_obj->line_items as $line_no => $print_line) {
       
        if($print_line->disstatus=='1'){
                 $discount = ($print_line->amount * $print_line->ratio)/100;
                 $receivables_account = ($print_line->amount - $discount);
                 $discounttotal += $discount;

           $glsqlamntd = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['receivables_account']) . "," . db_escape('applicant payment receivables debit amount') . ", " . db_escape($receivables_account) . ")";
//           display_error($glsqlamntd);
           db_query($glsqlamntd, "could not update");
           
           $type_no = $type_no+1;
           $glsqldis = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_discount_account']) . "," . db_escape('applicant discount payment debit amount') . ", " . db_escape($discount) . ")";
//           display_error($glsqldis);
           db_query($glsqldis, "could not update");
           
           $type_no = $type_no+1;
           $glsqlc = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_account']) . "," . db_escape('applicant payment credit amount') . ", " . db_escape(-$print_line->amount) . ")";
//           display_error($glsqlc);
           db_query($glsqlc, "could not update");

         }else{
             $glsqld = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['receivables_account']) . "," . db_escape('applicant payment debit amount') . ", " . db_escape($print_line->amount) . ")";
//             display_error($glsqld);
           db_query($glsqld, "could not update"); 
           
            $type_no = $type_no+1;
            $glsqlc = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_account']) . "," . db_escape('applicant payment credit amount') . ", " . db_escape(-$print_line->amount) . ")";
//            display_error($glsqlc);
           db_query($glsqlc, "could not update");
            
        }
       
       $type_no++;
       $alltotal += $print_line->amount;
    }
            $type_no = $type_no+1;
            $glsqltotaldr = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
            values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['receivables_account']) . "," . db_escape('applicant total payment receivables debit amount') . ", " . db_escape($amnttotal) . ")";
//            display_error($glsqltotaldr);
           db_query($glsqltotaldr, "could not update");
           
           $type_no = $type_no+1;
           $glsqldistotal = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_discount_account']) . "," . db_escape('applicant discount total debit amount') . ", " . db_escape($distotal) . ")";
//           display_error($glsqldistotal);
           db_query($glsqldistotal, "could not update");
           
           if($print_obj->spdiscount!=''){
           $type_no = $type_no+1;
           $glsqlspdiscount = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_discount_account']) . "," . db_escape('applicant special discount debit amount') . ", " . db_escape($print_obj->spdiscount) . ")";
//           display_error($glsqlspdiscount);
           db_query($glsqlspdiscount, "could not update");
           }
           
           $type_no = $type_no+1;
           $glsqltotalcr = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_account']) . "," . db_escape('applicant payment credit amount') . ", " . db_escape(-$alltotal) . ")";
//             display_error($glsqltotalcr);
           db_query($glsqltotalcr, "could not update");
//           
    //end gl entry
           
    $tablesql = "UPDATE " . TB_PREF . "sms_student SET 
             status = 0 WHERE id = " . db_escape($print_obj->sttableid);
    db_query($tablesql, "could not update");

    $sqlu = "UPDATE " . TB_PREF . "sms_student_result SET 
             flag = 2 WHERE student_id = " . db_escape($print_obj->stid);
    db_query($sqlu, "could not update");
    
    commit_transaction();
    return $prtn;
}

function student_payment_collection(&$print_obj, $user) {
    begin_transaction();
    /* Insert to print_requisition_master  record */
    $tamount = 0;

    $sql = "INSERT INTO " . TB_PREF . "sms_transaction_details (school_year,student_id,trans_date,payment_method,cheque_no, 
     	cheque_date,bank_name,branch_name,total_amount,comments,received_by,flag) VALUES(";
    $sql .= db_escape($print_obj->syear) . "," .
            db_escape($print_obj->st_id) . ", '" .
            date2sql($print_obj->payment_dt) . "', " .
            db_escape($print_obj->payment_method) . "," .
            db_escape($print_obj->cheque_no) . ", '" .
            date2sql($print_obj->cheque_date) . "', " .
            db_escape($print_obj->bank_name) . "," .
            db_escape($print_obj->branch_name) . "," .
            db_escape($tamount) . "," .
            db_escape($print_obj->comments) . "," .
            db_escape($user) . "," .
            1 . ")";
    db_query($sql, "The transaction details could not be inserted");
    //display_error($sql);     
    $last_id = db_insert_id();
    $prtn = "trid=".$last_id."&stid=".$print_obj->st_id;
    /* Insert the print_requisition_details records */
    foreach ($print_obj->line_items as $line_no => $print_line) {
        
        if($print_line->disstatus=='1'){
                 $dishw = ($print_line->amount * $print_line->ratio)/100;
                 $totalamnt = ($print_line->amount - $dishw);
                 $distotal += $dishw;
         }else{
                  $dishw = 0;
                  $totalamnt = $print_line->amount; 
                  
        }
        
        $sql1 = "INSERT INTO " . TB_PREF . "sms_transaction (trand_id, head_id, amount) VALUES (";
        $sql1 .= db_escape($last_id) . ", " .
                db_escape($print_line->tbl_id) . "," .
                db_escape($totalamnt) . ")";

        db_query($sql1, "One of the sms transactions could not be inserted");
        //display_error($sql1);
        
        $sql2 = "UPDATE " . TB_PREF . "sms_tbl_receivable SET student_id =" . db_escape($print_obj->st_id) . ", payment_date =" . db_escape(date2sql(Today())) . ", discount=" . db_escape($dishw) . ",realize =" . db_escape($totalamnt) . ",  flag = 1
                WHERE id= " . db_escape($print_line->tbl_id);
        
        db_query($sql2, "could not update sms tbl receivable");
        //display_error($sql2);
        $tamount += $totalamnt;
    }
        $amnttotal = ($tamount - $print_obj->spdiscount);
    
    $sql3 = "UPDATE " . TB_PREF . "sms_transaction_details SET 
             discount_total =" . db_escape($distotal) .", sp_discount =" . db_escape($print_obj->spdiscount) .", total_amount =" . db_escape($amnttotal) .
            " WHERE id= " . db_escape($last_id);
    //display_error($sql3);
    db_query($sql3, "could not update sms_transaction_details");
        
        //For entry in gl accounts
        $glsttbl = db_fetch(db_query("SELECT sales_account, sales_discount_account, receivables_account, payment_discount_account FROM ".TB_PREF."sms_student WHERE student_id=".db_escape($print_obj->st_id)));
        $type_no = get_gl_type_no(ST_APPPAYMENT);
        $reference = get_gl_type_no(ST_APPPAYMENT);
        global $Refs;
          
            //For slae purpose  [
           $glsqltotalcr = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_account']) . "," . db_escape('Student payment credit amount') . ", " . db_escape(-$tamount) . ")";
//            display_error($glsqltotalcr);
           db_query($glsqltotalcr, "could not update");
            
            add_audit_trail(ST_APPPAYMENT, $type_no, Today());
            add_comments(ST_APPPAYMENT, $type_no, Today(), $print_obj->st_id);
            
            //..........
           if($distotal!=''){
           
           $glsqldistotal = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_discount_account']) . "," . db_escape('Student discount total debit amount') . ", " . db_escape($distotal) . ")";
//           display_error($glsqldistotal);
           db_query($glsqldistotal, "could not update");
           
           add_audit_trail(ST_APPPAYMENT, $type_no, Today());
           add_comments(ST_APPPAYMENT, $type_no, Today(), $print_obj->st_id);
           
           }
           //..........
           
           if($print_obj->spdiscount!=''){
           
           $glsqlspdiscount = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_discount_account']) . "," . db_escape('Student special discount debit amount') . ", " . db_escape($print_obj->spdiscount) . ")";
//           display_error($glsqlspdiscount);
           db_query($glsqlspdiscount, "could not update");
          
            add_audit_trail(ST_APPPAYMENT, $type_no, Today());
            add_comments(ST_APPPAYMENT, $type_no, Today(), $print_obj->st_id);
           //..........
           }
           
           $glsqltotaldr = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
            values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['receivables_account']) . "," . db_escape('Student total payment receivables debit amount') . ", " . db_escape($amnttotal) . ")";
//           display_error($glsqltotaldr);
           db_query($glsqltotaldr, "could not update");
           
            add_audit_trail(ST_APPPAYMENT, $type_no, Today());
            add_comments(ST_APPPAYMENT, $type_no, Today(), $print_obj->st_id);
           //For slae purpose ]
           
           //For Payment Receive purpose [
           $type_no = $type_no+1;
           $glaccreccr = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
            values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['receivables_account']) . "," . db_escape('Student total account receivables credit amount') . ", " . db_escape(-$amnttotal) . ")";
//           display_error($glaccreccr);
           db_query($glaccreccr, "could not update");
           
           add_audit_trail(ST_APPPAYMENT, $type_no, Today());
           add_comments(ST_APPPAYMENT, $type_no, Today(), $print_obj->st_id); 
                      
           $glbankdr = "INSERT INTO " . TB_PREF . "gl_trans (type, type_no, tran_date, account, memo_, amount) 
    values (" . db_escape(ST_APPPAYMENT) . "," . db_escape($type_no) . "," . db_escape(date2sql(Today())) . "," . db_escape($glsttbl['sales_account']) . "," . db_escape('Student payment debit amount') . ", " . db_escape($amnttotal) . ")";
//            display_error($glbankdr);
           db_query($glbankdr, "could not update");
           
            add_audit_trail(ST_APPPAYMENT, $type_no, Today());
            add_comments(ST_APPPAYMENT, $type_no, Today(), $print_obj->st_id);
           //end For Payment Receive purpose ]
            
           if($print_obj->payment_method==1){
           $from_account = $print_obj->bank_account;
           $bank_gl_account = get_bank_gl_account($from_account);
           }elseif($print_obj->payment_method==2){
           $from_account = $print_obj->bank_name;
           $bank_gl_account = get_bank_gl_account($from_account);
           }
           
           $sqltc = "INSERT INTO ".TB_PREF."gl_trans_cheque(type, type_no, tran_date,account,bank_act,amount,cheque_no,cheque_date,reference,memo_)VALUES (".db_escape(ST_APPPAYMENT).", ".db_escape($type_no).", " . db_escape(date2sql(Today())) . ",".db_escape($bank_gl_account).",".db_escape($from_account).",".db_escape($amnttotal).",".db_escape($print_obj->cheque_no).",".db_escape($print_obj->cheque_date).",
            ".db_escape($reference).",".db_escape('applicant payment bank/cash debit amount').")";
//           display_error($sqltc);
           db_query($sqltc,"The GL could not be inserted");
           
            $id = get_gl_ref_no(ST_APPPAYMENT);
            $Refs->save(ST_APPPAYMENT, $id, $reference);     
    //end gl entry  
            
    //end payment

    commit_transaction();
    return $prtn;
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


function get_sql_for_fee_payment_list($syear, $cls, $datasearch, $datavalue) {

    $sql = "SELECT 
        st.student_id,
        CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) AS name,
        ssc.cat_name,
        sc.class_name,
        ss.session_name,
        st.school_year,
        st.applicant_id,
        st.st_class
        FROM " . TB_PREF . "sms_student st  
        LEFT JOIN " . TB_PREF . "sms_students_details sd ON st.student_id = sd.student_id
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON st.st_class = sc.id
        LEFT JOIN " . TB_PREF . "sms_session ss ON st.st_section = ss.id
        LEFT JOIN " . TB_PREF . "sms_student_scholarship sss ON st.student_id = sss.student_id 
        LEFT JOIN " . TB_PREF . "sms_student_category ssc ON sss.student_cat = ssc.id
        WHERE st.school_year = " . db_escape($syear);


    if ($datasearch == 'student_id') {
        $sql .= " AND st.$datasearch LIKE '%$datavalue%'";
    } elseif ($datasearch == 'st_class') {
        $sql .= " AND st.$datasearch= " . db_escape($datavalue);
    } elseif ($datasearch == 'st_section') {
        $sql .= " AND st.$datasearch= " . db_escape($datavalue);
    }
//    display_error($sql);
    return $sql;
}


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

function get_payment($ptype) {

    $sql = "SELECT tr.id,tr.month,tr.head_id, tr.due_date,tr.realize,tr.fineamnt,phm.head_name,hs.amount,phm.discount_status, ssc.ratio
            FROM " . TB_PREF . "sms_tbl_receivable tr
            LEFT JOIN " . TB_PREF . "sms_payment_head_setting hs ON tr.head_id = hs.id
            LEFT JOIN " . TB_PREF . "sms_payment_head phm ON hs.head_name = phm.id
            LEFT JOIN " . TB_PREF . "sms_student_scholarship ss ON tr.applicant_id = ss.applicant_id AND ss.is_approved_cat = 1 
            LEFT JOIN " . TB_PREF . "sms_student_category ssc ON ss.student_cat = ssc.id
            WHERE tr.id = " . db_escape($ptype);

    $result = db_query($sql, "an item could not be retreived");

    return db_fetch($result);
}

//used
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
//----------------------------------------------------------------------
//used
function get_sql_for_test_final_result($class,$applicant, $datasearch) {
    
    $sql = "SELECT 
        tr.applicant_id,
        CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name,
        sc.class_name,
        sd.mobile,
        sd.per_address,
        ad.obtain_marks,
        tr.result,
        tr.flag,
        sc.id,
        sd.school_year
        FROM 0_sms_test_result tr
        LEFT JOIN 0_sms_students_details sd ON tr.applicant_id = sd.applicant_id
        LEFT JOIN 0_sms_admission_marks ad ON tr.applicant_id = ad.applicant_id
        LEFT JOIN 0_sms_create_stud_class sc ON sd.class = sc.id  where tr.id  AND sd.class=" .db_escape($class); 
//     display_error($sql);
  
           
        if($datasearch){
            $sql .= " AND sd.$datasearch LIKE '%$applicant%'";
      } 
     
//    if(isset($appid) || isset($cls) || isset($mobile))
//    {
//    $sql .= "WHERE sd.applicant_id= '$appid'
//            OR sd.mobile= '$mobile'
//            OR sd.class= '$cls'" ;
//   
//    }
//    display_error($sql);  
return $sql;
}
//used

function get_sql_for_pass_student($appid,$name,$phone) {
    
    $sql = "SELECT 
        st.student_id,
        CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) AS name, 
        sc.class_name,sc.id as classid,
        ss.session_name,ss.id,
        sr.gpa,
        sr.cgpa,
        sr.result,
        sd.mobile,
        sr.flag,
        sr.student_roll
        FROM "      . TB_PREF . "sms_student_result sr
        LEFT JOIN " . TB_PREF . "sms_student st ON sr.stbl_id = st.id    
        LEFT JOIN " . TB_PREF . "sms_students_details sd ON st.applicant_id = sd.applicant_id
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON st.st_class = sc.id
        LEFT JOIN " . TB_PREF . "sms_session ss ON st.st_section = ss.id ";
   
   if(isset($appid) || isset($name) || isset($phone))
   {
       $sql .= "WHERE st.student_id= '$appid'
               OR sd.first_name= '$name'
               OR sd.mobile= '$phone'";
      // display_error($sql);
       return $sql;
       
   }
    
    return $sql;
}
//............search.........../

function get_sql_application_payment_report($year,$class,$sdate,$edate)
{
      $from_date= date2sql($sdate);
      $to_date= date2sql($edate);
      
      $sql= "SELECT * FROM " . TB_PREF . "sms_app_payment";
      
        if(isset($year)||isset($class)||(isset($from_date) && isset($to_date))){
        $sql .= " WHERE school_year='$year'
            AND class='$class'
            AND sale_date >='$from_date' 
            AND sale_date<='$to_date'";
        }
       // display_error($sql);
      return $sql;
}
//.........................................................db pager..
function sms_payment_setting($hs) {

    $sql = "SELECT ps.id,ph.head_name, ps.no_of_payment,ps.due_date,ps.student_type, sc.class_name, ps.amount
        FROM " . TB_PREF . "sms_payment_head_setting ps
        LEFT JOIN " . TB_PREF . "sms_payment_head ph ON ps.head_name = ph.id
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON ps.st_class = sc.id";
    
    if ($_POST['class'] != '') {
        $sql .= " WHERE ps.st_class=" . db_escape($_POST['class']);
    }
    return $sql;
}
//-----------
//For marksheet
function get_sql_for_result_view($studentid) {

    $sql = "SELECT *, CONCAT(first_name,' ',middle_name,' ',last_name) AS name,sp.father_name,sp.mother_name,st.roll_number,cls.class_name,ses.session_name,ag.group_name,ss.student_cat,ssc.cat_name 
         FROM " . TB_PREF . "sms_students_details sd
         LEFT JOIN " . TB_PREF . "sms_stud_parent_details sp ON sp.applicant_id = sd.applicant_id
         LEFT JOIN " . TB_PREF . "sms_student st ON sd.student_id = st.student_id
         LEFT JOIN " . TB_PREF . "sms_create_stud_class cls ON st.st_class = cls.id
         LEFT JOIN " . TB_PREF . "sms_session ses ON st.st_section = ses.id
         LEFT JOIN " . TB_PREF . "sms_applicant_group ag ON sd.group = ag.id
         LEFT JOIN " . TB_PREF . "sms_student_scholarship ss ON sd.applicant_id = ss.applicant_id
         LEFT JOIN " . TB_PREF . "sms_student_category ssc ON ss.student_cat = ssc.id
         WHERE sd.student_id =" . db_escape($studentid);

    $res = db_fetch(db_query($sql, 'Could not get student data.'));
   
    return $res;
}
//For payment due list
function sql_view($stid) {


    $sql = "SELECT *, CONCAT(first_name,' ',middle_name,' ',last_name) AS name,st.roll_number,cls.class_name,ses.session_name,st.student_id 
         FROM " . TB_PREF . "sms_students_details sd
         LEFT JOIN " . TB_PREF . "sms_stud_parent_details sp ON sp.applicant_id = sd.applicant_id
         LEFT JOIN " . TB_PREF . "sms_student st ON sd.student_id = st.student_id
         LEFT JOIN " . TB_PREF . "sms_create_stud_class cls ON st.st_class = cls.id
         LEFT JOIN " . TB_PREF . "sms_session ses ON st.st_section = ses.id
         LEFT JOIN " . TB_PREF . "sms_applicant_group ag ON sd.group = ag.id";
       
    if($stid!=''){
        $sql .= " WHERE sd.student_id='".$stid."'";
    }
    
    $res = db_fetch(db_query($sql, 'Could not get student data.'));
 
    return $res;
}