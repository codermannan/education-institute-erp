<?php

$page_security = 'SS_SMS_STDNT_XM_SCHDL';
$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/exammanager/includes/ui/exam_ui_lists.inc");
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

if (!isset($_GET['sc']))
{
	die ("<BR>" . _("This page must be called with a Spinning  Order Requisition to review."));
}
else
{
    $sc = $_GET['sc'];
}

function get_schedule_info() {
    $sql = "SELECT scs.class_name,
          sen.exam_name,
          ss.subject_name,
          sse.date,
          rm.room_no,
          es.shift,
          sse.status,
          sse.exam_id
          FROM "      . TB_PREF . "sms_stud_exam sse
          LEFT JOIN " . TB_PREF . "sms_exam_name sen ON sse.exam_name = sen.id
          LEFT JOIN " . TB_PREF . "sms_create_stud_class scs ON sse.class_name = scs.id
          LEFT JOIN " . TB_PREF . "sms_subject ss ON sse.subject_name = ss.id
          LEFT JOIN " . TB_PREF . "sms_room_setup rm ON sse.room = rm.id
          LEFT JOIN " . TB_PREF . "sms_shift es ON es.id = sse.shift";
   
    if ($_GET['sc'] != '') {
        $sql .= " WHERE sse.class_name=" . db_escape($_GET['sc']);
    }
    //
    return db_query($sql);
}

function status($row) 
{
         if($row['status']==1)
      return 'Open'; 
         else
      return 'Close';
    
}

br();

start_table(TABLESTYLE, "colspan=9 width=90%");

$th = array(_("SL No"),_("Class"),_("Exam Name"),_("Subject Name"),_("Date"),_("Allowcated Room"),_("Shift"),_("Status"));

table_header($th);

$result = get_schedule_info();
$k = 0;  //row colour counter
$i = 1;
while ($myrow = db_fetch($result)) {
$sql = "SELECT scs.class_name,
          sen.exam_name,
          ss.subject_name,
          sse.date,
          rm.room_no,
          es.shift,
          sse.status,
          sse.exam_id";
    alt_table_row_color($k);

    label_cell($i++, "align = 'center'");
    label_cell($myrow['class_name'], "align = 'center'");
    label_cell($myrow['exam_name'], "align = 'center'");
    label_cell($myrow['subject_name'], "align = 'center'");
    label_cell(sql2date($myrow['date']), "align = 'center'");
    label_cell($myrow['room_no'], "align = 'center'");
    label_cell($myrow['shift'], "align = 'center'");
    label_cell(status($myrow), "align = 'center'");

    end_row();
}


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
