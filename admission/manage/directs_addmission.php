<?php

/* * ********************************************************************
  developed by Mannan
 * ********************************************************************* */
$page_security = 'SS_SMS_ADMSN_MRK_ENTRY';
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
    page(_($help_context = "Sport Addmission"), false, false, "", $js);
}
$syear = get_current_schoolyear();
//-----------------------------------------------------------------------------------

if(list_updated('class'))
$Ajax->activate('_page_body');

//-----------------------------------------------------------------------------------

if (isset($_POST['SearchOrders'])) 
{   
	
        if (strlen($_POST['class']) == '') 
	{
		$input_error = 1;
		display_error( _('Student class must be selected.'));
		set_focus('class');
                return false;
	} 
        
}
function applicantId($row) {
    hidden('appid['.$row['id'].']', $row['applicant_id']);
    return $row['applicant_id'] ;
}

function test_result($row)
{        
    return 'P';
}
//----------------------------------
if (isset($_POST['Process'])) 
{     
    foreach($_POST['stchk'] as $key =>$val)
        {
            $chk = $val;
        }   
    if ($chk == 1)
        {
            foreach($_POST['stchk'] as $key =>$val)
                {              
                    $pass = 1;            
                    $stid = $_POST['stid'][$key];
                    //display_error($stid);
                    process_sport_short_list($stid, $pass);           
                    display_notification(_('Short list result has been added'));
                }
        }
    else
        {
            foreach($_POST['appid'] as $key => $apl)
                {    
                    $pass = 1;
                    //display_error($apl);
                    process_sport_short_list($apl, $pass);           
                    display_notification(_('Short list result has been added'));
                }       
        }
    $Ajax->activate('_page_body');
    $Mode = 'RESET';             
}

$Ajax->activate('_page_body');
//---------------------------------------------------------------------------------------------
if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Select Class:"), 'class', $_POST['class'], 'Select Class', false, $query);

submit_cells('SearchOrders', _("Search"), '', _('Select Class'), 'default');
end_row();
end_table(1);

//---------------------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2,"width=60%");
        start_row();
            labelheader_cell( '','width=3%');
            labelheader_cell( 'SL#','width=5%');
            labelheader_cell( 'Applicant ID','width=10%');
            labelheader_cell( 'Applicant Name','width=10%');
            labelheader_cell( 'Status','width=10%');
        end_row();      
//------------------------------------------------------------------------------------------------         
    
if(isset($_POST['SearchOrders'])){ 
    
    $sl = 1; 
  
    $sql = get_sql_admission_drirect_list($class);

    $result = db_query($sql,"data could not be found");

 if(mysql_num_rows($result)>0){
  while ($row = db_fetch($result))
    {
        start_row();
            check_cells(null, 'stchk['.$row['id'].']', '',false, '','align=center width=5%');
            label_cell($sl,'align=center');
            label_cell(applicantId($row),'align=center');
            hidden('stid['.$row['id'].']', $row['applicant_id']);
            label_cell($row['name'],'align=center');
            label_cell(test_result($row),'align=center');
        end_row();
        $sl++;
    }
 }
    else {
     display_notification(_('There are no students for Sport Addmission'));
    }


} 

end_table();

br();

div_start('controls');
  submit_center('Process', _("Process Short List"), true, '', 'default');
div_end();
//---------------------------------------------------------------------------------------------------

if (!@$_GET['popup']) {
    end_form();
    end_page();
}