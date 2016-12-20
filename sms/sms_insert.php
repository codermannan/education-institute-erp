<?php

$page_security = 'SS_SMS_LIB_BENTRY';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/applicant_info_ui_lists.inc");
include_once($path_to_root . "/sms/includes/ui/sms_ui.inc");
include_once($path_to_root . "/sms/includes/db/sms_db.php");

$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(450, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

  page(_($help_context = "Send SMS"), false, false, "", $js);

//-------------------------------------------------------------------------------------
  
if($_GET['id']){
    $selected_id = $_GET['id'];
}

$Ajax->activate('_page_body');
 
//-------------------------------------------------------------------------------------

if (isset($_POST['add']) || isset($_POST['update'])) 
{

	$input_error = 0;

        if (strlen($_POST['message']) == '') 
	{
		$input_error = 1;
		display_error(_("Message must be added."));
		set_focus('message');
                return false;
	}
        	
       $time = $_POST['start_hr'].':'.$_POST['start_mm'].':'.'00';
       $n_date=date2sql($_POST['sms_date']);
       
    	if ($selected_id) 
		{
			
    		     update_sms($selected_id,$n_date,$time,$_POST['message']);
                           
				$Ajax->activate('id'); 
				display_notification(_("Selected message record has been updated."));
			}
		
    	else 
		{
                
           add_sms( $n_date, $time, $_POST['message']);
                           
                      display_notification(_("New message has been added."));
					
		}
                set_focus('id');
		$Ajax->activate('_page_body');
	}

//--------------------------------------------------------------------------------------

if (isset($_POST['delete'])) 
{

	 dynamic_delete('sms_content', $selected_id);
		display_notification(_('Selected message has been deleted'));
		unset($_POST['id']);
		$Ajax->activate('_page_body');
	
} 

//-------------------------------------------------------------------------------------

start_form();	
br(1);
start_table(TABLESTYLE2);


    start_row();
        label_cell('<b>Message</b>','align=center colspan=10');
    end_row();
    
if ($selected_id != "") {
	
	$myrow = view_sms($selected_id);
                $_POST['sms_date'] = $myrow["sms_date"];
                $_POST['sms_time']  = $myrow["sms_time"];
                $_POST['message']  = $myrow["message"];
               
        hidden('selected_id', $selected_id);

} 

date_row(_("Date:"), 'sms_date');
digital_time(_("Time:"), 'start_hr', 'start_mm');

$query=array('id','group_name','sms_group_setup');
combo_list_row(_("Group Name :"), 'group_name', '', 'Select Group Name', false, $query);  
textarea_row(_("Message:"), 'message', $_POST['message'], 30, 3);

end_table(1);

if ($selected_id == "") 
{
	submit_center('add', _("Send"), true, '', 'default');
} 
else 
{
    submit_center_first('update', _("Update SMS"), '', 'default');
    submit_center_last('delete', _("Delete SMS"), '',true);
}
end_form();

end_page();

?>
