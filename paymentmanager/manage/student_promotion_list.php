<?php

/* * ********************************************************************
developed by Mannan
 * ********************************************************************* */
$page_security = 'SS_SMS_STDNT_PAYMNT_RCV';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");
if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Student Payment Receive"), false, false, "", $js);
}

//-----------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------

if(isset($_POST['SearchOrders']))
{
    global $AJAX;
    $name= $_POST['first_name'];
    $appid= $_POST['student_id'];
    $phone = $_POST['mobile'];
    
    $Ajax->activate('_page_body');
}

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);


br();

start_row();

$query=array('student_id','student_id','sms_student');
        combo_list_cells(_("Student ID:"), 'student_id', $_POST['student_id'], 'Select ID', false, $query); 
        
        $query1=array('first_name','first_name','sms_students_details');
        combo_list_cells(_("Student Name:"), 'first_name', $_POST['name'], 'Select Name', false, $query1);
        
        $query2=array('mobile','mobile','sms_students_details');
        combo_list_cells(_("Applicant Phone No:"), 'mobile', $_POST['mobile'], 'Select No', false, $query2);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
//---------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------
function test_status($row) {
    
        if($row['result'] == 1){
            return P;
        }
        else{
            return WP;
        }
    
}
function admission($row) {
    
    if($row['flag'] == 2){
        return 'Admitted';
    }
    elseif($row['flag'] == 1){
    return pager_link( _("Promotion"),
	"/paymentmanager/manage/promoted_student_payment_receive.php?studentid=" . $row['student_id']."&rollid=" . $row['student_roll']."&class=" .$row['classid']."&rid=" .$row['id'], ICON_RECEIVE);
    }
    elseif($row['flag'] == 0){
    return pager_link( _("Payment Setting"),
	"/resultmanager/manage/student_pass_list.php?", ICON_EDIT);
    }
    
    return true;
}

//---------------------------------------------------------------------------------------------

//display_error($_POST['student_id']);

$sql = get_sql_for_pass_student($appid,$name,$phone);

$cols = array(
    _("Student ID") ,
    _("Student Name") ,
    _("Class") ,
    _("Section") ,
    _("Grade") ,
    _("GPA") ,
    _("Status"),
    _("Print") => array('insert' => true, 'fun' => 'admission', 'align' => 'center')
);

//---------------------------------------------------------------------------------------------------

$table = & new_db_pager('sms_test_result', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}