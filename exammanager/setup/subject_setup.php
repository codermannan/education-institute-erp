<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_CLS_SBJCT_SETP';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/exammanager/includes/ui/exam_ui_lists.inc");
include_once($path_to_root . "/exammanager/includes/db/exam_db.inc");

page(_("Subject Entry"));

simple_page_mode(true);

//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['subject_name']) == 0) 
	{
		$input_error = 1;
		display_error(_("The subject name cannot be empty."));
		set_focus('subject_name');
                return false;
	}
        //.........................................
        if (strlen($_POST['class_id']) == '') 
	{
		$input_error = 1;
		display_error(_("Allocated Class must be selected."));
		set_focus('class_id');
                return false;
	}

       
    	if ($selected_id != -1) 
    	{                
		    update_subject_class($selected_id,$_POST['subject_name'],$_POST['subject_code'],$_POST['total'],$_POST['class_id'],$_POST['credit_mark'], $_POST['assign_sub']);    		
			display_notification(_('Selected subject name has been updated'));
    	} 
    	else 
    	{
		    add_subject($_POST['subject_name'], $_POST['subject_code'], $_POST['total'], $_POST['class_id'], $_POST['credit_mark'], $_POST['assign_sub']);
			display_notification(_('Subject has been added'));
    	}
		$Mode = 'RESET';
	
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
        global $selected_id;
        
        if($selected_id!=''){
           
            $stquery = "SELECT * FROM ".TB_PREF."sms_exam_attendence WHERE subject=".$selected_id;
            $stres = db_query($stquery);
            $strows = db_num_rows($stres);
            
            $mkquery = "SELECT * FROM ".TB_PREF."sms_exam_mark_entry WHERE subject=".$selected_id;
            $mktres = db_query($mkquery);
            $mktrows = db_num_rows($mktres);
            
            $exquery = "SELECT * FROM ".TB_PREF."sms_exam_setting WHERE subject=".$selected_id;
            $extres = db_query($exquery);
            $exrows = db_num_rows($extres);
            if($strows>0 OR $mktrows>0 OR $exrows>0){
                
                display_notification(_('Selected subject can not be deleted,because it has been used another table as foreign key'));
            }
            else{
               dynamic_delete('sms_subject',$selected_id);
                    display_notification(_('Selected subject has been deleted')); 
            }
       
        }
	
	
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}

//.............................................................../
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



//----------------------------------------------------------------------------------
if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

start_row();

 $query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class:"),'class', null, 'Select Class', false, $query);

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
br();

$a=get_sqlp_db_data();

$cols = array(
     _("Class")=>array('align'=>'center'),
    _("Subject Name")=>array('align'=>'center'),
    _("Subject Code")=>array('align'=>'center'),
    _("Total Marks")=>array('align'=>'center'),     
    _("Assign Subject")=>array('align'=>'center'),
    array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
    array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_subject', $a, $cols);

$table->width = "45%";

display_db_pager($table);
//$k = 0; //row colour counter

echo '<br>';
//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{   
 	if ($Mode == 'Edit') {
             $myrow = data_retrieve('sms_subject', 'id', $selected_id);
           
		$_POST['subject_name'] = $myrow["subject_name"];
                $_POST['sub_code'] = $myrow["sub_code"];
                $_POST['total'] = $myrow["total_mark"];
		$_POST['class_id']  = $myrow["class"];
//                $_POST['credit_mark']  = $myrow["credit"];
                $_POST['assign_sub']  = $myrow["assign_sub"];
	}
	hidden('selected_id', $selected_id);
	
}
text_row(_("Subject Name:"), 'subject_name', null, 40, 45); 
text_row(_("Subject Code:"), 'subject_code', $_POST['sub_code'], 40, 45); 

text_row(_("Total Marks:"), 'total', null, 40, 45); 

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_row(_("Allocated Class:"), 'class_id', $_POST['class_id'], 'Select Class', false, $query);

assing_subject(_("Assign Subject: "), 'assign_sub','', null, true, true);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}
?>
