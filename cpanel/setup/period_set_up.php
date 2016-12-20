<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_XM_STNG';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/cpanel/includes/ui/cpanel_ui_lists.inc");
include_once($path_to_root . "/cpanel/includes/db/cpanel_db.inc");


page(_("Class Wise Period Set Up"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];


if(list_updated('school_year'))
$Ajax->activate('_page_body');

if(list_updated('class'))
$Ajax->activate('_page_body');

if(list_updated('section'))
$Ajax->activate('_page_body');

$Ajax->activate('_page_body');


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
   
	$input_error = 0;

	
//        
//        if (strlen($_POST['class']) == '') 
//	{
//		$input_error = 1;
//		display_error(_("Class must be selected."));
//		set_focus('class');
//	}
////        if (strlen($_POST['subject']) == '') 
////	{
////		$input_error = 1;
////		display_error(_("Subject must be selected."));
////		set_focus('subject');
////	}
//      
//        if (strlen($_POST['exam_name']) == '') 
//	{
//		$input_error = 1;
//		display_error(_("Exam name must be selected."));
//		set_focus('exam_name');
//	}
//        
//        if (strlen($_POST['period_name']) == '') 
//	{
//		$input_error = 1;
//		display_error(_("no of period must be entered."));
//		set_focus('period_name');
//	}

	if ($input_error !=1)
	{
    	if ($selected_id != -1) 
    	{ 
               update_period_settings($selected_id,$_POST['class'],$_POST['section'],$_POST['period_name']); 
                    
			display_notification(_('Selected exam setting has been updated'));
    	} 
    	else 
    	{

            add_period_setting($_POST['class'],$_POST['section'],$_POST['period_name']);
			display_notification(_('New exam setting has been added'));
    	} 
		$Mode = 'RESET';
	}
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
              delete_data($selected_id,"sms_period_set_up");
		display_notification(_('Selected data has been deleted'));

	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------

$sql = get_period_data();
$result = db_query($sql, "could not get exam");

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(_('Class'), _('Section'), _('No of Period'), '','');
table_header($th);
$k = 0; //row colour counter

while ($myrow = db_fetch($result)) 
{
	alt_table_row_color($k);
//display_error($myrow["school_year"]);
	label_cell($myrow["class_name"]);
        label_cell($myrow["session_name"]);
        label_cell($myrow["no_of_period"]);
   
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
           
            
                $myrow = data_retrieve("sms_period_set_up", "id", $selected_id);
		$_POST['class']  = $myrow["class_name"];
                $_POST['section'] = $myrow["section"];
               // display_error($myrow["section"]);
                $_POST['period_name'] = $myrow["no_of_period"];
             
	}
	hidden('id', $selected_id);
}




$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_row(_("Class :"), 'class', $_POST['class_name'], 'Select Class', true, $query);

$query=array('id','session_name','sms_session','class',$_POST['class']);
combo_list_row(_("Section :"), 'section', $_POST['session_name'], 'Select Session', true, $query);

text_row(_("No of period:"),'period_name', null, 35);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

end_form();

end_page();

?>
