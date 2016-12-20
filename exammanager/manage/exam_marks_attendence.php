<?php

/* * ********************************************************************
  
 * ********************************************************************* */
$page_security = 'SS_SMS_TAKE_XM_ATNDCNC';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui_input.inc");
include_once($path_to_root . "/exammanager/includes/ui/exam_ui_lists.inc");
include_once($path_to_root . "/exammanager/includes/db/exam_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Take Exam Attendance"), false, false, "", $js);
}
$syear = get_current_schoolyear();
//-----------------------------------------------------------------------------------

if(list_updated('subject'))
$Ajax->activate('_page_body');

if(list_updated('exam_name'))
$Ajax->activate('_page_body');

//-----------------------------------------------------------------------------------

if (isset($_POST['SearchOrders'])) 
{   
	
        if (strlen($_POST['subject']) == '') 
	{
		$input_error = 1;
		display_error( _('Student subject must be selected.'));
		set_focus('subject');
                return false;
	} 
        
}
//----------------------------------
if (isset($_POST['Process'])) 
{

        foreach($_POST['attendance'] as $key => $attn){ 
	if ($attn == '') 
	{
		$input_error = 1;
		display_error( _('Attendance field  must be selected.'));
		set_focus('attendance');
	} 
             if($_POST['subject']){
             $condition = array('id'=>$_POST['subject']);
             $field = array('class');
             $classval = db_fetch(data_retrieve_condition("sms_subject", $field, $condition));
              }

               if($_POST['subject']){
               $con = array('class_name'=>$classval['class'],'parent'=>'0','status'=>'1');
               $fld = array('id');
               $exname = db_fetch(data_retrieve_condition("sms_exam_name", $fld, $con));
                }

              $reason  = $_POST['reason'][$key];
              $stid  = $_POST['stid'][$key];
              $sl = "SELECT exam_name FROM " . TB_PREF . "sms_exam_name WHERE parent= 0 AND status= 1";
              $res= db_query($sl);
              $tr = db_fetch($res);
              $ex = $exname['id'];
              $row = mysql_num_rows($res);
              if($row <= 0)
              {
                  display_notification('there is no active exam right now');
              }
              
              else{
                        add_exam_attendance($syear,$ex,$_POST['stclass'],$_POST['subject'],$stid, $attn,$reason);
			display_notification(_('Students attendance has been added'));
              }
                  
        }
        
        $Mode = 'RESET';
}

$Ajax->activate('_page_body');
//---------------------------------------------------------------------------------------------
if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

start_row();

$query=array(array('id','sub_code','select id, sub_code from '.TB_PREF.'sms_subject
       ORDER BY sub_code DESC'));

$_SESSION['searchfildsmssub'] = array("title"=>"Subject Code", "inputflid"=>"subject", "tabhead1"=>"ID", 
"tabhead2"=>"Subject Code", "fld1"=>"id", "fld2"=>"sub_code", "tbl"=>"sms_subject");
$url = "includes/sview/search_view.php?dat=searchfildsmssub";
$vlink = viewer_link(_("Search"), $url, "", "", ICON_VIEW);

combo_list_cells(_("Subject Code :"), 'subject', '', 'Select subject', true, $query, $vlink);

if($_POST['subject']){
    $condition = array('id'=>$_POST['subject']);
    $field = array('class');
    $classval = db_fetch(data_retrieve_condition("sms_subject", $field, $condition));
}

if($_POST['subject']){
    $con = array('class_name'=>$classval['class'],'parent'=>'0','status'=>'1');
    $fld = array('id');
    $exname = db_fetch(data_retrieve_condition("sms_exam_name", $fld, $con));
}
              
submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
//-------------
function retstid($syear, $exam_name, $class, $subject, $stid){
    
         $sqlattn = "SELECT *
                FROM " . TB_PREF . "sms_exam_attendence
                WHERE school_year = " . db_escape($syear)."
                AND exam_name = " . db_escape($exam_name)."
                AND st_class = " . db_escape($class)."
                AND subject = " . db_escape($subject)."
                AND student_id = " . db_escape($stid)."
                "; 
       $r = db_fetch(db_query($sqlattn,"data could not be found"));
       return $r['student_id'];
        
}
//---------------------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2,"width=80%");
         start_row();
             labelheader_cell( 'SL#','width=5%');
             labelheader_cell( 'Student ID','width=10%');
             labelheader_cell( 'Student Name','width=15%');
             labelheader_cell( 'Attendance','width=15% ');
             labelheader_cell( 'Reason','width=15% ');
         end_row();
    
if(isset($_POST['SearchOrders'])){
    
 
$sl = 1;   
$sql = get_sql_for_students_exam_attendance($classval['class']);

$result = db_query($sql,"data could not be found");
$ex = $exname['id'];

  while ($rep = db_fetch($result))
    {
         $stid = retstid($syear, $ex, $classval['class'], $_POST['subject'],$rep['student_id']);
       
         if($stid != $rep['student_id']){
         start_row();
             label_cell($sl,'align=center');
             label_cell( $rep['student_id'],'align=center');
             label_cell( $rep['name'],'align=center');
             
             ?>
               
              <td> 
                <input type="radio" name="attendance[<?php echo $rep['id']; ?>]" value="1" <?php echo $pre; ?>checked >Present
           
                <input type="radio" name="attendance[<?php echo $rep['id']; ?>]" value="0" <?php echo $ab; ?>>Absent
              </td> 
 <?php 
 
            
             textarea_cells('','reason['.$rep['id'].']', null, 25,1); 
             hidden('stid['.$rep['id'].']', $rep['student_id']);
             hidden('examname', $ex['id']);
             hidden('stclass', $classval['class']);
             
             
         end_row();
         $sl++;
        } 
//    }
    
    else {
         display_notification(_('There are no students for taking attendance in this subject'));
        }
        }

} 
end_table();

br();

div_start('controls');
  submit_center('Process', _("Take Attendance"), true, '', 'default');
div_end();
//---------------------------------------------------------------------------------------------------

if (!@$_GET['popup']) {
    end_form();
    end_page();
}