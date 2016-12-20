<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_SBJCT_TCHR';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/exammanager/includes/ui/exam_ui_lists.inc");
include_once($path_to_root . "/exammanager/includes/db/exam_db.inc");


page(_("Subject Wise Teacher"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];

if(list_updated('datasearch'))
$Ajax->activate('_page_body');

if(list_updated('class'))
$Ajax->activate('_page_body');


if(list_updated('section'))
$Ajax->activate('_page_body');

if(list_updated('subject'))
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
        
        if (strlen($_POST['section']) == '') 
	{
		$input_error = 1;
		display_error(_("Section must be selected."));
		set_focus('section');
	}
        
        if (strlen($_POST['teacher_name']) == '') 
	{
		$input_error = 1;
		display_error(_("Teacher must be selected."));
		set_focus('teacher_name');
	}
        
	if ($input_error !=1)
	{
    	if ($selected_id != -1) 
    	{ 
		    update_teacher_allocation($selected_id,$_POST['class'],$_POST['section'],$_POST['subject'],$_POST['teacher_name']);    		
			display_notification(_('Selected exam setting has been updated'));
    	} 
        
     
    
               
    	else 
    	{
            $res= "SELECT * FROM " . TB_PREF . "sms_teacher_allocation WHERE class=" . db_escape($_POST['class']) . 
               "AND section=" . db_escape($_POST['section']) . "AND subject=" . db_escape($_POST['subject']) . "AND teacher=" . db_escape($_POST['teacher_name']) ;
            
            $pr= db_query($res);
            $rep= db_fetch($pr);
            
            if($_POST['class'] == $rep['class'] && $_POST['section'] == $rep['section'] && $_POST['subject'] == $rep['subject'] && $_POST['teacher_name'] == $rep['teacher'])
            {
                display_error('Entered value is duplicate, Enter a different value');
            }  
            
            else
            {
            
		    add_teacher_allocation($_POST['class'],$_POST['section'],$_POST['subject'],$_POST['teacher_name']);
			display_notification(_('Teacher allocation has been added'));
            }
    	}
		$Mode = 'RESET';
	}
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
        delete_data($selected_id,'sms_teacher_allocation');
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
//----------------------------------------------------------------------------------


if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

br();
 

start_row();
 
search_field_student(_("Select Search Type : "), 'datasearch','',null,true,true);

if($_POST['datasearch'] == 'teacher'){
 
$sql = "SELECT he.emp_code as tcod, he.name as name FROM " . TB_PREF . "sms_teacher_allocation ta                   
        LEFT JOIN " . TB_PREF . "hcm_emp he ON he.emp_code=ta.teacher GROUP BY he.emp_code ORDER BY he.name DESC";

$query=array(array('tcod','name',$sql));
combo_list_cells(_("Teacher :"), 'datavalue', '', 'Select teacher', true, $query );

}
elseif($_POST['datasearch']=='class'){
    $query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
           ORDER BY class_name ASC'));
    combo_list_cells(_("Class :"), 'datavalue','', 'Select Class', true, $query);
}
elseif($_POST['datasearch']=='section'){
     $sql = "SELECT ss.id as sd, ss.session_name as sname FROM " . TB_PREF . "sms_teacher_allocation ta                   
        LEFT JOIN " . TB_PREF . "sms_session ss ON ss.id=ta.section GROUP BY ss.session_name ORDER BY ss.session_name ASC";

    $query=array(array('sd','sname',$sql));
    combo_list_cells(_("Section :"), 'datavalue', '', 'Select Section', true, $query );
 
 }
elseif($_POST['datasearch']=='subject'){
     $sql = "SELECT sb.id as si, sb.subject_name as sn FROM " . TB_PREF . "sms_teacher_allocation ta                   
     LEFT JOIN " . TB_PREF . "sms_subject sb ON sb.id=ta.subject GROUP BY sb.subject_name ORDER BY sb.subject_name ASC";

    $query=array(array('si','sn',$sql));
    combo_list_cells(_("Subject :"), 'datavalue','', 'Select Subject', true, $query );  
}

 
submit_cells('SearchOrders', _("Search"), '', _('Select applicant'), 'default');

 end_row();
end_table(1);
//----------------------------------------------------------------------------------
if (!@$_GET['popup'])
    start_form();
 
$datasearch=$_POST['datasearch'];
$datavalue=$_POST['datavalue'];
 

$ec = get_sql_teacher_allocation_data($datasearch,$datavalue);

$cols = array(
     _("Teacher Name")=>array('align'=>'center'),
    _("Class")=>array('align'=>'center'),
    _("Section")=>array('align'=>'center'),
    _("Subject")=>array('align'=>'center'),
    _("Subject Code")=>array('align'=>'center'),
    array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
    array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_teacher_allocation', $ec, $cols);

$table->width = "60%";

display_db_pager($table);
echo '<br>';

//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);


if ($selected_id != -1) 
{

 	if ($Mode == 'Edit') {
            
                $myrow = data_retrieve("sms_teacher_allocation", "id", $selected_id);
           
		$_POST['class'] = $myrow['class'];
		$_POST['section'] = $myrow["section"];
                $_POST['subject'] = $myrow["subject"];
		$_POST['teacher_name'] = $myrow["teacher"];
	}
	hidden('id', $selected_id);
}

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_row(_("Class :"), 'class', $_POST['class_name'], 'Select Class', true, $query);

  $query=array('id','session_name','sms_session','class',$_POST['class']);
  combo_list_row(_("Section :"), 'section', $_POST['session_name'], 'Select Session', true, $query); 

$query=array('id','subject_name','sms_subject','class',$_POST['class']);
 combo_list_row(_("Subject :"), 'subject', $_POST['subject_name'], 'Select Subject', true, $query);

$query=array('emp_code','name','hcm_emp','grade','66');
combo_list_row(_("Teacher :"), 'teacher_name', $_POST['name'], 'Select Teacher', true, $query);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>
