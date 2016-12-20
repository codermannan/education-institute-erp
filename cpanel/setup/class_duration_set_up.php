<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_CLS_RTN_SETP';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/cpanel/includes/ui/cpanel_ui_lists.inc");
include_once($path_to_root . "/cpanel/includes/db/cpanel_db.inc");


page(_("Class Duration"));

simple_page_mode(true);
$syear = get_current_schoolyear();
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];

if(list_updated('class'))
$Ajax->activate('_page_body');

if(list_updated('subject'))
$Ajax->activate('_page_body');

if(list_updated('teacher'))
$Ajax->activate('_page_body');

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
         $start_time = $_POST['start_hr'].':'.$_POST['start_mm'].':'.'00';
         $end_time = $_POST['end_hr'].':'.$_POST['end_mm'].':'.'00';
	if ($input_error !=1)
	{
    	if ($selected_id != -1) 
    	{ 
		    update_time_duration($selected_id,$_POST['class'],$_POST['section'],$_POST['subject'],$_POST['period'],$start_time,$end_time);    		
			display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{
               $sql= "SELECT * FROM " . TB_PREF . "sms_class_duration WHERE school_year=" .  db_escape($_POST['school_year']) .
                       " AND class=" . db_escape($_POST['class']) . " AND section=" . db_escape($_POST['section']) ." AND subject=" 
                       . db_escape($_POST['subject']) . " AND period=" . db_escape($_POST['period']) . 
                       "AND start_time=" . db_escape($start_time) . " AND end_time=" . db_escape($end_time);
               
               //display_error($sql);
               $rep= db_query($sql);
               $result = db_fetch($rep);
            
               if($syear == $result['school_year'] && $_POST['class'] == $result['class'] && 
                  $_POST['section'] == $result['section'] && $_POST['subject'] == $result['subject'] && 
                  $_POST['period'] == $result['period'] && $start_time == $result['start_time'] && $end_time == $result['end_time'])
                   
               {
                   //  display_error($rep['school_year']);
                     display_notification('data already exits');
               }
                else
                {
                  
		   add_time_duration($syear,$_POST['class'],$_POST['section'],$_POST['subject'],$_POST['period'],$start_time,$end_time);
			display_notification(_('Class duration has been added'));
                }
    	}
		$Mode = 'RESET';
	}
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
        delete_data($selected_id,'sms_class_duration');         
		display_notification(_('Selected data has been deleted'));

	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------


$sql1 = "SELECT sy.id,sy.school_year,cd.id, sc.class_name , ss.session_name,sb.subject_name,cd.period, cd.start_time, cd.end_time
         FROM " . TB_PREF . "sms_class_duration cd
         LEFT JOIN " . TB_PREF . "sms_school_year sy ON cd.school_year = sy.id
         LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON cd.class = sc.id
         LEFT JOIN " . TB_PREF . "sms_session ss ON cd.section = ss.id
         LEFT JOIN " . TB_PREF . "sms_subject sb ON cd.subject = sb.id
         ";
//display_error($sql1);
$result = db_query($sql1, "could not get subject wise teacher");

start_form();
start_table(TABLESTYLE, "width=80%");
$th = array(_('Sl#'),_('Class'),_('Section'), _('Subject'),_('Period'), _('Start Time'),_('End Time'),'', '');
table_header($th);
$k = 0; //row colour counter
$sl = 1;
while ($myrow = db_fetch($result)) 
{
    
	alt_table_row_color($k);
        label_cell($sl);
	label_cell($myrow["class_name"],'align=center');
        label_cell($myrow["session_name"]);
        label_cell($myrow["subject_name"]);
        label_cell($myrow["period"]);
        label_cell($myrow["start_time"]);
        label_cell($myrow["end_time"]);
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
	end_row();
        $sl++;
}

end_table();
end_form();
echo '<br>';
//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);


if ($selected_id != -1) 
{

 	if ($Mode == 'Edit') {
            
            $rep =get_sql_for_duration($selected_id);
            
         
                $result =db_fetch(db_query($rep, "could not get subject wise teacher"));
		$_POST['class'] = $result['class'];
                 $_POST['period'] = $result["period"];
                $_POST['section'] = $result["section"];
                 $_POST['subject'] = $result["subject"];
                $_POST['start_time'] = $result["start_time"];
		$_POST['end_time']  = $result["end_time"];
                
	}
	hidden('id', $selected_id);
}

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_row(_("Class :"), 'class', '', 'Select Class', true, $query);


$query=array('id','session_name','sms_session','class',$_POST['class']);
combo_list_row(_("Section :"), 'section', $_POST['session_name'], 'Select Session', true, $query);

$query=array('id','subject_name','sms_subject','class',$_POST['class']);
combo_list_row(_("Subject :"), 'subject', $_POST['subject_name'], 'Select Subject', true, $query);


//$query=array('id','subject_name','sms_subject','class',$_POST['class']);
//combo_list_row(_("Subject :"), 'subject', $_POST['subject_name'], 'Select Subject', false, $query);
text_row('Period','period');

digital_time(_("Start Time:"), 'start_hr', 'start_mm');

digital_time(_("End Time:"), 'end_hr', 'end_mm');

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

end_form();

end_page();

?>
