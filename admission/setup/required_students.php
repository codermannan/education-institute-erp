<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = 'SS_SMS_REQ_STDNT_SETING';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/sms/includes/db/student_db.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/applicant_info_ui_lists.inc");

$js = "";
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "Seats Settings"), false, false, "", $js);

simple_page_mode(true);
//---------------------------------------------------------------------------------------------
//
//function check_data()
//{
//	if (!is_date($_POST['from_date']) || is_date_in_schoolyears($_POST['from_date']))
//	{
//		display_error( _("Invalid BEGIN date in school year."));
//		set_focus('from_date');
//		return false;
//	}
//	if (!is_date($_POST['to_date']) || is_date_in_schoolyears($_POST['to_date']))
//	{
//		display_error( _("Invalid END date in school year."));
//		set_focus('to_date');
//		return false;
//	}
////	if (!check_begin_end_date($_POST['from_date'], $_POST['to_date']))
//	{
//		display_error( _("Invalid BEGIN or END date in school year."));
//		set_focus('from_date');
//		return false;
//	}
//	if (date1_greater_date2($_POST['from_date'], $_POST['to_date']))
//	{
//		display_error( _("BEGIN date bigger than END date."));
//		set_focus('from_date');
//		return false;
//	}
//	return true;
//}

function handle_submit()
{
	global $selected_id, $Mode;

	$ok = true;
	if ($selected_id != -1)
	{
		
   			update_required_stud($selected_id,$_POST['present_stud'],$_POST['required_stud']);
			display_notification(_('Selected data has been updated'));
			
	}
	else
	{
			//return false;
   		add_required_stud($_POST['class_id'], $_POST['present_stud'], $_POST['required_stud']);
		display_notification(_('New required seats has been added'));
	}
	$Mode = 'RESET';
}

//---------------------------------------------------------------------------------------------



//---------------------------------------------------------------------------------------------

function display_required_stud()
{
    $sql= "select sr.*,sc.class_name from " . TB_PREF . "sms_seat_required sr
           LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON sr.st_class = sc.id";
    
    $result= db_query($sql, 'cannt get required settings');

	start_form();
	start_table(TABLESTYLE);

	$th = array(_("Class"), _("Present Students"), _("Required Students"), "", "");
	table_header($th);

	$k=0;
	while ($myrow=db_fetch($result))
	{
   		alt_table_row_color($k);
		label_cell($myrow['class_name']);
		label_cell($myrow['present_student']);
                label_cell($myrow['required_seat']);
		edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	        delete_button_cell("Delete".$myrow['id'], _("Delete"));
		end_row();
	}

	end_table();
	end_form();
	//display_note(_("The marked School year is the current fiscal year which cannot be deleted."), 0, 0, "class='currentfg'");
}

//---------------------------------------------------------------------------------------------

function display_required_stud_settings($selected_id)
{
	global $Mode;

	start_form();
	start_table(TABLESTYLE2);
        
        if($selected_id != -1)
        {
            if($Mode == 'Edit')
            {
                 $myrow = data_retrieve("sms_seat_required", "id", $selected_id);
		$_POST['present_stud'] = $myrow["present_student"];
		$_POST['required_stud']  = $myrow["required_seat"];
            }
            
          hidden('selected_id', $selected_id);  
        }

	
	
        
       $query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
        combo_list_row(_("Allocated Class:"), 'class_id', $_POST['class_id'], 'Select Class', false, $query);
        
	text_row(_("Present Students"), 'present_stud', NULL, 30, 35);
        text_row(_("Required Students"), 'required_stud', NULL, 30, 35);
        

	end_table(1);

	submit_add_or_update_center($selected_id == -1, '', 'both');

	end_form();
}

//---------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{
	handle_submit();
}

//---------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	global $selected_id;
	delete_info($selected_id);
       
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
}
//---------------------------------------------------------------------------------------------

display_required_stud();

echo '<br>';

display_required_stud_settings($selected_id);

//---------------------------------------------------------------------------------------------

end_page();

?>
