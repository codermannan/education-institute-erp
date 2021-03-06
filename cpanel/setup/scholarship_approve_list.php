<?php

/* * ********************************************************************
 
 * ********************************************************************* */
$page_security = 'SS_SMS_APLCNT_LST_VW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/cpanel/includes/ui/cpanel_ui_lists.inc");
include_once($path_to_root . "/cpanel/includes/db/cpanel_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Scholarship applied leave"), false, false, "", $js);
}

//-----------------------------------------------------------------------------------

if(list_updated('class'))
$Ajax->activate('_page_body');

if(isset($_POST['SearchOrders']))
$Ajax->activate('_page_body');
//---------------------------------------------------------------------------------------------

//..............................................................................................

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

br();
 

 start_row();

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));

combo_list_cells(_("Select Class :"), 'class', '', 'Select Class', true, $query);

search_field(_("Select Search Type : "), 'datasearch');

text_cells(null, 'applicant');

 
submit_cells('SearchOrders', _("Search"), '', _('Select applicant'), 'default');

 end_row();
end_table(1);
//---------------------------------------------------------------------------------------------
function process_scholarship($row) {
    
    if($row['is_approved_cat'] == 1){
        return 'Approved';
    }else{
        return pager_link( _("Approve Scholarship"), "/cpanel/setup/scholarship_approve.php?applicantid=" . $row['applicant_id'], ICON_ADD);
    }

    return true;  
}

function applicant_image($row) {
        $stock_img_link = "";
	$check_remove_image = false;
	if (file_exists($row['photo_upload'])){
		$stock_img_link .= "<img style='border:1px solid #333333; border-radius:60px;',id='item_img' alt = '[".$row['photo_upload']."]' src='".$row['photo_upload'].
			"?nocache=".rand()."'"." height='50' width='50' >";
		$check_remove_image = true;
	} 
	else {
           $stock_img_link =  '<img style="border:1px solid #333333; border-radius:60px;",  src='.company_path().'/images/sabuj.jpg height="50" width="50" />';
	}
        return $stock_img_link;
    
}
function applicant_form($row) {
    
    return viewer_link($row['applicant_id'], "/admission/report/addmission_form_view.php?Applicantid=" . $row['applicant_id'] . "");
    return true;
}
//---------------------------------------------------------------------------------------------
$cls= $_POST['class'];
$applicant= $_POST['applicant'];
$datasearch=$_POST['datasearch'];

$sql = get_sql_for_scholarship_view($cls,$applicant, $datasearch);

$cols = array(
    _("Applicant ID")=> array('insert' => false, 'fun' => 'applicant_form', 'align' => 'center'),
    _("Applicant Image") => array('insert' => false, 'fun' => 'applicant_image', 'align' => 'center'),
    _("Applicant Name") ,
    _("Mobile")=> array('align' => 'center'),
    _("Address"),
    _("Student Category")=> array('align' => 'center'),
    _("Scholarship Status") => array('insert' => true, 'fun' => 'process_scholarship', 'align' => 'center')
);


//---------------------------------------------------------------------------------------------------

$table = &new_db_pager('students_details', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}