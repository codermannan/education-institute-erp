<?php
/**********************************************************************/
$page_security = 'SS_SMS_APLCNT_LST_VW';
$path_to_root = "../..";

//include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");


page(_($help_context = "Application Form View"), true);


if (!isset($_GET['Applicantid'])) {
    die("<BR>" . _("This page must be called with a Performa Invoice to review."));
} else {
    $applicantid = $_GET['Applicantid'];
}
//--------------------------------------------------


$sql_ex = get_sql_for_stform_view($applicantid);

    /////
    $stock_img_link = "";
	$check_remove_image = false;
	if (file_exists($sql_ex['photo_upload']))
	{
	 // 31/08/08 - rand() call is necessary here to avoid caching problems. Thanks to Peter D.
		$stock_img_link .= "<img style='border:1px solid #000000;' id='item_img' alt = '[".$sql_ex['photo_upload']."]' src='".$sql_ex['photo_upload'].
			"?nocache=".rand()."'"." height='100' width='100' border='1'>";
		$check_remove_image = true;
	} 
	else 
	{
		$stock_img_link .= '<img  src='.company_path().'/images/sabuj.jpg height="100" width="100"  border="1"  />';
	}
      
//

// outer table
 /*-----------------main table start----------------------*/  
br();


 start_table(TABLESTYLE1);
    start_row();
      label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
        end_row();
        start_row();
   
        labelheader_cell('Admit Card For Admission Test','width=95%','colspan=4 style="font-size:18px"');
        end_row();
end_table();
br();
  start_table(TABLESTYLE_NOBORDER,'width=70%');
   label_cell($stock_img_link ,'align=right ' );
  end_table();
  
br();
  start_table(TABLESTYLE_NOBORDER,'width=80%');

  start_row();
             label_cell( 'Applicant ID','width=20%');
             label_cell( ':','width=10%');
             label_cell( $_GET['Applicantid']);
  end_row();
   start_row();
   
             label_cell('Applicant Name');
             label_cell( ':','width=10%');
             label_cell($sql_ex['name']);
  end_row();
  start_row();
   
             label_cell('Father Name');
             label_cell( ':','width=10%');
             label_cell($sql_ex['father_name']);
  end_row();
  start_row();
   
             label_cell( 'Mother Name');
             label_cell( ':','width=10%');
             label_cell($sql_ex['mother_name']);
  end_row();
  start_row();
             label_cell( 'Applicant Class');
             label_cell( ':','width=10%');
             label_cell($sql_ex['class_name']);
             
  end_row();
    start_row();
             label_cell( 'Applicant Group');
             label_cell( ':','width=10%');
             label_cell($sql_ex['group_name']);
  end_row();
  end_table();
   
  br();
  
  $sch = get_sql_for_exam_schedule();
  
  start_table(TABLESTYLE2,'width=80%');
   start_row('background-color:none');
            labelheader_cell( 'Exam Date','align=center');
            labelheader_cell( 'Exam Time','align=center');
            labelheader_cell( 'Vanue','align=center');
         end_row();
  start_row();
             label_cell( $sch['exam_date'],'align=center');
             label_cell( $sch['exam_time'],'align=center');
             label_cell( $sch['exam_venue'],'align=center');
  end_row();
  end_table();
  br();
  
  
  br(1);
  br(1);
  br(1);
   
end_page(true, false, false, ST_BOM, $style_no);