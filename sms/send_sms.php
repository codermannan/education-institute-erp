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
$page_security = 'SS_SMS_DAY_WISE_SRDNT_VW';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/sms_ui.inc");
include_once($path_to_root . "/sms/includes/db/sms_db.php");
//include_once($path_to_root . "/sms/includes/db/applicant_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Send SMS"), false, false, "", $js);
}

//-----------------------------------------------------------------------------------
if(list_updated('zone'))
$Ajax->activate('_page_body');

if(list_updated('area'))
$Ajax->activate('_page_body');

if(list_updated('district'))
$Ajax->activate('_page_body');

if(list_updated('thana'))
$Ajax->activate('_page_body');

if(isset($_POST['SearchOrders']))
$Ajax->activate('_page_body');

//-----------------------------------------------------------------------------------

if (isset($_POST['SearchOrders'])) 
{   
//	if (strlen($_POST['zone']) == '') 
//	{
//		$input_error = 1;
//		display_error( _('Zone must be selected.'));
//		set_focus('zone');
//                return false;
//	} 
//        
//        elseif (strlen($_POST['area']) == '')
//	{
//		$input_error = 1;
//		display_error( _('Area must be selected.'));
//		set_focus('area');
//                return false;
//	} 
//        
//        elseif (strlen($_POST['district']) == '') 
//	{
//		$input_error = 1;
//		display_error( _('District must be selected.'));
//		set_focus('section');
//                return false;
//	} 
//        
//         elseif (strlen($_POST['thana']) == '') 
//	{
//		$input_error = 1;
//		display_error( _('Thana must be selected.'));
//		set_focus('thana');
//                return false;
//	} 
}

////..........................................................
//-------------------------------------------------------------------------------------
if (isset($_POST['Process'])) 
{
        if (strlen($_POST['approveby']) == '') 
	{
		$input_error = 1;
		display_error( _('Approve by must be selected.'));
		set_focus('approveby');
                return false;
	} 
        
        elseif (strlen($_POST['sms_content']) == '') 
	{
		$input_error = 1;
		display_error( _('Sms content must be entered.'));
		set_focus('sms_content');
                return false;
	} 
        
        $sms_id = time();
        foreach($_POST['mobile_no'] as $key =>$val){
        $sql = "INSERT INTO " . TB_PREF . "sms_tbl_sms (sms_id,mobile_no,sms_content) VALUES (
            ". db_escape($sms_id) . ",". db_escape($val) . "," . db_escape($_POST['sms_content']) . ")" ;
        db_query($sql,'insert fail');
           // display_error($sql);
        }
        
        //if(count($_POST['mobile_no'])){
        $sql_app = "INSERT INTO " . TB_PREF . "sms_tbl_approval (sms_id,mobile_no) VALUES (
            ". db_escape($sms_id) . ",". db_escape($_POST['approvmobile']) . ")" ;
         // display_error($sql_app); 
        db_query($sql_app,'insert fail');
             display_notification(_('SMS has been sent'));
        //}
  $Ajax->activate('_page_body');
            $Mode = 'RESET';
            
            
}        
if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

start_row();

$query=array('id','zone_name','sms_zone_setup');
combo_list_cells(_("Zone :"), 'zone', $_POST['zone'], 'Select Zone', true, $query);

$query=array('id','area','sms_area');
combo_list_cells(_("Area :"), 'area', $_POST['area'], 'Select Area', true, $query);

$queryd=array('id','district_n','sms_district');
combo_list_cells(_("District :"), 'district', $_POST['district'], 'Select District', true, $queryd);

$queryt=array('id','thana_n','sms_thana_setup');
combo_list_cells(_("Thana :"), 'thana', $_POST['thana'], 'Select Thana', true, $queryt);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');


end_table();
br();
//------.......................
function apprbyid($apprby){
    $sql ="SELECT mobile FROM " . TB_PREF . "sms_approval WHERE id =" . db_escape($apprby);
    $ad = db_fetch(db_query($sql,'can not select'));
    return $ad['mobile']; 
} 
//.................................

start_form();

start_table(TABLESTYLE2,"width=50%");
//         start_row();
//             labelheader_cell( 'SL#','width=5%');
//             labelheader_cell( 'Name','width=10%');
//             labelheader_cell( 'Designation','width=10%');
//             labelheader_cell( 'Zone','width=10%');
//             labelheader_cell( 'Area','width=10%');
//             labelheader_cell( 'District','width=10%');
//             labelheader_cell( 'Thana','width=10%');
//             labelheader_cell( 'Mobile No','width=10%');
//             end_row();
    
if(isset($_POST['SearchOrders'])){    
    $zone = $_POST['zone'];
    $area = $_POST['area'];
    $district = $_POST['district']; 
    $thana =$_POST['thana'];

    $sl = 1; 
    
$sql = get_sql_for_mobile($zone,$area,$district,$thana);
$result = db_query($sql,"data could not be found");

//display_error($pr);
while($rp = db_fetch($result))
{ 
    start_row();
    //label_cell( $rp['mobile_no'],'width=10%');
    hidden('mobile_no['.$rp['id'].']', $rp['mobile_no']);
    end_row();
   $sl++; 
}

}

$query=array('id','name','sms_approval');
combo_list_cells(_("Approved By :"), 'approveby', $_POST['approveby'], 'Select Approved By', true, $query);

$apprbyid = apprbyid($_POST['approveby']);

hidden('approvmobile', $apprbyid);

textarea_row(_("SMS Content:"), 'sms_content', null, 50,5);
end_table();

br();

div_start('controls');
  submit_center('Process', _("Process Result"), true, '', 'default');
div_end();


br();

if (!@$_GET['popup']) {
    end_form();
    end_page();
}
