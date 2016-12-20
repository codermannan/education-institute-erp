<?php

$page_security = 'SS_SMS_APLCNT_PAYMNT_RCV';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/sms_ui.inc");
include_once($path_to_root . "/sms/includes/db/sms_db.php");


page(_("Group Set Up"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
$Ajax;
if($_POST['id'])
    $selected_id=$_POST['id'];

$Ajax->activate('_page_body');

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['group_name']) == 0) 
	{
		$input_error = 1;
		display_error(_("The group name must be entered"));
		set_focus('group_name');
	}
    
        if($input_error != 1)
        {
        
    	if ($selected_id != -1) 
    	{ 
            update_group($selected_id,$_POST['group_name']);    		
                   
                    display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{ 
                add_group($_POST['group_name']);
                    display_notification(_('Data  has been added'));
    	}
		
        }         
	$Mode = 'RESET';
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{

	delete_data($selected_id, 'sms_group_setup');
		display_notification(_('Selected data has been deleted'));
	
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------

$sql = "SELECT * FROM " . TB_PREF . "sms_group_setup";
$result = db_query($sql, "could not get value");


start_form();
start_table(TABLESTYLE, "width=20%");
$th = array(_('Group Name'),'Edit','Delete');
table_header($th);
$k = 0; //row colour counter

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
    	label_cell($myrow["group_name"]);
      
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
	end_row();
}
     
 
end_table();
end_form();
echo '<br>';
start_form();

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
                $myrow = data_retrieve("sms_group_setup", "id", $selected_id);
		$_POST['group_name'] = $myrow["group_name"];
		 
	}
	hidden('selected_id', $selected_id);
}
 
text_row(_("Group Name:"), 'group_name', null, 25, 30);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

end_form();

end_page();
?>



 























