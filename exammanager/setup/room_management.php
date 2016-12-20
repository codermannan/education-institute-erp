<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_XM_ROM_STNG';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/exammanager/includes/ui/exam_ui_lists.inc");
include_once($path_to_root . "/exammanager/includes/db/exam_db.inc");

page(_("Room Set Up"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['room_no']) == 0) 
	{
		$input_error = 1;
		display_error(_("The room no cannot be empty."));
		set_focus('room_no');
                return false;
	}
        
        if (strlen($_POST['capacity']) == 0) 
	{
		$input_error = 1;
		display_error(_("The room capacity cannot be empty."));
                set_focus('capacity');
                return false;
	}

    	if ($selected_id != -1) 
    	{ 
		    update_room_setup($selected_id,$_POST['room_name'], $_POST['room_no'],$_POST['capacity']);    		
			display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{
            $re= "SELECT room_name,room_no FROM " . TB_PREF . "sms_room_setup WHERE room_name=" . db_escape($_POST['room_name']) . "AND room_no=" . db_escape($_POST['room_no']);
            $res= db_fetch(db_query($re));
            
            if($_POST['room_name'] == $res['room_name'] && $_POST['room_no'] == $res['room_no'])
            {
                display_error('data already exits');
            }
            
            else
            {
		    add_room_setup($_POST['room_name'],$_POST['room_no'],$_POST['capacity']);
			display_notification(_('Room set up has been added'));
            }
    	}
		$Mode = 'RESET';
	
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{

	delete_room_set_up($selected_id);
		display_notification(_('Selected data has been deleted'));
	
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------


$sql= "SELECT * FROM " . TB_PREF . "sms_room_setup";


$result = db_query($sql, "could not get room info");

start_form();

start_table(TABLESTYLE2, "width=30%");

$th = array(_('Room Name'), _('Room No'),_('Capacity'), '', '');

table_header($th);
$k = 0; //row colour counter
while ($myrow = db_fetch($result)) 
{
    
	
	alt_table_row_color($k);
start_row();
	label_cell($myrow["room_name"]);
        label_cell($myrow["room_no"]);
        label_cell($myrow["capacity"]);
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
	end_row();
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
                $myrow = data_retrieve("sms_room_setup", "id", $selected_id);
		$_POST['room_name'] = $myrow["room_name"];
		$_POST['room_no']  = $myrow["room_no"];
                $_POST['capacity']  = $myrow["capacity"];
	}
	hidden('id', $selected_id);
}

text_row(_("Room Name:"), 'room_name', null, 30, 25); 

text_row(_("Room No :"), 'room_no', null, 30, 25); 

text_row(_("Capacity:"), 'capacity', null, 30, 25); 

//textarea_row(_("Description:"), 'description', null, 30,3);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

end_form();

end_page();

?>

