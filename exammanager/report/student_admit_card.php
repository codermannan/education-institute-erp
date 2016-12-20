<?php

/* * ********************************************************************

 * ********************************************************************* */
$page_security = 'SS_SMS_STDNT_ADMT_CRD';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/exammanager/includes/db/exam_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Student Admit Card"), false, false, "", $js);
}

if(list_updated('school_year'))
$Ajax->activate('_page_body');

if(list_updated('class'))
$Ajax->activate('_page_body');
//---------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);


br();

start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class :"), 'class', '', 'Select Class', true, $query);
submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
      

end_row();
end_table(1);

if(isset($_POST['class'])){
    $class = $_POST['class'];
    $Ajax->activate('_page_body');
}
//---------------------------------------------------------------------------------------------
function exam_name($row) {
    
    $condition = array('class_name'=>$_POST['class'],'parent'=>0,'status'=>1);
    $field = array('exam_name');
    $classval = db_fetch(data_retrieve_condition("sms_exam_name", $field, $condition));
    return $classval['exam_name'];
}


//-----------------------------------------------------------------
function admit_card($row) {
        
        $condition = array('class_name'=>$_POST['class'],'parent'=>0,'status'=>1);
        $field = array('exam_name');
        $classval = db_fetch(data_retrieve_condition("sms_exam_name", $field, $condition));
        
        return viewer_link(null, "/exammanager/report/student_admit_card_view.php?Studentid=" . $row['student_id'] . " &examname=".$classval['exam_name']."&class=" .$_POST['class'], null, null, ICON_VIEW);
        return true;
}
//--------------------------------------------------------------------

$sql = get_sql_for_admit_card($_POST['class']);

$cols = array(
    _("Student ID") ,
    _("Student Name") ,
    _("Class"),
    _("Section"),
    _("Exam Name")=> array('insert' => true, 'fun' => 'exam_name', 'align' => 'center'),
    _("Admit Card") => array('insert' => true, 'fun' => 'admit_card', 'align' => 'center')
);

//---------------------------------------------------------------------------------------------------

$table = & new_db_pager('admit_card', $sql, $cols);

$table->width = "60%";

display_db_pager($table);

start_table();
    br(1);
    display_heading2(viewer_link(_("&View Student Admit Card"), "exammanager/report/view_student_admit_card_info.php?c=".$class));
end_table(2);
if (!@$_GET['popup']) {
    end_form();
    end_page();
}
?>
