<?php

/* * ********************************************************************
  
 * ********************************************************************* */
$page_security = 'SS_SMS_CLAS_WISE_STDNT';
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
    page(_($help_context = "Class Wise Student Report"), false, false, "", $js);
}

//-------------------------------------------------------------------------------------------


if (!@$_GET['popup'])
start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();

get_student_clas(_("Select Class:"), 'app_class', $_POST['app_class'], 'Select Class', true);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
end_form();
//---------------------------------------------------------------------------------------------


start_table(TABLESTYLE2,"width=80%");
         start_row();
             labelheader_cell( 'SL#','width=5%');
             labelheader_cell( 'Student ID','width=5%');
             labelheader_cell( 'Student Name','width=6% ');
             labelheader_cell( 'Class','width=7%'); 
         end_row();
         
           
         
//if(list_updated('app_class')){   
    $class = $_POST['app_class'];
    $sql = get_class_wise_student($class);
    
    $result = db_query($sql,"data could not be found");
 
 $sl=1;
  while ($rep = db_fetch($result))
    {  
         start_row();
            
             label_cell($sl ,'width=5% align=center');
             label_cell($rep['student_id'],'width=10% align=center');
             label_cell($rep['name'],'width=15% height=16');
             label_cell($rep['class_name'],'width=7% align=center');  
         end_row();
           
         $sl++;
    }
//} 
end_table();

//---------------------------------------------------------------------------------------------------

br();

 div_start('det');
     display_heading2(viewer_link(_("&Print Class Wise Student Report"), "/reportmanager/report/print_stud_report.php?class=".$_POST['app_class']));
div_end();

if (!@$_GET['popup']) {

    end_page();
}