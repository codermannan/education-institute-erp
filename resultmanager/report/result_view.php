<?php
/* * ****************************************************************** */
$page_security = 'SS_SMS_STDNT_RSLT_VW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
//include_once($path_to_root . "/sms/includes/ui/applicant_info_ui_lists.inc");
include_once($path_to_root . "/resultmanager/includes/db/result_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
   page(_($help_context = "Result View"), true, false, "", $js);

}
//-----------------------------------------------------------------------------------
 

if (!isset($_GET['stid'])) {
    die("<BR>". _("This page must be called with a Student ID."));
} else {
    $std_id = $_GET['stid'];
    $ex =    $_GET['ex'];
    $syear = $_GET['syear'];
    $class = $_GET['class'];
    
}

function mark1($syear, $std_id, $class, $ex, $sbid,$cename) {
        $cen1 = $cename[0]!=''?$cename[0]:2050;
        $cen2 = $cename[1]!=''?$cename[1]:2050;
        $cen3 = $cename[2]!=''?$cename[2]:2050;
        $cen4 = $cename[3]!=''?$cename[3]:2050;
        
        $qsq = "SELECT student_id, 
        SUM( IF( child_exam_name =$cen1 AND subject = $sbid, mark, NULL ) ) AS ct , 
        SUM( IF( child_exam_name =$cen2 AND subject = $sbid, mark, NULL ) ) AS mcq , 
        SUM( IF( child_exam_name =$cen3 AND subject = $sbid, mark, NULL ) ) AS written , 
        SUM( IF( child_exam_name =$cen4 AND subject = $sbid, mark, NULL ) ) AS practical , 
        SUM( mark ) AS `total`
        FROM " . TB_PREF . "sms_exam_mark_entry
        WHERE school_year = ".db_escape($syear)." AND student_id = ".db_escape($std_id)." AND st_class = ".db_escape($class)." AND exam_name = ".db_escape($ex)."
        GROUP BY student_id";
       
//        display_error($qsq);
        $res = db_query($qsq);
        $result = db_fetch($res);
         
        $sbsql = "SELECT allocated_marks, 
        SUM( IF( child_exam_name =$cen1 AND subject = $sbid, min_marks, NULL ) ) AS `ctmn` , 
        SUM( IF( child_exam_name =$cen2 AND subject = $sbid, min_marks, NULL ) ) AS `mcqmn` , 
        SUM( IF( child_exam_name =$cen3 AND subject = $sbid, min_marks, NULL ) ) AS `wrmn` , 
        SUM( IF( child_exam_name =$cen4 AND subject = $sbid, min_marks, NULL ) ) AS `pracmn`
        FROM " . TB_PREF . "sms_exam_setting
        WHERE school_year = ".db_escape($syear)." AND class = ".db_escape($class)." AND exam_name = ".db_escape($ex)."
        GROUP BY exam_name";

        $sbquery = db_query($sbsql);
        $sbresult = db_fetch($sbquery);
//        display_error($sbsql);
        $totalmark = ($result['mcq'] + $result['written'] + $result['practical']);
        $eightypercent = round((($totalmark * 80)/100));
        
        $totalNumber = ($result['ct'] + $eightypercent);
               
        $finalarray = array(
            'mkarray'=>array($result['ct'],$result['mcq'],$result['written'],$result['practical'],$totalmark,$eightypercent,$totalNumber),
            'minarray'=>array($sbresult['ctmn'],$sbresult['mcqmn'],$sbresult['wrmn'],$sbresult['pracmn'])
        );
        

        return $finalarray;           
  }

function findgrade($mark,$flag){
    
    $sql = "SELECT letter_grade,cpoint FROM " . TB_PREF . "sms_grade_set_up"; 
            
    if($flag!=1){
        $sql .= " WHERE start_mark <= '$mark' AND end_mark >= '$mark'" ;
    }
    else{
        $sql .= " WHERE start_point <= '$mark' AND end_point >= '$mark'" ;
    }    

    $query = db_query($sql);
    $result = db_fetch($query);
    return $result;
  
}

function getstatus($array){
    $mn = min($array);
    return $mn;
} 
  $same;
  $gpa = 0;
  $sqex = db_fetch(db_query("SELECT exam_name FROM " . TB_PREF . "sms_exam_name WHERE id = '".$ex."'"));

  $prac = get_mark_for_result($class);
  $mainresult = db_query($prac);
  $tsub = mysql_num_rows($mainresult);
 
  $str= db_fetch(db_query("SELECT ss.student_id,sse.session_name,sc.class_name FROM " . TB_PREF . "sms_student ss LEFT JOIN 0_sms_create_stud_class sc ON sc.id=ss.st_class
      LEFT JOIN  0_sms_session sse ON sse.class= ss.st_class
      WHERE ss.student_id='". $std_id ."'" ));
  
      start_table(TABLESTYLE1,'width=80%');
        start_row();
        label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'colspan=11 align=center style="font-size:16px"'); 
        end_row();
        start_row();
        label_cell($_SESSION['SysPrefs']->prefs['postal_address'],'colspan=3 align=center style="font-size:15px"');
        end_row();
        start_row();
            label_cell( $sqex['exam_name'],'colspan=11 align=center style="font-weight:bold; font-size:14px;"');
        end_row();
       
        end_table();
        br();
        $sql_ex = get_sql_for_result_view($std_id);
        start_table(TABLESTYLE_NOBORDER,'width=80%');
  start_row();
             label_cell( 'Student ID','width=20%');
             label_cell( ':');
             label_cell($std_id,'width=30%');
  end_row();
  start_row();
             label_cell( 'Name Of Student','width=20%');
             label_cell( ':');
             label_cell($sql_ex['name'],'width=30%');
  end_row();
 
start_row();
   
             label_cell('Father Name');
             label_cell( ':','width=10%');
             label_cell($sql_ex['father_name']);
  end_row();
  start_row();
   
             label_cell( 'Mother Name');
             label_cell( ':','width=10%');
             label_cell($sql_ex['mother_name']);
  end_row();
   start_row();
             label_cell( 'Class');
             label_cell( ':','width=10%');
             label_cell($sql_ex['class_name']);
             label_cell( 'Roll No.');
             label_cell( ':','width=10%');
             label_cell($sql_ex['roll_number']);
             
   end_row();
   start_row();
             
             label_cell( 'Section Name','width=20%'); 
             label_cell( ':','width=10%');
             label_cell( $sql_ex['session_name'],'width=20%');
             label_cell( 'Registration No.','width=20%'); 
             label_cell( ':','width=10%');
             
   end_row();
    start_row();
             label_cell( 'Group');
             label_cell( ':','width=10%');
             label_cell($sql_ex['group_name']);
             label_cell( 'Type Of Student','width=20%'); 
             label_cell( ':','width=10%');
             label_cell('Regular');
  end_row();
   start_row();
             label_cell( 'Date Of Birth');
             label_cell( ':','width=10%');
             label_cell(sql2date($sql_ex['dob']));
             
  end_row();
  end_table();
   
  br();
    start_table(TABLESTYLE2,'width=80%'); 
        
    $condition = array('parent'=>$ex);
    $field = array('exam_name','id');
    $qr = data_retrieve_condition("sms_exam_name", $field, $condition);
    
  start_row();
             labelheader_cell( 'Subject','align=center');
             while($row = db_fetch($qr)){
             labelheader_cell( $row['exam_name'],'width=6% ');
             $sub_id[] = $row['id'];
             }
//             labelheader_cell( '','align=center');
             labelheader_cell( 'Total','align=center');
             labelheader_cell( '80% of Total','align=center');
             labelheader_cell( 'Total Number','align=center');
             labelheader_cell( 'Heighest Number','align=center');
             labelheader_cell( 'G.P','align=center');
             labelheader_cell( 'G.P.A','align=center');
             labelheader_cell( 'Letter Grade','align=center');
  end_row();

  //-------------------
       while($mrow = db_fetch($mainresult)){
           $finalarray = mark1($syear, $std_id, $class, $ex, $mrow['id'],$sub_id); 
           
            $heigestnum = $mrow['total_mark'];
            $theigest += $mrow['total_mark'];
            
           start_row();
            label_cell($mrow['subject_name'],'align=center');
            $ter = 1;
            foreach($finalarray['mkarray'] as $value){
                if($value != NULL){
                    label_cell($value,'align=center'); 
                }
                else{
                    label_cell('--','align=center');
                    $ter++;
                }
            }
            
            $totalnumber += $finalarray[mkarray][6];
            label_cell($heigestnum,'align=center');
            
            if($finalarray['mkarray'][0]< $finalarray['minarray'][0]){
                $point = 10;
                $lgrade = findgrade($point);
                label_cell($lgrade['letter_grade'],'align=center');
            }elseif($finalarray['mkarray'][1]< $finalarray['minarray'][1]){
                $point = 10;
                $lgrade = findgrade($point);
                label_cell($lgrade['letter_grade'],'align=center');
            }elseif($finalarray['mkarray'][2]< $finalarray['minarray'][2]){;
                $point = 10;
                $lgrade = findgrade($point);
                label_cell($lgrade['letter_grade'],'align=center');
            }elseif($finalarray['mkarray'][3]< $finalarray['minarray'][3]){
                $point = 10;
                $lgrade = findgrade($point);
                label_cell($lgrade['letter_grade'],'align=center');
            }else{
                $lgrade = findgrade($value);
                label_cell($lgrade['letter_grade'],'align=center');  
            }
            
            $gpa += $lgrade['cpoint'];
            
            $tpoint = number_format2(($gpa / $tsub),2);
            
            label_cell($lgrade['cpoint'],'align=center'); 
            
            $lowestpoint[] = $lgrade['cpoint'];
            end_row();
        }
       
            $lgrade = findgrade($tpoint,1);
            $mn = getstatus($lowestpoint);
            
           start_row();
            label_cell('<b>Total</b>','align=center');
            label_cell('','align=center colspan='.count($sub_id));
            label_cell($gtotal,'align=center');
            label_cell(null,'align=center');
            label_cell('<b>'.$totalnumber.'</b>','align=center');
            label_cell('<b>'.$theigest.'</b>','align=center');
            label_cell('','align=center');
            label_cell('<b>'.$tpoint.'</b>','align=center');
            if($mn==0){
            label_cell('<b>F</b>','align=center');    
            }else{
            label_cell('<b>'.$lgrade['letter_grade'].'</b>','align=center');    
            }
           end_row();
  
  end_table();
  start_form();
  start_table(TABLESTYLE2);
  
  end_table();
  end_form();

  br(1);
  br(1);
 end_page(true, false, false);

//}

?>
