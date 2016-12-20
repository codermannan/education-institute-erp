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


page(_($help_context = "Approved List View"), true);


//if (!isset($_GET['Applicantid'])) {
//    die("<BR>" . _("This page must be called with a Performa Invoice to review."));
//} else {
//    $applicantid = $_GET['Applicantid'];
//}
//--------------------------------------------------
start_table(TABLESTYLE1);
start_row();
      label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
        end_row();
        start_row();
            label_cell('<b>Approve Applicant List</b>','align=center colspan3=10');
        end_row();
end_table();

br();
start_form ();

 start_table(TABLESTYLE2,'width=80%');
 
  start_row();
   
             labelheader_cell( 'Applicant ID');
             labelheader_cell( 'Applicant Name');
             labelheader_cell( 'Result');

  end_row();
  
 $sql = "SELECT tr.applicant_id,CONCAT(sd.first_name,' ' ,sd.middle_name,' ',sd.last_name) as name, tr.result,tr.status FROM "
           . TB_PREF . "sms_test_result tr LEFT JOIN "
           . TB_PREF . "sms_students_details sd ON tr.applicant_id = sd.applicant_id
            WHERE tr.status =3 AND user='" .$_GET['user']."' AND time= " .$_GET['time']  ;
 
$pr= db_query($sql);

while($result = db_fetch($pr))
{
    //
  start_row();
             
             label_cell($result['applicant_id']);
             
             label_cell($result['name']);
             if($result['result']== 1)
                 label_cell ('P');
             else
                 
             label_cell('WP');
             
  end_row();
}
  
  end_table();
  br();

    end_form();
  
  
  br(1);
  br(1);
  br(1);
   
end_page(true, false, false);