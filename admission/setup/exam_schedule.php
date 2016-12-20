<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_CNDIDT_XM_SDL';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Candidate Exam Schedule"), @$_REQUEST['popup'], false, "", $js); 

simple_page_mode(true);
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
   
	$input_error = 0;

	if (strlen($_POST['exam_date']) == 0) 
	{
		$input_error = 1;
		display_error(_("The exam date cannot be empty."));
		set_focus('description');
	}
        
        if (strlen($_POST['exam_venue']) == 0) 
	{
		$input_error = 1;
		display_error(_("The exam vanue cannot be empty."));
		set_focus('description');
	}
        
//        if ($time == 0) 
//	{
//		$input_error = 1;
//		display_error(_("The srart hour cannot be empty."));
//		set_focus('description');
//	}
        
//        if ($time == 0) 
//	{
//		$input_error = 1;
//		display_error(_("The end hour cannot be empty."));
//		set_focus('description');
//	}
        
        
        if (strlen($_POST['status']) == 0) 
	{
		$input_error = 1;
		display_error(_("The status cannot be empty."));
		set_focus('description');
	}
        
        $time = $_POST['start_hr'].':'.$_POST['start_mm'].':'.'00';
       
	if ($input_error !=1)
	{
    	if ($selected_id != -1) 
    	{ 
		    update_exam_schedule($selected_id,$_POST['exam_date'], $_POST['exam_venue'], $time,$_POST['status']);    		
			display_notification(_('Selected Schedule has been updated'));
    	} 
    	else 
    	{
		    add_exam_schedule($_POST['exam_date'],$_POST['exam_venue'],$time,$_POST['status']);
			display_notification(_('Schedule has been added'));
    	}
		$Mode = 'RESET';
	}
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{

	delete_selected_schedule($selected_id);
		display_notification(_('Selected group has been deleted'));
    
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------
function edit_link($row) 
{
    
        return "<center>".button("Edit".$row["id"], _("Edit"), _("Edit"), ICON_EDIT).
        "</center>";

}

function delete_link($row) 
{
    submit_js_confirm("Delete".$row['id'],
                    sprintf(_("Are you sure you want to delete ?")));
	
     return  "<center>".button("Delete".$row["id"], _("Delete"), _("Delete"), ICON_DELETE).
        "</center>";
}
function status($row) 
{   
    if($row['close']==1){
        return 'Open';
    }
    else{
        return 'Close';
    }
}

//................................................................

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

start_row();
date_cells(_("Exam Date:"), 'xm_date', '', true);
submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
br();

if(isset ($_POST['Search']))
{
$pdt=$_POST['xm_date'];

}

$ec=scrh_exam_scdle($pdt);

$cols = array(
     _("Exam Date")=>array('align'=>'center'),
    _("Exam Venue")=>array('align'=>'center'),
    _("Exam Time")=>array('align'=>'center'),
    _("Status")=>array('fun'=>'status', 'align'=>'center'),
    array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
    array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_exam_schedule', $ec, $cols);

$table->width = "40%";

display_db_pager($table);
echo '<br>';

//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
             
               $myrow = data_retrieve('sms_exam_schedule', 'id', $selected_id);

		$_POST['exam_date'] = $myrow["exam_date"];
		$_POST['exam_venue']  = $myrow["exam_venue"];
                $_POST['exam_time'] = $myrow["exam_time"];
                $_POST['status'] = $myrow["close"];
                
	}
	hidden('selected_id', $selected_id);

}

date_row(_("Exam Date:"), 'exam_date');
 
text_row(_("Exam Venue:"), 'exam_venue', $_POST['exam_venue'], 45, 50);

digital_time(_("Exam Time:"), 'start_hr', 'start_mm');
schedule_list_cells("Status","status");


end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>


