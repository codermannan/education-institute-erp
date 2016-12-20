<?php
$page_security = 'SS_SMS_REC_OF_COUR_WORK';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/sms_ui.inc");
include_once($path_to_root . "/resultmanager/includes/db/result_db.inc");

page(_("Students' Course Work Report"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
 

start_table(TABLESTYLE1);
start_row();
      label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
        end_row();
        start_row();
            label_cell('<b>Course Work Record Form</b>','align=center colspan3=10');
        end_row();
end_table();

br();

start_table(TABLESTYLE2, "width=90% height=20%");
 
start_row();
labelheader_cell('Roll Number ', " rowspan=2 style='background-color:#FFFFFF'");
labelheader_cell('Student Name', " rowspan=2 style='background-color:#FFFFFF'");


labelheader_cell('Half Yearly/Primary Selection Examination', "colspan=5 style='background-color:#FFFFFF'");
labelheader_cell('Final/Final Selection Examination', "colspan=5 style='background-color:#FFFFFF'");

end_row();

$th = array(_('Class Work(practical work)'), _('Home Work()'), _('Class Test(written/practical)'), _('Total'), _('Average'), 
    _('Class Work(practical Work)'), _('Home Work()'), _('Class Test(written/practical)'), _('Total'), _('Average'));

table_header($th);

start_row();
             label_cell( '','width=5%');
             label_cell( '','width=6%','heigth=10%');
             label_cell( '','width=5%');
             label_cell( '','width=5%');
             label_cell( '','width=5%');
             label_cell( '','width=5%');
             label_cell( '','width=6%');
             label_cell( '','width=5%');
             label_cell( '','width=5%');
             label_cell( '','width=5%');
             label_cell( '','width=5%');
             label_cell( '','width=5%');
           
             
         end_row();
 
 
end_table();
end_form();
echo '<br>';
//----------------------------------------------------------------------------------

 start_table(TABLESTYLE1,"width=90%");
         start_row();
             label_cell( '','colspan=16  height=30');  
         end_row();
         start_row();
             label_cell( '<u><b>_____________________________</b></u>','colspan=3 align=center');           
             label_cell( '_______________________','align=right colspan=10'); 
         end_row();
         start_row();
             label_cell( 'Signature of Specified Subject Teacher','colspan=3 align=center');
             label_cell( 'signature of professor','align=right colspan=3');  
         end_row();
         start_row();
             label_cell( '','colspan=16  height=10');  
         end_row();
   end_row();
  end_table();

end_page();
?>
