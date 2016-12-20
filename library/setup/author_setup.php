<?php
$page_security = 'SS_SMS_CLS_RTN_SETP';
$path_to_root="../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/library/includes/ui/library_ui_lists.inc");
include_once($path_to_root . "/library/includes/db/library_db.php");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);

page(_("Author Setup"));

simple_page_mode(true);

//---------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];

$upload_plan_img = "";
if (isset($_FILES['pic'])) 
{
       $upload_plan_img = "";
    if ($_FILES["pic"]["name"] != '') {
      
            $result = $_FILES['pic']['error'];
            $upload_plan_img = 'Yes'; //Assume all is well to start off with
            $filename = company_path().'/assignment/';



            if (!file_exists($filename))
            {
                    mkdir($filename);
            }


            if ($upload_plan_img == 'Yes')
            {       $filename .= $_FILES["pic"]["name"];
            
                    $result  =  move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
            }
    

}

}


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
    $input_error = 0;

    if (strlen($_POST['author_name']) == '') 
    {
            $input_error = 1;
            display_error(_("Author name must be entered."));
            set_focus('author_name');
    }

    if (strlen($_POST['address']) == '') 
    {
            $input_error = 1;
            display_error(_("Address must be entered."));
            set_focus('address');
    }

    if (strlen($_POST['status']) == '') 
    {
            $input_error = 1;
            display_error(_("Status must be selected."));
            set_focus('status');
    }


    if ($input_error !=1)
    {
       
        if ($selected_id != -1) 
            
            
        { 
	    update_author($selected_id,$_POST['author_name'],$_POST['address'],$filename,$_POST['status']);    	    		
			display_notification(_('Selected data has been updated'));
        } 
        else
        {
            
           add_author($_POST['author_name'],$_POST['address'],$filename,$_POST['status']);    		
           display_notification(_('New Author has been added'));
        }
      //  $Mode = 'RESET';
            
        }
    
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
        dynamic_delete('sms_lib_author_setup',$selected_id);
               
		display_notification(_('Selected author has been deleted'));
        
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------
function author_image($row) {
        $stock_img_link = "";
	$check_remove_image = false;
	if (file_exists($row['image'])){
		$stock_img_link .= "<img style='border:1px solid #333333; border-radius:60px;',id='item_img' alt = '[".$row['image']."]' src='".$row['image'].
			"?nocache=".rand()."'"." height='50' width='50' >";
		$check_remove_image = true;
	} 
	else {
           $stock_img_link =  '<img style="border:1px solid #333333; border-radius:60px;",  src='.company_path().'/assignment/ height="50" width="50" />';
	}
        return $stock_img_link;
    
}
function edit_link($row) 
{
    
        return "<center>".button("Edit".$row["id"], _("Edit"), _("Edit"), ICON_EDIT).
        "</center>";

}

function delete_link($row) 
{
    submit_js_confirm("Delete".$row['id'],
                    sprintf(_("Are you sure you want to delete ?")));
	
     return  "<center>".button("Delete".$row["id"], _("Delete"), _("Delete"), ICON_DELETE).
        "</center>";
}

//...................................................................................
//$result =  view_author();
if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);


end_table();
br();

$newsql=view_author();

$cols = array(
    _("Image") => array('insert' => false, 'fun' => 'author_image', 'align' => 'center'),
    _("Author Name")=>array('align'=>'center'),
    _("Address")=>array('align'=>'center'),
    array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
    array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_lib_author_setup', $newsql, $cols);

$table->width = "60%";

display_db_pager($table);
end_form();
echo '<br>';

//----------------------------------------------------------------------------------

start_form(true);

start_table(TABLESTYLE2);


if ($selected_id != -1) 
{

 	if ($Mode == 'Edit') {
            
                $myrow = data_retrieve('sms_lib_author_setup', 'id', $selected_id);       
              
                $_POST['author_name'] = $myrow['author_name'];
		$_POST['address'] = $myrow['address'];
                $_POST['status'] = $myrow["status"];
	}
	hidden('id', $selected_id);
}

text_row(_("Author Name:"),'author_name',$_POST['location_name'],30,30);
textarea_row("Address:", 'address', $_POST['address'], 20, 3);
file_row(_("Author Image (.jpg)") . ":", 'pic', 'pic');
sms_status(_("Status:"), 'status', null);

end_table(1);
//end_form();
submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}
?>
