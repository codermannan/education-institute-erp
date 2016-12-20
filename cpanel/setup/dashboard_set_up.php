<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_DASH_BRD_SETP';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/applicant_info_ui_lists.inc");
include_once($path_to_root . "/sms/includes/db/student_db.inc");


page(_("Dash Board"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];
//display_error($selected_id);

//if(list_updated('class'))
//$Ajax->activate('_page_body');
//-------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

        $input_error = 0;

//	if (strlen($_POST['class']) == '') 
//	{
//		$input_error = 1;
//		display_error(_("Class must be selected."));
//		set_focus('class');
//	}
//        
//        if (strlen($_POST['section']) == '') 
//	{
//		$input_error = 1;
//		display_error(_("Section must be selected."));
//		set_focus('section');
//	}
//        
//        if (strlen($_POST['teacher']) == '') 
//	{
//		$input_error = 1;
//		display_error(_("Teacher must be selected."));
//		set_focus('teacher');
//	}
//        
	if ($input_error !=1)
	{
    	if ($selected_id != -1) 
    	{ 
		    update_dashboard($selected_id,$_POST['class'],$_POST['notice']);    		
			display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{
		    add_dashboard($_POST['notice'],$_POST['app_class']);
			display_notification(_('Dash board has been added'));
    	}
		$Mode = 'RESET';
	}
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
        delete_data($selected_id,'sms_dashboard');         
		display_notification(_('Selected data has been deleted'));

	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//---------------------------------------------------------------

$result= "SELECT * FROM " . TB_PREF . "sms_dashboard";
$final = db_query($result);
start_form();
start_table(TABLESTYLE, "width=40%");
$th = array(_('Class'), _('Notice'),'', '');
table_header($th);
$k = 0; //row colour counter

while ($myrow = db_fetch($final)) 
{
    
	alt_table_row_color($k);
	label_cell($myrow["class"]);
        label_cell($myrow["notice"]);
//        label_cell($myrow["start_time"]);
//        label_cell($myrow["end_time"]);
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
	end_row();
}

end_table();

echo '<br>';
start_table();

get_student_clas(_("Select Class:"), 'app_class', $_POST['app_class'], 'Select Class', true);
br();

textarea_row("Notice", "notice", $_POST['notice'],30,5);

end_table();
//---------------------------------------------------------------


start_table(TABLESTYLE2,'width=40%');

if ($selected_id != -1) 
{

 	if ($Mode == 'Edit') {
            
            $rep =get_sql_for_edit($selected_id);
            
         
                $result =db_fetch(db_query($rep, "could not get data"));
                 //display_error($_POST['app_class']);
		$_POST['app_class'] = $result['class_name'];
                $_POST['notice'] = $result["notice"];
               
	}
	hidden('id', $selected_id);
}

end_table();
br();

submit_add_or_update_center($selected_id == -1, '', true);
end_form();

end_page();

?>
