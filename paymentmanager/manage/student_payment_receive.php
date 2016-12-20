<?php

/* * ********************************************************************
  Create purchase requisition add item in cart -- abiR
 * ********************************************************************* */
if(@$_GET['Student']=='Yes'){
   $page_security = 'SS_SMS_STUD_DBOARD'; 
}else{
   $page_security = 'SS_SMS_DAY_WISE_PAYMNT_REP'; 
}
$path_to_root = "../..";
include_once($path_to_root . "/paymentmanager/includes/ui/student_payment_cart.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/student_entry_ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");


$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(800, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

if (isset($_GET['studentid'])) {

    $_SESSION['stid'] = $_GET['studentid'];
    $_SESSION['clas'] = $_GET['class'];
    $_SESSION['syear'] = $_GET['syear'];
    
    $_SESSION['page_title'] = _($help_context = "Student Payment #") . $_SESSION['stid'];

    page(_($help_context = $_SESSION['page_title']), false, false, "", $js);

    //create_cart_print(null, $_GET['ModifyOrderNumber']);
}
else
    page(_($help_context = "Student Payment"), false, false, "", $js);

$user = $_SESSION['wa_current_user']->username;

//-----------payment receive print area--------------//
if (isset($_GET['trid'])) {
	$trans_no = $_GET['trid'];
        $stid = $_GET['stid'];
        
	display_notification_centered(sprintf( _("Transaction no # %d has been entered."),$trans_no));
        
        display_note(viewer_link(_("&View This Invoice"), "/paymentmanager/report/app_payment_inv.php?transno=" . $trans_no ."&stid=".$stid."", 0, 1));
        br(1);
	
	submenu_print(_("&Print This Invoice"), ST_APPPAYMENT, $trans_no, 'prtopt');
	submenu_print(_("&Email This Invoice"), ST_APPPAYMENT, $trans_no, null, 1);
	set_focus('prtopt');
	
	submenu_option(_("Make new payment for another student"),"/paymentmanager/manage/fee_payment_list.php'");

	display_footer_exit();

}

//---------------------------------------------------

if (list_updated('payment_type')) {
    $_SESSION['paytype'] = $_POST['payment_type'];
    $Ajax->activate('ptype');
    //create_cart_by_st_items($orderid);
}

//--------------------------------------------------------------------------------------------------


function create_cart_by_st_items($pr_order=null) {
    global $Refs;

    if (isset($_SESSION['student_st_req_raw'])) {
        unset($_SESSION['student_st_req_raw']);
    }

    $cart = new print_sr_cart_raw();

    $_SESSION['student_st_req_raw'] = &$cart;
}

//------------------------------------------------------------------------------------------


function line_start_focus() {
    global $Ajax;

    $Ajax->activate('items_table');
    set_focus('_stock_id_edit');
}

//-----------------------------------------------------------------------------------------------
function handle_new_order() {
    
    if (isset($_SESSION['student_st_req_raw'])) {
        $_SESSION['student_st_req_raw']->clear_printing_raw_items();
        unset($_SESSION['student_st_req_raw']);
    }


    $_SESSION['student_st_req_raw'] = new print_sr_cart_raw(null);

    $_POST['AdjDate'] = new_doc_date();
    if (!is_date_in_fiscalyear($_POST['AdjDate']))
        $_POST['AdjDate'] = end_fiscalyear();
}

//-----------------------------------------------------------------------------------------------

if (isset($_POST['Process']) || isset($_POST['Update'])) {
    global $Refs;

    $tr = &$_SESSION['student_st_req_raw'];
    
    $input_error = 0;
    if (count($tr->line_items) == 0) {
        display_error(_("You must enter at least one non empty item linen."));
        set_focus('order_item_no');
        return false;
    }  
   if ($_POST['payment_method'] == 0) {
            display_error(_("You must Select payment method."));
            set_focus('payment_method');
            $input_error = 1;
            return false;
        }
   if($_POST['payment_method'] == 1) {
            
            if ($_POST['bank_account'] == '') {
                display_error(_("You must Into From Account."));
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

    $tr = &$_SESSION['student_st_req_raw'];
    copy_to_student_cart();
    
    $trans_no = student_payment_collection($tr,$user); 

    $_SESSION['student_st_req_raw']->clear_printing_raw_items();
    unset($_SESSION['student_st_req_raw']);
    unset($_SESSION['stid']);
    unset($_SESSION['clas']);
    unset($_SESSION['syear']);
    
    meta_forward($_SERVER['PHP_SELF'], "$trans_no");
} /* end of process credit note */


if (isset($_POST['Update'])) {

    $tr = &$_SESSION['student_st_req_raw'];
    copy_to_cart();

//display_error(print_r($tr,true));
//	$trans_no = update_requsition_order($tr);
//$trans_no = update_common_sr_order($tr);

    $_SESSION['student_st_req_raw']->clear_printing_raw_items();
    unset($_SESSION['student_st_req_raw']);

    meta_forward($_SERVER['PHP_SELF'], "UpdateID=$trans_no");
}

//-----------------------------------------------------------------------------------------------

function check_item_data() {
    if ($_POST['ptype'] == '') {
        display_error(_(" Payment head must be selected "));
        set_focus('ptype');
        return false;
    }
    return true;
}

//-----------------------------------------------------------------------------------------------

function handle_update_item() {
    if ($_POST['UpdateItem'] != "" && check_item_data()) {
        $id = $_POST['LineNo'];
        $_SESSION['student_st_req_raw']->update_cart_item($id, $_POST['part'], input_num('qty'));
    }
    line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_delete_item($id) {
    $_SESSION['student_st_req_raw']->remove_from_cart($id);
    line_start_focus();
}

//-----------------------------------------------------------------------------------------------
function handle_new_item() {
    if (!check_item_data())
        return;
    add_to_student_payment_raw($_SESSION['student_st_req_raw'],$_POST['ptype'], $_SESSION['stid'],$_SESSION['syear']);
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

if (isset($_GET['NewOrder']) || !isset($_SESSION['student_st_req_raw'])) {
    handle_new_order();
}

//-----------------------------------------------------------------------------------------------
start_form();

st_display_order_header_raw($_SESSION['student_st_req_raw'],$_SESSION['stid'],$_SESSION['syear'],$_SESSION['clas']);

start_table(TABLESTYLE, "width=70%", 10);
start_row();
echo "<td>";
st_display_transfer_items_raw(_("Student Payment"), $_SESSION['student_st_req_raw'],$_SESSION['paytype'],$_SESSION['stid'],$_SESSION['syear']);
st_transfer_options_controls();
echo "</td>";
end_row();
end_table(1);

if ($_GET['ModifyOrderNumber']) {
    submit_center_first('Update', _("Update Order"), '', 'default');
} else {
    submit_center_first('Update', _("Update"), '', null);
    submit_center_last('Process', _("Pay Now"), '', 'default');
}


end_form();
end_page();
?>
