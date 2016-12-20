<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_XM_NAME_SETING';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_("Exam Name Entry"));

simple_page_mode(true);


if(list_updated('head_name'))
$Ajax->activate('_page_body');

if(list_updated('SearchOrders'))
$Ajax->activate('_page_body');
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['head_name']) == '') 
	{
		$input_error = 1;
		display_error(_("Head name cannot be empty."));
		set_focus('head_name');
	}
        

           if($input_error != 1)
           {
    	if ($selected_id != -1) 
    	{ 
                $uparr = array("head_name"=>"'".$_POST['head_name']."'","discount_status"=>"'".$_POST['discount_status']."'");
                $condition = array("id"=>$selected_id);
                $sqlup = update_data('sms_payment_head',$uparr, $condition);
		display_notification(_('Selected Head name has been updated'));
    	} 
    	else 
    	{
            
                 $insarr = array("head_name"=>"'".$_POST['head_name']."'","discount_status"=>"'".$_POST['discount_status']."'");
                 $sqlins = insert_data('sms_payment_head',$insarr);
                 display_notification(_('Head name has been added'));
              
    	}
		$Mode = 'RESET';
      }
	
}
//----------------------------------------------------------------------------------
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
//.........................................................----------------------
if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);

br();
 
 start_row();

$query=array(array('id','head_name','select id, head_name from '.TB_PREF.'sms_payment_head
       ORDER BY head_name ASC'));
combo_list_cells(_("Head Name:"),'headname', null, 'Select Head Name', false, $query);
submit_cells('SearchOrders', _("Search"), '', _('Select head name'), 'default');

 end_row();
end_table(1);
 
//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
        global $selected_id;
        
        if($selected_id!=''){
 
            $exquery = "SELECT * FROM ".TB_PREF."sms_payment_head_setting WHERE head_name=".$selected_id;
            $extres = db_query($exquery);
            $exrows = db_num_rows($extres);
            
            if($exrows>0){
                
                display_notification(_('Selected head can not be deleted,because it has been used another table as foreign key'));
            }
            else{
                $condition = array("id"=>$selected_id);
                data_delete('sms_payment_head', $condition);
                    display_notification(_('Selected head has been deleted'));
            }        
        }
	
	
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------
function head_name_select($head_name){
        $sql="SELECT  @num:=@num+1, head_name,
              CASE
                WHEN discount_status = '1' THEN 'Discountable'
                WHEN discount_status = '2' THEN 'Non Discountable'
              ELSE discount_status
              END as discount_status,
              id FROM (SELECT @num:=0) as n, ".TB_PREF."sms_payment_head";
        if($head_name!=''){
            $sql.=" WHERE  id=".db_escape($head_name);
        }
        return $sql;
}
//----------------------------------------------------------------------------------
start_form();
br();

$sqlh=head_name_select($_POST['headname']);

$cols = array(
     _("Sl#")=>array('align'=>'center'),
     _("Head Name")=>array('align'=>'center'),
     _("Discount Status")=>array('align'=>'center'),
     array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
     array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_exam_name', $sqlh, $cols);

$table->width = "60%";

display_db_pager($table);
echo '<br>';

//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
                $myrow = data_retrieve("sms_payment_head", "id", $selected_id);
		$_POST['head_name'] = $myrow["head_name"];
                $_POST['discount_status'] = $myrow["discount_status"];
	}
	hidden('id', $selected_id);
}

text_row(_("Head Name:"), 'head_name', $_POST['head_name'], 45, 60);

$discount = array('0'=>'Please Select','1'=>'Discountable','2'=>'Non Discountable');
free_combo_list_row(_("Discount Status:"), 'discount_status', $_POST['discount_status'], $discount);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>
