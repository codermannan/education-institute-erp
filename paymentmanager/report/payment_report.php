<?php

/* * ********************************************************************

 * ********************************************************************* */
$page_security = 'SS_SMS_DAY_WISE_PAYMNT_REP';
$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");


if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Students Payment Report"), false, false, "", $js);
}

//.....................................................
$Ajax;
//if($_POST['id'])
//    $selected_id=$_POST['id'];

if(list_updated('class'))
$Ajax->activate('_page_body');

//if(list_updated('FromDate'))
//$Ajax->activate('_page_body');

//..............................................
if (isset($_POST['Search'])) { 
    global $Ajax;
    
    $class = $_POST['class'];
    $from_date=$_POST['FromDate'];
    $to_date=$_POST['ToDate'];
    
    $Ajax->activate('_page_body');
    
   // display_error($class);
}
//..........................
if (!@$_GET['popup'])
    start_form ();

start_table(TABLESTYLE_NOBORDER);

start_row();
//$query=array('id','class_name','sms_create_stud_class');
//combo_list_cells(_("Class:"),'class', null, 'Select Class', false, $query);
date_cells(_("From:"), 'FromDate', '', null, 0, -1, 0);
date_cells(_("To:"), 'ToDate');
submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
br();

 
//start_row();
//        echo "<td>";
        start_table(TABLESTYLE2,'width=60%');
        start_row();
            labelheader_cell('Payment Date');
            labelheader_cell('Student ID');
            labelheader_cell('Payment Method');
            labelheader_cell('Cheque No');
            labelheader_cell('Cheque Date');
            labelheader_cell('Bank Name');
            labelheader_cell('Branch Name');
            labelheader_cell('Amount');
            labelheader_cell('Received By');
       end_row();

         
         $sql = get_sql_for_payment_report($class,$from_date,$to_date);
         $rep= db_query($sql);
         
         $amount=1;
         $total = 0;
         while($res=db_fetch($rep))
         {  
           $sql= "SELECT bank_name FROM " . TB_PREF . "bank_name WHERE id=" .db_escape($res['bank_name']);
           
           $bank = db_fetch(db_query($sql, 'Could not select bank name'));
           
           $total = ($total + $res['total_amount']);
           
            $len = count($cnt);
             start_row();
             label_cell($res['trans_date'],'align=center');
             label_cell($res['student_id']);
             
             if($res['payment_method'] == 1){
             label_cell('CASH','align=center');
             }
             elseif($res['payment_method'] == 2){
                 label_cell('CHEQUE','align=center');
             }
             elseif($res['payment_method'] == 3){
                 label_cell('DEBIT CARD','align=center');
             }
             elseif($res['payment_method'] == 4){
                 label_cell('CREDIT CARD','align=center');
             }
             label_cell($res['cheque_no']);
             label_cell($res['cheque_date']);         
             label_cell($bank['bank_name'],'align=center');
             label_cell($res['branch_name']);
             label_cell($res['total_amount'],'align=center');         
             label_cell($res['received_by'],'align=center');
           // amount_cell($amount);
             end_row();
         }
         
            start_row();
             label_cells(_("<b>Amount Total  </b>"), '<b>'.$total.'</b>', "colspan=7 align='center'");
            end_row();

end_table();  

end_form();
end_page();
?>
