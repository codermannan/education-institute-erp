<?php

/* * ********************************************************************
  Developed by Mannan
 /** ********************************************************************* */
$page_security = 'SS_SMS_XM_MRKS_ENTRY';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/exammanager/includes/ui/exam_ui_lists.inc");
include_once($path_to_root . "/exammanager/includes/db/exam_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Student Mark Entry"), false, false, "", $js);
}
$user = $_SESSION['wa_current_user']->username;
$syear = get_current_schoolyear();
//-----------------------------------------------------------------------------------

if(list_updated('subject'))
$Ajax->activate('_page_body');

if(list_updated('exam_name'))
$Ajax->activate('_page_body');

if(list_updated('child_exam_name'))
$Ajax->activate('_page_body');

if(isset($_POST['SearchOrders']))
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
   
        elseif (strlen($_POST['child_exam_name']) == '') 
	{
		$input_error = 1;
		display_error( _('Child Exam name must be selected.'));
		set_focus('child_exam_name');
                return false;
	}  
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

//----------------------------------
if (isset($_POST['Process'])) 
{
        foreach($_POST['mark'] as $key => $marks){ 
            
	if ($marks == '') 
	{
		$input_error = 1;
		display_error( _('The mark field  must be entered.'));
		set_focus('mark');
                return false;
	} 
        
                 if($marks > 79 && $marks<= 100){
                     $grade = 'A+';
                     $gpa = 5;
                 }
                 elseif($marks > 69 && $marks< 80){
                      $grade = 'A';
                      $gpa = 4;
                 }
                 elseif($marks > 59 && $marks< 70){
                      $grade = 'A-';
                      $gpa = 3.5;
                 }

                 elseif($marks > 49 && $marks< 60){
                      $grade = 'B';
                      $gpa = 3;
                 }
                 elseif($marks > 39 && $marks< 50){
                      $grade = 'C';
                      $gpa = 2;
                 }
                 elseif($marks > 32 && $marks< 40){
                      $grade = 'D';
                      $gpa = 1;
                 }
                 else{
                      $grade = 'F';
                      $gpa = 0;
                 }
                
              $stid  = $_POST['stid'][$key];
              $syear = $_POST['syear'][$key];
              $ids   = $_POST['id'][$key];
              $stclass   = $_POST['stclass'][$key];
              $chkmarklimit   = $_POST['chkmarklimit'][$key];
                   
              
            if($marks > $chkmarklimit)
            {
                
                display_notification('Entered mark excced maxmimum mark for '.$stid);
            }
            
            else
            {
              $ex = $exname['id'];

            add_exam_marks_with_ct($ids,$syear,$ex,$_POST['child_exam_name'],$stclass,$_POST['section'],$_POST['subject'],$stid, $marks,$grade,$gpa,$user);
			display_notification(_('Students mark has been added'));
            }
        }
        $Ajax->activate('_page_body');
        $Mode = 'RESET';
}
//---------------------------------------------------------------------------------------------
if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
$query=array(array('id','subject','select tal.subject, sub.sub_code from 
    '.TB_PREF.'sms_teacher_allocation tal 
        left join '.TB_PREF.'sms_subject sub on tal.subject = sub.id'));

combo_list_cells(_("Subject :"), 'subject', $_POST['subject'], 'Select Subject', true, $query);


$query=array(array('child_exam_name','exam_name','select ex.child_exam_name, exn.exam_name from '.TB_PREF.'sms_exam_setting ex
        left join '.TB_PREF.'sms_exam_name exn on ex.child_exam_name = exn.id 
        where ex.exam_name='.  db_escape($exname['id']).' and ex.class='.  db_escape($classval['class']).' and ex.subject='.  db_escape($_POST['subject'])));
combo_list_cells(_("Child Exam :"), 'child_exam_name', $_POST['subject_name'], 'Select Child Exam', false, $query);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
//---------------------------------------------------------------------------------------------
function chkmarklimit($syear,$class,$subject,$exam_name,$child_exam_name){
    
         $condition = array('school_year'=>$syear,'class'=>$class,'subject'=>$subject,'exam_name'=>$exam_name,'child_exam_name'=>$child_exam_name);
         $field = array('allocated_marks');
         $chkval = db_fetch(data_retrieve_condition("sms_exam_setting", $field, $condition));
         
         if($chkval['allocated_marks']!=''){
             
             return $chkval['allocated_marks'];
         }
         else{
             return 'not setup';
         }
       
}

function checkmarkentry($stid,$syear,$st_class,$subject,$exam_name,$child_exam_name){
        
         $condition = array('student_id'=>$stid,'school_year'=>$syear,'st_class'=>$st_class,'subject'=>$subject,'exam_name'=>$exam_name,'child_exam_name'=>$child_exam_name);
         $field = array('child_exam_name');
         $chkval = data_retrieve_condition("sms_exam_mark_entry", $field, $condition);
         
         if(mysql_num_rows($chkval)>0){
            
             return 1;
         }
         
}
start_form();

start_table(TABLESTYLE2,"width=80%");
         start_row();
             labelheader_cell( 'SL#','width=5%');
             labelheader_cell( 'Student ID','width=15%');
             labelheader_cell( 'Student Name','width=30%');
             labelheader_cell( 'Mark','width=16% ');
         end_row();
    
if(isset($_POST['SearchOrders'])){ 
    $teacher = $user;
    $class = $classval['class'];
    $subject = $_POST['subject'];
    $exam = $exname['id'];
    
    $child_exam_name = $_POST['child_exam_name'];
    $sl = 1; 

$sql = get_sql_for_students_mark_entry($syear, $class, $subject, $exam);

$result = db_query($sql,"data could not be found");
 if(mysql_num_rows($result)>0){
     
  while ($rep = db_fetch($result))
    {

         $markchk = checkmarkentry($rep['student_id'],$syear,$class,$subject,$exam,$child_exam_name);
         
         $chkmarklimit = chkmarklimit($syear,$class,$subject,$exam,$child_exam_name);
         
         if($markchk!=1){
         start_row();
             label_cell($sl,'align=center');
             label_cell( $rep['student_id'],'align=center');
             hidden('stid['.$rep['id'].']', $rep['student_id']);
             label_cell( $rep['name'],'align=center');
             text_cells($rep['mark'], 'mark['.$rep['id'].']', null, 20,2,false,'','','Maximum marks '.$chkmarklimit);
             hidden('syear['.$rep['id'].']', $rep['school_year']);
             hidden('stclass['.$rep['id'].']', $class);
             hidden('id['.$rep['id'].']', $rep['id']);
             hidden('chkmarklimit['.$rep['id'].']', $chkmarklimit);
         end_row();
         }
           else {
            display_notification(_('Mark has already been entered for this subject'));
           } 
         
     $sl++;
    }

 }
 else {
     display_notification(_('Please take attendance for this subject'));   
 } 

} 

end_table();

br();

div_start('controls');
  submit_center('Process', _("Insert Mark"), true, '', 'default');
div_end();
//---------------------------------------------------------------------------------------------------

if (!@$_GET['popup']) {
    end_form();
    end_page();
}