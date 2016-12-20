<?php
 /* ********************************************************************* */
$page_security = 'SS_SMS_LIB_BREQ';
$path_to_root="../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/library/includes/ui/library_ui_lists.inc");
include_once($path_to_root . "/library/includes/db/library_db.php");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Book List"), false, false, "", $js);
}
//$syear = get_current_schoolyear();
//-----------------------------------------------------------------------------------
// Ajax updates

//if(list_updated('class'))
//    $Ajax->activate('_page_body');
//if(list_updated('section'))
//    $Ajax->activate('_page_body');
if(isset($_POST['SearchOrders']))
    $Ajax->activate('_page_body');


//---------------------------------------------------------------------------------------------
if (isset($_POST['SearchOrders'])) 
{   
	
        if (strlen($_POST['datasearch']) == '') 
	{
		$input_error = 1;
		display_error( _('Data Search must be selected.'));
		set_focus('datasearch');
                return false;
	} 
        
        elseif (strlen($_POST['searchvalue']) == '') 
	{
		$input_error = 1;
		display_error( _('Search Value must be selected.'));
		set_focus('searchvalue');
                return false;
	} 
       
       
}
//---------------------------------------------------------------------------------------------
if (!@$_GET['popup'])
  start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();

search_field(_("Select Search Type : "), 'datasearch', 'book_req');

text_cells(null, 'searchvalue');

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
//---------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------

function add_books($row) {
     
    return viewer_link(null,
            "library/manage/add_books_page.php?id=".$row['book_id']. "", null, null, ICON_ADD);

        return true;
}


function edit_book($row) {
            if ($row['id'] != 1)
                return pager_link(_("Edit Book"), "/sms/library/book_entry.php?book_id=" . $row["book_id"], ICON_EDIT);
            else
                return "<center>".''."</center>"; 
}
        
function delete_link($row){
    submit_js_confirm("Delete".$row['id'],
                    sprintf(_("Are you sure you want to delete ?")));
    
     if($_POST["Delete".$row["id"]]) {
            delete_book_data('sms_lib_book_entry', "id", $row["id"]); 
             
             meta_forward($_SERVER['PHP_SELF'], "delID=".$row["id"]); 
                        
        }
	
     return  "<center>".button("Delete".$row["id"], _("Delete"), _("Delete"), ICON_DELETE).
        "</center>";
}

if (isset($_GET['delID'])){
   display_notification(_('Selected Book data have been deleted')); 
}


function tr_view_link($row){
    return viewer_link($row['book_id'],
            "/library/report/lib_book_view.php?book_id=".$row['book_id']. "");
     return true;
}

//---------------------------------------------------------------------------------------------
$datasearch= $_POST['datasearch'];
$searchvalue=$_POST['searchvalue'];
$sql = get_sql_for_book_list($datasearch,$searchvalue);

$cols = array(
    _("Book ID")=> array('insert' => false, 'fun' => 'tr_view_link', 'align' => 'center'),
    _("Book Name")=> array('ord' => 'asc'),
     _("Book Type")=> array('ord' => 'asc'),
    _("Author Name")=> array('ord' => 'asc'),
    _("Edition")=> array('ord' => 'asc'),
    _("Publication")=> array('ord' => 'asc'),
    _("Entry Date")=> array('ord' => 'asc','type'=>'date'),
    _("Source")=> array('ord' => 'asc'),
    _("Cost")=> array('ord' => 'asc'),
    _("Add Books") => array('insert' => true, 'fun' => 'add_books', 'align' => 'center'),
     array('insert' => true, 'fun' => 'edit_book', 'align' => 'center'),
    array('insert' => true, 'fun' => 'delete_link', 'align' => 'center')
);

//---------------------------------------------------------------------------------------------------

$table = & new_db_pager('sms_lib_book_entry', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}