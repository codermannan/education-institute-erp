<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_CLS_RTN_SETP';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/library/includes/db/library_db.php");


page(_("Location Setup"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

        $input_error = 0;

	if (strlen($_POST['location_name']) == '') 
	{
		$input_error = 1;
		display_error(_("Location name must be entered."));
		set_focus('location_name');
	}
        
        if (strlen($_POST['shelf']) == '') 
	{
		$input_error = 1;
		display_error(_("Shelf number must be entered."));
		set_focus('shelf');
	}
        
        if (strlen($_POST['shelf_row']) == '') 
	{
		$input_error = 1;
		display_error(_("Row must be entered."));
		set_focus('row');
	}
        
         if (strlen($_POST['shelf_column']) == '') 
	{
		$input_error = 1;
		display_error(_("Column must be entered."));
		set_focus('column');
	}
        
	if ($input_error !=1)
	{
    	if ($selected_id != -1) 
    	{ 
	    update_location_setup($selected_id,$_POST['location_name'],$_POST['shelf'],$_POST['shelf_row'],$_POST['shelf_column']);    	    		
			display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{
           
            add_location_setup($_POST['location_name'],$_POST['shelf'],$_POST['shelf_row'],$_POST['shelf_column']);    		
			display_notification(_('New location has been added'));
               
    	}
		$Mode = 'RESET';
	}
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
       dynamic_delete('sms_lib_location_setup',$selected_id);
               
		display_notification(_('Selected location has been deleted'));
        
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------



$result =  view_location_setup();
// form.......................................................
start_form();
start_table(TABLESTYLE, "width=80%");
$th = array(_('Location'),_('Shelf'),_('Row'), _('Column'),'', '');
table_header($th);
$k = 0; //row colour counter

while ($myrow = db_fetch($result)) 
{
	alt_table_row_color($k);
        label_cell($myrow["location_name"]);
	label_cell($myrow["shelf"]);
        label_cell($myrow["shelf_row"]);
        label_cell($myrow["shelf_column"]);
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
            
                $myrow = data_retrieve('sms_lib_location_setup', 'id', $selected_id);       
                //$result =db_fetch(db_query($rep, "could not get subject wise teacher"));
                 //display_error($result['class']);
                $_POST['location_name'] = $myrow['location_name'];
		$_POST['shelf'] = $myrow['shelf'];
                $_POST['shelf_row'] = $myrow["shelf_row"];
                $_POST['shelf_column'] = $myrow["shelf_column"];
	}
	hidden('id', $selected_id);
}

text_row(_("Location:"),'location_name',$_POST['location_name'],20,30);
text_row(_("Shelf No:"),'shelf',$_POST['shelf'],20,30);
text_row(_("Row:"),'shelf_row',$_POST['shelf_row'],20,30);
text_row(_("Column:"),'shelf_column',$_POST['shelf_column'],20,30);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

end_form();

end_page();

?>
