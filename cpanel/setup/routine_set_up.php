<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_CLS_RTN_SETP';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/cpanel/includes/ui/cpanel_ui_lists.inc");
include_once($path_to_root . "/cpanel/includes/db/cpanel_db.inc");


page(_("Class Routine Set Up"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];
//display_error($selected_id);

if(list_updated('class'))
$Ajax->activate('_page_body');

if(list_updated('subject'))
$Ajax->activate('_page_body');

if(list_updated('teacher'))
$Ajax->activate('_page_body');

//if(list_updated('class'))
//$Ajax->activate('_page_body');


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

        $input_error = 0;

//	if (strlen($_POST['class']) == '') 
//	{
//		$input_error = 1;
//		display_error(_("Class must be selected."));
//		set_focus('class');
//	}
//        
//        if (strlen($_POST['section']) == '') 
//	{
//		$input_error = 1;
//		display_error(_("Section must be selected."));
//		set_focus('section');
//	}
//        
//        if (strlen($_POST['teacher']) == '') 
//	{
//		$input_error = 1;
//		display_error(_("Teacher must be selected."));
//		set_focus('teacher');
//	}
//        
	if ($input_error !=1)
	{
    	if ($selected_id != -1) 
    	{ 
		    update_routine_set_up($selected_id,$_POST['class'],$_POST['day'],$_POST['subject'],$_POST['teacher'],$_POST['period']);    		
			display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{
		    add_routine_set_up($_POST['class'],$_POST['day'],$_POST['subject'],$_POST['teacher'],$_POST['period']);
			display_notification(_('Routine set up has been added'));
    	}
		$Mode = 'RESET';
	}
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
        delete_data($selected_id,'sms_class_routine');         
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
//-----------------------------------------------------------------------------------
//$sql1 = "SELECT sr.id, sc.class_name,sr.week_day ,ss.subject_name,he.name,cd.period
//           FROM "      . TB_PREF . "sms_class_routine sr
//           LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON sc.id = sr.class
//           LEFT JOIN " . TB_PREF . "sms_subject ss ON ss.id = sr.subject
//           LEFT JOIN " . TB_PREF . "hcm_emp he ON  sr.teacher = he.emp_code
//           LEFT JOIN " . TB_PREF . "sms_class_duration cd ON cd.class = sr.class";

//$result = db_query($sql1, "could not get subject wise teacher");
if (!@$_GET['popup'])
start_form();

$ec=routine_setup();
$cols = array(
     _("Class")=>array('align'=>'center'),
    _("Day")=>array('align'=>'center'),
    _("Subject")=>array('align'=>'center'),
    _("Teacher")=>array('align'=>'center'),
     _("Period")=>array('align'=>'center'),
//    _("Amount")=>array('align'=>'center'),
    array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
    array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_class_routine', $ec, $cols);

$table->width = "40%";

display_db_pager($table);
echo '<br>';

//start_table(TABLESTYLE, "width=40%");
//$th = array(_('Class'), _('Day'),_('Subject'), _('Teacher'),_('Period'),'', '');
//table_header($th);
//$k = 0; //row colour counter
//
//while ($myrow = db_fetch($result)) 
//{
//    
//	alt_table_row_color($k);
//	label_cell($myrow["class_name"]);
//        if($myrow["week_day"] == 1){
//            label_cell('Monday'); 
//        }
//        elseif($myrow["week_day"] == 2){
//            label_cell('Tuesday'); 
//        }
//        elseif($myrow["week_day"] == 3){
//            label_cell('Wednesday'); 
//        }
//        elseif($myrow["week_day"] == 4){
//            label_cell('Thursday'); 
//        }
//        elseif($myrow["week_day"] == 5){
//            label_cell('Friday'); 
//        }
//        elseif($myrow["week_day"] == 6){
//            label_cell('Saturday'); 
//        }
//        elseif($myrow["week_day"] == 7){
//            label_cell('Sunday'); 
//        }
//   
//        label_cell($myrow["subject_name"]);
//        label_cell($myrow["name"]);
//        label_cell($myrow["period"]);
//       	edit_button_cell("Edit".$myrow['id'], _("Edit"));
// 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
//	end_row();
//}
//
//end_table();
//end_form();
//echo '<br>';
//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);


if ($selected_id != -1) 
{

 	if ($Mode == 'Edit') {
            

                 $myrow = data_retrieve("sms_class_routine", "id", $selected_id);
           
		$_POST['class'] = $myrow['class'];
		$_POST['day']  = $myrow["week_day"];
                $_POST['subject'] = $myrow["subject"];
		$_POST['teacher']  = $myrow["teacher"];
              $_POST['period']  = $myrow["period"];
	}
	hidden('id', $selected_id);
}


$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_row(_("Class :"), 'class', $_POST['class'], 'Select Class', true, $query);

$query=array('day_id','day','hcm_weekdays1');
combo_list_row(_("Day"),'day',$_POST['day'],'Select Day',true,$query);

$query=array('id','subject_name','sms_subject','class',$_POST['class']);
combo_list_row(_("Subject :"), 'subject', $_POST['subject_name'], 'Select Subject', false, $query);

$query=array('emp_code','name','hcm_emp','grade','66');
combo_list_row(_("Teacher :"), 'teacher', $_POST['name'], 'Select Teacher', false, $query);

$query=array('id','period','sms_class_duration','class',$_POST['class']);
combo_list_row(_("Period :"), 'period', $_POST['period'], 'Select Period', false, $query);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

 if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>
