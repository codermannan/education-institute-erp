<?php

/* * ********************************************************************

 * ********************************************************************* */
$page_security = 'SS_SMS_STDNT_RESLT_STATS';
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
    page(_($help_context = "Result Status"), false, false, "", $js);
}
$syear = get_current_schoolyear();
//.....................................................
$Ajax;
//if($_POST['id'])
//    $selected_id=$_POST['id'];

if(list_updated('class'))
$Ajax->activate('_page_body');
//..............................................
if (isset($_POST['Search'])) { 
    global $Ajax;
    
    $class = $_POST['class'];
    
    $Ajax->activate('_page_body');
    
}
//..........................
if (!@$_GET['popup'])
    start_form ();

start_table(TABLESTYLE_NOBORDER);

start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class :"), 'class', '', 'Select Class', true, $query);
//display_error($_POST['class']);

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
br();

 
//start_row();
//        echo "<td>";
        start_table(TABLESTYLE2,'width=50%');
        start_row();
            labelheader_cell('Student ID');
            labelheader_cell( 'Class');            
            labelheader_cell('Exam Status');
        end_row();

         
         $sql = sql_for_result_status($syear,$class);
         $rep= db_query($sql);
         //display_error($sql);
        
         while($res=db_fetch($rep))
         {
             
             start_row();
             label_cell($res['student_id']);
             label_cell($res['class_name']);         
             label_cell($res['result']);
              end_row();
         }
         
end_table();  

end_form();
end_page();
?>
