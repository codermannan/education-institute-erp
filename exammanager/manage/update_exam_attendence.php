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
    page(_($help_context = "Update Exam Attendance"), false, false, "", $js);
}
$syear = get_current_schoolyear();
//-----------------------------------------------------------------------------------

if(list_updated('subject'))
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
    foreach($_POST['status'] as $key =>$stus){
            $chk = $stus;     
    }
        
       if($chk == 1){
        foreach($_POST['status'] as $key =>$stus){

             $id = $_POST['id'][$key];
             $reason = $_POST['reason'][$key];
             $atten = $_POST['attendance'][$key];
              
               update_student_attendance($id,$atten,$reason);
			display_notification(_('Students attendance has been added'));
       }
     }
       else{
           foreach($_POST['attendance'] as $key =>$atten){

                  $id = $_POST['id'][$key];
                  $reason = $_POST['reason'][$key];

                   update_student_attendance($id,$atten,$reason);
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

//combo_list_cells(_("Subject Code :"), 'subject', '', 'Select subject', false, $query);

if($_POST['subject']){
    $condition = array('id'=>$_POST['subject']);
    $field = array('class');
    $classval = db_fetch(data_retrieve_condition("sms_subject", $field, $condition));
}
//if($_POST['subject']){
//    $con = array('class_name'=>$classval['class'],'parent'=>'0','status'=>'1');
//    $fld = array('id');
//    $exname = db_fetch(data_retrieve_condition("sms_exam_name", $fld, $con));
//}

text_cells(null, 'student');
 
submit_cells('SearchOrders', _("Search"), '', _('Select student'), 'default');

end_row();
end_table(1);

//---------------------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2,"width=80%");
         start_row();
         
             labelheader_cell( 'SL#','width=5%');
             labelheader_cell( 'Applicant ID','width=10%');
             labelheader_cell( 'Applicant Name','width=10%');
             labelheader_cell( 'Status','width=10%'); 
             labelheader_cell( 'Mark','width=10%');

         end_row();
         

//-----------------------------------------------------------------------------------------------
function  get_student_id($stid)
{
    $sql ="SELECT CONCAT(first_name,' ',middle_name,' ',last_name) as name FROM "
    . TB_PREF ."sms_students_details WHERE student_id =" . db_escape($stid) ;
    
    $result = db_fetch(db_query($sql));

    return $result['name'];
}
         
//------------------------------------------------------------------------------------------------         
    
if(isset($_POST['SearchOrders'])){ 
    
$sl = 1; 
$sub = $_POST['subject'];  
$student= $_POST['student'];
$datasearch=$_POST['datasearch'];
$sql = get_student_update_info($student,$sub,$datasearch);

$result = db_query($sql,"data could not be found");

 if(mysql_num_rows($result)>0){
  while ($rep = db_fetch($result))
    {
      $stname = get_student_id($rep['student_id']);
         start_row();         
             check_cells(null, 'status['.$rep['id'].']', '',false,  _('set DECLARATION'));
             hidden('id['.$rep['id'].']',$rep['id']);
             label_cell($rep['student_id'],'align=center');                 
             label_cell($stname,'align=center');
             ?>
               
              <td> 
                <input type="radio" name="attendance[<?php echo $rep['id']; ?>]" value="1" <?php if($rep['status'] == 1){?>checked<?php }?>>Present
           
                <input type="radio" name="attendance[<?php echo $rep['id']; ?>]" value="0" <?php if($rep['status'] == 0){?>checked<?php }?>>Absent
              </td> 
              
               <?php
//             attendence_lst_cells('attendance['.$rep['id'].']', $rep['status']);
             text_cells(null,'reason['.$rep['id'].']', null, 20,30); 
         end_row();
     $sl++;
        }
 }
    else {
     display_notification(_('There are no students for taking attendance in this subject'));
    }
} 

end_table();

br();

div_start('controls');
  submit_center('Process', _("Process Short List"), true, '', 'default');
div_end();
//---------------------------------------------------------------------------------------------------

if (!@$_GET['popup']) {
    end_form();
    end_page();
}