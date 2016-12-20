<?php

/* * ********************************************************************
 
 * ********************************************************************* */
$page_security = 'SS_SMS_TST_RSLT_VW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");


if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Generate Payment Slip"), false, false, "", $js);
}


//-----------------------------------------------------------------------------------

if(list_updated('class'))
$Ajax->activate('_page_body');

if (isset($_GET['notification']) == 1) {
    display_notification(_('Payment head setting has not been setup for this class'));
}
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
function test_status($row) {
    
        if($row['result'] == 1){
            return P;
        }
        else{
            return WP;
        }
    
}
function admission($row) {
    $syear = get_current_schoolyear();
    $datar = data_retrieve('sms_student_scholarship', 'applicant_id', $row['applicant_id']);
    
    if($datar['student_cat'] != 1 AND $datar['is_approved_cat'] == 0){
        return 'Waiting for scholarship approve';
    }elseif($row['flag'] == 2){
        return 'Admitted';
    }elseif($row['flag'] == 0){
        return pager_link( _("Payment Setting"), "/admission/manage/receavable_payment_setting.php?applicantid=" . $row['applicant_id']."&class=" .$row['id']."&syear=" .$syear, ICON_ADD);
    }else{
    return viewer_link(null, "admission/report/payment_slip.php?Applicantid=" . $row['applicant_id'] . "", null, null, ICON_VIEW);
    }
    return true;  
}

//---------------------------------------------------------------------------------------------

$sql = get_sql_for_test_final_result($_POST['class'],$_POST['applicant'], $_POST['datasearch']);

$cols = array(
    _("Applicant ID") ,
    _("Student ID") ,
    _("Applicant Name") ,
    _("Admitted Class") ,
    _("Mobile"),
    _("Address"),
    _("Mark"),
    _("Status")=> array('insert' => true, 'fun' => 'test_status', 'align' => 'center'),
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