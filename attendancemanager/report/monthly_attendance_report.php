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
    page(_($help_context = "View Attendance Details"), false, false, "", $js);
}
$syear = get_current_schoolyear();
//-----------------------------------------------------------------------------------

if(list_updated('class'))
$Ajax->activate('_page_body');

if(list_updated('section'))
$Ajax->activate('_page_body');

if(list_updated('subject'))
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

////..........................................................
function absent_list_view($row) {
    
        return viewer_link(null, "/sms/view/view_absent.php?studentid=". $row . "", null, null, ICON_VIEW);
   
    
    return true;
}

//------------------------------------------------------------
start_table(TABLESTYLE1,"width=90%");
    start_row();
       label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'colspan=3 align=center style="font-size:16px"');
    end_row();
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['postal_address'],'colspan=3 align=center style="font-size:15px"');
    end_row();
    start_row();
        label_cell('<b>Monthly Attendance Report</b>','align=center');
    end_row();
 end_table();
 br();
    
if (!@$_GET['popup'])  
    start_form();

start_table(TABLESTYLE_NOBORDER);

start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class :"), 'class', '', 'Select Class', true, $query);

$query=array(array('id','session_name','select id, session_name from '.TB_PREF.'sms_session
       WHERE class='.$_POST['class']." ORDER BY session_name ASC"));
combo_list_cells(_("Section :"), 'section', $_POST['session_name'], 'Select Section', true, $query);

date_cells(_('From Date'),'fdate');

date_cells(_('To Date'),'tdate');

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');


end_table();
br();
//------.......................
function absent($stid,$fdt,$tdt){
    $sql = "SELECT * FROM ".TB_PREF."sms_stud_class_attendence
            WHERE student_id='".$stid."' AND attendence=0 AND atten_date >='$fdt' 
            AND atten_date<='$tdt'";
    $query = db_query($sql);
    $res = db_fetch($query);
    $num = mysql_num_rows($query);
      
    if($num>0){
        return $norow = $num;
    }
    else{
        return $norow = 0;  
    }
}
function present($fdt,$tdt,$abs,$flag){
    $month = date_parse(date2sql($fdt));
  
    $sql = "SELECT * FROM ".TB_PREF."sms_holiday_set_up
            WHERE month = ".$month['month']."
            AND holidaydate >='$fdt' 
            AND holidaydate<='$tdt'"; 
    $query = db_query($sql);
    $res = db_fetch($query);
    $num = mysql_num_rows($query);
    $sep = $res['month'];
    $activedays = ($res['days'] - $num);
    $presentd = ($activedays - $abs);
    
    $percent = number_format2((($presentd * 100)/$activedays),2);
    if($flag == 1){
        return $presentd;
    }
    else{
        return $percent;
    }
    
        
}

//.................................

start_form();

start_table(TABLESTYLE2,"width=80%");
         start_row();
             labelheader_cell( 'SL#','width=5%');
             labelheader_cell( 'Student ID','width=10%');
             labelheader_cell( 'Student Name','width=10%');
             labelheader_cell( 'Present(In Days)','width=10%');
             labelheader_cell( 'Absent(In Days)','width=10%');
             labelheader_cell( 'Attendance Percent(%)','width=10%');
             labelheader_cell( 'View','width=10%');
             end_row();
    
if(isset($_POST['SearchOrders'])){
    
    $class = $_POST['class'];
    $section = $_POST['section'];
    $fdt = $_POST['fdate']; 
    $tdt =$_POST['tdate'];

    $sl = 1; 

$sql = "SELECT ss.student_id,CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name FROM " . TB_PREF . 
       "sms_student ss LEFT JOIN " . TB_PREF . "sms_students_details sd ON sd.student_id = ss.student_id
       WHERE ss.school_year=".$syear. 
       " AND ss.st_class=".$class." AND ss.st_section=".$section;

$result = db_query($sql,"data could not be found");
$pr = substr($fdt,3,2); 

while($rp = db_fetch($result))
{ 
    $abs = absent($rp['student_id'],$fdt,$tdt);
    $pre = present($fdt,$tdt,$abs,1);
    $per = present($fdt,$tdt,$abs,2);
    
    start_row();
    label_cell($sl,'align=center');
    label_cell($rp['student_id'],'align=center');
    label_cell($rp['name'],'align=center');
    label_cell($pre,'align=center');
    label_cell($abs,'align=center');
    label_cell($per,'align=center');
    label_cell(absent_list_view($rp['student_id']),'align=center');
    end_row();
    
   $sl++; 
}

}
end_page();
?>