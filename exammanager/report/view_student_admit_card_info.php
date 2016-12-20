<?php

$page_security = 'SS_SMS_STDNT_ADMT_CRD';
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/exammanager/includes/db/exam_db.inc");

$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(900, 500);
page(_($help_context = "Printing Daily Production "), true, false, "", $js);

  start_table(TABLESTYLE_NOBORDER,'width=50%');
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'colspan=3 align=center style="font-size:20px; font-width:bold"');
    end_row();
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['postal_address'],'colspan=3 align=center style="font-size:15px"');
    end_row();
   end_table();
  br();

if (!isset($_GET['c']))
{
	die ("<BR>" . _("This page must be called with a Spinning  Order Requisition to review."));
}
else
{
    $c = $_GET['c'];
}

function exam_name($row) {
    
    $condition = array('class_name'=>$_GET['c'],'parent'=>0,'status'=>1);
    $field = array('exam_name');
    $classval = db_fetch(data_retrieve_condition("sms_exam_name", $field, $condition));
    return $classval['exam_name'];
}

function admit_card($row) {
        
        $condition = array('class_name'=>$_GET['c'],'parent'=>0,'status'=>1);
        $field = array('exam_name');
        $classval = db_fetch(data_retrieve_condition("sms_exam_name", $field, $condition));
        
        return viewer_link(null, "/sms/view/student_admit_card_view.php?Studentid=" . $row['student_id'] . " &examname=".$classval['exam_name']."&class=" .$_GET['c'], null, null, ICON_VIEW);
        return true;
}

br();

start_table(TABLESTYLE, "colspan=9 width=90%");

$th = array(_("SL No"),_("Student ID"),_("Student Name"),_("Class"),_("Section"),_("Exam Name"),_("Admit Card"));

table_header($th);

$sql = get_sql_for_admit_card($c);
$result = db_query($sql);
$k = 0;  //row colour counter
$i = 1;
while ($myrow = db_fetch($result)) {

    alt_table_row_color($k);

    label_cell($i++, "align = 'center'");
    label_cell($myrow['student_id'], "align = 'center'");
    label_cell($myrow['name'], "align = 'center'");
    label_cell($myrow['class_name'], "align = 'center'");
    label_cell($myrow['session_name'], "align = 'center'");
    label_cell(exam_name($myrow), "align = 'center'");
    label_cell(admit_card($myrow), "align = 'center'");

    end_row();
}
//start_row();
//label_cell(null,"colspan=4 ");
//label_cell(' Total : '," class='tableheader2'");
//number_cell($total_pics);
//number_cell($total_pro);
//number_cell($total_app);
//number_cell($total_spt);
//number_cell($total_rej);
//end_row();

end_table(1);
?>
<html>
    <style>

        .line{
            margin-left: 10%;
            width: 20%;
            display: block;
            float: left;
            text-align: center;
        }
        .words{
            margin-left: 20%;

        }

    </style>
    <div class="footer-table" style="position: relative; width: 90%; margin: 0 auto; height: 60px; margin-top: 32px;">
        <div style="">
            <span class="line">__________________</br>Prepared By</span>
            <span class="line"> &nbsp; </span>
            <span class="line">__________________</br> Approved By</span>

        </div>
    </div>

</html>
<?
end_page(true, false, false);
?>
