<?php

$page_security = 'SS_SMS_APLCNT_LST_VW';
$path_to_root = "../..";

//include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/attendancemanager/includes/ui/attendance_ui_lists.inc");
include_once($path_to_root . "/attendancemanager/includes/db/attendance_db.inc");
//----------------------------------------------------------------
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Leave Form"), false,false);

//--------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];

if(list_updated('class'))
$Ajax->activate('_page_body');

if(list_updated('section'))
$Ajax->activate('_page_body');

if(list_updated('stid'))
$Ajax->activate('_page_body');

$Ajax->activate('_page_body');
//--------------------------------------

if (isset($_POST['submit'])) 
{
    $input_error = 0;
    
    if($input_error != 1)
    {
        if($selected_id != -1)
        {
            $stuid = $_POST['stid'];
            $dt= date2sql($_POST['fdate']);
            $ds= date2sql($_POST['tdate']);
            $subdate= date2sql(Today());
            
            $sql = "SELECT id,student_id,date FROM " . TB_PREF . "sms_stud_class_attendence WHERE student_id=" . db_escape($stuid) . "AND date >=" . db_escape($dt). " AND date <=" . db_escape($ds). " ORDER BY date ASC";
            $res= db_query($sql,'cannot get data');
           
            if($_POST['leave_type'] == 2) 
            {
              if(db_num_rows($res)>0){
               while($rep=  db_fetch($res)) 
               {
               
//                  $dt1= $rep['date'];
//                  
//                  //$dt2= $rep['date'];
//                  $row = db_num_rows($res);
//                  $k = 0;
//                  for($i=0;$i<=$row;$i++){
//                   // $d = $rep['date'] + $i ; 
//                    $due_date = date('Y-m-d',date( strtotime("+" .$i. "days"),strtotime($rep['date'])));
//                     $edate=$k+$i;
                     //display_error($row);
//                    if($rep['date'] == $due_date)
//                    {
//                        $sdate = date('Y-m-d',date( strtotime("- 1 days"),strtotime($due_date)));
                        
                          $sq = "INSERT INTO " . TB_PREF ."sms_leave_form (class,section,student_id,leave_type,reason,from_date,to_date,sub_date,attnid) values("
                        . db_escape($_POST['class']) . "," . db_escape($_POST['section']) . ",". db_escape($_POST['stid']) . ","
                        . db_escape($_POST['leave_type']) . "," . db_escape($_POST['reason']) . ",". db_escape($dt) . ",". db_escape($ds) .",". db_escape($subdate) .",". db_escape($rep['id']) .")";
                          //display_error($sq);  
                          db_query($sq,'could not insert');
                     
                          display_notification('Leave application has been submitted');

                   // }
//                    else
//                    {
//                         $sq = "INSERT INTO " . TB_PREF ."sms_leave_form (class,section,student_id,leave_type,reason,from_date,to_date) values("
//                        . db_escape($_POST['class']) . "," . db_escape($_POST['section']) . ",". db_escape($_POST['stid']) . ","
//                        . db_escape($_POST['leave_type']) . "," . db_escape($_POST['reason']) . ",". db_escape($dt1) . ",". db_escape($dt2) .")";
//           //display_error($sq);
//                         db_query($sq,'could not insert');
//                     
//                   $sql = "UPDATE " . TB_PREF . "sms_stud_class_attendence SET attendence= 1 WHERE id=" .$rep['id'];
//                      db_query($sql,'could not insert');
//                       display_notification('data has been added');
//                    }
//                  }
                  
//                
                  //}
               } // end while
              } //end num rows if
              else{
                  display_notification('The selected student was not absent between the date'); 
              } // end num rows else
            }  // end if condition
          else
          {
               
              add_form($_POST['class'],$_POST['section'],$_POST['stid'],$_POST['leave_type'],$_POST['reason'],$_POST['fdate'],$_POST['tdate'],$subdate);
              
                  display_notification('data has been added'); 
          } // end else condition
           
        }
}
}


start_table(TABLESTYLE1);
start_row();
      label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
        end_row();
        start_row();
            label_cell('<b>Leave Form</b>','align=center colspan3=10');
        end_row();
end_table();

br();



start_form();

start_table(TABLESTYLE2,'width=22%');
start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_row(_("Class:"),'class', null, 'Select Class', true, $query);

$query=array(array('id','session_name','select id, session_name from '.TB_PREF.'sms_session
       WHERE class='.$_POST['class']." ORDER BY session_name ASC"));
combo_list_row(_("Section:"),'section', null, 'Select Section', true, $query);

$query=array('student_id','student_id','sms_student','st_section',$_POST['section']);
combo_list_row(_("Student ID:"),'stid', null, 'Select ID', true, $query);


if($_POST['stid']){
    $condition = array('student_id'=>$_POST['stid']);
    $field = array('CONCAT(first_name," ",middle_name," ",last_name) as name');
    $data = db_fetch(data_retrieve_condition("sms_students_details", $field, $condition));
}   

label_row(_("Name"),$data['name']); 
leave_type_cells(_("Leave Type"),'leave_type');
textarea_row(_("Reason"), 'reason', '',21,2);
date_row(_("From Date"), 'fdate');
date_row(_("To Date"), 'tdate');
end_row();
end_table();
br();
div_start('controls');
submit_center('submit', _("Submit"), true, '', 'default');
div_end();

end_form();

end_page();
?>
