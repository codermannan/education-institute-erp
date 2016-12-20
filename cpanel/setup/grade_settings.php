<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_GRADE_STNG';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/cpanel/includes/ui/cpanel_ui_lists.inc");
include_once($path_to_root . "/cpanel/includes/db/cpanel_db.inc");



page(_("Grade Settings"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
$Ajax;
if($_POST['id'])
    $selected_id=$_POST['id'];

if(list_updated('school_year'))
$Ajax->activate('_page_body');

if(list_updated('class'))
$Ajax->activate('_page_body');



if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

        if (strlen($_POST['start']) == 0) 
	{
		$input_error = 1;
		display_error(_("The starting mark cannot be empty."));
		set_focus('exam_name');
	}
        
        if (strlen($_POST['end']) == 0) 
	{
		$input_error = 1;
		display_error(_("The ending mark cannot be empty."));
		set_focus('exam_name');
	}
        
        if (strlen($_POST['letter_grade']) == 0) 
	{
		$input_error = 1;
		display_error(_("The grade cannot be empty."));
		set_focus('exam_name');
	}
        
        if (strlen($_POST['point']) == 0) 
	{
		$input_error = 1;
		display_error(_("The point cannot be empty."));
		set_focus('exam_name');
	}
        
        if($input_error != 1)
        {
        
    	if ($selected_id != -1) 
    	{ 
               
		    update_grade_settings($selected_id,$_POST['start'],$_POST['end'],$_POST['letter_grade'],$_POST['point'],$_POST['stpoint'],$_POST['endpoint']);    		
                   
                    display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{
		    add_grade_settings($_POST['start'],$_POST['end'],$_POST['letter_grade'],$_POST['point'],$_POST['stpoint'],$_POST['endpoint']);
                    display_notification(_('Grade settings  has been added'));
    	}
		$Mode = 'RESET';
        }         
	
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{

	delete_data($selected_id,'sms_grade_set_up');
		display_notification(_('Selected data has been deleted'));
	
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------

$sql = get_grade_settings();
$result = db_query($sql, "could not get exam name");

start_form();
start_table(TABLESTYLE, "width=70%");
$th = array(_('Start Mark'),('End Mark'),('Letter Grade'),('Point'),('Start Point'),('End Point'),'', '');
table_header($th);
$k = 0; //row colour counter

while ($myrow = db_fetch($result)){
	alt_table_row_color($k);
        label_cell($myrow["start_mark"]);
        label_cell($myrow["end_mark"]);
        label_cell($myrow["letter_grade"]);
        label_cell($myrow["cpoint"]);
        label_cell($myrow["start_point"]);
        label_cell($myrow["end_point"]);
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
                $myrow = data_retrieve("sms_grade_set_up", "id", $selected_id);
		
                $_POST['start']  = $myrow["start_mark"];
                $_POST['end']  = $myrow["end_mark"];
                $_POST['letter_grade']  = $myrow["letter_grade"];
                $_POST['point']  = $myrow["cpoint"];
                $_POST['stpoint']  = $myrow["start_point"];
                $_POST['endpoint']  = $myrow["end_point"];
	}
	hidden('id', $selected_id);
}

text_row(_('Start Mark:'),'start', $_POST['start'], 35, 30);

text_row(_('End Mark:'),'end', $_POST['end'], 35, 30);

text_row(_('Letter Grade:'),'letter_grade', $_POST['letter_grade'], 35, 30);

text_row(_('Point:'),'point', $_POST['point'], 35, 30);
text_row(_('Start Point:'),'stpoint', $_POST['stpoint'], 35, 30);
text_row(_('End Point:'),'endpoint', $_POST['endpoint'], 35, 30);


end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

end_form();

end_page();

?>

 























