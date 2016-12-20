<?php
/* * ****************************************************************** */
$page_security = 'SS_SMS_CLS_RTN_VIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/reportmanager/includes/ui/report_ui_lists.inc");
include_once($path_to_root . "/reportmanager/includes/db/report_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Exam Routine View"), false, false, "", $js);
}
//-----------------------------------------------------------------------------------
  

if (!@$_GET['popup'])
start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
        ORDER BY class_name ASC'));
combo_list_cells(_("Select Class :"), 'app_class', null, 'Select Class', true, $query);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);

function showdata($stclass,$subject){
    
         $sql= "SELECT ex.date,ex.time,rm.room_no FROM ". TB_PREF . "sms_stud_exam ex
                LEFT JOIN " . TB_PREF . "sms_room_setup rm ON ex.room = rm.id
                WHERE class_name = ".db_escape($stclass)." AND subject_name=".db_escape($subject);
         $query =  db_query($sql);
         return $query;
}

start_table(TABLESTYLE2,'width=70%');
    start_row();
        labelheader_cell("Subject Code");
        labelheader_cell("Subject Name");
        labelheader_cell("Exam Date");
        labelheader_cell("Exam Time");
        labelheader_cell("Room No");
     end_row();
$condition = "SELECT sub_code,subject_name,id FROM " . TB_PREF . "sms_subject WHERE class=".$_POST['app_class'] ;
$res = db_query($condition);
while($pr=db_fetch($res))
{
    
    $data = db_fetch(showdata($_POST['app_class'],$pr['id']));
    //display_error($pr['id']);
    start_row();
    label_cell( $pr['sub_code'],'width=10% align=center');
    label_cell( $pr['subject_name'],'width=10% align=center');
    label_cell( sql2date($data['date']),'width=10% align=center');
    label_cell( $data['time'],'width=10% align=center');
    label_cell( $data['room_no'],'width=10% align=center');

    end_row();
}

end_table();
//-------------------------
br();

   $stclas = $_POST['app_class'];
   $d= $res['id'];
   $id = $d;
     div_start('det');
     display_heading2(viewer_link(_("&Print Schedule"), "sms/view/print_exam_schudule.php?class=$stclas"));
div_end();

if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>

