<?php
$page_security = 'SS_SMS_LIB_BENTRY';
$path_to_root="../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/library/includes/ui/library_ui_lists.inc");
include_once($path_to_root . "/library/includes/db/library_db.php");

$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(450, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();

  page(_($help_context = "Book Entry"), false, false, "", $js);
if($_GET['book_id']){
    $bkid = $_GET['book_id'];
}
//-------------------------------------------------------------------------------------
  
if($_POST['bk_id']){
    $selected_id=$_POST['bk_id'];
}elseif($_GET['book_id']){
    $selected_id = $_GET['book_id'];
}
if(list_updated('location'))
$Ajax->activate('_page_body');
 
//-------------------------------------------------------------------------------------

if (isset($_POST['add']) || isset($_POST['update'])) 
{

	$input_error = 0;

        if (strlen($_POST['book_name']) == '') 
	{
		$input_error = 1;
		display_error(_("Book name must be added."));
		set_focus('book_name');
                return false;
	}
        
         if (strlen($_POST['book_type']) == '') 
	{
		$input_error = 1;
		display_error(_("Book type must be selected."));
		set_focus('book_type');
                return false;
	}
	

    	if ($selected_id) 
		{
			
    		     update_book($selected_id, $_POST['book_name'], $_POST['auth_name'], $_POST['edition'],
                            $_POST['cost'],$_POST['book_type'],$_POST['publication'],$_POST['isbn'],$_POST['location'],$_POST['shelf'],
                             $_POST['feature'],$_POST['key'],$_POST['source'],$_POST['new'],$_POST['display'],$_POST['active']);
                           
				$Ajax->activate('book_id'); 
				display_notification(_("Selected book record has been updated."));
			}
		
    	else 
		{
                           
           add_book( $_POST['book_name'], $_POST['auth_name'], $_POST['edition'],
                            $_POST['cost'],$_POST['book_type'],$_POST['publication'],$_POST['isbn'],$_POST['location'],$_POST['shelf'],$user,
                            $_POST['feature'],$_POST['key'],$_POST['source'],$_POST['new'],$_POST['display'],$_POST['active']);
            					
			   display_notification(_("New book has been added."));
					
		}
                set_focus('book_id');
		$Ajax->activate('_page_body');
	}

//--------------------------------------------------------------------------------------

if (isset($_POST['delete'])) 
{

	 dynamic_delete('sms_lib_book_entry', $selected_id);
		display_notification(_('Selected Book entry has been deleted'));
		unset($_POST['book_id']);
		$Ajax->activate('_page_body');
	
} 

//-------------------------------------------------------------------------------------

start_form();


	start_table(TABLESTYLE_NOBORDER);
	start_row();
        
     $_SESSION['searchflid'] = array("title"=>"Book", "inputflid"=>"bk_id", "tabhead1"=>"Id", 
            "tabhead2"=>"Book", "fld1"=>"book_id", "fld2"=>"book_name", "tbl"=>"sms_lib_book_entry");
         $url = "includes/sview/search_view.php?dat=searchflid";
         
   $vlink= viewer_link(_("Search"), $url, "", "", ICON_VIEW);  
         
   $book_query=array('book_id','book_name','sms_lib_book_entry');
   
   double_combo_list_cells(_("Select Book: "), 'bk_id', $_GET['book_id'],  _('New Book'), true,false,false,$book_query,$vlink);
   select_button_cell("itemsubmit",_("Search"));
  
	end_row();
	end_table();



	
br(1);
start_table(TABLESTYLE2);


    start_row();
        label_cell('<b>Book Entry</b>','align=center colspan=10');
    end_row();
    
if ($selected_id != "") {
	
	$myrow = view_book_list($selected_id);
//    display_error($myrow["source"]);
                $_POST['book_name'] = $myrow["book_name"];
                $_POST['auth_name']  = $myrow["auth_name"];
                $_POST['edition']  = $myrow["edition"];
                $_POST['cost']  = $myrow["cost"];
                $_POST['book_type']  = $myrow["book_type"];
                $_POST['publication'] = $myrow["publication"];
                $_POST['isbn'] = $myrow["isbn"];
                $_POST['new'] = $myrow["new"];
                $_POST['source'] = $myrow["source"];
                $_POST['locationupdate'] = $myrow["location"];
                $_POST['shelf'] = $myrow["shelf"];
                $_POST['display'] = $myrow["display"];
                $_POST['feature'] = $myrow["feature"];
                $_POST['key'] = $myrow["key_word"];
                $_POST['active'] = $myrow["active"];
              
//                display_error($_POST['locationupdate']);
        hidden('selected_id', $selected_id);

} 


text_row(_("Book Name:"), 'book_name', $_POST['book_name'], 40, 45); 

$query=array('id','author_name','sms_lib_author_setup');
combo_list_row(_("Author Name:"), 'auth_name', '', 'Select Author', true, $query);
//text_row(_("Author Name:"), 'auth_name', $_POST['auth_name'], 40, 45); 
text_row(_("Edition:"), 'edition', $_POST['edition'], 40, 45); 
text_row(_("Cost:"), 'cost', $_POST['cost'], 40, 45); 


$query=array('id','book_type','sms_lib_book_type_setup');
combo_list_row(_("Book Type :"), 'book_type', '', 'Select Book Type', true, $query);

$query=array('id','publisher_name','sms_lib_publisher_setup');
combo_list_row(_("Publication:"), 'publication', '', 'Select Publication', true, $query);

//text_row(_("Publication:"), 'publication',   $_POST['publication'], 40, 45);
text_row(_("ISBN/ISSN:"), 'isbn',   $_POST['isbn'], 40, 45);
    
?>

  <td>New?</td>
        <td>    
            <input type="radio" name="new" value="1" checked>Yes
            <input type="radio" name="new" value="0" <?php if($_POST['new']==0){?>checked<?php }else{ ?>notchecked<?php }?>>No
        </td>
  


<?php


text_row(_("Source:"), 'source',   $_POST['source'], 40, 45);

    $query=array(array('location_name','location_name','select distinct location_name, location_name from '.TB_PREF.'sms_lib_location_setup
          ORDER BY location_name ASC'));
    
    combo_list_row(_("Location Name:"), 'location', $_POST['locationupdate'], 'Select Location', true, $query);
    
    $query=array(array('shelf','shelf','SELECT shelf, shelf from '.TB_PREF.'sms_lib_location_setup
        WHERE location_name =\''. $_POST['location'].'\' ORDER BY shelf ASC'));
    combo_list_row(_("Shelf Name:"), 'shelf', null, 'Select Location', false, $query);
?>
<td>Display?</td>
        <td>    
            <input type="radio" name="display" value="1"  checked >Eng
            <input type="radio" name="display" value="0" <?php if($_POST['display']==0){?>checked<?php }else{ ?>notchecked<?php }?> >Bng
        </td>
<?php

textarea_row(_("Special Feature:"), 'feature', $_POST['feature'], 30, 3);
textarea_row(_("Key Word:"), 'key', $_POST['key'], 30, 3);

?>

   <td>Active?</td>
        <td>    
            <input type="radio" name="active" value="1" checked >Yes
            <input type="radio" name="active" value="0" <?php if($_POST['active']==0){?>checked<?php }else{ ?>notchecked<?php }?>>No
        </td>     
        
<?php

end_table(1);

if ($selected_id == "")    
{
	submit_center('add', _("Add Book"), true, '', 'default');
} 
else 
{
    submit_center_first('update', _("Update Book"), '', 'default');
    submit_center_last('delete', _("Delete Book"), '',true);
}
end_form();

end_page();

?>


