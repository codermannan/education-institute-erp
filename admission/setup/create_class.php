<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_CRT_STDNT_CLS';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");


page(_("Students Class"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
   
	$input_error = 0;

	if (strlen($_POST['class_name']) == 0) 
	{
		$input_error = 1;
		display_error(_("The class name cannot be empty."));
		set_focus('class_name');
	}
        
        if (strlen($_POST['school_set']) == '') 
	{
		$input_error = 1;
		display_error(_("School set must be selected."));
		set_focus('school_set');
	}

	if ($input_error !=1)
	{
    	if ($selected_id != -1) 
    	{ 
		    update_student_class($selected_id,$_POST['class_name'], $_POST['school_set'],$_POST['capacity'],$_POST['hierarchy']);    		
			display_notification(_('Selected class has been updated'));
    	} 
    	else 
    	{
		    add_student_class($_POST['class_name'],$_POST['school_set'],$_POST['capacity'],$_POST['hierarchy']);
			display_notification(_('New class has been added'));
    	}
		$Mode = 'RESET';
	}
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
        if($selected_id!=''){
            $query = "SELECT * FROM ".TB_PREF."sms_students_details WHERE class=".$selected_id;
            $res = db_query($query);
            $rows = db_num_rows($res);
            
            $stquery = "SELECT * FROM ".TB_PREF."sms_student WHERE st_class=".$selected_id;
            $stres = db_query($stquery);
            $strows = db_num_rows($stres);
            
            if($rows>0 OR $strows>0){
                
                display_notification(_('Selected class can not be deleted,because it has been used another table as foreign key'));
            }
            else{
                delete_data_by_id($selected_id,'sms_create_stud_class');
                  display_notification(_('Selected class has been deleted')); 
            }
//         
        }
 
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
function set($row) 
{
    
      if($row['school_set']==1){
        return 'Set 1';
    }
    else{
        return 'Set 2';
    }

}

//...............................................................
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
//$sql = "SELECT class_name,school_set,id FROM ".TB_PREF."sms_create_stud_class";

$cl=srch_create_class();
$cols = array(
    _("Class Name")=>array('align'=>'center'),
    _("Capacity")=>array('align'=>'center'),
    _("School Set")=>array('fun'=>'set','align'=>'center'),
     _("Hierarchy Number")=>array('align'=>'center'),
    array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
    array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_create_stud_class', $cl, $cols);

$table->width = "30%";

display_db_pager($table);

echo '<br>';
//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

if ($selected_id != -1){
 	if ($Mode == 'Edit') {
                $myrow = data_retrieve("sms_create_stud_class", "id", $selected_id);
		$_POST['class_name'] = $myrow["class_name"];
                $_POST['capacity']  = $myrow["total_student"];
                $_POST['hierarchy']  = $myrow["hierarchy"];
		$_POST['school_set']  = $myrow["school_set"];
	}
	hidden('selected_id', $selected_id);
}

text_row(_("Class Name:"), 'class_name', null, 45, 60); 
text_row(_("Student Capacity:"), 'capacity', $_POST['capacity'], 45, 3);
text_row(_("Hierarchy Number:"), 'hierarchy', $_POST['hierarchy'], 45, 3);

classset_list(_("School Set :"), 'school_set', $school_set,_("Select"), false, $query);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}
?>
