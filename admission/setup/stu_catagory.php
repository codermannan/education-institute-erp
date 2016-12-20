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

page(_("Student Category"));

simple_page_mode(true);
//-------------------------------------------------------------
$Ajax;
if($_POST['id'])
    $selected_id=$_POST['id'];


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

    $input_error = 0;
    if (strlen($_POST['cat_name']) == '') 
	{
            $input_error = 1;
            display_error(_("Catagory Name must be selected."));
            set_focus('cat_name');
	}
        
    if (strlen($_POST['cat_description']) == '') 
	{
            $input_error = 1;
            display_error(_("Catagory Description must be entered."));
            set_focus('cat_description');
	}
       
    if (strlen($_POST['ratio']) == '') 
	{
            $input_error = 1;
            display_error(_("Ratio must be entered."));
            set_focus('ratio');
	}
    if ($input_error !=1)
	{
    	    if ($selected_id != -1) 
                { 
                    update_category($selected_id,$_POST['cat_name'],$_POST['cat_description'] ,input_num('ratio', 0));    	    		
                    display_notification(_('Selected data has been updated'));
                } 
    	    else 
                {
                    add_category($_POST['cat_name'],$_POST['cat_description'] ,input_num('ratio', 0));    		
                    display_notification(_('New catagory has been added'));

                }
            $Mode = 'RESET';
	}
}
//------------------------------------------------------------
function can_delete($selected_id)
{
    if (key_in_foreign_table($selected_id, 'sms_student_scholarship', 'student_cat'))	
        {
            display_error(_("Cannot delete this Student category because it already in use !"));
            return false;
        }
    else
        {
           return true;
        }
                
}
if ($Mode == 'Delete')
{
    if (can_delete($selected_id))
        {
            dynamic_delete('sms_student_category',$selected_id);               
            display_notification(_('Selected artical has been deleted'));
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
    $sql=view_category();

    $cols = array(
                  _("#")=>array('align'=>'center'),
                  _("Category Name")=>array('align'=>'center'),
                  _("Category Description")=>array('align'=>'center'),
                  _("Ratio"), 
                  array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
                  array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
                );
$table = & new_db_pager('sms_student_category', $sql, $cols, null, null,15);

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
            $myrow = data_retrieve('sms_student_category', 'id', $selected_id);       
            $_POST['cat_name'] = $myrow['cat_name'];
            $_POST['cat_description'] = $myrow['cat_description'];
            $_POST['ratio'] = $myrow["ratio"];
        }
    hidden('selected_id', $selected_id);
}

text_row(_("Category Name:"),'cat_name',$_POST['cat_name'],35,30);
textarea_row(_("Category Description:"), 'cat_description', $_POST['cat_description'], 35, 3);
qty_row(_("Ratio:"),'ratio',$_POST['ratio']);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);


if (!@$_GET['popup'])
    {
        end_form();
        end_page();
    }
?>
