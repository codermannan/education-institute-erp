<?php
$page_security = 'SS_SMS_STDNT_RSLT_VW';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/resultmanager/includes/db/result_db.inc");

page(_("Students Evaluation Report"));

simple_page_mode(true);
$syear = get_current_schoolyear();
//----------------------------------------------------------------------------------

if(list_updated('class'))
$Ajax->activate('_page_body');

if(list_updated('section'))
$Ajax->activate('_page_body');

if(list_updated('subject'))
$Ajax->activate('_page_body');

if(list_updated('exam_name'))
$Ajax->activate('_page_body');

if(list_updated('child_exam_name'))
$Ajax->activate('_page_body');

if(isset($_POST['SearchOrders']))
$Ajax->activate('_page_body');

if (!@$_GET['popup'])
start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();
$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class"), 'class', '', 'Select Class', true, $query);

$query=array('id','session_name','sms_session','class',$_POST['class']);
combo_list_cells(_("Section:"), 'section', $_POST['session_name'], 'Select Session', true, $query);


$query=array('id','subject_name','sms_subject','class',$_POST['class']);
combo_list_cells(_("Subject:"), 'subject', $_POST['subject_name'], 'Select Subject', true, $query);

$query=array('id','exam_name','sms_exam_name','parent = 0 AND class_name',$_POST['class']);
combo_list_cells(_("Parent Exam :"), 'exam_name', $_POST['exam_name'], 'Select Exam', true, $query);


submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
end_form();

br();
//--------------------------------------------------------
function mark1($stid, $syear,$class,$sec, $exname, $cexam,$sub) {
     $qsq = "SELECT mark FROM 0_sms_exam_mark_entry
                          where student_id = " . db_escape($stid) .
                          " AND school_year = " . db_escape($syear) .
                          " AND st_class ="  . db_escape($class) .
                          " AND section = " . db_escape($sec) .
                          " AND exam_name = " . db_escape($exname) .
                          " AND child_exam_name = " . db_escape($cexam) .
                          " AND subject ="  . db_escape($sub);
     
                $res = db_query($qsq);
                $result = db_fetch($res);
                return $result['mark']; 
}
if(isset($_POST['SearchOrders'])){
       $cls= $_POST['class'];
       $sec = $_POST['section'];
       $sub = $_POST['subject'];
       $pexam = $_POST['exam_name'];

    $condition = array('parent'=>$pexam);
    $field = array('exam_name','id');
    $qr = data_retrieve_condition("sms_exam_name", $field, $condition);
//---------------------------
start_table(TABLESTYLE2, 'width=90%');
  
         start_row();
             labelheader_cell( 'Serial No','width=5%');
             labelheader_cell( 'Student ID','width=5%');
             labelheader_cell( 'Roll Number','width=5%');
             while($row = db_fetch($qr)){
             labelheader_cell( $row['exam_name'],'width=6% ');
             $sub_id[] = $row['id'];
             }
             labelheader_cell('Total Mark(2+3+4)(5)', 'width=5%');
             labelheader_cell( '80% of Total Mark(6)','width=7%');
             labelheader_cell( 'Grand Total Mark(1+6)','width=6%');
             labelheader_cell( 'GPA','width=5%');
             labelheader_cell( 'Comment','width=5%');
           
         end_row();
     
       
       $sql = "SELECT roll_number,student_id FROM " . TB_PREF . "sms_student  WHERE school_year=" . db_escape($syear) . "AND st_class=" . db_escape($cls) . "AND st_section=" . db_escape($sec);

       $result= db_query($sql);
      $sl = 1;
     while($mrow = db_fetch($result)){
           $cr = $mrow['credit'];
           $heightn = $mrow['total_mark'];
           $mk1 += $mark1;
           start_row();
            label_cell($sl,'align=center');
            label_cell($mrow['student_id'],'align=center');
            label_cell($mrow['roll_number'],'align=center');
            
            foreach($sub_id as $sbid){ 
                
            $mk.$sbid = mark1($mrow['student_id'],$syear, $cls, $sec, $pexam, $sbid,$sub);
                if($mk.$sbid!=''){
                 label_cell($mk.$sbid,'align=center');
        
               $mark1t += $mk.$sbid; 

                }
                else{
                 label_cell(0,'align=center');   
                }
            }
           label_cell($mark1t,'align=center');
               // display_error($tm);
                $mrk=($mark1t*80)/100; 
                label_cell($mrk,'align=center');
                $gtotal= $mark1t + $mrk ;
                label_cell($gtotal,'align=center');
     $sl++;
         }
   }
    
 // }



echo '<br>';
//----------------------------------------------------------------------------------

 start_table(TABLESTYLE1,"width=90%");
         start_row();
             label_cell( '','colspan=16  height=30');  
         end_row();
         start_row();
             label_cell( '<u><b>_____________________________</b></u>','colspan=3 align=center');           
             label_cell( '_______________________','align=right colspan=10'); 
         end_row();
         start_row();
             label_cell( 'Signature of Specified Subject Teacher','colspan=3 align=center');
             label_cell( 'signature of professor','align=right colspan=3');  
         end_row();
         start_row();
             label_cell( '','colspan=16  height=10');  
         end_row();
   end_row();
  end_table();
end_form();
end_page();
?>
