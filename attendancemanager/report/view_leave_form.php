<?php

/* * ********************************************************************
 
 * ********************************************************************* */
$page_security = 'SS_SMS_STDNT_WISE_PAYMNT_REP';
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
    page(_($help_context = "Student Leave Form View"), false, false, "", $js);
}

//-----------------------------------------------------------------------------------

if(list_updated('class'))
$Ajax->activate('_page_body');

if(isset($_POST['SearchOrders']))
$Ajax->activate('_page_body');
//---------------------------------------------------------------------------------------------
$cls = $_POST['class'];
$sec = $_POST['section'];
//----------------------------------------------------
if (isset($_POST['SearchOrders'])) 
{   
	
        if (strlen($_POST['class']) == '') 
	{
		$input_error = 1;
		display_error( _('Department must be selected.'));
		set_focus('class');
                return false;
	} 
        
        elseif (strlen($_POST['section']) == '') 
	{
		$input_error = 1;
		display_error( _('Shift must be selected.'));
		set_focus('section');
                return false;
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

$query=array('id','session_name','sms_session','class',$_POST['class']);
combo_list_cells(_("Section :"), 'section', $_POST['session_name'], 'Select Section', true, $query);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
//---------------------------------------------------------------------------------------------
function leave_list_view($row) {
    
        return viewer_link(null, "/attendancemanager/report/leave_form_view.php?id=" . $row['id'] . "", null, null, ICON_VIEW);
   
    
    return true;
}

function leave_report_view($row) {
    
        return viewer_link(null, "/attendancemanager/report/leave_report.php?stid=" . $row['student_id'] . "", null, null, ICON_VIEW);
   
    
    return true;
}
//---------------------------------------------------------------------------------------------
//
function get_info_for_leave_view($cls,$sec)
{
    $sql = "SELECT slf.student_id,CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name,sc.class_name,ss.session_name,slf.reason,slf.from_date,slf.to_date,slf.id,slf.approve,slf.attnid FROM "
    . TB_PREF . "sms_leave_form slf LEFT JOIN " . TB_PREF . "sms_students_details sd ON sd.student_id = slf.student_id LEFT JOIN "
    . TB_PREF ."sms_create_stud_class sc ON sc.id= slf.class LEFT JOIN "
    . TB_PREF . "sms_session ss ON slf.section=  ss.id
    WHERE slf.class=" . db_escape($cls) . " AND slf.section=" . db_escape($sec);

    return $sql;
}

function approve($row) {
 
     if($row['approve'] == 1)
     {
         return Approved;
     }
     else
     {
     return pager_link( _("Approved Leave"),
            "/attendancemanager/manage/approve_leave.php?id=". $row['id']." & attnid=".$row['attnid'],ICON_MONEY);
     }

        return true;
}

//---------------------------------------------------------------------------------------------

$sql = get_info_for_leave_view($cls,$sec);

$cols = array(
    _("Student ID") ,
    _("Student Name") ,
    _("Class") ,
    _("Section") ,
    _("Reason"),
    _("From Date"),
    _("To Date"),
    _("Leave Application") => array('insert' => true, 'fun' => 'leave_list_view', 'align' => 'center'),
    _("Absent Report") => array('insert' => true, 'fun' => 'leave_report_view', 'align' => 'center'),
    _("Status") => array('insert' => true, 'fun' => 'approve', 'align' => 'center')
);

//---------------------------------------------------------------------------------------------------

$table = & new_db_pager('sms_leave_form', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}
