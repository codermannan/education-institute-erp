<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_XM_NAME_SETING';
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

page(_("Exam Name Entry"), false, false, "", $js);
simple_page_mode(true);
$tday = Today();
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];

if (list_updated('class')) {
	$Ajax->activate('ex_parent');
}


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
        
	$input_error = 0;

	if (strlen($_POST['exam_name']) == ''){
		$input_error = 1;
		display_error(_("The exam name cannot be empty."));
		set_focus('exam_name');
	}
        
        if (strlen($_POST['class']) == ''){
		$input_error = 1;
		display_error(_("The class name cannot be empty."));
		set_focus('class');
	}
        
        if ($_POST['start_date'] == $tday AND $_POST['ex_parent']==''){
		$input_error = 1;
		display_error(_("Exam Start date have to assign according to exam plan."));
		set_focus('start_date');
	}

       if($input_error != 1)
       {
    	if ($selected_id != -1) 
    	{ 
                
		    update_class_exam_name($selected_id,$_POST['exam_name'], $_POST['class'], $_POST['ex_parent'],$_POST['start_date'],$_POST['end_date'],$_POST['examstatus']);                    
			display_notification(_('Selected exam name has been updated'));
    	} 
    	else 
    	{
            
            $sql = "SELECT exam_name,parent,class_name FROM " . TB_PREF . "sms_exam_name WHERE exam_name=" . db_escape($_POST['exam_name']) . "AND parent=" . db_escape($_POST['ex_parent']) . "AND class_name=" . db_escape($_POST['class']) ;
              $res = db_query($sql);
              $ressult = db_fetch($res);
              
              if($_POST['exam_name'] == $ressult['exam_name'] && $_POST['class'] == $ressult['class_name'] && $_POST['ex_parent'] == $ressult['parent'])
              {
                 
                  display_notification('Exam name already exits');
              }
              
              else
              {
		   add_exam_name($_POST['exam_name'], $_POST['class'], $_POST['ex_parent'],$_POST['start_date'],$_POST['end_date'],$_POST['examstatus']);
			display_notification(_('Exam name has been added'));
              }
    	}
		$Mode = 'RESET';
      }
	
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
        global $selected_id;
        
        if($selected_id!=''){
           
            $stquery = "SELECT * FROM ".TB_PREF."sms_exam_attendence WHERE exam_name=".$selected_id;
            $stres = db_query($stquery);
            $strows = db_num_rows($stres);
            
            $mkquery = "SELECT * FROM ".TB_PREF."sms_exam_mark_entry WHERE exam_name=".$selected_id." OR child_exam_name=".$selected_id;
            $mktres = db_query($mkquery);
            $mktrows = db_num_rows($mktres);
            
            $exquery = "SELECT * FROM ".TB_PREF."sms_exam_setting WHERE exam_name=".$selected_id." OR child_exam_name=".$selected_id;
            $extres = db_query($exquery);
            $exrows = db_num_rows($extres);
            
            if($strows>0 OR $mktrows>0 OR $exrows>0){
                
                display_notification(_('Selected subject can not be deleted,because it has been used another table as foreign key'));
            }
            else{
               delete_exam_name($selected_id);
                    display_notification(_('Selected exam has been deleted'));
            }        
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

function status($row) 
{   
    if($row['status']==1){
        return 'Active';
    }
    else{
        return 'Inactive';
    }
}
//.........................................................

if (!@$_GET['popup'])
    start_form();
start_table(TABLESTYLE_NOBORDER);

start_row();

 $query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class:"),'xmclass', null, 'Select Class', false, $query);

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
br();

if(isset($_POST['xmclass'])){
    $class = $_POST['xmclass'];
    $Ajax->activate('_page_body');
}

$ec=xm_name();
$cols = array(
     _("Name")=>array('align'=>'center'),
     _("Parent")=>array('align'=>'center'),
     _("Class Name")=>array('align'=>'center'),
     _("Start Date")=>array('align'=>'center','type'=>'date'),
     _("End Date")=>array('align'=>'center','type'=>'date'),
     _("Status")=>array('fun'=>'status', 'align'=>'center'),
     array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
     array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_exam_name', $ec, $cols);

$table->width = "60%";

display_db_pager($table);
echo '<br>';

start_table();
    br(1);
    display_heading2(viewer_link(_("&View Exam Name Entry"), "sms/view/view_exam_name_info.php?c=".$class));
end_table(2);

//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
             
                $myrow = data_retrieve('sms_exam_name', 'id', $selected_id);
		$_POST['exam_name'] = $myrow["exam_name"];
                $_POST['parent']  = $myrow["parent"];
                $_POST['class']  = $myrow["class_name"];
                $_POST['start_date']  = $myrow["start_date"];
                $_POST['end_date']  = $myrow["end_date"];
                $_POST['examstatus']  = $myrow["status"];
		 
	}
	hidden('selected_id', $selected_id);
}

text_row(_("Exam Name:"), 'exam_name', null, 45, 60); 

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_row(_("Class:"), 'class', $_POST['class'], 'Select Class', true, $query);

$query=array('id','exam_name','sms_exam_name','parent = 0 AND class_name ',$_POST['class']);
combo_list_row(_("Exam Head :"),'ex_parent', $_POST['parent'], 'Parent', false, $query);

date_row(_("Start Date:"), 'start_date');

date_row(_("End Date:"), 'end_date');

$items = array('1'=>'Active','0'=>'Inactive');
free_combo_list_row(_("Status :"), 'examstatus', $_POST['examstatus'], $items);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>