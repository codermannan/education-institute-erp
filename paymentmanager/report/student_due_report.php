<?php
if(@$_GET['Student']=='Yes'){
   $page_security = 'SS_SMS_STUD_DBOARD'; 
}else{
   $page_security = 'SS_SMS_DAY_WISE_PAYMNT_REP'; 
}
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");


page(_($help_context = "Student Payment Report"), true);
$syear = get_current_schoolyear();

if (!@$_GET['popup'])
    
start_form ();
 
start_table(TABLESTYLE1);
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
    end_row();
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['postal_address'],'colspan=3 align=center style="font-size:15px"');
    end_row();
    start_row();
        label_cell('<b>Student Payment Report</b>','align=center colspan3=10');
    end_row();
end_table();
//---------------------------------------------------
function findratio($stid){
    
    $sql="SELECT ssc.ratio
          FROM " . TB_PREF . "sms_student_category ssc
          LEFT JOIN " . TB_PREF . "sms_student_scholarship ss ON ssc.id = ss.student_cat 
          WHERE ss.student_id='" . $stid ."' AND ss.is_approved_cat = 1 ";
      
    $rep= db_fetch(db_query($sql));
    
    return $rep['ratio'];
}
function totalSpecialdiscount($stid,$syear){
    $condition = array('student_id'=>$stid,'school_year'=>$syear);
    $field = array('sp_discount');
    $spd = data_retrieve_condition('sms_transaction_details', $field, $condition);
    
    while($data=  db_fetch($spd)){
        $dtotal += $data['sp_discount'];
    }

    return $dtotal;
} 
   br();
      
   $sql_ex =sql_view($_GET['id']);
 
   start_table(TABLESTYLE_NOBORDER,'width=80%');
   start_row();
             label_cell( 'Student ID','width=10%');
             label_cell( ':');
             label_cell($sql_ex['student_id']);
  end_row();
  start_row();
             label_cell( 'Name Of Student','width=10%');
             label_cell( ':');
             label_cell( $sql_ex['name'],'width=30%');
  end_row();
    start_row();
             label_cell( 'Class');
             label_cell( ':','width=10%');
             label_cell($sql_ex['class_name']);
          ;
             
   end_row();
   start_row();
             label_cell( 'Roll No.');
             label_cell( ':','width=10%');
             label_cell($sql_ex['roll_number']);
               end_row();
    start_row();           
             label_cell( 'Section Name','width=20%'); 
             label_cell( ':','width=10%');
             label_cell( $sql_ex['session_name'],'width=20%');
    end_row();
             
 
end_table();
br();
//..........................
if($_GET['id']){
    $condition = array('student_id'=>$_GET['id']);
    $field = array('applicant_id');
    $appid = db_fetch(data_retrieve_condition("sms_students_details", $field, $condition));
    
}
//start_row();
//        echo "<td>";
        start_table(TABLESTYLE2,'width=80%');
        start_row();
            labelheader_cell('SL No');
            labelheader_cell('Head Name');
            labelheader_cell('Month');
            labelheader_cell('Payment Date');
            labelheader_cell('Due Date');
            labelheader_cell('Payable Amnt Before Discount');
            labelheader_cell('Discount Amnt');
            labelheader_cell('Payable Amnt After Discount');
            labelheader_cell('Paid Amnt');
            labelheader_cell('Due Amnt');
       end_row();

      $sql="SELECT mon.full_name,str.payment_date,str.due_date,str.realize,str.id, str.head_id , phm.head_name, phm.discount_status, phs.amount
              FROM " . TB_PREF . "sms_tbl_receivable str
              LEFT JOIN " . TB_PREF . "sms_payment_head_setting phs ON str.head_id = phs.id
              LEFT JOIN " . TB_PREF . "sms_payment_head phm ON phs.head_name = phm.id
              LEFT JOIN " .TB_PREF . "hcm_salary_month mon ON str.month = mon.id
              LEFT JOIN " . TB_PREF . "sms_student_scholarship ss ON str.student_id = ss.student_id AND ss.is_approved_cat = 1 
              LEFT JOIN " . TB_PREF . "sms_student_category ssc ON ss.student_cat = ssc.id
              WHERE (str.student_id='" . $_GET['id']."' OR str.applicant_id='".$appid['applicant_id']."') AND str.school_year =".$syear;
      
         $rep= db_query($sql);
      
         $sl=1;
         while($res=db_fetch($rep)){  
             start_row();
             label_cell($sl,'align=center');
             label_cell($res['head_name']);
             label_cell($res['full_name'],'align=center');

             if($res['payment_date']>$res['due_date']){
                 label_cell(sql2date($res['payment_date']),'align=center style="color:#FF0000;"');    
             }else{
                 label_cell(sql2date($res['payment_date']),'align=center');
             }
             label_cell(sql2date($res['due_date']),'align=center');
             label_cell($res['amount'],'align=center');
             
           if($res['discount_status'] == '1'){
             $scratio = findratio($_GET['id']); 
             
             $dishw = ($res['amount'] * $scratio)/100;
             $discount_total += $dishw; 
             
             $invtotal = ($res['amount'] - $dishw );
             $payableamnt_total += $invtotal;
             
             label_cell($dishw,'align=center');
             label_cell($invtotal,'align=center'); 
             
             $dueamnt = ($invtotal - $res['realize']);
             $totaldue += $dueamnt;
             
             label_cell($res['realize'],'align=center');
             
             label_cell($dueamnt,'align=center');
             
            }else{
             $payableamnt_total += $res['amount'];  
             label_cell(0,'align=center');
             label_cell($res['amount'],'align=center');

             $dueamnt = ($res['amount'] - $res['realize']);
             $totaldue += $dueamnt;
             
             label_cell($res['realize'],'align=center');
             label_cell($dueamnt,'align=center');
           }
             
             $sl++;
             end_row();
             $amount += $res['amount'];
             $realize += $res['realize'];
             
         }
            start_row();
             label_cell("<b>Amount Total  </b>",'align=center colspan=5');
             label_cell('<b>'.$amount.'</b>','align=center');
             label_cell('<b>'.$discount_total.'</b>','align=center');
             label_cell('<b>'.$payableamnt_total.'</b>','align=center');
             label_cell('<b>'.$realize.'</b>','align=center');
             label_cell('<b>'.price_format($totaldue).'</b>','align=center');
            end_row();
            start_row();
             $spdis = totalSpecialdiscount($_GET['id'],$syear);
             $gtotaldue = ($totaldue - $spdis);
             
             label_cell("<b>Special Discount</b>",'align=right colspan=9');
             label_cell('<b>'.price_format($spdis).'</b>','align=center');
            end_row();
            start_row();
             label_cell("<b>Total Due</b>",'align=right colspan=9');
             label_cell('<b>'.price_format($gtotaldue).'</b>','align=center');
            end_row();

end_table(); 

br();
//
// div_start('det');
//     display_heading2(viewer_link(_("&Report View"), "sms/view/view_payment_report.php?class=$class"));
//div_end();

end_form();
end_page(true);

?>

