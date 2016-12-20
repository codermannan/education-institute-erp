<?php

/* * ********************************************************************
 
 * ********************************************************************* */
$page_security = 'SA_SALESORDER';
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
    page(_($help_context = "Candidate List View"), false, false, "", $js);
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
function applicant_list_view($row) {
    
        return viewer_link(null, "/admission/report/addmission_form_view.php?Applicantid=" . $row['applicant_id'] . "", null, null, ICON_VIEW);
//    
    
    return true;
}
function admit_card($row) {
//        if($row['applicant_status'] == 1){

        return viewer_link(null, "/admission/report/addmission_test_admin.php?Applicantid=" . $row['applicant_id'] . "", null, null, ICON_VIEW);
        return true;
//        }
    
}
function applicant_image($row) {
 
   // display_error($row['photo_upload']);
        $stock_img_link = "";
	$check_remove_image = false;
	if (file_exists($row['photo_upload']))
	{
	 // 31/08/08 - rand() call is necessary here to avoid caching problems. Thanks to Peter D.
		$stock_img_link .= "<img style='border:1px solid #333333; border-radius:60px;',id='item_img' alt = '[".$row['photo_upload']."]' src='".$row['photo_upload'].
			"?nocache=".rand()."'"." height='50' width='50' >";
		$check_remove_image = true;
	} 
	else 
	{
//		$stock_img_link .= _("No Image");
//            $stock_img_link .="<img id='item_img' src=".'/company/0/images/102.jpg'.
//			 " height='20' width='20' border='1'>";
           $stock_img_link =  '<img style="border:1px solid #333333; border-radius:60px;",  src='.company_path().'/images/sabuj.jpg height="50" width="50" />';
	}
        return $stock_img_link;
    
}
//---------------------------------------------------------------------------------------------
$cls= $_POST['class'];
$applicant= $_POST['applicant'];
$datasearch=$_POST['datasearch'];
$sql = get_sql_for_list_view($cls,$applicant, $datasearch);

$cols = array(
    _("Applicant ID") ,
    _("Applicant Image") => array('insert' => false, 'fun' => 'applicant_image', 'align' => 'center'),
    _("Applicant Name") ,
    _("Mobile"),
    _("Address"),
    _("Form View") => array('insert' => true, 'fun' => 'applicant_list_view', 'align' => 'center'),
    _("Admit Card") => array('insert' => true, 'fun' => 'admit_card', 'align' => 'center')
);

//if (get_post('StockLocation') != $all_items) {
//    $cols[_("Location")] = 'skip';
//}
//---------------------------------------------------------------------------------------------------

$table = &new_db_pager('students_details', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}