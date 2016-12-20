<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_STDNT_XM_SCHDL';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/exammanager/includes/ui/exam_ui_lists.inc");
include_once($path_to_root . "/exammanager/includes/db/exam_db.inc");


$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Guard Distribution"), @$_REQUEST['popup'], false, "", $js); 
simple_page_mode(true);
//---------------------------------------------------------------------------------- 
if($_POST['id'])
    $selected_id=$_POST['id'];

if(list_updated('shift'))
$Ajax->activate('_page_body');

if(list_updated('mannandate'))
$Ajax->activate('_page_body');

if(list_updated('room'))
$Ajax->activate('_page_body');
//-----------------------------------------------------
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
    $input_error = 0;

     $date =$_POST['mannandate'];
      
       if ($input_error !=1)
	{
    	    if ($selected_id != -1) 
    	        { 
		    update_guard_set_up($selected_id,$_POST['shift'],$date,$_POST['room'],$_POST['teacher_name']);    		
		
                    display_notification(_('Selected Schedule has been updated'));
    	        } 
    	    else 
                {
                         $sl = "SELECT shift,date,allocated_room,teacher  FROM " . TB_PREF . "sms_guard_set_up WHERE shift=" . db_escape($_POST['shift']) . " AND date=" . db_escape($date) .
                               " AND allocated_room=" . db_escape($_POST['room']) . " AND teacher=" . db_escape($_POST['teacher_name']);

                       $pr = db_query($sl);
                       $result = db_fetch($pr); 

                    $sql= "SELECT shift,date,teacher FROM " . TB_PREF . "sms_guard_set_up WHERE date=" . db_escape($date) .
                          " AND teacher=" . db_escape($_POST['teacher_name']) ." AND shift=" . db_escape($_POST['shift']);

                    $res= db_fetch(db_query($sql));
        //  
                   if($_POST['shift'] == $result['shift'] AND $date == $result['date'] AND $_POST['room'] == $result['allocated_room'] AND $_POST['teacher_name'] == $result['teacher']) 
                   {

                           display_notification('set up already exits');
                    }  

                    elseif($_POST['shift'] == $res['shift'] AND $date == $res['date'] AND $_POST['teacher_name'] == $res['teacher'])
                    {
                            display_notification('Teacher is already allocated for current shift');
                    }
                    else
                    {

                           add_guard_set_up($_POST['shift'],$date,$_POST['room'],$_POST['teacher_name']);
                            display_notification(_('Schedule has been added'));
                    }
        //            
                }
	$Mode = 'RESET';
	}
         
}
//-----------------------------------------------------------
if ($Mode == 'Delete')
    
{
	  delete_data($selected_id, 'sms_guard_set_up');
          	
    display_notification(_('Selected data has been deleted'));
    
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//---------------------------------------------------------
function edit_link($row) 
{
    
        return "<center>".button("Edit".$row["id"], _("Edit"), _("Edit"), ICON_EDIT).
        "</center>";

}

function delete_link($row) 
{
    submit_js_confirm("Delete".$row['id'],
                    sprintf(_("Are you sure you want to delete ?")));
   
     return  "<center>".button("Delete".$row["id"], _("Delete"), _("Delete"), ICON_DELETE).
        "</center>";
}
function dateformate($row) 
{
    return sql2date($row['date']);
}

//----------------------------------------------------------------------------------
if (!@$_GET['popup'])
    start_form();
 
$rep = get_sql_for_guard();

$cols = array(
     _("Shift")=>array('align'=>'center'),
     _("Date")=>array('fun'=>'dateformate', 'align'=>'center'),
     _("Allocated Room")=>array('align'=>'center'),
     _("Teacher")=>array('align'=>'center'),
     array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
     array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = &new_db_pager('sms_guard_set_up', $rep, $cols);

$table->width = "60%";

display_db_pager($table);
echo '<br>';
//------------------------------------------------------

start_form();
start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
    if ($Mode == 'Edit') 
        {          
            $myrow = data_retrieve("sms_guard_set_up", "id", $selected_id);
        	$_POST['shift'] = $myrow["shift"];
		$_POST['date']  = $myrow["date"];
                $_POST['room']  = $myrow["allocated_room"];
                $_POST['teacher_name'] = $myrow["teacher"];
                $_POST['status'] = $myrow["status"];               
	}
	hidden('id', $selected_id);
}

$qu=array('id','shift','sms_shift');
combo_list_row(_("Select Shift :"), 'shift', $_POST['shift'], 'Select Shift', true, $qu);


$query=array('exam_id','date','sms_stud_exam','shift',$_POST['shift']);
combo_list_row(_("Select Date :"), 'mannandate', $_POST['date'], 'Select Date', true, $query);

$query=array(array('id','room_no','select rs.id, rs.room_no from '.TB_PREF.'sms_stud_exam se LEFT JOIN ' .TB_PREF .
       'sms_room_setup rs ON rs.id = se.room WHERE se.date ='.db_escape($_POST['mannandate'])));
combo_list_row(_("Allocated Room:"), 'room', $_POST['room'], 'Select Room', true, $query);

if($_POST['room'])
    {
        $sql = "SELECT std.class_name FROM " . TB_PREF . "sms_stud_exam stex
           LEFT JOIN " . TB_PREF . "sms_create_stud_class std ON stex.class_name = std.id
           WHERE stex.date=".db_escape($_POST['mannandate'])." AND stex.shift=".db_escape($_POST['shift'])." AND stex.room=".db_escape($_POST['room']);
        $data = db_fetch(db_query($sql));
    }   

label_row(_("Class Name"),$data['class_name']);

$gradeid = db_fetch(db_query("SELECT id FROM ".TB_PREF."hcm_grade WHERE grade = 'Teacher'"));

$query=array('emp_code','name','hcm_emp','grade',$gradeid['id']);
combo_list_row(_("Teacher :"), 'teacher_name', $_POST['name'], 'Select Teacher', false, $query);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup'])
    {
        end_form();
        end_page();
    }

?>


 
