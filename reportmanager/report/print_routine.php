<?php

$page_security = 'SS_SMS_CLS_RTN_VIEW';
$path_to_root = "../..";

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
    page(_($help_context = "Class Wise  Routine Print"),true);
}
if($_GET['class']){
    $condition = array('id'=>$_GET['class']);
    $field = array('class_name');
    $data = db_fetch(data_retrieve_condition("sms_create_stud_class", $field, $condition));
    
}
//-----------------------------------------------------------------------------------
  
start_table(TABLESTYLE1,'width=85%');
    start_row();
      label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
        end_row();
        start_row();
            label_cell('<b>Class Routine</b>','align=center colspan3=10');
        end_row();
        br(2);
        start_row();
            label_cell('<b>Class Name : </b>'.$data['class_name'],'align=left colspan=0');
            
        end_row();
        
end_table();
br(1);
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

start_table(TABLESTYLE2,'width=85%');
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
   
   $classperiod = classperiod($_GET['class']);

        foreach ($classperiod as $value) {
                $period = routineclass($_GET['class'],$day_id,$value);
              
        }
  
    end_row();
}

end_table();


  br(1);
  br(1);
  br(1);
   
end_page(true, false, false);
?>
