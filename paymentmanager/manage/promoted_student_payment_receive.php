<?php

/* * ********************************************************************
  Create Payment receive cart -- Mannan
 * ********************************************************************* */
$page_security = 'SS_SMS_STDNT_RSLT_PRCS';
$path_to_root = "../..";
include_once($path_to_root . "/paymentmanager/includes/ui/promoted_payment_cart.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/promoted_entry_ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");

$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(800, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();
$user = $_SESSION['wa_current_user']->username;

if (isset($_GET['studentid'])) {
    $stid = $_GET['studentid'];

    $_SESSION['page_title'] = _($help_context = "Student Payment Receive #") . $stid;

    page(_($help_context = $_SESSION['page_title']), false, false, "", $js);

    create_cart_by_app_id($stid);
}
else
    page(_($help_context = "Student Payment Receive"), false, false, "", $js);


//if (list_updated('print_order')) {
//
//    $orderid = $_POST['print_order'];
//    create_cart_by_sr_items($orderid);
//}

//--------------------------------------------------------------------------------------------------


function create_cart_by_app_id($stid=null) {
    global $Refs;

    if (isset($_SESSION['promo_pay_rec'])) {
        unset($_SESSION['promo_pay_rec']);
    }

    $cart = new promo_payment_cart();


    if ($stid) {
        
        $sql="SELECT  str.id,str.head_id, str.due_date, phs.head_name, phs.amount FROM " . TB_PREF . "sms_tbl_receivable str
              LEFT JOIN " . TB_PREF . "sms_payment_head_setting phs ON str.head_id = phs.id
              WHERE str.student_id=".  db_escape($stid). " AND str.due_date = '2015-01-31' And str.realize = 0";
         
        $query = db_query($sql);

        while ($row = db_fetch($query)) {
            
         $cart->add_to_promo_payment_cart_raw(null,$row['due_date'],$row['head_name'],$row['amount'],$row['id'],$row['head_id']);
        }
    }
    $_SESSION['promo_pay_rec'] = &$cart;
}

//------------------------------------------------------------------------------------------


function line_start_focus() {
    global $Ajax;

    $Ajax->activate('items_table');
    set_focus('_stock_id_edit');
}

//-----------------------------------------------------------------------------------------------
function handle_new_order() {
    if (isset($_SESSION['promo_pay_rec'])) {
        $_SESSION['promo_pay_rec']->clear_printing_raw_items();
        unset($_SESSION['promo_pay_rec']);
    }


    $_SESSION['promo_pay_rec'] = new promo_payment_cart(null);

    $_POST['AdjDate'] = new_doc_date();
    if (!is_date_in_fiscalyear($_POST['AdjDate']))
        $_POST['AdjDate'] = end_fiscalyear();
}

//-----------------------------------------------------------------------------------------------

if (isset($_POST['Process']) || isset($_POST['Update'])) {
    global $Refs;

    $tr = &$_SESSION['promo_pay_rec'];
    
      $input_error = 0;
      
      if($_POST['pclass'] == 0) {
        display_error(_("You must Select Allocate Class."));
        set_focus('pclass');
        $input_error = 1;
        return false;
      }  
      if($_POST['psection'] == 0) {
        display_error(_("You must Select Allocate Section."));
        set_focus('psection');
        $input_error = 1;
        return false;
      }  
      
      if($_POST['payment_method'] == 0) {
        display_error(_("You must Select Payment Method."));
        set_focus('print_order');
        $input_error = 1;
        return false;
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
    
    $tr = &$_SESSION['promo_pay_rec'];
   
    copy_to_promo_pay_cart();
    
    
    $trans_no = add_promotion_student_payment($tr,$user); 
//    display_error($trans_no);  
//    $_SESSION['promo_pay_rec']->clear_printing_raw_items();
//    unset($_SESSION['promo_pay_rec']);

     meta_forward($path_to_root.'/paymentmanager/manage/student_promotion_list.php');
} /* end of process credit note */


//if (isset($_POST['Update'])) {
//
//    $tr = &$_SESSION['promo_pay_rec'];
//    copy_to_cart();
//
////display_error(print_r($tr,true));
////	$trans_no = update_requsition_order($tr);
////$trans_no = update_common_sr_order($tr);
//
//    $_SESSION['promo_pay_rec']->clear_printing_raw_items();
//    unset($_SESSION['promo_pay_rec']);
//
//    meta_forward($_SERVER['PHP_SELF'], "UpdateID=$trans_no");
//}

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
        $_SESSION['promo_pay_rec']->update_cart_item($id,input_num('amount'));
    }
    line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_delete_item($id) {
    $_SESSION['promo_pay_rec']->remove_from_cart($id);
    line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_new_item() {
    if (!check_item_data())
        return;

//    add_to_printing_order_raw($_SESSION['promo_pay_rec'], $_POST['stock_id'], $_POST['qty']);
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

if (isset($_GET['NewOrder']) || !isset($_SESSION['promo_pay_rec'])) {
    handle_new_order();
}

//-----------------------------------------------------------------------------------------------
start_form();

promoted_payment_header_raw($_SESSION['promo_pay_rec']);

start_table(TABLESTYLE, "width=70%", 10);
start_row();
echo "<td>";
promoted_payment_items_raw(_("Student Payment"), $_SESSION['promo_pay_rec']);
promoted_payment_options_controls();
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
