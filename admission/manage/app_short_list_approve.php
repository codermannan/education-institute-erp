<?php

/* * ********************************************************************* */
$page_security = 'SS_SMS_APLCNT_SHRT_LST_APV';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Applicant short list approve"), false, false, "", $js);
}
$user = $_SESSION['wa_current_user']->username;
$timekey = date(Hi);
//-----------------------------------------------------------------------------------

//----------------------------------
if (isset($_POST['approve'])) 
{

        foreach($_POST['selectr'] as $key =>$sel){
         echo   $chk = $sel;
         br();
        } 
            if($chk == 1)
            {
              
               foreach($_POST['appid'] as $key =>$app){
                   approve_short_list($app,$user,$timekey);
			display_notification(_('Short list has been approved'));
                }
           } 
            else
            {
                foreach($_POST['appid'] as $key =>$app){
   
                    approve_short_list($app,$user,$timekey);
            
			display_notification(_('Short list has been approved'));
                }
                
           }
        
    
}

if(get_post('print'))
{
      $Ajax->activate('det');
}


//---------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
    start_form();



//---------------------------------------------------------------------------------------------
function edit_link($row) {

    return "<center>" . pager_link(_("Edit"),
    			"/admission/manage/app_short_list_edit.php?app_id=" . $row["applicant_id"], ICON_EDIT);
    "</center>";
}

function delete_link($row) {
    submit_js_confirm("Delete" . $row['applicant_id'], sprintf(_("Are you sure you want to delete ?")));
 

    return "<center>" . button("Delete" . $row["applicant_id"], _("Delete"), _("Delete"), ICON_DELETE) .
            "</center>";
}
function applicantId($row) {
    hidden('appid['.$row['id'].']', $row['applicant_id']);
    return $row['applicant_id'] ;
}


//---------------------------------------------------------------------------------------------



start_table(TABLESTYLE2,"width=60%");
         start_row();
             labelheader_cell( 'Select','width=5%');
             labelheader_cell( 'Applicant ID','width=10%');
             labelheader_cell( 'Applicant Name','width=15% ');
             labelheader_cell( 'Mark','width=15% ');
             labelheader_cell( 'Status','width=15% ');
             end_row();

$sql = get_sql_for_test_result_edit();

$result = db_query($sql,"data could not be found");
 if(mysql_num_rows($result)>0){
  while ($rep = db_fetch($result))
    {
      //display_error('');
      
        // if($r['applicant_id'] != $rep['applicant_id']){
         start_row();
             check_cells(null, 'selectr['.$rep['id'].']', '',false,  _('set DECLARATION'));
             label_cell( $rep['applicant_id'],'align=center');
             label_cell($rep['name'],'align=center');
             label_cell($rep['obtain_marks'],'align=center');
             hidden('appid['.$rep['id'].']', $rep['applicant_id']);
             
             
            if($rep['result']== 1)
                 label_cell ('P');
             else
                 
                label_cell('WP');
             
          
         end_row();
            }
   // } 
 }
 else {
     display_notification(_('There are no students for approved'));
 
} 
end_table();

//---------------------------------------------------------------------------------------------------
br();

div_start('controls');

        submit_center('approve', _("Approve"), true, '', 'default');
        br();
  
    div_end();
    
     div_start('det');
     display_heading2(viewer_link(_("&View Approved List"), "admission/report/approved_list_view.php?time=$timekey&user=$user"));
div_end();

if (!@$_GET['popup']) {
    end_form();
    end_page();
}