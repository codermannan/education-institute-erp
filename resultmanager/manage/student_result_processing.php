<?php
/* * *********************************************************************** */
$page_security = 'SS_SMS_STDNT_RSLT_PRCS';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/resultmanager/includes/db/result_db.inc");


if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Student Result Process"), false, false, "", $js);
}
$user = $_SESSION['wa_current_user']->username;
$syear = get_current_schoolyear();
//----------------------------------

if(list_updated('class'))
$Ajax->activate('_page_body');

if(isset($_POST['SearchOrders']))
$Ajax->activate('_page_body');
//----------------------------------
if (isset($_POST['SearchOrders'])) 
{   
	
        if (strlen($_POST['class']) == '') 
	{
		$input_error = 1;
		display_error( _('Student class must be selected.'));
		set_focus('class');
                return false;
	}       
}

//-------------------------------------------------------------------------------------
if (isset($_POST['Process'])){       
        $class = $_POST['cla'];
        $exam_name = $_POST['exam'];
        
        $cond = array('id'=>$class);
        $fsld = array('school_set','id');
        $qrr = db_fetch(data_retrieve_condition("sms_create_stud_class", $fsld, $cond));
        
        
        foreach($_POST['stchk'] as $key =>$val){
            $chk = $val;
        }
        
        if ($chk == 1)
   	{
            foreach($_POST['stchk'] as $key =>$val){
                
            $tblid = $_POST['tblid'][$key];
            $roll = $_POST['rank'][$key];
            $grade = $_POST['grade'][$key];
            $gp = $_POST['gp'][$key];
            $stid = $_POST['stid'][$key];
            $res = $_POST['st'][$key];
            
           $sql = get_sql_for_add_promoted_student($tblid,$syear,$stid,$roll,$class,$section,$grade,$gp,$res,$user);
            }
        }
    	else
            {
                foreach($_POST['st'] as $key =>$res)
                    {

                        $tblid = $_POST['tblid'][$key];
                        $roll = $_POST['rank'][$key];
                        $grade = $_POST['grade'][$key];
                        $gp = $_POST['gp'][$key];
                        $stid = $_POST['stid'][$key];

                        $sql = get_sql_for_add_promoted_student($tblid,$syear,$stid,$roll,$class,$section,$grade,$gp,$res,$user);
                    }           
            }
            unset($_SESSION['strankarray']);
            $Ajax->activate('_page_body');
            $Mode = 'RESET';
}
//---------------------------------------------------------------------------------------------
function finalcalculation($syear, $stid, $stclass, $stexam, $stsub){
    $con = array('parent'=>$stexam);
    $fld = array('id');
    $getexam = data_retrieve_condition("sms_exam_name", $fld, $con);
           
    while($ceid = db_fetch($getexam)){
            $sub_exam_id[] = $ceid['id'];
    }
          
        $mark1 = mark1($syear, $stid, $stclass, $stexam, $stsub, $sub_exam_id[0]); 
        $mark2 = mark2($syear, $stid, $stclass, $stexam, $stsub, $sub_exam_id[1]);
        $mark3 = mark3($syear, $stid, $stclass, $stexam, $stsub, $sub_exam_id[2]); 
        $mark4 = mark4($syear, $stid, $stclass, $stexam, $stsub, $sub_exam_id[3]);
        
        $total = ($mark2 + $mark3 + $mark4);
        $per = (($total * 80)/100);
        $gtotal = round($per + $mark1);
        
        return $gtotal;       
}
function mark1($syear, $std_id, $class, $ex, $sub,$subexam) {
     $qsq = "SELECT mark FROM ".TB_PREF."sms_exam_mark_entry
                          where school_year = '".$syear."'
                          AND student_id = '".$std_id."'
                          AND st_class = '".$class."'
                          AND exam_name = '".$ex."'
                          AND child_exam_name = '".$subexam."'
                          AND subject = '".$sub."'";              
                $res = db_query($qsq);
                $result = db_fetch($res);
               
                return $result['mark'];           
  }
function mark2($syear, $std_id, $class, $ex, $sub,$subexam) {
     $qsq = "SELECT mark FROM ".TB_PREF."sms_exam_mark_entry
                          where school_year = '".$syear."'
                          AND student_id = '".$std_id."'
                          AND st_class = '".$class."'
                          AND exam_name = '".$ex."'
                          AND child_exam_name = '".$subexam."'
                          AND subject = '".$sub."'";
    // display_error($qsq);          
                $res = db_query($qsq);
                $result = db_fetch($res);
                return $result['mark'];           
  }
function mark3($syear, $std_id, $class, $ex, $sub,$subexam) {
     $qsq = "SELECT mark FROM ".TB_PREF."sms_exam_mark_entry
                          where school_year = '".$syear."'
                          AND student_id = '".$std_id."'
                          AND st_class = '".$class."'
                          AND exam_name = '".$ex."'
                          AND child_exam_name = '".$subexam."'
                          AND subject = '".$sub."'";
                         
                $res = db_query($qsq);
                $result = db_fetch($res);
                return $result['mark'];           
  }
function mark4($syear, $std_id, $class, $ex, $sub,$subexam) {
     $qsq = "SELECT mark FROM ".TB_PREF."sms_exam_mark_entry
                          where school_year = '".$syear."'
                          AND student_id = '".$std_id."'
                          AND st_class = '".$class."'
                          AND exam_name = '".$ex."'
                          AND child_exam_name = '".$subexam."'
                          AND subject = '".$sub."'";
//          display_error($qsq);                
                $res = db_query($qsq);
                $result = db_fetch($res);
                return $result['mark'];           
  }
function getstatus($array){
    $mn = min($array);
    return $mn;
}   
function grace_link($id, $stid, $grade, $cgpa,$mnsub,$syear) {
    
    return pager_link( _("Result Amendment"),"/resultmanager/manage/grace.php?id=" .$id."&stid=".$stid."&grade=".$grade."&cgpa=".$cgpa."&sub=".$mnsub."&syear=".$syear."&class=".$_POST['class']."&section=".$_POST['section'], ICON_SUBMIT);
}
     
function studentfinalranking($grarray,$totalmark,$subtotal){
    
     $rank = 1;
     //GPA duplication  start
     //non duplicate items
     $uniq  = array_unique($grarray);
     $dupli = array_diff_assoc($grarray, $uniq);
     $gpanonduplicateitems = array_diff($grarray, $dupli);
    //duplicate items
     $gpaduplicateitems = array_diff($grarray, $gpanonduplicateitems);
     //GPA duplication  end
     //
     //Total number duplication  start
     //non duplicate items
     $tnuniq  = array_unique($totalmark);
     $tndupli = array_diff_assoc($totalmark, $tnuniq);
     $tnnonduplicateitems = array_diff($totalmark, $tndupli);
    //duplicate items
     $tnduplicateitems = array_diff($totalmark, $tnnonduplicateitems);
     //Total number duplication  end
 
         if(count($gpaduplicateitems)>0){
            if(count($tnduplicateitems)>0){
                $subjectarray = array_intersect_key($subtotal, $tnduplicateitems);
                $sortarr = arsort($subjectarray);
                foreach($subjectarray as $stid=>$gpv){ 
                 $frank[$stid] = $rank;
                 $rank++;
                }
            }else{
                $numberarray = array_intersect_key($totalmark, $gpaduplicateitems);
                $sortarr = arsort($numberarray);
                foreach($numberarray as $stid=>$gpv){ 
                 $frank[$stid] = $rank;
                 $rank++;
                } 
            }
         }
         if(count($gpanonduplicateitems)>0){
             $nsortarr = arsort($gpanonduplicateitems);
              foreach($gpanonduplicateitems as $stid=>$gpv){ 
                $frank[$stid] = $rank;
                $rank++;
             }
         }
    return $frank;
}

if (!@$_GET['popup'])
start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class :"), 'class', '', 'Select Class', false, $query);

$condition = array('class_name'=>$_POST['class'],'parent' =>0,'status' =>1);
$field = array('id');
$data = db_fetch(data_retrieve_condition("sms_exam_name", $field, $condition));

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
end_form();
//---------------------------------------------------------------------------------------------
start_form();

hidden('cla', $_POST['class']);
hidden('exam', $data['id']);

    $condition = array('class'=>$_POST['class']);
    $field = array('subject_name','total_mark','id');
    $qr = data_retrieve_condition("sms_subject", $field, $condition);
    $tmark = 0;
start_table(TABLESTYLE2,"width=95%");
         start_row();
//             check_cells(null, '', '',false, '','align=center width=5%');
             labelheader_cell( '','width=3%');
             labelheader_cell( 'Student ID','width=5%');
             labelheader_cell( 'Student Name','width=5%');
             labelheader_cell( 'Class Roll','width=5%');
             while($row = db_fetch($qr)){ 
             labelheader_cell( $row['subject_name'].'('.$row['total_mark'].')','width=6%');
             $tmark = ($tmark + $row['total_mark']);
             $sub_id[] = $row['id'];
             }
             labelheader_cell( 'GRADE','width=6%');
             labelheader_cell( 'GPA','width=6%');
             labelheader_cell( 'Rank','width=6%');
             labelheader_cell( 'Status','width=5%');
             labelheader_cell( 'Amendment','width=5%'); 
         end_row();
        
if (isset($_POST['SearchOrders'])){  
 
  $sql = get_student_for_result_processing($syear);

  $result = db_query($sql,"data could not be found");
  $tomark = 0;
  $gpa = 0;
  $res = 1;
  while ($rep = db_fetch($result)){
         start_row();
             check_cells(null, 'stchk['.$rep['id'].']', '',false, '','align=center width=5%');
             label_cell( $rep['student_id'] ,'width=7%');
             hidden('stid['.$rep['id'].']', $rep['student_id']);
             label_cell( $rep['name'],'width=10%');
             label_cell( $rep['roll_number'],'width=3% align=center');
             
             foreach($sub_id as $sbid){ 
                 $mk.$sbid = finalcalculation($syear, $rep['student_id'], $_POST['class'], $data['id'], $sbid);

                 label_cell( $mk.$sbid,'width=3% align=center');
                 
                 if($mk.$sbid > 79 && $mk.$sbid<= 200){
                     $grade = 'A+';
                     $gpa += 5;
                     $tsmark += $mk.$sbid;
                 }
                 elseif($mk.$sbid > 69 && $mk.$sbid< 80){
                      $grade = 'A';
                      $gpa += 4;
                      $tsmark += $mk.$sbid;
                 }
                 elseif($mk.$sbid > 59 && $mk.$sbid< 70){
                      $grade = 'A-';
                      $gpa += 3.5;
                      $tsmark += $mk.$sbid;
                 }

                 elseif($mk.$sbid > 49 && $mk.$sbid< 60){
                      $grade = 'B';
                      $gpa += 3;
                      $tsmark += $mk.$sbid;
                 }
                 elseif($mk.$sbid > 39 && $mk.$sbid< 50){
                      $grade = 'C';
                      $gpa += 2;
                      $tsmark += $mk.$sbid;
                 }
                 elseif($mk.$sbid > 32 && $mk.$sbid< 40){
                      $grade = 'D';
                      $gpa += 1;
                      $tsmark += $mk.$sbid;
                 }
                 else{
                      $grade = 'F';
                      $gpa += 0;
                      $tsmark += $mk.$sbid;
                 }
                 $gp = number_format(($gpa / count($sub_id)),2);
                 
                 $arr[]= $mk.$sbid;
             } // end main foreach
             
                 if($gp >= 5){
                     label_cell('A+','width=5% align=center');
                     hidden('grade['.$rep['id'].']', 'A+');
                     $grad = 'A+';
                 }
                 elseif($gp > 3.99 && $gp < 5){
                     label_cell('A','width=5% align=center');
                     hidden('grade['.$rep['id'].']', 'A');
                     $grad = 'A';
                 }
                 elseif($gp > 3.49 && $gp < 4){
                     label_cell('A-','width=5% align=center');
                     hidden('grade['.$rep['id'].']', 'A-');
                     $grad = 'A-';
                 }
                 elseif($gp > 2.99 && $gp < 3.50){
                     label_cell('B','width=5% align=center');
                     hidden('grade['.$rep['id'].']', 'B');
                     $grad = 'B';
                 }
                 elseif($gp > 1.99 && $gp < 3){
                     label_cell('C','width=5% align=center');
                     hidden('grade['.$rep['id'].']', 'C');
                     $grad = 'C';
                 }
                 elseif($gp > 0.99 && $gp < 2){
                     label_cell('D','width=5% align=center');
                     hidden('grade['.$rep['id'].']', 'D');
                     $grad = 'D';
                 }
                 else{
                     label_cell('F','width=5% align=center');
                     hidden('grade['.$rep['id'].']', 'F');
                     $grad = 'F';
                 }
             label_cell( $gp,'width=5% align=center');
             hidden('gp['.$rep['id'].']', $gp);
             
             
            $mn = getstatus($arr); 
            
             if($gp < 0.99 OR $mn<32){
                 
                label_cell( 0,'width=5% align=center'); 
                label_cell( 'F','width=3% align=center'); 
                label_cell( grace_link($rep['id'],$rep['student_id'],$grad,$gp,$mn,$syear,$_POST['class'],$_POST['section']),'width=5% align=center');
                hidden('st['.$rep['id'].']', 'F'); 
             
             }else{
                 $acondition = array('class'=>$_POST['class'],'assign_sub'=>1);
                 $afield = array('id');
                 $asssub = db_fetch(data_retrieve_condition("sms_subject", $afield, $acondition));
                 
                 $sbnum = finalcalculation($syear, $rep['student_id'], $_POST['class'], $data['id'], $asssub['id']);
                 
                 $grarray[$rep['student_id']] = $gp;
                 $totalmark[$rep['student_id']] = $tsmark;
                 $subtotal[$rep['student_id']] = $sbnum;

                 $strank = studentfinalranking($grarray,$totalmark,$subtotal);
                 
                 foreach($_SESSION['strankarray'] as $sid=>$pos){
                     
                     if($rep['student_id'] == $sid){
                         label_cell($pos,'width=5% align=center');
                         hidden('rank['.$rep['id'].']', $pos);
                     }
                 }
                   
                 label_cell( 'P','width=3% align=center'); 
                 label_cell( null,'width=3% align=center');
                 hidden('st['.$rep['id'].']', 'P');
              } 
             hidden('tblid['.$rep['id'].']', $rep['id']);
             
//             display_error($tsmark);
         end_row();
         $gpa = 0;
//         $tomark = 0;
         $tsmark = 0;
         $arr = array();
    } //end while
    
    
    
    $_SESSION['strankarray'] = $strank;
    
} 
//else{
//    display_notification(_('There are no final examination to process result'));
//}
end_table();

br();

div_start('controls');
  submit_center('Process', _("Process Result"), true, '', 'default');
div_end();
//---------------------------------------------------------------------------------------------------

br();

if (!@$_GET['popup']) {
    end_form();
    end_page();
}
