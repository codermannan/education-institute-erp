<?php
/**********************************************************************/
$page_security = 'SS_SMS_APLCNT_PAYMNT_RCV';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");

page(_($help_context = "Payment Slip"), true);

if (!isset($_GET['Applicantid'])) {
    
    die("<BR>" . _("This page must be called with a Performa Invoice to review."));
} else {
    $applicantid = $_GET['Applicantid'];
}



$sql_ex = get_sql_for_payment_view($applicantid);
$rep = db_fetch(db_query($sql_ex,'can not get data')) ;
    /////
    $stock_img_link = "";
	$check_remove_image = false;
	if (file_exists($sql_ex['photo_upload']))
	{
		$stock_img_link .= "<img id='item_img' alt = '[".$rep['photo_upload']."]' src='".$rep['photo_upload'].
			"?nocache=".rand()."'"." height='100' width='100' border='1'>";
		$check_remove_image = true;
	} 
	else 
	{
		$stock_img_link .= _("No image");
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
   
        labelheader_cell('Student Payment Slip','width=95%','colspan=4 style="font-size:18px"');
        end_row();
end_table();
 
  br();
  start_table(TABLESTYLE_NOBORDER,'width=80%');
  start_row();
             label_cell( 'Student ID','width=20%');
             label_cell( ':','width=10%');
             label_cell($_GET['Applicantid']);
  end_row();
   start_row();
   
             label_cell('Student Name');
             label_cell( ':','width=10%');
             label_cell($rep['name']);
             
  end_row();
  start_row();
   
             label_cell('Father Name');
             label_cell( ':','width=10%');
             label_cell($rep['father_name']);
  end_row();
  start_row();
   
             label_cell( 'Mother Name');
             label_cell( ':','width=10%');
             label_cell($rep['mother_name']);
  end_row();
  start_row();
             label_cell( 'Student Class');
             label_cell( ':','width=10%');
             label_cell($rep['class_name']);
             
  end_row();
    start_row();
             label_cell( 'Student Section');
             label_cell( ':','width=10%');
             label_cell($rep['st_section']);
  end_row();
  end_table();
   
  br();

  $sql="SELECT phs.head_name, phs.amount FROM " . TB_PREF . "sms_tbl_receivable str
              LEFT JOIN " . TB_PREF . "sms_payment_head_setting phs ON str.head_id = phs.id
              WHERE str.student_id=".  db_escape($applicantid). " AND str.due_date = '2015-01-31' And str.realize = 0";
 
  $sl = 1; 
  $result = db_query($sql,"data could not be found");
 
  start_table(TABLESTYLE2,'width=80%');
   start_row('background-color:none');
            labelheader_cell( 'SL#','align=center');
            labelheader_cell( 'Payment Head','align=center');
            labelheader_cell( 'Amount','align=center');
         end_row();
         
  while ($rep = db_fetch($result))
    {
  start_row();
             label_cell( $sl,'align=center');
             label_cell( $rep['head_name'],'align=center');
             label_cell( $rep['amount'],'align=center');
  end_row();
   $sl++;
   $display_total += $rep['amount'];
    }
   start_row();
             label_cells(_("<b>Total Amount</b>"), $display_total, "colspan=2 align='center'");
   end_row();
  end_table();
  br();
  
  
  br(1);
  br(1);
  br(1);
   
end_page(true, false, false, ST_BOM, $style_no);