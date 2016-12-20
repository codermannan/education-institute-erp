<?php

$page_security = 'SS_SMS_DAY_WISE_PAYMNT_REP';
$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Application Form Payment Report "), false, false, "", $js);
}
//$user = $_SESSION['wa_current_user']->username;
$syear = get_current_schoolyear();
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

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class:"),'class', null, 'Select Class', false, $query);


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
           labelheader_cell('SL No');
           
            labelheader_cell('Applicant Id');
            labelheader_cell('Form Price');
            labelheader_cell('Discount');
            labelheader_cell('Rest Amount');
            labelheader_cell('Sale Date');
            labelheader_cell('Received By');
            //labelheader_cell('amount');
       end_row();

//         $sql1= "SELECT * FROM ".TB_PREF."sms_app_payment";
//         $rep1= db_query($sql1);
        
    
    $sr=get_sql_application_payment_report($syear,$class,$from_date,$to_date);
    
      $rep1= db_query($sr);
         
        // $amount=1;
         $total = 0;
         $sl=1;
         while($res1=db_fetch($rep1))
         {  
           $total = ($total + $res1['rest_amnt']);

             start_row();
            label_cell($sl,'align=center');
             label_cell($res1['applicant_id']);
             
             label_cell($res1['form_price'],'align=center');
             label_cell($res1['discount'],'align=center');         
             label_cell($res1['rest_amnt'],'align=center');
             label_cell(sql2date($res1['sale_date']),'align=center');
             label_cell($res1['receive_by'],'align=center');
               $sl++;
             end_row();
         }
         
            start_row();
             label_cells(_("<b>Amount Total  </b>"), '<b>'.$total.'</b>', "colspan=4 align='center'");
            end_row();

end_table();  

end_form();
end_page();

?>
