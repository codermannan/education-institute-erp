<?php

/* * ********************************************************************
  
 * ********************************************************************* */
$page_security = 'SS_SMS_APLCNT_SHRT_LST';
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
    page(_($help_context = "Applicant short List"), false, false, "", $js);
}
//if (isset($_GET['order_number'])) {
//    $order_number = $_GET['order_number'];
//}
//-----------------------------------------------------------------------------------

if (isset($_POST['SearchOrders'])) 
{   
	if (strlen($_POST['app_class']) == '') 
	{
		$input_error = 1;
		display_error( _('Applicant class must be selected.'));
		set_focus('app_class');
                return false;
	} 
        
        elseif (strlen($_POST['start_mark']) == '') 
	{
		$input_error = 1;
		display_error( _('Applicant start mark must be entered.'));
		set_focus('start_mark');
                return false;
	} 
        
        elseif (strlen($_POST['end_mark']) == '') 
	{
		$input_error = 1;
		display_error( _('Applicant end mark must be entered.'));
		set_focus('start_mark');
                return false;
	} 
        
       
}
//----------------------------------
if (isset($_POST['Process'])) 
{
   if($input_error != 1)
   {
        foreach($_POST['appid'] as $key => $apl){
    
            $pass = $_POST['p'][$key];
            
           // $wpass = $_POST['wp'][$key];

            process_short_list($apl, $pass);
            
	   display_notification(_('Short list result has been added'));
        }
      }

}
//---------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);


br();

start_row();
$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));

combo_list_cells(_("Select Class :"), 'app_class', '', 'Select Class', false, $query);

text_cells(_("Start Mark:"), 'start_mark', null);

text_cells(_("End Mark:"), 'end_mark', null);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
//---------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------

function applicantId($row) {
    hidden('appid['.$row['id'].']', $row['applicant_id']);
    return $row['applicant_id'] ;
}

function test_result($row) {
 
    if($row['obtain_marks'] >= $_POST['start_mark'] && $row['obtain_marks'] <= $_POST['end_mark']){
        hidden('p['.$row['id'].']', 1);
        return 'P';
    }else{
        hidden('p['.$row['id'].']', 2);
        
        return 'WP';
    }
    
    
}

//---------------------------------------------------------------------------------------------

//$pi_type = 'spn_pr_item';
$sql = get_sql_short_list_data($class_id);

$cols = array(
    _("Applicant ID") => array('fun' => 'applicantId', 'align' => 'center') ,
    _("Applicant Name") ,
    _("Mark"),
    _("Status")=> array('fun' => 'test_result', 'align' => 'center') 

);

//---------------------------------------------------------------------------------------------------

$table = & new_db_pager('students_details', $sql, $cols);


$table->width = "80%";

display_db_pager($table);
br();

div_start('controls');

        submit_center('Process', _("Process Short List"), true, '', 'default');

    div_end();

if (!@$_GET['popup']) {
    end_form();
    end_page();
}