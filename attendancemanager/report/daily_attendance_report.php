<?php

/* * ********************************************************************
 
 * ********************************************************************* */
$page_security = 'SS_SMS_DAY_WISE_SRDNT_VW';
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
    page(_($help_context = "Student Attendance Status"), false, false, "", $js);
}
$syear = get_current_schoolyear();
//-----------------------------------------------------------------------------------

if (list_updated('class'))
    $Ajax->activate('_page_body');

if (list_updated('section'))
    $Ajax->activate('_page_body');

if (list_updated('subject'))
    $Ajax->activate('_page_body');

if (isset($_POST['SearchOrders']))
    $Ajax->activate('_page_body');

//-----------------------------------------------------------------------------------
$class = $_POST['class'];
$section = $_POST['section'];
$dt = $_POST['date'];

//if (isset($_POST['SearchOrders'])) 
//{   
//	
//        if (strlen($_POST['class']) == '') 
//	{
//		$input_error = 1;
//		display_error( _('Student class must be selected.'));
//		set_focus('class');
//                return false;
//	} 
//        
//        elseif (strlen($_POST['section']) == '') 
//	{
//		$input_error = 1;
//		display_error( _('Student section must be selected.'));
//		set_focus('section');
//                return false;
//	} 
//}
////..........................................................
//function absent_list_view($row) {
//    
//        return viewer_link(null, "/sms/view/view_absent.php?studentid=". $row . "", null, null, ICON_VIEW);
//   
//    
//    return true;
//}
//------------------------------------------------------------
start_table(TABLESTYLE1, "width=90%");
start_row();
label_cell($_SESSION['SysPrefs']->prefs['coy_name'], 'colspan=3 align=center style="font-size:16px"');
end_row();
start_row();
label_cell($_SESSION['SysPrefs']->prefs['postal_address'], 'colspan=3 align=center style="font-size:15px"');
end_row();
start_row();
label_cell('<b>Student Attendance Status</b>', 'align=center');
end_row();
end_table();
br();

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

start_row();

$query = array(array('id', 'class_name', 'select id, class_name from ' . TB_PREF . 'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class:"), 'class', '', 'Select Class', true, $query);

$query = array(array('id', 'session_name', 'select id, session_name from ' . TB_PREF . 'sms_session
       WHERE class=' . $_POST['class'] . " ORDER BY session_name ASC"));
combo_list_cells(_("Section:"), 'section', $_POST['session_name'], 'Select Section', true, $query);

date_cells(_('Date:'), 'fdate');

//date_cells(_('To Date'), 'tdate');

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');


end_table();
br();

//------.......................
//
//function atten_info($date, $stid) {
//    $dt = $date;
//    $sql = "SELECT student_id,from_date FROM " . TB_PREF . "sms_leave_form WHERE from_date= '$dt' AND student_id= '$stid'";
//
//    $re = db_query($sql);
//    $rows = mysql_num_rows($re);
//    return $rows;
//}

function get_atten_info($sid,$dt)
{
 
//    $dt=  Today();
    $sql = "SELECT * FROM " . TB_PREF . "sms_stud_class_attendence
           WHERE student_id=" . db_escape($sid). " AND atten_date=" . db_escape($dt) ;

    $re = db_query($sql);
//    $query = mysql_num_rows($re);
    return $re;
}

function get_leave_info($sid,$fdt)
{
    $query = "SELECT student_id,from_date FROM " . TB_PREF . "sms_leave_form
             WHERE student_id=" . db_escape($sid). " AND from_date=" . db_escape($fdt) ;
//    display_error($query);
    $re = db_query($query);
    $myrw = mysql_num_rows($re);
    return $myrw;
}

//.................................

start_form();

start_table(TABLESTYLE2, "width=60%");
$k = 0;
table_section_title("Date: " . $_POST['tdate'], 5);
start_row();
//             labelheader_cell( 'SL#','width=5%');
labelheader_cell('Student ID', 'width=10%');
labelheader_cell('Student Name', 'width=15%');
labelheader_cell('Class', 'width=10%');
labelheader_cell('Section', 'width=10%');
labelheader_cell('Atten Status', 'width=10%');
end_row();

//if (isset($_POST['SearchOrders'])) {

    $class = $_POST['class'];
    $section = $_POST['section'];
    $dt = date2sql($_POST['fdate']);
    $sid = $_POST['stid'];
    $sl = 1;
//
    $sql = "SELECT ss.student_id,CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name,
        sc.class_name,sse.session_name  
        FROM " . TB_PREF . "sms_student ss 
        LEFT JOIN " . TB_PREF . "sms_students_details sd ON sd.student_id = ss.student_id
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON sc.id = ss.st_class
        LEFT JOIN " . TB_PREF . "sms_session sse ON sse.id = ss.st_section
        WHERE ss.school_year=" . $syear;
//    display_error($sql);
    if (isset($_POST['SearchOrders'])) {
//    if(isset($class) && isset($section))
//    {
        $sql .= " AND ss.st_class=" . db_escape($class)." AND ss.st_section=" . db_escape($section);
//        
//    }
    }
// display_error($sql);
    $result = db_query($sql);
    
//    display_error($dataFound);
    while ($rp = db_fetch($result)) {
        $k++;
        $abs = get_atten_info($rp['student_id'],$dt);
        
        $no_ofAbsent= mysql_num_rows($abs);
 
//        print_r($abs);
        
        $leave = get_leave_info($rp['student_id'],$dt);
        
        if($no_ofAbsent > 0)
        {
        start_row("style='background-color: DarkSeaGreen'");
        label_cell($rp['student_id'], 'align=center');
        label_cell($rp['name'], 'align=left');
        label_cell($rp['class_name'], 'align=left');
        label_cell($rp['session_name'], 'align=left');
        label_cell('Absent', "align=center ");
        end_row();
        }
        
        elseif($leave> 0)
            {
        start_row("style='background-color: #FFC'");
        label_cell($rp['student_id'], 'align=center');
        label_cell($rp['name'], 'align=left');
        label_cell($rp['class_name'], 'align=left');
        label_cell($rp['session_name'], 'align=left');
        label_cell('Leave', "align=center ");
        end_row();
        }
        else
              {
        start_row();
        label_cell($rp['student_id'], 'align=center');
        label_cell($rp['name'], 'align=LEFT');
        label_cell($rp['class_name'], 'align=left');
        label_cell($rp['session_name'], 'align=left');
        label_cell('Present', "align=center ");
        end_row();
        }
        $sl++;
        $totalAbsent += $no_ofAbsent;
        $totalLeave += $leave;
}

$totalStudent = db_num_rows($result); 
$presentStudent = ($totalStudent - ($totalAbsent + $totalLeave));

start_row();
label_cell('<b>Total: </b>','align=right');
label_cell('Student: '.$totalStudent.', Present : '.$presentStudent.', Absent :'.$totalAbsent.' Leave, :'.$totalLeave, 'colspan=4 align=left');
//label_cell($k, 'align=center');
end_row();
end_table();
br(1);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}
?>