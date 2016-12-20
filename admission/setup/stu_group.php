<?php

$page_security = 'SS_SMS_CRT_STDNT_CLS';
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

page(_("Student Group"));

simple_page_mode(true);
//-------------------------------------------------------------
$Ajax;
if($_POST['id'])
    $selected_id=$_POST['id'];


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

    $input_error = 0;
    if (strlen($_POST['group_name']) == '') 
	{
            $input_error = 1;
            display_error(_("Group Name must be selected."));
            set_focus('group_name');
	}
        
    if (strlen($_POST['group_description']) == '') 
	{
            $input_error = 1;
            display_error(_(" Description must be entered."));
            set_focus('group_description');
	}
       

    if ($input_error !=1)
	{
    	    if ($selected_id != -1) 
                { 
                     update_group($selected_id,$_POST['group_name'],$_POST['group_description']);    	    		
                    display_notification(_('Selected data has been updated'));
                } 
    	    else 
                {
                    add_group($_POST['group_name'],$_POST['group_description']);    		
                    display_notification(_('New data has been added'));

                }
            $Mode = 'RESET';
	}
}
//-------------------------------------------------------------------------------------
if ($Mode == 'Delete')
{
    if ($selected_id)
        {
           //display_error($selected_id);
            delete_group($selected_id)  ;           
            display_notification(_('Selected data has been deleted'));
        }        
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    $selected_id = -1;
    unset($_POST);
}
//--------------------------------------------------------------------
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
//

//-------------------------------------------------------------------
if (!@$_GET['popup'])
    start_form();
    br();
    $sql=view_group();

    $cols = array(
                  _("#")=>array('align'=>'center'),
                  _("Group Name")=>array('align'=>'center'),
                  _("Group Description")=>array('align'=>'center'),
                   
                  array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
                  array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
                );
$table = & new_db_pager('sms_applicant_group', $sql, $cols, null, null,15);

$table->width = "40%";

display_db_pager($table);
//$k = 0; //row colour counter

echo '<br>';
//--------------------------------------------------------------------
start_form();

start_table(TABLESTYLE2);


if ($selected_id != -1) 
{
    if ($Mode == 'Edit')
        {
            $myrow = data_retrieve('sms_applicant_group', 'id', $selected_id);       
            $_POST['group_name'] = $myrow['group_name'];
            $_POST['group_description'] = $myrow['description'];
            
        }
    hidden('selected_id', $selected_id);
}

text_row(_("Group Name:"),'group_name',$_POST['group_name'],35,30);
textarea_row(_("Group Description:"), 'group_description', $_POST['group_description'], 35, 3);


end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);


if (!@$_GET['popup'])
    {
        end_form();
        end_page();
    }
?>
