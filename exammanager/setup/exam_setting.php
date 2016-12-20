<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_XM_STNG';
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

page(_("Subject Wise Examination Setting"), false , false, "", $js);

simple_page_mode(true);
$syear = get_current_schoolyear();
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];

if(list_updated('class'))
$Ajax->activate('_page_body');

if(list_updated('exam_name'))
$Ajax->activate('_page_body');

if(list_updated('child_exam_name'))
$Ajax->activate('_page_body');


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
   
	$input_error = 0;

        
        if (strlen($_POST['class']) == '') 
	{
		$input_error = 1;
		display_error(_("Class must be selected."));
		set_focus('class');
	}
        if (strlen($_POST['subject']) == '') 
	{
		$input_error = 1;
		display_error(_("Subject must be selected."));
		set_focus('subject');
	}
      
        if (strlen($_POST['exam_name']) == '') 
	{
		$input_error = 1;
		display_error(_("Exam name must be selected."));
		set_focus('exam_name');
	}
        
        if (strlen($_POST['child_exam_name']) == '') 
	{
		$input_error = 1;
		display_error(_("Child exam name must be selected."));
		set_focus('child_exam_name');
	}
        if (strlen($_POST['child_exam_name']) != '' && strlen($_POST['marks']) == ''){
		$input_error = 1;
		display_error(_("Allocated mark have to assign for Child exam."));
		set_focus('marks');
	}
        $manmademinmarks = round((($_POST['marks'] * 33)/100));
        
//        display_error($manmademinmarks);
	if ($input_error !=1)
	{
    	if ($selected_id != -1) 
    	{ 
		    update_exam_setting($selected_id,$_POST['class'],$_POST['subject'],$_POST['exam_name'],$_POST['child_exam_name'],$_POST['marks'],$manmademinmarks);    		
			display_notification(_('Selected exam setting has been updated'));
    	} 
    	else 
    	{
            $res= "SELECT * FROM " . TB_PREF . "sms_exam_setting WHERE school_year= " .  db_escape($syear) . "AND class=" 
                    . db_escape($_POST['class']) . "AND subject=" . db_escape($_POST['subject']) . "AND exam_name=" . db_escape($_POST['exam_name']) .
                     "AND child_exam_name=" . db_escape($_POST['child_exam_name']);
          
            $pr=db_query($res);
            $result= db_fetch($pr);
           // $tr=$result['exam_name'];
       
            if($syear == $result['school_year'] && $_POST['class'] == $result['class'] &&  $_POST['subject'] == $result['subject'] && $_POST['exam_name'] == $result['exam_name'] && $_POST['child_exam_name'] == $result['child_exam_name'])
            {
               
               display_notification(_('exam name already exists'));
            }
            else
            {
                
		    add_exam_setting($syear,$_POST['class'],$_POST['subject'],$_POST['exam_name'],$_POST['child_exam_name'],$_POST['marks'],$manmademinmarks);
			display_notification(_('New exam setting has been added'));
            }
    	}
		$Mode = 'RESET';
	}
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
              //delete_data($selected_id,"sms_exam_setting");
//	delete_selected_class($selected_id);
		display_notification(_('Selected data has been deleted'));

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
function serialno($row) 
{
    return $row["id"];    
}
//.....................................................................
if (!@$_GET['popup'])
    start_form();
start_table(TABLESTYLE_NOBORDER);

start_row();

 $query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class:"),'searchclass', null, 'Select Class', false, $query);

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
br();

if(isset($_POST['searchclass'])){
    $class = $_POST['searchclass'];
    $Ajax->activate('_page_body');
}


$ec= get_sql_exam_setting_data();

$cols = array(
     _("Sl#")=>array('insert'=>true, 'fun'=>'serialno', 'align'=>'center'),
     _("Class")=>array('align'=>'center'),
     _("Exam Name")=>array('align'=>'center'),
     _("Child Exam Name")=>array('align'=>'center'),
     _("Allocated Marks")=>array('align'=>'center'),
    _("Minimum Marks")=>array('align'=>'center'),
     _("Subject Name")=>array('align'=>'center'),
     array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
     array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_exam_setting', $ec, $cols);

$table->width = "70%";

display_db_pager($table);
echo '<br>';
start_table();
    br(1);
    display_heading2(viewer_link(_("&View Subject Wise Examination Setting"), "sms/view/view_subwise_exam_set.php?c=".$class));
end_table(2);

//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
                $myrow = data_retrieve("sms_exam_setting", "id", $selected_id);
		$_POST['school_year']  = $myrow["school_year"];
                $_POST['class'] = $myrow["class"];
                $_POST['subject'] = $myrow["subject"];
                $_POST['exam_name']  = $myrow["exam_name"];
                $_POST['child_exam_name']  = $myrow["child_exam_name"];
                $_POST['marks']  = $myrow["allocated_marks"];
                $_POST['minimarks']  = $myrow["min_marks"];
               
	}
	hidden('selected_id', $selected_id);
}

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Select Class :"), 'class', '', 'Select Class', true, $query);

$query=array('id','subject_name','sms_subject','class',$_POST['class']);
combo_list_row(_("Subject Name :"), 'subject', $_POST['subject'], 'Select Subject', true, $query);

$query=array('id','exam_name','sms_exam_name','parent = 0 AND class_name',$_POST['class']);
combo_list_row(_("Exam Name :"), 'exam_name', $_POST['exam_name'], 'Select Exam Name', true, $query);

$query=array('id','exam_name','sms_exam_name','parent',$_POST['exam_name']);
combo_list_row(_("Child Exam Name :"), 'child_exam_name', $_POST['child_exam_name'], 'Select Child Exam', true, $query);

text_row(_("Allocated Marks:"), 'marks', $_POST['marks'], 30,3);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>