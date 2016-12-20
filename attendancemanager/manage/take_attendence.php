<?php

/* * ********************************************************************
 * ********************************************************************* */
$page_security = 'SS_SMS_CLS_STDNT_ATNDNC';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/attendancemanager/includes/ui/attendance_ui_lists.inc");
include_once($path_to_root . "/attendancemanager/includes/db/attendance_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Take Students Attendance"), false, false, "", $js);
}
$syear = get_current_schoolyear();
//-----------------------------------------------------------------------------------
if(list_updated('class'))
$Ajax->activate('_page_body');

if(list_updated('section'))
$Ajax->activate('_page_body');

if(isset($_POST['SearchOrders']))
$Ajax->activate('_page_body');

//-----------------------------------------------------------------------------------


if (isset($_POST['SearchOrders'])) 
{   
	
        if (strlen($_POST['class']) == '') 
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
//----------------------------------
if (isset($_POST['Process'])) 
{     
      foreach($_POST['stchk'] as $key =>$val){
           $chk = $val;
       }   
        if ($chk == 1){
          foreach($_POST['stchk'] as $key =>$val){
             
               
           $attn= $_POST['attendance'][$key];
           $stid = $_POST['stid'][$key];
            //display_error($attn);
           if ($attn == '') {
		$input_error = 1;
   	        display_error( _('Attendence field  must be selected.'));
		set_focus('attendance');
                return false;
            } 
            $dt= date2sql(Today());
          $sql = "SELECT * FROM " . TB_PREF ."sms_stud_class_attendence WHERE student_id=" . db_escape($stid) . "AND sclass=" . db_escape($_POST['class']). "AND section=" . db_escape($_POST['section']) . "AND date=" . db_escape($dt) ; 
    
          $res= db_fetch(db_query($sql));
          
          if($stid == $res['student_id'] && $_POST['class'] == $res['sclass'] && $_POST['section'] == $res['section'] && $dt == $res['date'])
          {
              display_notification('attendance already taken for selected date');
          }
          else
          {
              
            add_attendance($stid,$syear,$_POST['class'],$_POST['section'],$attn);
            display_notification(_('Stdents attendance has been added'));
         }
           }
       }
    	else{
            foreach($_POST['attendance'] as $key =>$attn){
                
            $stid = $_POST['stid'][$key];
            if ($attn == '') {
		$input_error = 1;
		display_error( _('Attendence field  must be selected.'));
		set_focus('attendance');

            }
          
         add_attendance($stid,$syear,$_POST['class'],$_POST['section'],$attn);
         
          display_notification(_('Students attendance has been added'));
          
          }
       
      }
        
        
}
//---------------------------------------------------------------------------------------------
if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class :"), 'class', '', 'Select Class', true, $query);

$query=array(array('id','session_name','select id, session_name from '.TB_PREF.'sms_session
       WHERE class='.$_POST['class']." ORDER BY session_name ASC"));
combo_list_cells(_("Section :"), 'section', $_POST['session_name'], 'Select Section', false , $query);

//$query=array('id','period','sms_class_duration','school_year = '.$syear.' AND class = '.$_POST['class'].' AND section',$_POST['section']);
//combo_list_cells(_("Period :"), 'period', $_POST['period'], 'Select Period', false, $query);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');

end_table();
br();
//---------------------------------------------------------------------------------------------

function get_sql_for_attendance($cls,$sec)
{
    $sql = "SELECT student_id,roll_number,st_class,st_section FROM " . TB_PREF . "sms_student WHERE st_class=" . db_escape($cls). "AND st_section=" . db_escape($sec) ;
   
       $query =  db_query($sql);
         while($result = db_fetch($query)){
             $studid = $result['student_id'];
       return $studid;
             }
         //$result = db_fetch($query);
         
         
}

function check_for_leave($stid)
{
   $dt= date2sql(Today()); 
  
   $sql = "SELECT * FROM " . TB_PREF . "sms_leave_form WHERE student_id =" . db_escape($stid). " AND approve = 1 AND from_date <=". db_escape($dt) . "AND to_date >=" . db_escape($dt);
   $result = db_query($sql,"data could not be found");
   
    if(mysql_num_rows($result)>0){
        
        $stres = db_fetch($result);
        
        return $stres['student_id'];
    }
}

function check_date($stid,$date)
{
    $dt= date2sql(Today());
    $sl = "SELECT student_id,atten_date FROM " . TB_PREF ."sms_stud_class_attendence WHERE atten_date=" . db_escape($date) . " AND student_id=" . db_escape($stid) ;
    //display_error($sl);
    $res= db_query($sl);
    $rows= mysql_num_rows($res);
    //$result= db_fetch($res);
    return $rows;
}


start_form();

start_table(TABLESTYLE2,"width=60%");
         start_row();
             labelheader_cell( 'SL#','width=5%');
             labelheader_cell( 'Student ID','width=6%');
             labelheader_cell( 'Student Name','width=12%');
             labelheader_cell( 'Roll NO','width=5%');
             labelheader_cell( 'Attendance','width=10% ');
             end_row();
    
if(isset($_POST['SearchOrders'])){  
    
    
    $class = $_POST['class'];
    $section = $_POST['section'];
    $sl = 1; 
 
$sql = take_student_attendance($syear,$class,$section);
$result = db_query($sql,"data could not be found");



  while ($rep = db_fetch($result))
    {
     $re=  check_for_leave($rep['student_id']);
     $dt= date2sql(Today());
     $dcheck = check_date($rep['student_id'],$dt);
     //display_error($dcheck);
     if($dcheck == 1 )
     {
         //display_notification('attendance already taken for the day');
     }

         else {
             start_row();
             check_cells(null, 'stchk['.$rep['id'].']', '',false, '','align=center width=5%');
             label_cell( $rep['student_id'],'align=center');
             hidden('stid['.$rep['id'].']', $rep['student_id']);
             label_cell( $rep['name'],'align=center');
             label_cell( $rep['roll_number'],'align=center');
             hidden('sroll['.$rep['id'].']', $rep['roll_number']);
        
             
             if($re == $rep['student_id']){
                 label_cell('On Leave',"style='background-color: #FFC'");
             }
             else{
              
                ?>
               
              <td> 
                <input type="radio" name="attendance[<?php echo $rep['id']; ?>]" value="1" <?php echo $pre; ?>checked >Present
           
                <input type="radio" name="attendance[<?php echo $rep['id']; ?>]" value="2" <?php echo $ab; ?>>Absent
              </td> 
              
               <?php
                
             }
         
         end_row();
         }
     $sl++;
    }
// }
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