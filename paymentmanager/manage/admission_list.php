<?php

/* * ********************************************************************
 
 * ********************************************************************* */
$page_security = 'SS_SMS_APLCNT_PAYMNT_RCV';
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
    page(_($help_context = "Applicant Payment Receive"), false, false, "", $js);
}

//-----------------------------------------------------------------------------------
if(list_updated('school_year'))
$Ajax->activate('_page_body');

if(list_updated('class'))
$Ajax->activate('_page_body');
//---------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

br();
 

 start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));

combo_list_cells(_("Select Class :"), 'class', '', 'Select Class', true, $query);

search_field(_("Select Search Type : "), 'datasearch');

text_cells(null, 'applicant');
 
submit_cells('SearchOrders', _("Search"), '', _('Select applicant'), 'default');

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
    
        return pager_link( _("Admission"),
	"/paymentmanager/manage/payment_receive.php?app_id=" .$row['applicant_id'], ICON_RECEIVE);
    }
    elseif($row['flag'] == 0){
        return pager_link( _("Generate Payment Slip"),
	"/admission/report/test_result_view.php?", ICON_EDIT);
    }
    return true;
}

//---------------------------------------------------------------------------------------------



$sql = get_sql_for_test_final_result($_POST['class'],$_POST['applicant'],$_POST['datasearch']);

$cols = array(
    _("Applicant ID") ,
    _("Applicant Name") ,
    _("Expected Class") ,
    _("Mobile"),
    _("Address"),
    _("Mark"),
    _("Status")=> array('insert' => true, 'fun' => 'test_status', 'align' => 'center'),
    _("Admission") => array('insert' => true, 'fun' => 'admission', 'align' => 'center')
);

//---------------------------------------------------------------------------------------------------

$table = & new_db_pager('sms_test_result', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}