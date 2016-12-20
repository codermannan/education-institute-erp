<?php

/* * ********************************************************************
  
 * ********************************************************************* */
$page_security = 'SS_SMS_STDNT_PAS_LST';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/resultmanager/includes/db/result_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Student Pass List"), false, false, "", $js);
}
$syear = get_current_schoolyear();
//-----------------------------------------------------------------------------------

//------------------------------------

if(list_updated('class'))
$Ajax->activate('_page_body');

if(list_updated('section'))
$Ajax->activate('_page_body');

if(isset($_POST['SearchOrders']))
$Ajax->activate('_page_body');
//---------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);


br();

start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class :"), 'class', $_POST['class_name'], 'Select Class', true, $query);

$query=array('id','session_name','sms_session','class',$_POST['class']);
combo_list_cells(_("Section :"), 'section', $_POST['session_name'], 'Select Session', true, $query);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
//---------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------
function test_status($row) {
    
        if($row['result'] == 1){
            return P;
        }
        else{
            return WP;
        }
    
}
function admission($row) {
    $condition = array('student_id'=>$row['student_id']);
    $field = array('applicant_id');
    $dt = db_fetch(data_retrieve_condition('sms_students_details', $field, $condition));
    
    $sql= "SELECT sum( hs.amount )
           FROM " .TB_PREF . "sms_tbl_receivable tr
           LEFT JOIN " .TB_PREF . "sms_payment_head_setting hs ON tr.head_id = hs.id
           WHERE tr.school_year =" .db_escape($syear)  ." 
           AND (tr.student_id=" .  db_escape($row['student_id']) . " 
           OR tr.applicant_id=" .  db_escape($dt['applicant_id']) . ") 
           AND tr.realize = '0'";
   
          $result = db_query($sql,'select failed');
          $rw = db_fetch($result);
          
          if($rw['sum( hs.amount )']>0){
              //return pager_link( _("Promotion"),"/paymentmanager/manage/student_payment_receive.php?studentid=" . $row['student_id']."&class=" .$_POST['class']."&syear=" .$syear,ICON_CREDIT);
              return pager_link( _("Credit List"), "/paymentmanager/manage/promoted_student_payment_setting.php?studentid=" . $row['student_id']."&class=" .$row['st_class']."&rid=" .$row['tableid'], ICON_CREDIT);
          }
          else{
            if($row['flag'] == 2){
                return 'Admitted';
            }elseif($row['flag'] == 0){
                return pager_link( _("Payment Setting"), "/paymentmanager/manage/promoted_student_payment_setting.php?studentid=" . $row['student_id']."&class=" .$row['st_class']."&rid=" .$row['tableid'], ICON_ADD);
            }
            else{
                return viewer_link(null, "paymentmanager/report/student_payment_slip.php?Applicantid=" . $row['student_id'] . "", null, null, ICON_VIEW);
            }
            return true;  
          }
}

//---------------------------------------------------------------------------------------------

$class = $_POST['class'];
$section = $_POST['section'];


$sql = get_sql_for_pass_promo_student($class,$section);

$cols = array(
    _("Student ID") => array('align' => 'center'),
    _("Student Name") ,
    _("Class") => array('align' => 'center'),
    _("Section") => array('align' => 'center'),
    _("Grade") => array('align' => 'center'),
    _("GPA") => array('align' => 'center'),
    _("Status")=> array('align' => 'center'),
    _("Print") => array('insert' => true, 'fun' => 'admission', 'align' => 'center')
);

//---------------------------------------------------------------------------------------------------

$table = & new_db_pager('sms_test_result', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}