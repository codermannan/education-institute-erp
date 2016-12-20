<?php

/* * ********************************************************************
  Create Payment receive cart -- Mannan
 * ********************************************************************* */
$page_security = 'SS_SMS_APLCNT_PAYMNT_RCV';
$path_to_root = "../..";
include_once($path_to_root . "/paymentmanager/includes/ui/sch_payment_cart.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/payment_entry_ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");


$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(800, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

if (isset($_GET['app_id'])) {
    $appid = $_GET['app_id'];
    
    $_SESSION['page_title'] = _($help_context = "Applicant Payment Receive #") . $appid;

    page(_($help_context = $_SESSION['page_title']), false, false, "", $js);

    create_cart_by_app_id($appid);
}
else
    page(_($help_context = "Applicant Payment Receive"), false, false, "", $js);
//------------------------------------
if (isset($_GET['trid'])) {
	$trans_no = $_GET['trid'];
        $stid = $_GET['stid'];
        
	display_notification_centered(sprintf( _("Transaction no # %d has been entered."),$trans_no));
        
        display_note(viewer_link(_("&View This Invoice"), "/paymentmanager/report/app_payment_inv.php?transno=" . $trans_no ."&stid=".$stid."", 0, 1));
        br(1);
	
	submenu_print(_("&Print This Invoice"), ST_APPPAYMENT, $trans_no, 'prtopt');
	submenu_print(_("&Email This Invoice"), ST_APPPAYMENT, $trans_no, null, 1);
	set_focus('prtopt');
	
	submenu_option(_("Make new payment for another student"),"/paymentmanager/report/admission_list.php'");

	display_footer_exit();

}
//------------------------------------
if (list_updated('print_order')) {

    $orderid = $_POST['print_order'];
    create_cart_by_sr_items($orderid);
}

//--------------------------------------------------------------------------------------------------


function create_cart_by_app_id($app_id=null) {
    global $Refs;

    if (isset($_SESSION['sch_pay_rec'])) {
        unset($_SESSION['sch_pay_rec']);
    }

    $cart = new sch_payment_cart();


    if ($app_id) {
        $syear = get_current_schoolyear();
        $condition = array('id'=>$syear);
        $field = array('begin');
        $fy = db_fetch(data_retrieve_condition("fiscal_year", $field, $condition));
        $dpar = date_parse($fy['begin']);
//        $dt =  $dpar['year'].'-01-31';
        $dt =  '2015-01-31';
        
        $sql="SELECT  str.id, str.due_date, phm.head_name,phm.discount_status, phs.amount, ssc.ratio FROM " . TB_PREF . "sms_tbl_receivable str
              LEFT JOIN " . TB_PREF . "sms_payment_head_setting phs ON str.head_id = phs.id
              LEFT JOIN " . TB_PREF . "sms_payment_head phm ON phs.head_name = phm.id
              LEFT JOIN " . TB_PREF . "sms_student_scholarship ss ON str.applicant_id = ss.applicant_id AND ss.is_approved_cat = 1 
              LEFT JOIN " . TB_PREF . "sms_student_category ssc ON ss.student_cat = ssc.id
              WHERE str.applicant_id=".  db_escape($app_id). " AND str.due_date = '".$dt."'";
         
        $query = db_query($sql);

        while ($row = db_fetch($query)) {
            
         $cart->add_to_sch_payment_cart_raw(null,$row['due_date'],$row['head_name'],$row['amount'],$row['id'],$row['discount_status'],$row['ratio']);
        }
    }
    $_SESSION['sch_pay_rec'] = &$cart;
}

//------------------------------------------------------------------------------------------


function line_start_focus() {
    global $Ajax;

    $Ajax->activate('items_table');
    set_focus('_stock_id_edit');
}

//-----------------------------------------------------------------------------------------------
function handle_new_order() {
    if (isset($_SESSION['sch_pay_rec'])) {
        $_SESSION['sch_pay_rec']->clear_printing_raw_items();
        unset($_SESSION['sch_pay_rec']);
    }


    $_SESSION['sch_pay_rec'] = new sch_payment_cart(null);

    $_POST['AdjDate'] = new_doc_date();
    if (!is_date_in_fiscalyear($_POST['AdjDate']))
        $_POST['AdjDate'] = end_fiscalyear();
}

//-----------------------------------------------------------------------------------------------

if (isset($_POST['Process']) || isset($_POST['Update'])) {
    global $Refs;

    $tr = &$_SESSION['sch_pay_rec'];
    
    $input_error = 0;
  
      if($_POST['payment_method'] == 0) {
        display_error(_("You must Select Payment Method."));
        set_focus('print_order');
        $input_error = 1;
        return false;
       } 
       
       if($_POST['payment_method'] == 1) {
            
            if ($_POST['bank_account'] == '') {
                display_error(_("You must Select Into Account."));
                set_focus('bank_account');
                $input_error = 1;
                return false;
            }
       } 
       if ($_POST['payment_method'] == 2) {
            
            if ($_POST['bank_name'] == '') {
                display_error(_("You must Select Bank Name."));
                set_focus('bank_name');
                $input_error = 1;
                return false;
            }
            if ($_POST['branch_name'] == '') {
                display_error(_("You must enter Branch Name."));
                set_focus('branch_name');
                $input_error = 1;
                return false;
            }
            if ($_POST['cheque_no'] == '') {
                display_error(_("You must enter Cheque No."));
                set_focus('cheque_no');
                $input_error = 1;
                return false;
            }
        }

    if ($input_error == 1)
        unset($_POST['Process']); 
}

//-------------------------------------------------------------------------------

if (isset($_POST['Process'])) {
    
    $tr = &$_SESSION['sch_pay_rec'];
    copy_to_sch_pay_cart();
    
    $condition = array('school_year'=>$tr->syear,'st_class'=>$tr->class);
    $field = array('school_year','st_class','roll_number');
    $qr = data_retrieve_condition("sms_student", $field, $condition, 'roll_number DESC');
    $trow = db_fetch($qr);
    $studentroll = ($trow['roll_number'] + 1);
   
    $trans_no = add_school_student_payment($tr,$studentroll); 
    //display_error($trans_no);  
    $_SESSION['sch_pay_rec']->clear_sch_payment_items();
    unset($_SESSION['sch_pay_rec']);
    
    //for create user
    meta_forward($_SERVER['PHP_SELF'], "$trans_no");
} /* end of process credit note */


if (isset($_POST['Update'])) {

    $tr = &$_SESSION['sch_pay_rec'];
    copy_to_cart();

//display_error(print_r($tr,true));
//	$trans_no = update_requsition_order($tr);
//$trans_no = update_common_sr_order($tr);

    $_SESSION['sch_pay_rec']->clear_printing_raw_items();
    unset($_SESSION['sch_pay_rec']);

    meta_forward($_SERVER['PHP_SELF'], "UpdateID=$trans_no");
}

//-----------------------------------------------------------------------------------------------

function check_item_data() {
//	if (!check_num('qty', 0))
//	{
//		display_error(_("The quantity entered must be a positive number."));
//		set_focus('qty');
//		return false;
//	}

    if (input_num('amount') == NULL || input_num('amount') == 0) {
        display_error(_(" Amount can not be  0  "));
        set_focus('amount');
        return false;
    }
    return true;
}

//-----------------------------------------------------------------------------------------------

function handle_update_item() {
    if ($_POST['UpdateItem'] != "" && check_item_data()) {
        $id = $_POST['LineNo'];
        $_SESSION['sch_pay_rec']->update_cart_item($id,input_num('amount'));
    }
    line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_delete_item($id) {
    $_SESSION['sch_pay_rec']->remove_from_cart($id);
    line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_new_item() {
    if (!check_item_data())
        return;

//    add_to_printing_order_raw($_SESSION['sch_pay_rec'], $_POST['stock_id'], $_POST['qty']);
    line_start_focus();
}

//-----------------------------------------------------------------------------------------------
$id = find_submit('Delete');
if ($id != -1)
    handle_delete_item($id);

if (isset($_POST['AddItem']))
    handle_new_item();

if (isset($_POST['UpdateItem']))
    handle_update_item();

if (isset($_POST['CancelItemChanges'])) {
    line_start_focus();
}
//-----------------------------------------------------------------------------------------------

if (isset($_GET['NewOrder']) || !isset($_SESSION['sch_pay_rec'])) {
    handle_new_order();
}

//-----------------------------------------------------------------------------------------------
start_form();

sch_payment_header_raw($_SESSION['sch_pay_rec']);

start_table(TABLESTYLE, "width=70%", 10);
start_row();
echo "<td>";
sch_payment_items_raw(_("Applicant Payment"), $_SESSION['sch_pay_rec']);
sch_payment_options_controls();
echo "</td>";
end_row();
end_table(1);

if ($_GET['ModifyOrderNumber']) {
    submit_center_first('Update', _("Update Order"), '', 'default');
} else {
    submit_center_first('Update', _("Update"), '', null);
    submit_center_last('Process', _("Applicant Payment Receive"), '', 'default');
}
end_form();
end_page();
?>
