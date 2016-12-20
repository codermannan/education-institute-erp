<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_APLCNT_STATS';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");

page(_("Applicant Attendance Status"));
simple_page_mode(true);
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];

if(list_updated('class'))
$Ajax->activate('_page_body');

if(isset($_POST['SearchOrders']))
$Ajax->activate('_page_body');


if(isset($_POST['process']))   
{
    
    foreach($_POST['status'] as $key =>$stus){
            $chk = $stus;
            
        }
        
        if ($chk == 1)
   	{
            foreach($_POST['status'] as $key =>$stus){
                
            $atten = $_POST['attendance'][$key];
            $apid = $_POST['apid'][$key];
          
             add_applicant_status($stus,$apid,$atten,$_POST['class']);
             
                   display_notification(_('Status has been added'));
//           
           
           }
        }
    	else{
            foreach($_POST['attendance'] as $key =>$atten){
                
                 $atten = $_POST['attendance'][$key];
            $apid = $_POST['apid'][$key];
                
                add_applicant_status($stus,$apid,$atten,$_POST['class']);
                   display_notification(_('Status has been added'));
            }
            
        }
        $Ajax->activate('_page_body');
        $Mode = 'RESET';
   
}   

//------------------------------------------------------------

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class :"), 'class', '', 'Select Class', true, $query);
        
        submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
        
end_row(); 

end_table();

start_form();

start_table(TABLESTYLE2,"width=50%");
         start_row();
             labelheader_cell( 'Status','width=5%');
             labelheader_cell( 'Applicant ID','width=10%');
             labelheader_cell( 'Applicant Name','width=10%');
             labelheader_cell( 'Attendance','width=15% ');
             end_row();
    
if(isset($_POST['SearchOrders'])){ 
    
    $class = $_POST['class'];
    $atten = $_POST['attendance'];

$sql = get_sql_for_applicant_attendance($class,$atten);

$result = db_query($sql,"data could not be found");
 if(mysql_num_rows($result)>0){
  while ($rep = db_fetch($result))
    {
      
         if($r['applicant_id'] != $rep['applicant_id']){
         start_row();
             check_cells(null, 'status['.$rep['id'].']', '',false,  _('set DECLARATION'));
             label_cell( $rep['applicant_id'],'align=center');
             hidden('apid['.$rep['id'].']', $rep['applicant_id']);
              label_cell( $rep['name'],'align=center');
                 ?>
               
              <td> 
                <input type="radio" name="attendance[<?php echo $rep['id']; ?>]" value="1" <?php echo $pre; ?>checked >Present
           
                <input type="radio" name="attendance[<?php echo $rep['id']; ?>]" value="0" <?php echo $ab; ?>>Absent
              </td> 
              
               <?php
           
         end_row();
            }
    } 
 }
 else {
     display_notification(_('There are no students for attendance'));
 }
} 
end_table();

br();

div_start('controls');
  submit_center('process', _("Take Attendance"), true, '', 'default');
div_end();
//-----------------

if (!@$_GET['popup']) {
    end_form();
    end_page();
}
?>


