<?php

/* * ********************************************************************
  Copyright (C) FrontAccounting, LLC.
  Released under the terms of the GNU General Public License, GPL,
  as published by the Free Software Foundation, either version 3
  of the License, or (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 * ********************************************************************* */
$page_security = 'SA_SUPPTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/applicant_info_ui_lists.inc");
include_once($path_to_root . "/sms/includes/db/student_db.inc");
//include_once($path_to_root . "/sms/includes/db/applicant_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Student Promotion Process"), false, false, "", $js);
}
//----------------------------------
if(list_updated('school_year'))
$Ajax->activate('_page_body');

if(list_updated('class'))
$Ajax->activate('_page_body');

if(isset($_POST['SearchOrders']))
$Ajax->activate('_page_body');
//----------------------------------
if (isset($_POST['SearchOrders'])) 
{   
	if (strlen($_POST['school_year']) == '') 
	{
		$input_error = 1;
		display_error( _('School year must be selected.'));
		set_focus('school_year');
                return false;
	} 
        
        elseif (strlen($_POST['class']) == '') 
	{
		$input_error = 1;
		display_error( _('Student class must be selected.'));
		set_focus('class');
                return false;
	} 
        
        elseif (strlen($_POST['section']) == '') 
	{
		$input_error = 1;
		display_error( _('Student section must be selected.'));
		set_focus('section');
                return false;
	} 
       
       
}

//-------------------------------------------------------------------------------------
if (isset($_POST['Process'])) 
{
        $class = $_POST['cla'];
        $section = $_POST['sec'];
        $exam_name = $_POST['exam'];
        
        if($class == 10){
            $promoted_class = 8;
        }
       
        $cond = array('id'=>$class);
        $fsld = array('school_set','id');
        $qrr = db_fetch(data_retrieve_condition("sms_create_stud_class", $fsld, $cond));
        
        $condition = array('school_set'=>$qrr['school_set'],'closed'=>0);
        $field = array('school_year','id');
        $syid = db_fetch(data_retrieve_condition("sms_school_year", $field, $condition));
        
        foreach($_POST['stchk'] as $key =>$val){
            $chk = $val;
        }
        
        if ($chk == 1)
   	{
            foreach($_POST['stchk'] as $key =>$val){
                
            $tblid = $_POST['tblid'][$key];
            $roll = $_POST['rank'][$key];
            $gp = $_POST['gp'][$key];
            $stid = $_POST['stid'][$key];
            $res = $_POST['st'][$key];
            
           $sql = get_sql_for_add_promoted_student($tblid,$syid['id'],$stid,$roll,$promoted_class,$section,$gp,$res);
            }
        }
    	else{
            foreach($_POST['st'] as $key =>$res){
            
            $tblid = $_POST['tblid'][$key];
            $roll = $_POST['rank'][$key];
            $gp = $_POST['gp'][$key];
            $stid = $_POST['stid'][$key];
            
           $sql = get_sql_for_add_promoted_student($tblid,$syid['id'],$stid,$roll,$promoted_class,$section,$gp,$res);
            }
            
        }
 
      //  meta_forward($path_to_root.'/sms/manage/app_short_list_approve.php');
	
}
//---------------------------------------------------------------------------------------------
function grace_link($id, $stid, $cgpa,$mnsub) {
    return pager_link( _("Result Amendment"),
		"/sms/manage/grace.php?id=" .$id."&stid=".$stid."&cgpa=".$cgpa."&sub=".$mnsub."&syear=".$_POST['school_year']."&class=".$_POST['class']."&section=".$_POST['section'], ICON_SUBMIT);
}

if (!@$_GET['popup'])
start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();

 $query=array('id','school_year','sms_school_year');
 combo_list_cells(_("School Year :"), 'school_year', $_POST['school_year'], 'Select School Year', true, $query);
 
$query=array(array('id','class_name','select sc.id, sc.class_name from 
    '.TB_PREF.'sms_create_stud_class sc 
        left join '.TB_PREF.'sms_school_year sy on sc.school_set = sy.school_set
         where sy.id='.  db_escape($_POST['school_year'])));
combo_list_cells(_("Class :"), 'class', '', 'Select Class', true, $query);

$query=array('id','session_name','sms_session','class',$_POST['class']);
combo_list_cells(_("Section :"), 'section', $_POST['session_name'], 'Select Session', true, $query);

//$query=array(array('id','exam','select sen.id, sen.exam_name from 
//    '.TB_PREF.'sms_exam_setting ses 
//        left join '.TB_PREF.'sms_exam_name sen on sen.id=ses.exam_name
//         where    ses.class='.  db_escape($_POST['class'])));
//combo_list_cells(_("Exam Name :"), 'exam_name', $_POST['exam_name'], 'Select Exam', false, $query);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
end_form();
//---------------------------------------------------------------------------------------------
start_form();

hidden('cla', $_POST['class']);
hidden('sec', $_POST['section']);
hidden('exam', $_POST['exam_name']);

    $condition = array('class'=>'10');
    $field = array('subject_name','total_mark','id');
    $qr = data_retrieve_condition("sms_subject", $field, $condition);
    $nosub = mysql_num_rows($qr);
    $tmark = 0;
start_table(TABLESTYLE2,"width=95%");
         start_row();
//             check_cells(null, '', '',false, '','align=center width=5%');
             labelheader_cell( '','width=3%');
             labelheader_cell( 'Student ID','width=5%');
             labelheader_cell( 'Student Name','width=5%');
             labelheader_cell( 'Class Roll','width=5%');
             while($row = db_fetch($qr)){  
             labelheader_cell( $row['subject_name']."(".$row['total_mark'].")",'width=6% ');
             $tmark = ($tmark + $row['total_mark']);
             $sub_id[] = $row['id'];
             }
             labelheader_cell( 'GPA','width=6% ');
             labelheader_cell( 'CGPA','width=6% ');
             labelheader_cell( 'Rank','width=6% ');
             labelheader_cell( 'Status','width=5%');
             labelheader_cell( 'Amendment','width=5%'); 
         end_row();
         
        // display_error(print_r($sub_id,true)); 
         
if (isset($_POST['SearchOrders'])){  
    
//    $class = $_POST['app_class'];$rep['subject_name']

  $sql = get_student_for_result_processing();

  $result = db_query($sql,"data could not be found");
  $tomark = 0;
  $gpa = 0;
  
  while ($rep = db_fetch($result))
    {
         $con = array('student_id'=>$rep['student_id'],'st_class'=>$_POST['class'],'section'=>$_POST['class'],'exam_name'=>'3');
         $fld = array('mark','student_id','subject','id');
         $query = data_retrieve_condition("sms_exam_mark_entry", $fld, $con);
         
         start_row();
             check_cells(null, 'stchk['.$rep['id'].']', '',false, '','align=center width=5%');
             label_cell( $rep['student_id'] ,'width=5%');
             hidden('stid['.$rep['id'].']', $rep['student_id']);
             label_cell( $rep['name'],'width=10%');
             label_cell( $rep['roll_number'],'width=3% align=center');
              
             while($mrow = db_fetch($query)){
                 $lmark[] = $mrow['mark'];
                 //$sub[] = $mrow['subject'];
                 $mn = min($lmark);
                 $tomark = ($tomark + $mrow['mark']);
                 
                 if($mrow['mark'] > 79 && $mrow['mark']<= 100){
                 label_cell('A+ ('. $mrow['mark'] .')','width=5% align=center');
                 $gpa = ($gpa + 5);
                 }
                 elseif($mrow['mark'] > 69 && $mrow['mark']< 80){
                 label_cell('A ('. $mrow['mark'] .')','width=5% align=center');
                 $gpa = ($gpa + 4);
                 }
                 elseif($mrow['mark'] > 59 && $mrow['mark']< 70){
                 label_cell('A- ('. $mrow['mark'] .')','width=5% align=center');
                 $gpa = ($gpa + 3.5);
                 }
                 elseif($mrow['mark'] ==0 ){
                 label_cell(0);
                 $gpa = ($gpa + 3.5);
                 }
                 elseif($mrow['mark'] > 49 && $mrow['mark']< 60){
                 label_cell('B ('. $mrow['mark'] .')','width=5% align=center');
                 $gpa = ($gpa + 3);
                 }
                 elseif($mrow['mark'] > 39 && $mrow['mark']< 50){
                 label_cell('C ('. $mrow['mark'] .')','width=5% align=center');
                 $gpa = ($gpa + 2);
                 }
                 elseif($mrow['mark'] > 32 && $mrow['mark']< 40){
                 label_cell('D ('. $mrow['mark'] .')','width=5% align=center');
                 $gpa = ($gpa + 1);
                 }
                 else{
                     label_cell('F ('. $mrow['mark'] .')','width=5% align=center');
                     $gpa = ($gpa + 0);
                 }
                 
             }
             
             $gpaa = number_format($gpa / $nosub,2);
             $grarray[$rep['student_id']] = $gpaa;
             $sortarr = arsort($grarray);
             
             $rank = 1;
             foreach($grarray as $stid=>$gpv){
                 $frank[$stid] = $rank;
                 $rank++; 
             }
        
             label_cell( $gpaa,'width=5% align=center');
             hidden('gp['.$rep['id'].']', $gpaa);
             label_cell( $gpaa,'width=5% align=center');
             
             foreach($frank as $sid=>$gv){
                 if($rep['student_id'] == $sid){
                     
                     label_cell( $gv,'width=5% align=center'); 
                     hidden('rank['.$rep['id'].']', $gv);
                 }    
             }
               
             if($gpaa >= '1' && $mn>32){
                 
             label_cell( 'P','width=3% align=center'); 
             label_cell( null,'width=3% align=center');
             hidden('st['.$rep['id'].']', 'P');
             }
             else{
             label_cell( 'F','width=3% align=center'); 
             label_cell( grace_link($rep['id'],$rep['student_id'],$gpaa,$mn,$_POST['school_year'],$_POST['class'],$_POST['section']),'width=5% align=center');
             hidden('st['.$rep['id'].']', 'F');
              } 
             hidden('tblid['.$rep['id'].']', $rep['id']);
             
             
         end_row();
         $gpa = 0;
         $tomark = 0;
         $lmark = array();
    }
   // display_error(print_r($frank,true));
} 
end_table();

br();

div_start('controls');
  submit_center('Process', _("Process Promotion List"), true, '', 'default');
div_end();
//---------------------------------------------------------------------------------------------------

br();

if (!@$_GET['popup']) {
    end_form();
    end_page();
}
