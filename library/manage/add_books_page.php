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
    $js .= get_js_open_window(900, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

page(_($help_context = "Book Receive Page"), true, false, "", $js);

//----------------------------------------------------------------------------------
if($_POST['bookid'])
    $selected_id=$_POST['bookid'];

$user = $_SESSION['wa_current_user']->username;


//----------------------------------------------------------------------------------

if (isset($_POST['add_books'])) {
            
            add_books_in_lib($user, $_POST['book_id'], $_POST['num_books']);
            display_notification(_('Books Added Successfully'));

            $Ajax->activate('_page_body');
            $Mode = 'RESET';
               
         
} 

//---------------------------------------------------------------------------------- 
br();

start_form();

$result = get_data_by_key($_GET['id'], 'sms_lib_book_entry', 'book_id');
$dt = explode('/',Today());

    div_start('details');
    start_outer_table(TABLESTYLE2);
    table_section(1);
    table_section_title(_("Book Receive"));

    label_row(_("Book Id:"), $result['book_id']);
    label_row(_("Book Name :"), $result['book_name']);
    label_row(_("Author Name :"), $result['auth_name']);
    label_row(_("Edition :"), $result['edition']);
    label_row(_("Publication :"), $result['publication']);
    text_row(_("Number of Books:"), 'num_books', $_POST['num_books'], 40, 30);
    
    hidden('book_id', $result['book_id']);
    hidden('student_id', $user);
   
    end_outer_table(1);
    div_end();

    submit_center_first('Update', _("Update"), '', null);
    submit_center_last('add_books', _("Add Books"), '', 'default');

    end_form();
    br();

end_page();
?>