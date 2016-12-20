<?php

/* * ************************************************************************************************************** */
$page_security = 'SS_SMS_LIB_BREQ';
$path_to_root="../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/library/includes/ui/library_ui_lists.inc");
include_once($path_to_root . "/library/includes/db/library_db.php");

$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(800, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

simple_page_mode(true);

//if (isset($_GET['book_id'])) {
//    $book_id = $_GET['book_id'];
//}
//----------------------------------------------------------------------------------
if($_POST['bookid'])
    $selected_id=$_POST['bookid'];

////////////////////////////////////////////////////////////////////////////////////////////////////////
$user = $_SESSION['wa_current_user']->username;
$cls = $_GET['cls'];
$name = $_GET['name'];
$roll = $_GET['roll'];
//display_error($user);
page(_($help_context = "Book Request Page"),true);

//----------------------------------------------------------------------------------
if (isset($_POST['process'])) {
        
            add_request($user,$name,$cls,$roll,$_POST['book_id'],$_POST['amendment_note']);
            display_notification(_('New book record has been added'));

            $Ajax->activate('_page_body');
            $Mode = 'RESET';
            
//             meta_forward($path_to_root . "/sms/library/book_request.php?");
      
} 

//---------------------------------------------------------------------------------- 
br();

start_form();

$adm_fees = 5000;

$result = get_data_by_key($_GET['id'], 'sms_lib_book_entry', 'book_id');
$dt = explode('/',Today());
//$studentid = substr($result['class_name'], 6).$dt[2].rand();

    div_start('details');
    start_outer_table(TABLESTYLE2);
    table_section(1);
    table_section_title(_("Request for a Book"));

    label_row(_("Book Id:"), $result['book_id']);
    label_row(_("Book Name :"), $result['book_name']);
    label_row(_("Author Name :"), $result['auth_name']);
    label_row(_("Edition :"), $result['edition']);
    label_row(_("Publication :"), $result['publication']);
    textarea_row(_("Note :"), 'amendment_note', null, 30, 3);

    hidden('book_id', $result['book_id']);
    hidden('student_id', $user);
   
    
    
    
    end_outer_table(1);
    div_end();


    submit_center_first('Update', _("Update"), '', null);
    submit_center_last('process', _("Process"), '', 'default');

    end_form();
    br();

end_page();
?>