<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_CRT_STDNT_CLS';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/attendancemanager/includes/ui/attendance_ui_lists.inc");
include_once($path_to_root . "/attendancemanager/includes/db/attendance_db.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Holiday Setup"), false, false, "", $js);

simple_page_mode(true);
if($_POST['id'])
    $selected_id=$_POST['id'];

//----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
	global $selected_id, $Mode;
        $syear = get_current_schoolyear();
        
	$ok = true;
        
//        if (strlen($_POST['month_name']) == '') 
//	{
//            $input_error = 1;
//            display_error(_("Month must be selected."));
//            set_focus('month_name');
//	}
        if ($input_error !=1)
	{
            if ($selected_id != -1)
            {          
                update_holy_data($selected_id,$_POST['month_nam'],$_POST['date_st'],$_POST['date_end'],$_POST['description']);
                display_notification(_('Selected data has been updated'));

            }
            else
            {    
                add_holy_data($syear,$_POST['month_nam'],$_POST['date_st'],$_POST['date_end'],$_POST['description']);
                display_notification(_('New holy day has been added'));
            }
        
	   $Mode = 'RESET';
        }
}
if ($Mode == 'Delete')
{
    delete_data($selected_id,'sms_holiday_set_up');
    display_notification(_('Selected data has been deleted'));
    $Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    $selected_id = -1;
    unset($_POST);
}



//---------------------------------------------------------------------------------------------

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

function get_holiday_info()
{
    $sql = "SELECT sm.month_name,sh.start_date,sh.end_date,sh.reason,sh.id FROM " . TB_PREF ."sms_holiday_set_up sh
           LEFT JOIN " . TB_PREF . "hcm_salary_month sm ON sm.id = sh.month" ;
    
//    $er = db_query($sql);
    return $sql;
}

//---------------------------------------------------------------------------------------------
if (!@$_GET['popup'])
start_form();
//    start_outer_table(TABLESTYLE1, "align=center");
//        table_section(1);
//            start_row();
//                $query=array('id','month_name','hcm_salary_month');
//                combo_list_row(_("Select Month:"), 'month_nam', $_POST['month_nam'], 'Select Month', TRUE, $query);
//            end_row();
//    end_outer_table(1);
    
    if(isset($_POST['month_nam'])){
        $month = $_POST['month_nam'];
        $Ajax->activate('_page_body');
    }
    
    start_table(TABLESTYLE);
    $sql = get_holiday_info();
    $cols = array(
                    
                    _("Month"),
                    _("Start Date"),
                    _("End Date"),
                    _("Reason"),
                    array('insert' => true, 'fun' => 'edit_link', 'align' => 'center'),
                    array('insert' => true, 'fun' => 'delete_link', 'align' => 'center')
                );
    $table = & new_db_pager('month', $sql, $cols , null, null,15);

    $table->width = "50%";
    end_table();
    display_db_pager($table);
end_form();
	//display_note(_("The marked School year is the current fiscal year which cannot be deleted."), 0, 0, "class='currentfg'");
br(1);

start_table();
    br(1);
//    display_heading2(viewer_link(_("&View Holy Day Setup"), "sms/view/view_holyday_setup.php?m=".$month));
end_table(2);
//...................................................................................

start_form();

    start_table(TABLESTYLE2);

        if ($selected_id != -1) 
            {
                if ($Mode == 'Edit')
                    {
                        $myrow = data_retrieve("sms_holiday_set_up", "id", $selected_id);
                        $_POST['month_nam']  = $myrow["month"];
                        $_POST['date_st']  = sql2date($myrow["start_date"]);
                        $_POST['date_end']  = sql2date($myrow["end_date"]);
                        $_POST['description']  = $myrow["reason"];
                    }
                hidden('selected_id', $selected_id);
            }
   $query=array('id','month_name','hcm_salary_month');
   combo_list_cells(_("Select Month:"), 'month_nam', $_POST['month_nam'], 'Select Month', FALSE, $query);

    date_row(_("Start Date:"), 'date_st');
    date_row(_("End Date:"), 'date_end');
    textarea_row(_("Reason:"), 'description', null, 15, 2);


    end_table(2);

    submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup'])
{
    end_form();
    end_page();
}
?>

