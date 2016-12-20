<?php

/* * ********************************************************************
 
 * ********************************************************************* */
$page_security = 'SS_SMS_STDNT_WISE_PAYMNT_REP';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Student Payment View"), false, false, "", $js);
}
$syear = get_current_schoolyear();

//-----------------------------------------------------------------------------------
// Ajax updates

if(list_updated('datasearch'))
$Ajax->activate('_page_body');

if(list_updated('datavalue'))
$Ajax->activate('_page_body');

if(isset($_POST['SearchOrders']))
$Ajax->activate('_page_body');
//---------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------
if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
search_field_payment(_("Select Search Type : "), 'datasearch','',null,true,true);

if($_POST['datasearch']=='student_id'){
    
    $query=array(array('student_id','SELECT student_id from'.TB_PREF.'sms_student ORDER BY student_id ASC'));
    text_cells(_("student id:"),'datavalue','', 35, $max);
}

if($_POST['datasearch']=='st_class'){
     
$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
ORDER BY class_name ASC'));
combo_list_cells(_("Class :"), 'datavalue', null, 'Select Class', true, $query);
}

elseif($_POST['datasearch']=='st_section'){
     $sql = "SELECT ss.id as sd, ss.session_name as sname FROM " . TB_PREF . "sms_teacher_allocation ta                   
        LEFT JOIN " . TB_PREF . "sms_session ss ON ss.id=ta.section GROUP BY ss.session_name ORDER BY ss.session_name ASC";

    $query=array(array('sd','sname',$sql));
    combo_list_cells(_("Section :"), 'datavalue', '', 'Select Section', true, $query );
 
 }

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);

//---------------------------------------------------------------------------------------------
function tpayable($row) {
    
    $sql= "SELECT sum( hs.amount ),sum(tr.fineamnt)
           FROM " .TB_PREF . "sms_tbl_receivable tr
           LEFT JOIN " .TB_PREF . "sms_payment_head_setting hs ON tr.head_id = hs.id
           WHERE tr.school_year =" .db_escape($row['school_year'])  ." AND ( tr.student_id=" .  db_escape($row['student_id']) . " OR tr.applicant_id=" .  db_escape($row['applicant_id']) . ")";
      $result = db_query($sql,'select failed');
      $rw = db_fetch($result);
      
      //For find fine amount
      $sqlhead = "SELECT id FROM ".TB_PREF."sms_payment_head WHERE head_name LIKE '%Attendance%'";
      $head = db_fetch(db_query($sqlhead));
    
      $condition = array('st_class'=>$row['st_class'],'head_name'=>$head['id']);
      $field = array('id','amount');
      $paymentval = db_fetch(data_retrieve_condition("sms_payment_head_setting", $field, $condition));
      
      $amount = ($rw['sum( hs.amount )'] - $paymentval['amount']);
      $totalpaid = ($amount + $rw['sum(tr.fineamnt)']);
     
      return $totalpaid;
}
//------------------------
function totaldiscount($row) {
    
    $sql="SELECT sum(( phs.amount * ssc.ratio )/100) 
      FROM " . TB_PREF . "sms_tbl_receivable str
      LEFT JOIN " . TB_PREF . "sms_payment_head_setting phs ON str.head_id = phs.id
      LEFT JOIN " . TB_PREF . "sms_payment_head phm ON phs.head_name = phm.id
      LEFT JOIN " . TB_PREF . "sms_student_scholarship ss ON str.applicant_id = ss.applicant_id AND ss.is_approved_cat = 1 
      LEFT JOIN " . TB_PREF . "sms_student_category ssc ON ss.student_cat = ssc.id
      WHERE str.school_year =" .db_escape($row['school_year'])  ." AND phm.discount_status = 1 AND (str.student_id=" .  db_escape($row['student_id']) . " OR str.applicant_id=" .  db_escape($row['applicant_id']) . ")";
    
      $result = db_query($sql,'select failed');
      $rw = db_fetch($result);

      $totaldiscount = $rw['sum(( phs.amount * ssc.ratio )/100)'];
      
      return $totaldiscount;
}
//------------------------
function specialdiscount($row) {
    
    $condition = array('student_id'=>$row['student_id'],'school_year'=>$row['school_year']);
    $field = array('sp_discount');
    $result = data_retrieve_condition('sms_transaction_details', $field, $condition);
    
    while($data = db_fetch($result)){
        
        $spdiscount += $data['sp_discount'];
    }
     
      return $spdiscount;
}
//------------------------
function tpaid($row) {
    
    $condition = array('student_id'=>$row['student_id'],'school_year'=>$row['school_year']);
    $field = array('total_amount');
    $result = data_retrieve_condition('sms_transaction_details', $field, $condition);
    
    while($data = db_fetch($result)){
        
        $tpaid += $data['total_amount'];
    }
     
      return $tpaid;
}
//-----------------------
function tpayableadis($row) {
    
     $tpayableadis = (tpayable($row) - totaldiscount($row));
     
      return $tpayableadis;
}
//----------------------------------------------------
function coutstanding($row) {
    
    $coutstanding = (tpayableadis($row) - (tpaid($row) + specialdiscount($row)));
    return $coutstanding;
}
function pay($row) {
     
     return pager_link( _("Promotion"),
            "/paymentmanager/manage/student_payment_receive.php?studentid=" . $row['student_id']."&class=" .$row['st_class']."&syear=" .$row['school_year'],ICON_MONEY);

        return true;
}
function paymentreport($row) {
     
     return viewer_link(null,
            "/paymentmanager/report/student_due_report.php?id=".$row['student_id']. "", null, null, ICON_VIEW);

        return true;
}

//---------------------------------------------------------------------------------------------

$student= $_POST['student'];
$datasearch=$_POST['datasearch'];
$datavalue=$_POST['datavalue'];
$sql = get_sql_for_fee_payment_list($syear,$cls,$datasearch,$datavalue);

$cols = array(
    _("Student ID") ,
    _("Student Name") ,
    _("Student Type") ,
    _("Class") ,
    _("Section") ,
    _("Total Payable Before Discount") => array('insert' => true, 'fun' => 'tpayable', 'align' => 'center'),
    _("Total Discount") => array('insert' => true, 'fun' => 'totaldiscount', 'align' => 'center'),
    _("Special Discount") => array('insert' => true, 'fun' => 'specialdiscount', 'align' => 'center'),
    _("Total Payable After Discount") => array('insert' => true, 'fun' => 'tpayableadis', 'align' => 'center'),
    _("Total Paid") => array('insert' => true, 'fun' => 'tpaid', 'align' => 'center'),
    _("Current Outstanding") => array('insert' => true, 'fun' => 'coutstanding', 'align' => 'center'),
    _("Payment Report")=> array('insert' => true, 'fun' => 'paymentreport', 'align' => 'center'),
    _("Pay") => array('insert' => true, 'fun' => 'pay', 'align' => 'center')
);

//---------------------------------------------------------------------------------------------------

$table = & new_db_pager('sms_test_result', $sql, $cols);

$table->width = "95%";

display_db_pager($table);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}