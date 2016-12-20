<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_STDNT_XM_SCHDL';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/exammanager/includes/ui/exam_ui_lists.inc");
include_once($path_to_root . "/exammanager/includes/db/exam_db.inc");


$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Students Exam Schedule"), @$_REQUEST['popup'], false, "", $js); 
simple_page_mode(true);
$syear = get_current_schoolyear();
//----------------------------------------------------------------------------------
if($_POST['exam_id'])
    $selected_id=$_POST['exam_id'];

if(list_updated('class'))
$Ajax->activate('_page_body');

if(list_updated('exam_name'))
$Ajax->activate('_page_body');

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
    $input_error = 0;

	if (strlen($_POST['class']) == 0) 
	{
		$input_error = 1;
		display_error(_("Class must be selected."));
		set_focus('class');
                 return false;
	}
    
        if (strlen($_POST['subject']) == 0) 
	{
		$input_error = 1;
		display_error(_("Subject must be selected."));
		set_focus('subject');
                 return false;
	}
       
        if (strlen($_POST['exam_name']) == 0) 
	{
		$input_error = 1;
		display_error(_("Exam name must be selected."));
		set_focus('exam_name');
                 return false;
	}
        

       
        if (strlen($_POST['aroom']) == 0) 
	{
		$input_error = 1;
		display_error(_("Allocated Room must be selected."));
		set_focus('aroom');
                 return false;
	}
        if (strlen($_POST['status']) == 0) 
	{
		$input_error = 1;
		display_error(_("Status must be selected."));
		set_focus('status');
                 return false;
	}
    
     $date =sql2date($_POST['exam_date']) ;
      


    	if ($selected_id != -1) 
    	{ 
		    update_stud_exam_schedule($selected_id,$_POST['exam_name'],$_POST['class'],$_POST['subject'],$date,$_POST['shift'],$_POST['aroom'],$_POST['status']);    		
			display_notification(_('Selected Schedule has been updated'));
    	} 
    	else 
    	{
		    add_stud_exam_schedule($_POST['exam_name'],$_POST['class'],$_POST['subject'],$date,$_POST['shift'],$_POST['aroom'],$_POST['status']);
                
                    display_notification(_('Schedule has been added'));
    	}
		$Mode = 'RESET';

         
}
//--------------------------------------------------------------------------------
function edit_link($row) 
{
    
      return "<center>".button("Edit".$row["exam_id"], _("Edit"), _("Edit"), ICON_EDIT).
        "</center>";

}

function delete_link($row) 
{
    submit_js_confirm("Delete".$row['exam_id'],
                    sprintf(_("Are you sure you want to delete ?")));
	
     return  "<center>".button("Delete".$row["exam_id"], _("Delete"), _("Delete"), ICON_DELETE).
        "</center>";
}

function status($row) 
{
         if($row['status']==1)
      return 'Open'; 
         else
      return 'Close';
    
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
 

	delete_schedule($selected_id);
		display_notification(_('Selected group has been deleted'));
    
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------
if (!@$_GET['popup'])
start_form();
start_table(TABLESTYLE_NOBORDER);

start_row();

 $query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class:"),'search_class', null, 'Select Class', true, $query);

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
br();

if(isset($_POST['search_class'])){
    $search_class = $_POST['search_class'];
    $Ajax->activate('_page_body');
}

$rep = get_schedule();

$cols = array(    
     _("Class")=>array('align'=>'center'),
     _("Exam Name")=>array('align'=>'center'),
     _("Subject Name")=>array('align'=>'center'),
     _("Date")=>array('align'=>'center', 'type' => 'date'),    
    _("Allocated Room")=>array('align'=>'center'),
    _("Shift")=>array('align'=>'center'),
    _("Status")=>array('fun'=>'status','align'=>'center'),
     array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
     array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_stud_exam', $rep, $cols);

$table->width = "60%";

display_db_pager($table);
echo '<br>';

start_table();
    br(1);
    display_heading2(viewer_link(_("&View Students Exam Schedule"), "exammanager/report/view_student_exam_schedule.php?sc=".$search_class));
end_table(2);
//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{   
 	if ($Mode == 'Edit') {
             $myrow = data_retrieve('sms_stud_exam', 'exam_id', $selected_id);
           
		$_POST['exam_name'] = $myrow["exam_name"];
                $_POST['class'] = $myrow["class_name"];
                $_POST['subject'] = $myrow["subject_name"];
		$_POST['exam_date']  = sql2date($myrow["date"]);
                $_POST['shift']  = $myrow["shift"];
                 $_POST['aroom'] = $myrow["room"];
                $_POST['status'] = $myrow["status"];
	}
	hidden('selected_id', $selected_id);
	
}

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_row(_("Allocated Class:"), 'class', $_POST['class_name'], 'Select Class', true, $query);

$query=array('id','subject_name','sms_subject','class',$_POST['class']);
combo_list_row(_("Subject Name:"), 'subject', $_POST['subject'], 'Select Subject', true, $query);
        
$query=array('id','exam_name','sms_exam_name','status = 1 AND parent = 0 AND class_name',$_POST['class']);
combo_list_row(_("Exam Name:"), 'exam_name', $_POST['exam_name'], 'Select Exam Name', true, $query);
     
date_row(_("Exam Date:"), 'exam_date');


$query=array(array('id','shift','select id, shift from '.TB_PREF.'sms_shift ORDER BY shift ASC'));
combo_list_row(_("Select Shift:"), 'shift', $_POST['shift'], 'Select Shift', false, $query);


$query=array(array('id','room_no','select id, room_no from '.TB_PREF.'sms_room_setup
       ORDER BY room_no ASC'));
combo_list_row(_("Allocated Room:"), 'aroom', $_POST['aroom'], 'Select Room', false, $query);

schedule_list_cells(_("Status:"),"status",$_POST['status']);


end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>


 