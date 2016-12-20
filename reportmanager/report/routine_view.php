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
    page(_($help_context = "Class Wise Routine View"), false, false, "", $js);
}
//-----------------------------------------------------------------------------------
  

if (!@$_GET['popup'])
start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();

get_student_clas(_("Select Class:"), 'app_class', $_POST['app_class'], 'Select Class', true);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
function routineclass($stclass,$day,$period){
    
         $sql= "SELECT sr.id, sc.class_name,sr.week_day ,ss.subject_name,he.name,sr.period
           FROM ". TB_PREF . "sms_class_routine sr
           LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON sc.id = sr.class
           LEFT JOIN " . TB_PREF . "sms_subject ss ON ss.id = sr.subject
           LEFT JOIN " . TB_PREF . "hcm_emp he ON he.emp_code = sr.teacher
           WHERE sr.class = ".db_escape($stclass)." 
           AND sr.period =".db_escape($period)." 
           AND sr.week_day =".db_escape($day)      
              ;
         $query =  db_query($sql);
         $result = db_fetch($query);
         
         if(mysql_num_rows($query)==0){
             return label_cell(null,'style="background-color:#F65F87;"');
         }
         else{
             return label_cell($result['subject_name']."<br>".$result['name'],'align=center');
         }
   
}
function classperiod($stclass){
    
         $sql= "SELECT id FROM ". TB_PREF . "sms_class_duration
           WHERE class = ".db_escape($stclass);
         $query =  db_query($sql);
         while($result = db_fetch($query)){
             $periodid[] = $result['id'];
         }
         return $periodid;
}

start_table(TABLESTYLE2,'width=70%');
    start_row();
        labelheader_cell("Day");
        labelheader_cell("1st");
        labelheader_cell("2nd");
        labelheader_cell("3rd");
        labelheader_cell("4th");
//        labelheader_cell("Break",'rowspan=8');
        labelheader_cell("5th");
        labelheader_cell("6th");
        labelheader_cell("7th");
        labelheader_cell("8th");
     end_row();
$condition = "SELECT day,day_id FROM " . TB_PREF . "hcm_weekdays1 WHERE weekend= '0'" ;
$res = db_query($condition);
while($pr=db_fetch($res))
{
    $day_id = $pr['day_id'];
    
    start_row();
    label_cell( $pr['day'],'width=10% align=center');
   
   $classperiod = classperiod($_POST['app_class']);

        foreach ($classperiod as $value) {
                $period = routineclass($_POST['app_class'],$day_id,$value);
              
        }
  
    end_row();
}

end_table();
//-------------------------
br();

    //$stclas = $_POST['app_class'];
     div_start('det');
     display_heading2(viewer_link(_("&View This Page"), "reportmanager/report/print_routine.php?class=".$_POST['app_class']));
div_end();

if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>
