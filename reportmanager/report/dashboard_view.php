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
include_once($path_to_root . "/sms/includes/db/student_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Dash Board View"), false, false, "", $js);
}
//if (isset($_GET['order_number'])) {
//    $order_number = $_GET['order_number'];
//}
simple_page_mode(true);
//------------------------------------------------------------

start_form();
start_table(TABLESTYLE2,"width=40%");
         start_row();
          
             labelheader_cell( 'SL#','width=3%');
             labelheader_cell( 'Notice','width=12% ');
             //labelheader_cell( 'Marks','width=7%'); 
         end_row();
         
           
         
//if( list_updated('app_class') ){   
    //$class = $_POST['clsid'];
    $sql = get_info_for_notice();
    
    $result = db_query($sql,"data could not be found");
 
 $sl=1;
  while ($rep = db_fetch($result))
    {
         start_row();
             
         label_cell( $sl,'width=3%');
//             label_cell( $rep['class'],'width=10%');
//             hidden('clsid['.$rep['id'].']', $rep['class']);
             label_cell( $rep['notice'],'width=15% height=16');
            // text_cells($rep['obtain_marks'], 'mark['.$rep['id'].']', null, 20,2);  
         end_row();
           
         $sl++;
   // }
} 
end_table();

end_form();

end_page();
?>
