<?php
$page_security = 'SS_SMS_STDNT_RSLT_VW';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/sms_ui.inc");
include_once($path_to_root . "/sms/includes/db/sms_db.php");
//include_once($path_to_root . "/sms/includes/db/applicant_db.inc");

page(_("Students Evaluation Report"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
 start_table(TABLESTYLE1,"width=80%");
        start_row();
//           label_cell('SCHOOL lOGO','height=100 width=10%');
           label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
        end_row();
        start_row();
            label_cell('<b>Number Form</b>','align=center colspan3=10');
        end_row();
 end_table();
br();
br();
 start_table(TABLESTYLE1,'width=50%');
  start_row();
            label_cells('<b>Class :</b>');
            
            check_cells("Class VI", 'classvi', 'classvi');
            check_cells('Class VII','classvii', $value);
            check_cells('Class VIII','classviii', $value);
            check_cells('Class IX', 'classix', $value);
            check_cells('Class X','classx', $value);
            check_cells('Give Tick', '', $value);
  end_row();
// end_table();
// br();
// start_table(TABLESTYLE ,'align=center' , 'width=50%'); 
  start_row();
      
          
         label_cells('<b>Section:</b>');
         check_cells('A', $name, $value);
         check_cells('B', $name, $value);
         check_cells('C', $name, $value);
         
       end_row();
        //end_table();
         br();
        // start_table(TABLESTYLE2, 'width=50%');
         start_row();
      label_cells('<b>Exam Name:</b>');
      
      check_cells("Half Yearly", 'half_year');
      check_cells('Primary Selection', 'primary_select', $value, $submit_on_change, 'align=center');
      check_cells('Final', 'final_mark', $value, $submit_on_change);
      check_cells('Final Selection', '', $value, $submit_on_change);
      
 
      
  end_row();
  end_table();
 //br();
  start_table(TABLESTYLE1, 'width=50%');
  start_row();
    label_cell( '<b>Subject :</b>','width=8%');
    //label_cell('');
//    label_cell();
//    label_cell();
    label_cell('.....................................');
    
   
  end_row();
end_table();
br();
start_table(TABLESTYLE2, 'width=90%');
     end_row();
         start_row();
             labelheader_cell( 'Serial No','width=5%');
             labelheader_cell( 'Roll Number','width=5%');
             labelheader_cell( 'Continuous Evaluation Mark(1)','width=6% ');
             labelheader_cell('Multiple Choices Mark(2)','width=5%');
             labelheader_cell('Creative Mark(3)', 'width=5%');
             labelheader_cell('Practical Mark(4)', 'width=5%');
             labelheader_cell('Total Mark(2+3+4)(5)', 'width=5%');
             labelheader_cell( '80% of Total Mark(6)','width=7%');
             labelheader_cell( 'Grand Total Mark(1+6)','width=6%');
             labelheader_cell( 'GPA','width=5%');
             labelheader_cell( 'Comment','width=5%');
           
         end_row();

start_row();
             label_cell( '','width=5%');
             label_cell( '','width=6% height=16');
             label_cell( '','width=7%');
             label_cell( '','width=7%');
             label_cell( '','width=5%');
             label_cell( '','width=5%');
             label_cell( '','width=6%');
             label_cell( '','width=5%');
             label_cell( '','width=5%');
             label_cell( '','width=5%');
             label_cell( '','width=5%');
             
         end_row();
$sql = get_data_for_mobile_entry();
$result = db_query($sql, "could not get exam name");
 

//start_form();
//start_table(TABLESTYLE, "width=60%");
//$th = array(_('Serial No'), _('Roll Number'),_('Continuous Evaluation Mark(1)'),_('Multiple Choices Mark(2)'),
//        _('Creative Mark(3)'),_('Practical Mark(4)'),_('Total Mark(2+3+4)(5)'),_('80% of Total Mark(6)'),_('Grand Total Mark(1+6)'),_('GPA'),_(Comment));
//table_header($th);


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
