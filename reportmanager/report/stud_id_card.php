<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_DASH_BRD_SETP';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/reportmanager/includes/ui/report_ui_lists.inc");
include_once($path_to_root . "/reportmanager/includes/db/report_db.inc");

page(_($help_context = "Generate Student ID Card"), false, false, "", $js);
simple_page_mode(true);
$syear = get_current_schoolyear();

//-----------------------------------------------------------------------------------
// Ajax updates

if(list_updated('class'))
$Ajax->activate('_page_body');

if(list_updated('section'))
$Ajax->activate('_page_body');

if(list_updated('SearchOrders'))
$Ajax->activate('_page_body');



$cls= $_POST['class'];
$sec = $_POST['section'];

$Ajax->activate('_page_body');

//--------------------------------------------------------------------------------------
//
function dispatchPaySlip($stid) {
    //$card = implode(',', $emp_code);
   // global $path_to_root;
    $new_win = "<a target='_blank' "
            . "href='$path_to_root/sms/view/IDcardView.php?"
            . 'Print Id Card'
            . "</a>";
    return $new_win;
}
//--------------------------------------------------------------------------------------
//start_form(false, false, $_SERVER['PHP_SELF'] ."?outstanding_only=$outstanding_only");

start_form();
start_table();
start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class :"), 'class', '', 'Select Class', true, $query);


$query=array('id','session_name','sms_session','class',$_POST['class']);
combo_list_cells(_("Section :"), 'section', $_POST['session_name'], 'Select Section', true, $query);

$query=array('student_id','student_id','sms_student','st_section',$_POST['section']);
combo_list_cells(_("Student ID :"), 'stid', $_POST['id'], 'Select ID', true, $query);

submit_cells('SearchOrders', _("Search"), '', '', 'default');
end_row();
end_table();
br();
?>

<style>
    .left{
        width: 100%;
        text-align: center;
    }
</style>
<?php
if(get_post('SearchOrders'))
{
    $stid = $_POST['stid'];
    
}
$sql=get_stud_info($syear,$stid);

$result=db_query($sql);
$stinfo=db_fetch($result);

div_start();


start_table(TABLESTYLE2,'width=14%');
label_cell(dispatchPaySlip($_POST['stid']));
end_table();
start_table(TABLESTYLE,'width=20%');
 //  $stock_img_link = "";
//	$check_remove_image = false;
//	if (isset($stinfo['student_id']) && file_exists(company_path().'/images/hr/'
//		.employee_img_name($stinfo['vendor_no']).".jpg")) 
//	{
//	 // 31/08/08 - rand() call is necessary here to avoid caching problems. Thanks to Peter D.
//		$stock_img_link .= "<img id='item_img' alt = '[".$stinfo['student_id'].".jpg".
//			"]' src='".company_path().'/images/hr/'.employee_img_name($stinfo['student_id']).
//			".jpg?nocache=".rand()."'"." height='70'  border='0'>";
//		$check_remove_image = true;
//	} 
//	else 
//	{
//		$stock_img_link .= _("No image");
//	}
//        $sign_Image .= "<img id='item_img' alt = 'signature' src=".company_path()."/images/sign.jpg"." height='40px' width='70px'  border='0'>";
       
        labelheader_cell($_SESSION['SysPrefs']->prefs['coy_name'],"colspan=2 align=center");
        start_row();
            label_cell($stock_img_link,"colspan=2 align=center");
        end_row();
        label_row(_('Name:'),$stinfo['name'],'style="font-size:10px"','style="font-size:10px"');
        label_row(_('Class:'),$stinfo['class_name'],'style="font-size:10px"','style="font-size:10px"');
        label_row(_('Section:'),$stinfo['session_name'],'style="font-size:10px"','style="font-size:10px"');
        label_row(_('ID No:'),$stinfo['student_id'],'style="font-size:10px"','style="font-size:10px"');
        label_row(_('Roll NO:'),$stinfo['roll_number'],'style="font-size:10px"','style="font-size:10px"');
        if($stinfo['blood_group']==1)
            $blood='A+';
        elseif($stinfo['blood_group']==2)
             $blood='A-';
        elseif($stinfo['blood_group']==3)
             $blood='AB+';
        elseif($stinfo['blood_group']==4)
             $blood='AB-';
        elseif($stinfo['blood_group']==5)
             $blood='B+';
        elseif($stinfo['blood_group']==6)
             $blood='B-';
        elseif($stinfo['blood_group']==7)
             $blood='O+';
        elseif($stinfo['blood_group']==8)
            $blood='O-';
        label_row(_('Blood Group:'),$blood,'style="font-size:10px"','style="font-size:10px"');
        start_row();
         label_cell('<u>'.$sign_Image.'</u><br/>Auth Sign','colspan=3 align=right style="font-size:10px"');
            
        end_row();
        end_table();

div_end();

end_form();
    
end_page();
?>
