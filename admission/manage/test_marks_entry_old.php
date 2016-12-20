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
$page_security = 'SS_SMS_ADMSN_MRK_ENTRY';
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
    page(_($help_context = "Applicant Mark Entry"), false, false, "", $js);
}
if (isset($_GET['order_number'])) {
    $order_number = $_GET['order_number'];
}
//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('SearchOrders')) {
    $Ajax->activate('orders_tbl');
} elseif (get_post('_order_number_changed')) {
    $disable = get_post('order_number') !== '';

    $Ajax->addDisable(true, 'OrdersAfterDate', $disable);
    $Ajax->addDisable(true, 'OrdersToDate', $disable);
    $Ajax->addDisable(true, 'StockLocation', $disable);
    $Ajax->addDisable(true, '_SelectStockFromList_edit', $disable);
    $Ajax->addDisable(true, 'SelectStockFromList', $disable);

    if ($disable) {
        $Ajax->addFocus(true, 'order_number');
    } else
        $Ajax->addFocus(true, 'OrdersAfterDate');

    $Ajax->activate('orders_tbl');
}
//----------------------------------
if (isset($_POST['Processstmarks'])) 
{
    
        foreach($_POST['obtain_marks'] as $key => $marks)
            {
            
        $_POST['obtain_marks'] = strtoupper($_POST['obtain_marks']);
        
	if ((strlen(db_escape($_POST['obtain_marks'])) > 2) || empty($_POST['obtain_marks']))
	{
		$input_error = 1;
		display_error(_("Marks must be less than 100."));
		set_focus('obtain_marks');
	}
//        elseif
//        {
//            $input_error = 1;
//		display_error(_("Marks can not be empty."));
//		set_focus('mark');
//        }
        
        if($input_error != 1)
        { 
////            if(strle ($marks) > 2)
////            {
////                display_error('Exam mark should be 0 - 100');
////            }
//            else
//           {
            
            $appid = $_POST['appid'][$key];
            add_test_marks($appid, $marks);
//			display_notification(_('Status has been added'));
           // }
        }
        
        
     }

}
//---------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);


br();

start_row();

get_student_clas(_("Select Class:"), 'app_class', $_POST['app_class'], 'Select Class', false);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
//---------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------
function applicant_marks($row) {
    if($_POST['obtain_marks']== NULL)
    {
    return small_number_cells(null, "mark[".$row[id]."]");
   
    }
    
   }

function applicantId($row) {
    hidden('appid['.$row['id'].']', $row['applicant_id']);
    return $row['applicant_id'] ;
}

//---------------------------------------------------------------------------------------------

$sql = get_sql_for_mark_entry($class_id);

$cols = array(
    _("Applicant ID") => array('fun' => 'applicantId', 'align' => 'center') ,
    _("Applicant Name") ,
    _("Marks") => array('insert' => true, 'fun' => 'applicant_marks', 'align' => 'center')

);


//---------------------------------------------------------------------------------------------------


$table = & new_db_pager('students_details', $sql, $cols);

$table->width = "80%";

display_db_pager($table);
br();

div_start('controls');

        submit_center('Processstmarks', _("Insert Applicant Marks"), true, '', 'default');

    div_end();

if (!@$_GET['popup']) {
    end_form();
    end_page();
}