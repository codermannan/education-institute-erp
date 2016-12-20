<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SA_LOCA';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/payroll_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");
#include_once($path_to_root . "/personnel/includes/personnel_db.inc");

simple_page_mode(true);
//----------------------------------------------------------------------------------
/* model page with database query */

if(isset($_GET['app_id']))
$app_id = $_GET['app_id'];

page(_($help_context = " Students Result Update # ".$app_id), @$_REQUEST['popup'], false, "", $js);

$sql = $sql = "Select tr.result, sam.obtain_marks from " . TB_PREF . "sms_test_result tr
               LEFT JOIN " . TB_PREF . "sms_admission_marks sam ON tr.applicant_id = sam.applicant_id
               where tr.applicant_id =" . db_escape($app_id); 

$res = db_fetch(db_query($sql, 'Can not get data.'));

//----------------------------------------------
if (isset($_POST['addupdate'])) 
{
	$input_error = 0;
        
	if (strlen($_POST['am_res']) == 0) 
	{
		$input_error = 1;
		display_error( _('The applicant result  must be entered.'));
		set_focus('am_res');
	} 
       
        get_sql_for_test_result_update($_POST['app_id'],$_POST['am_res']);
        
        display_notification(_("Applicant result has been updated."));
        
}

start_form();

start_table(TABLESTYLE2);
echo '<tr>';
label_cells(_("Applicant ID:"), $app_id);
echo '</tr>';

label_cells(_("Obtain Mark:"), $res['obtain_marks']);

text_row(_("Amendment Result:"), 'am_res', null, 30,3);

end_table(1);

submit_center_first('addupdate', _("Update Result"), '', 
    @$_REQUEST['popup'] ? true : 'default');
submit_center_last('cancel', _("Cancel"), _("Cancel Edition"), 'cancel');
hidden('app_id', $app_id);
end_form();
end_page(@$_REQUEST['popup']);
?>

