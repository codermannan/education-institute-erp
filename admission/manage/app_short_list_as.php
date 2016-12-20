<?php

/* * ********************************************************************
  Copyright (C) FrontAccounting, LLC.
  Released under the terms of the GNU General Public License, GPL,
  as published by the Free Software Foundation, either version 3
  of the License, or (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 * ********************************************************************* */
$page_security = 'SS_SMS_APLCNT_SHRT_LST_AS';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/applicant_info_ui_lists.inc");
include_once($path_to_root . "/sms/includes/db/applicant_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Applicant short List"), false, false, "", $js);
}

//----------------------------------
if (isset($_POST['SearchOrders'])) 
{   
	if (strlen($_POST['app_class']) == '') 
	{
		$input_error = 1;
		display_error( _('Applicant class must be selected.'));
		set_focus('app_class');
                return false;
	} 
  
       
}

//-------------------------------------------------------------------------------------
if (isset($_POST['Process'])) 
{
        foreach($_POST['appid'] as $key => $apl){

            $pass = $_POST['p'][$key];
        
           // $wpass = $_POST['wp'][$key];

            process_short_list($apl, $pass);
 
        }
        
        meta_forward($path_to_root.'/sms/manage/app_short_list_approve.php');
	
}
//---------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();

get_student_clas(_("Select Class:"), 'app_class', $_POST['app_class'], 'Select Class', true);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
end_form();
//---------------------------------------------------------------------------------------------
start_form();

start_table(TABLESTYLE2,"width=80%");
         start_row();
             labelheader_cell( 'Applicant ID','width=5%');
             labelheader_cell( 'Applicant Name','width=5%');
             labelheader_cell( 'Mark','width=6% ');
             labelheader_cell( 'Status','width=7%'); 
         end_row();
         
           
         
if( list_updated('app_class') ){   
    
    $class = $_POST['app_class'];



$sql = get_sql_short_list_data_st($class);



if($sql == 1){
    display_notification(_('Please setup required seat'));
}

//elseif($num == 0){
// display_notification(_('Students have not found to make short list'));
//}
else{
$result = db_query($sql,"data could not be found");
$num = mysql_num_rows($result);

  while ($rep = db_fetch($result))
    {
         start_row();
             label_cell( $rep['applicant_id'] ,'width=5%');
             hidden('appid['.$rep['id'].']', $rep['applicant_id']);
             label_cell( $rep['first_name'],'width=10%');
             label_cell( $rep['obtain_marks'],'width=15% height=16');
             label_cell( 'P','width=7%'); 
             hidden('p['.$rep['id'].']', 1);
         end_row();
     
    }
  }
}
end_table();

br();

div_start('controls');
  submit_center('Process', _("Process Short List"), true, '', 'default');
div_end();
//---------------------------------------------------------------------------------------------------

br();

if (!@$_GET['popup']) {
    end_form();
    end_page();
}