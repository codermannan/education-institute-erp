<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_CLS_RTN_SETP';
$path_to_root="../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/applicant_info_ui_lists.inc");
include_once($path_to_root . "/sms/includes/db/library_db.php");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);

page(_("Maximum Number of Request"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];




if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
    //print_r($_FILES['mannan']);
    //display_error($_FILES['mannan']['name']);
    $input_error = 0;

    if (strlen($_POST['max_request']) == '') 
    {
            $input_error = 1;
            display_error(_("Maximum request number must be entered."));
            set_focus('max_request');
    }

    if (strlen($_POST['duration_request']) == '') 
    {
            $input_error = 1;
            display_error(_("Duration of a active request must be selected."));
            set_focus('duration_request');
    }
    if (strlen($_POST['fine']) == '') 
    {
            $input_error = 1;
            display_error(_("Fine must be selected."));
            set_focus('fine');
    }
    if ($input_error !=1)
    {
        if ($selected_id != -1) 
        { 
//	    update_location_setup($selected_id,$_POST['location_name'],$_POST['shelf'],$_POST['shelf_row'],$_POST['shelf_column']);    	    		
//			display_notification(_('Selected data has been updated'));
        } 
        else 
        {

           add_book_type($_POST['book_type'],$_POST['status']);    		
           display_notification(_('New Book Type has been added'));
        }
      //  $Mode = 'RESET';
    }
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
        dynamic_delete('sms_lib_book_type_setup',$selected_id);
               
		display_notification(_('Selected book type has been deleted'));
        
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------



$result =  view_book_type();
// form.......................................................
start_form();
start_table(TABLESTYLE, "width=80%");
$th = array(_('Maximum Number of Request'),_('Duration of an active request in a month(in hours)'));
table_header($th);
$k = 0; //row colour counter

while ($myrow = db_fetch($result)) 
{
    
	alt_table_row_color($k);
        //label_cell($myrow["image"]);
       // label_cell($myrow["book_type"]);
	//label_cell($myrow["address"]);
        //label_cell($myrow["status"]);
 	//edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	//delete_button_cell("Delete".$myrow['id'], _("Delete"));
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
            
                $myrow = data_retrieve('sms_lib_book_type_setup', 'id', $selected_id);       
              
                $_POST['book_type'] = $myrow['book_type'];
                $_POST['status'] = $myrow["status"];
	}
	hidden('id', $selected_id);
}

text_row(_("Maximum Number of Request:"),'max_request',$_POST['max_request'],30,30);
text_row(_("Duration of a Active Request(In Hour):"),'duration_request',$_POST['duration_request'],30,30);
text_row(_("Maximum duration of book return(In Days):"),'duration return',$_POST['duration_return'],30,30);
text_row(_("Fine(Per Day):"),'fine',$_POST['fine'],30,30);


end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

end_form();

end_page();

?>
