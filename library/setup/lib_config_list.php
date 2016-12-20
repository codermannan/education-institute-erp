<?php
$page_security = 'SS_SMS_CLS_RTN_SETP';
$path_to_root="../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/library/includes/ui/library_ui_lists.inc");
include_once($path_to_root . "/library/includes/db/library_db.php");


page(_("Library Configuration List"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

        $input_error = 0;

	if (strlen($_POST['fine_day']) == '') 
	{
		$input_error = 1;
		display_error(_("Fine Day must be entered."));
		set_focus('fine_day');
	}
        
        if (strlen($_POST['q_length']) == '') 
	{
		$input_error = 1;
		display_error(_("Queue Length must be entered."));
		set_focus('q_length');
	}
        
        if (strlen($_POST['issue_period']) == '') 
	{
		$input_error = 1;
		display_error(_("Issue Period must be entered."));
		set_focus('issue_period');
	}
        
         if (strlen($_POST['issue_limit']) == '') 
	{
		$input_error = 1;
		display_error(_("Issue Limit must be entered."));
		set_focus('issue_limit');
	}
        
	if ($input_error !=1)
	{
    	if ($selected_id != -1) 
    	{ 
	    update_lib_config_list($selected_id,$_POST['fine_day'],$_POST['q_length'],$_POST['issue_period'],$_POST['date_reminder'],$_POST['hold_period'],$_POST['issue_limit']);    	    		
			display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{
           
            add_lib_config_list($_POST['fine_day'],$_POST['q_length'],$_POST['issue_period'],$_POST['date_reminder'],$_POST['hold_period'],$_POST['issue_limit']);    		
			display_notification(_('New Library Config has been added'));
               
    	}
		$Mode = 'RESET';
	}
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
       dynamic_delete('sms_lib_config',$selected_id);
               
		display_notification(_('Selected library Configuration  has been deleted'));
        
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------



$result = view_lib_config();
// form.......................................................
start_form();
start_table(TABLESTYLE, "width=80%");
$th = array(_('Sl.No.'),_('Fine Per Day'),_('Queue Length'), _('Issue Period'),_('Due Date Reminder'),_('On Hold Period'),_('Book Issue Limit'),'', '');
table_header($th);
$k = 0; //row colour counter
$sl = 1;
while ($myrow = db_fetch($result)) 
{
    
	alt_table_row_color($k);
        label_cell($sl);
        label_cell($myrow["fine_day"]);
	label_cell($myrow["queue_length"]);
        label_cell($myrow["issue_period"]);
        label_cell($myrow["due_date_reminder"]);
        label_cell($myrow["hold_period"]);
        label_cell($myrow["issue_limit"]);
      
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
	end_row();
         $sl++;
}

end_table();
end_form();
echo '<br>';
//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);


if ($selected_id != -1) 
{

 	if ($Mode == 'Edit') {
            
                $myrow = data_retrieve('sms_lib_config', 'id', $selected_id);       
                //$result =db_fetch(db_query($rep, "could not get subject wise teacher"));
                 //display_error($result['class']);
                $_POST['fine_day'] = $myrow['fine_day'];
		$_POST['q_length'] = $myrow['queue_length'];
                $_POST['issue_period'] = $myrow["issue_period"];
                $_POST['date_reminder'] = $myrow["due_date_reminder"];
                $_POST['hold_period'] = $myrow["hold_period"];
                $_POST['issue_limit'] = $myrow["issue_limit"];
	}
	hidden('id', $selected_id);
}

text_row(_("Fine Per Day:"),'fine_day',$_POST['fine_day'],20,30);
text_row(_("Queue Length:"),'q_length',$_POST['q_length'],20,30);
text_row(_("Issue Period:"),'issue_period',$_POST['issue_period'],20,30);
text_row(_("Due Date Reminder:"),'date_reminder',$_POST['date_reminder'],20,30);
text_row(_("On Hold Period:"),'hold_period',$_POST['hold_period'],20,30);
text_row(_("Book Issue Limit:"),'issue_limit',$_POST['issue_limit'],20,30);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

end_form();

end_page();

?>
