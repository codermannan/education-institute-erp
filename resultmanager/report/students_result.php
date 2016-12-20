<?php
/* * *********************************************************************************** */
$page_security = 'SS_SMS_STDNT_RSLT_VW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
//include_once($path_to_root . "/resultmanager/includes/ui/result_ui_lists.inc");
include_once($path_to_root . "/resultmanager/includes/db/result_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(1200, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Students Result"), false, false, "", $js);
}
$syear = get_current_schoolyear();
//-----------------------------------------------------------------------
$Ajax;
if($_POST['id'])
    $selected_id=$_POST['id'];

if(list_updated('class'))
$Ajax->activate('_page_body');
//-----------------------------------------------------------------------

if (isset($_POST['SearchOrders'])) 
{   
	
        if (($_POST['class']) == NULL) 
	{
		$input_error = 1;
		display_error( _('Class Must be Selected.'));
		set_focus('class');
                return false;
	} 
}
//-----------------------------------------------------------------------------

if (!@$_GET['popup'])
    start_form ();

start_table(TABLESTYLE_NOBORDER);

start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class :"), 'class', $_POST['class_name'], 'Select Class', true, $query);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');

end_row();
end_table(1);
//-----------------------------------------------------------------------

function transcript1($row) {
if($_POST['class']){
    $con = array('class_name'=>$_POST['class'],'parent'=>'0','status'=>'1');
    $fld = array('id');
    $exname = db_fetch(data_retrieve_condition("sms_exam_name", $fld, $con));
}
    $ex= $exname['id'];
    $stid= $row['student_id'];
    $class = $_POST['class'];

    if($ex)
        return viewer_link(null, "/resultmanager/report/result_view.php?ex=". $ex ."&stid=" . $stid ."&syear=" . $row['school_year']."&class=" . $class, null, null, ICON_VIEW);
          
}
function transcript2($row) {
    if($_POST['class']){
        $con = array('class_name'=>$_POST['class'],'parent'=>'0','status'=>'1');
        $fld = array('id');
        $exname = db_fetch(data_retrieve_condition("sms_exam_name", $fld, $con));
    }
        $ex= $exname['id'];
        $stid= $row['student_id'];
        $class = $_POST['class'];

        return viewer_link(null, "/resultmanager/report/narayanganj_result.php?ex=". $ex ."&stid=" . $stid ."&syear=" . $row['school_year']."&class=" . $class, null, null, ICON_VIEW);
    return true;
}
function transcript3($row) {
    
    if($_POST['class']){
    $con = array('class_name'=>$_POST['class'],'parent'=>'0','status'=>'1');
    $fld = array('id');
    $exname = db_fetch(data_retrieve_condition("sms_exam_name", $fld, $con));
    }
        $ex= $exname['id'];
        $stid= $row['student_id'];
        $class = $_POST['class'];

        return viewer_link(null, "/resultmanager/report/transcript_form_view.php?ex=". $ex ."&stid=" . $stid ."&syear=" . $row['school_year']."&class=" . $class, null, null, ICON_VIEW);
    return true;
}



$sql = get_sql_for_result($syear);

$cols = array(
    _("Student ID"),
    _("Student Name") ,
    _("Transcript Format1")=> array('fun' => 'transcript1', 'align' => 'center'),
    _("Transcript Format2")=> array('fun' => 'transcript2', 'align' => 'center'),
    _("Transcript Format3")=> array('fun' => 'transcript3', 'align' => 'center')

);

//----------------------------------------------------------------

$table = & new_db_pager('sms_student', $sql, $cols);


$table->width = "50%";

display_db_pager($table);

end_page();

?>