<?php
/**********************************************************************
   developed by Mannan
***********************************************************************/
$page_security = 'SS_SMS_STDNT_SECTN_MANGMNT';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");

$js = "";
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "Section Settings"), false, false, "", $js);

simple_page_mode(true);

//-----------------------------------------------------------------------
 
    if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
    {
        $input_error = 0;
    if(strlen(($_POST['class_id']) == 0))
    {
        $input_error = 1;
     
        display_error("Class can not be empty");
        set_focus('class_id');
        
    }
    
    if(strlen($_POST['section']) == 0)
    {
        $input_error = 1;
       
        display_error("Section can not be empty");
        set_focus('section');
        
    }
	
	
        if($input_error != 1)
        {
            global $selected_id, $Mode;

	  $ok = true;
        
	if ($selected_id != -1)
	{
   			update_section($selected_id,$_POST['class_id'],$_POST['section']);
                           
			display_notification(_('Selected data has been updated'));
			
	}
        
	else
	{
            $sql= "SELECT *  FROM 0_sms_session WHERE session_name=" . db_escape($_POST['section']) . "AND class=" . db_escape($_POST['class_id']);
            $result = db_fetch((db_query($sql)));
            
            if($_POST['section'] == $result['session_name'] && $_POST['class_id'] == $result['class'] )
            {
                
                display_notification('Section Already exits');
            }
	else
              {
                
   	        add_section($_POST['class_id'], $_POST['section']);
		display_notification(_('New data has been added'));
	        }
	$Mode = 'RESET';
        
        }  
     }
}


function display_required_data()
{
    $sql= "SELECT ss.session_name,ss.id, sc.class_name FROM 0_sms_session ss LEFT JOIN 0_sms_create_stud_class sc ON ss.class = sc.id";
    $result= db_query($sql, 'cannot get required data');

	start_form();
	start_table(TABLESTYLE, 'width= 30%');

	$th = array(_("Students Class"), _("Section"),"", "");
	table_header($th);

	$k=0;
	while ($myrow=db_fetch($result))
	{
   		alt_table_row_color($k);
		label_cell($myrow['class_name']);
		label_cell($myrow['session_name']);
                
		edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	        delete_button_cell("Delete".$myrow['id'], _("Delete"));
		end_row();
	}

	end_table();
	end_form();
	
}

function display_section($selected_id)
{
	global $Mode;

	start_form();
	start_table(TABLESTYLE2);
        
        if($selected_id != -1)
        {
            if($Mode == 'Edit')
            {
                 $myrow = data_retrieve("sms_session", "id", $selected_id);
		$_POST['class_id'] = $myrow["class"];
		$_POST['section']  = $myrow["session_name"];
            }
            
          hidden('id', $selected_id);  
        }

	
	$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
     
        combo_list_row(_("Allocated Class:"), 'class_id', $_POST['class_id'], 'Select Class', false, $query);
        
	text_row(_("Section"), 'section', NULL, 30, 35);
        
        

	end_table(1);

	submit_add_or_update_center($selected_id == -1, '', 'both');

	end_form();
}

//---------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	global $selected_id;
	delete_section($selected_id);
      
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
}
//---------------------------------------------------------------------------------------------

display_required_data();

echo '<br>';

display_section($selected_id);

//---------------------------------------------------------------------------------------------

end_page();

?>