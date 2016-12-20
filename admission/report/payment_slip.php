<?php
/**********************************************************************/
$page_security = 'SS_SMS_TST_RSLT_VW';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");
page(_($help_context = "Payment Slip"), true);

if (!isset($_GET['Applicantid'])) {
    die("<BR>" . _("This page must be called with a Performa Invoice to review."));
} else {
    $applicantid = $_GET['Applicantid'];
}

$sql_ex = get_sql_for_stform_view($applicantid);

    /////
    $stock_img_link = "";
	$check_remove_image = false;
	if (file_exists($sql_ex['photo_upload']))
            {
             // 31/08/08 - rand() call is necessary here to avoid caching problems. Thanks to Peter D.
                $stock_img_link .= "<img id='item_img' alt = '[".$sql_ex['photo_upload']."]' src='".$sql_ex['photo_upload'].
                    "?nocache=".rand()."'"." height='100' width='100' border='1'>";
                $check_remove_image = true;
            } 
	else 
            {
                $stock_img_link .= _("No image");
            }
// outer table
 /*-----------------main table start----------------------*/  
br();
start_table(TABLESTYLE1);
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:18px"'); 
    end_row();
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['postal_address'],'colspan=3 align=center style="font-size:15px"');
    end_row();
    start_row();  
        labelheader_cell('Payment Slip','width=95%','colspan=4 style="font-size:18px"');
    end_row();    
end_table();
  br();
  start_table(TABLESTYLE_NOBORDER,'width=80%');
//   start_row('background-color:none');
//            labelheader_cell( 'Applicant Personal Info','colspan=8 style="text-align:left;"');
//         end_row();
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
   start_row();
             label_cell( 'Applicant Type');
             label_cell( ':','width=10%');
             label_cell($sql_ex['cat_name']);
   end_row();
  end_table();
   
  br();
  $dpar = date_parse(Today());
//  $dt =  $dpar['year'].'-01-31';
  $dt =  '2015-01-31';
 
  $sql="SELECT phm.head_name,phm.discount_status, phs.amount,ssc.ratio FROM " . TB_PREF . "sms_tbl_receivable str
        LEFT JOIN " . TB_PREF . "sms_payment_head_setting phs ON str.head_id = phs.id
        LEFT JOIN " . TB_PREF . "sms_payment_head phm ON phs.head_name = phm.id
        LEFT JOIN " . TB_PREF . "sms_student_scholarship ss ON str.applicant_id = ss.applicant_id AND ss.is_approved_cat = 1 
        LEFT JOIN " . TB_PREF . "sms_student_category ssc ON ss.student_cat = ssc.id
        WHERE str.applicant_id=".  db_escape($applicantid). " AND str.due_date = '".$dt."'";
  
  $sl = 1; 
  $result = db_query($sql,"data could not be found");
 
  start_table(TABLESTYLE2,'width=80%');
   start_row('background-color:none');
            labelheader_cell( 'SL#','align=center');
            labelheader_cell( 'Payment Head','align=center');
            if($sql_ex['student_cat']!='1'){
                labelheader_cell( 'Discount Amount','align=center');
            }
            labelheader_cell( 'Amount','align=center');
    end_row();
         
  while ($rep = db_fetch($result)){
      $ratio = $rep['ratio'];
      start_row();
                 label_cell( $sl,'align=center');
                 label_cell( $rep['head_name'],'align=center');
                 if($sql_ex['student_cat']!='1'){
                 if($rep['discount_status']=='1'){
                 
                 $dishw = ($rep['amount'] * $ratio)/100;
                 $discount_total += $dishw; 
                 label_cell(price_format($dishw),'align=center');
                 }else{
                 label_cell(price_format(0),'align=center');    
                 }
                 }
                 label_cell(price_format($rep['amount']),'align=center');
      end_row();
       $sl++;
       
       $display_total += $rep['amount'];
       
    }
   
   $netamnt = price_format(($display_total - $discount_total));
   
   start_row();
   if($sql_ex['student_cat']!='1'){
            label_cells(_("<center><b>Total Amount</b></center>"), '<center><b>'.price_format($display_total).'</b></center>', "colspan=3"); 
   }else{
            label_cells(_("<center><b>Total Amount</b></center>"), '<center><b>'.price_format($display_total).'</b></center>', "colspan=2");  
   }
   end_row();
   if($sql_ex['student_cat']!='1'){
   start_row();
             label_cells(_("<center><b>Discount (".$ratio."%)</b></center>"), '<center><b>'.price_format($discount_total).'</b></center>', "colspan=3");
   end_row();
   
   start_row();
             label_cells(_("<center><b>Net Amount</b></center>"), '<center><b>'.$netamnt.'</b></center>', "colspan=3");
   end_row();
   }
  end_table();
  br();

  br(1);
  br(1);
  br(1);
   
end_page(true, false, false, ST_BOM, $style_no);