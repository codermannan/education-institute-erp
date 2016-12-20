<?php

$page_security = 'SS_SMS_XM_ROM_STNG';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/exammanager/includes/ui/exam_ui_lists.inc");
include_once($path_to_root . "/exammanager/includes/db/exam_db.inc");
simple_page_mode(true);
//------------------------------------------------------------------------------
page(_("Shift Setup"));

if ($Mode == 'Delete')
{
        delete_data($selected_id,'sms_shift');         
		display_notification(_('Selected data has been deleted'));

	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------

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

//------------------------------------------------------------------------------

$sql= "SELECT * FROM ".TB_PREF."sms_shift";
 
if (!@$_GET['popup']) {
  start_form();
    
}

$cols = array(
     _("SL#")=>array('align'=>'center'),
     _("Shift")=>array('align'=>'center'),
     _("Start Time")=>array('align'=>'center'),
     _("End Time")=>array('align'=>'center'),
     array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
     array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_shift',$sql, $cols);

$table->width = "60%";

display_db_pager($table);
echo '<br>';

 if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

        $input_error = 0;
     
         $start_time = $_POST['start_hr'].':'.$_POST['start_mm'].':'.'00';
         $end_time = $_POST['end_hr'].':'.$_POST['end_mm'].':'.'00';
	if ($input_error !=1)
	{
    	if ($selected_id != -1) 
    	{ 
		   update_shift($selected_id,$_POST['shift_name'],$start_time,$end_time);
                                   
			display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{
               $sql= "SELECT * FROM " . TB_PREF . "sms_shift WHERE shift=" .  db_escape($_POST['shift_name'])."AND start_time=" . db_escape($start_time) . " AND end_time=" . db_escape($end_time);
               
               $rep= db_query($sql);
               $result = db_fetch($rep);
            
               if($start_time == $result['start_time'] && $end_time == $result['end_time'])                   
               {
                     display_notification('data already exits');
               }
                else
                {
		  add_shift( $_POST['shift_name'],$start_time,$end_time);
			display_notification(_('Shift has been added'));
                }
    	}
		$Mode = 'RESET';
	}
}
//-------------------------------------------------------
start_form();

start_table(TABLESTYLE2);

if ($selected_id != -1) 
            {
                if ($Mode == 'Edit')
                    {
                        $myrow = data_retrieve("sms_shift", "id", $selected_id);
                        $_POST['shift_name']  = $myrow["shift"];
                        $start_time  = $myrow["start_time"];
//                        display_error($myrow["start_time"]);
                        $end_time  = $myrow["end_time"];
                    }
                hidden('selected_id', $selected_id);
            }

text_row(_("Shift:"), 'shift_name',null,'20','20');
//text_row($label, $name, $value, $size, $max);
digital_time(_("Start Time:"), 'start_hr', 'start_mm');

digital_time(_("End Time:"), 'end_hr', 'end_mm');

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);
//display_error($selected_id);
end_form();

if (!@$_GET['popup']) {
    end_form();
    end_page();
}
?>