<?php
/**********************************************************************
   developed by Mannan
***********************************************************************/
$page_security = 'SS_SMS_APLCNT_LST_VW';
$path_to_root = "../..";
include_once($path_to_root . "/paymentmanager/includes/ui/sch_payment_cart.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/payment_entry_ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 600);

if ($_GET['transno'] == ST_SALESQUOTE){

	display_heading(sprintf(_("Payment Transaction No #%d"),$_GET['transno']));
}else{
	page(_($help_context = "Payment Invoice"), true, false, "", $js);
        
        start_table(TABLESTYLE_NOBORDER,'width=50%');
        start_row();
        label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'colspan=3 align=center style="font-size:20px; font-width:bold"');
        end_row();
        start_row();
        label_cell($_SESSION['SysPrefs']->prefs['postal_address'],'colspan=3 align=center style="font-size:15px"');
        end_row();
        end_table();
  br();

	display_heading(sprintf(_("Payment Invoice #%d"),$_GET['transno']));
}

br(1);
start_table(TABLESTYLE2, "width=95%", 5);

if ($_GET['transno'])
{    
	echo "<tr valign=top><td>";
	display_heading2(_("Student Information"));
	
	echo "</td></tr>";
}	

echo "<tr valign=top><td>";

$sinfo = get_sql_for_result_view($_GET['stid']);

start_table(TABLESTYLE, "width=95%");
//table_section(1,'60%');
label_row(_("Student ID "), $_GET['stid'], "class='tableheader2'");
start_row();
label_row(_("Name Of Student"), $sinfo['name'], "class='tableheader2'");
end_row();
start_row();
label_row(_("Class"), $sinfo['class_name'], "class='tableheader2'");
end_row();
start_row();
label_row(_("Section"), $sinfo['session_name'], "class='tableheader2'");
end_row();
start_row();
label_row(_("Roll No."), $sinfo['roll_number'], "class='tableheader2'");
end_row();
start_row();
label_row(_("Group"), $sinfo['group_name'], "class='tableheader2'");
end_row();
start_row();
label_row(_("Student Category"), $sinfo['cat_name'], "class='tableheader2'");
end_row();

end_table();

echo "<center>";

$getsql = "SELECT rec.*,phm.head_name FROM " . TB_PREF . "sms_tbl_receivable rec 
           INNER JOIN  " . TB_PREF . "sms_transaction tr ON rec.id = tr.head_id
           INNER JOIN " . TB_PREF . "sms_payment_head_setting phs ON rec.head_id = phs.id
           INNER JOIN " . TB_PREF . "sms_payment_head phm ON phs.head_name = phm.id
           WHERE tr.trand_id = ".db_escape($_GET['transno']);

$qry = db_query($getsql);
br();
display_heading2(_("Payment Details"));
br();
start_table(TABLESTYLE, "colspan=9 width=95%");
$th = array(_("Sl#"), _("Payment Head"), _("Due Date"), _("Discount"), _("Amount"));
table_header($th);

$sl = 1;
while($cdata = db_fetch($qry)){
    //display_error($cdata['head_id']);
    start_row();
    label_cell($sl,'align=center');
    label_cell($cdata['head_name']);
    label_cell(sql2date($cdata['due_date']),'align=center');
    label_cell($cdata['discount'],'align=center');
    label_cell($cdata['realize'],'align=center');
    end_row();
    $sl++;
    $discounttotal += $cdata['discount'];
    $total += $cdata['realize'];
    $display_sub_total = price_format($total);
}

start_row();
label_cell(null,"align=right colspan=3");
label_cell('<b>'.$discounttotal.'</b>',"align=center");
label_cell('<b>'.$total.'</b>',"align=center");
end_row();
start_row();
$spdis = data_retrieve('sms_transaction_details', 'id', $_GET['transno']);

label_cell('<b>'._("Special Discount  ").'</b>',"align=right colspan=4"); 
label_cell('<b>'.$spdis['sp_discount'].'</b>',"align=center");
end_row();
start_row();
label_cell('<b>'._("Net Amount  ").'</b>',"align=right colspan=4"); 
label_cell('<b>'.($total-$spdis['sp_discount']).'</b>',"align=center");
end_row();

end_table(2);

end_page(true, false, false, '', '');

?>
