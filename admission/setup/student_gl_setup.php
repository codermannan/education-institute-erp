<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_XM_NAME_SETING';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/includes/db/common_function_dbinsert_data.inc");


$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_("Student GL Setup"));

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

           if($input_error != 1)
           {
    	if ($selected_id != -1) 
    	{ 
                $uparr = array("sales_account "=>"'".$_POST['sales_account']."'","sales_discount_account"=>"'".$_POST['sales_discount_account']."'",
                         "receivables_account "=>"'".$_POST['receivables_account']."'","payment_discount_account "=>"'".$_POST['payment_discount_account']."'");
                $condition = array("id"=>$selected_id);
                $sqlup = update_data('sms_student_gl_setup',$uparr, $condition);
		display_notification(_('Selected GL Set Up has been updated'));
    	} 
    	else 
    	{
                 $insarr = array("sales_account "=>"'".$_POST['sales_account']."'","sales_discount_account"=>"'".$_POST['sales_discount_account']."'",
                           "receivables_account "=>"'".$_POST['receivables_account']."'","payment_discount_account "=>"'".$_POST['payment_discount_account']."'");
                 $sqlins = insert_data('sms_student_gl_setup',$insarr);
                 display_notification(_('Student GL Set Up has been added'));
              
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
function show_gl_set_up(){
        $sql=" SELECT * FROM " . TB_PREF ."sms_student_gl_setup" ;
        return $sql;
}
//----------------------------------------------------------------------------------
start_form();
br();

$sqlh=show_gl_set_up();

$cols = array(
     _("Sl#")=>array('align'=>'center'),
     _("Sales Account")=>array('align'=>'center'),
     _("Sales Discount Account")=>array('align'=>'center'),
     _("Accounts Receivable Account")=>array('align'=>'center'),
     _("Prompt Payment Discount Account")=>array('align'=>'center')
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
                $myrow = data_retrieve("sms_student_gl_setup", "id", $selected_id);
		$_POST['sales_account'] = $myrow["sales_account"];
                $_POST['sales_discount_account'] = $myrow["sales_discount_account"];
                $_POST['receivables_account'] = $myrow["receivables_account"];
                $_POST['payment_discount_account'] = $myrow["payment_discount_account"];
	}
	hidden('id', $selected_id);
}

$_SESSION['searchfil1'] = array("title"=>"Sales", "inputflid"=>"sales_account", "tabhead1"=>"Id", 
"tabhead2"=>"Sales Account", "fld1"=>"account_code", "fld2"=>"account_name", "tbl"=>"chart_master");
$url1 = "includes/sview/search_view.php?dat=searchfil1";
$vlink1= viewer_link(_("Search"), $url1, "", "", ICON_VIEW);

gl_all_accounts_list_row(_("Sales Account:"), 'sales_account', 4010, false, false, true,false,$vlink1);

$_SESSION['searchfil2'] = array("title"=>"Sales Discount", "inputflid"=>"sales_discount_account", "tabhead1"=>"Id", 
"tabhead2"=>"Sales Discount", "fld1"=>"account_code", "fld2"=>"account_name", "tbl"=>"chart_master");
$url2 = "includes/sview/search_view.php?dat=searchfil2";
$vlink2= viewer_link(_("Search"), $url2, "", "", ICON_VIEW);
        
gl_all_accounts_list_row(_("Sales Discount Account:"), 'sales_discount_account',4510,false,false,false,false,$vlink2);

$_SESSION['searchfil3'] = array("title"=>"Receivable Account", "inputflid"=>"receivables_account", "tabhead1"=>"Id", 
"tabhead2"=>"Receivable Account", "fld1"=>"account_code", "fld2"=>"account_name", "tbl"=>"chart_master");
$url3 = "includes/sview/search_view.php?dat=searchfil3";
$vlink3= viewer_link(_("Search"), $url3, "", "", ICON_VIEW);

gl_all_accounts_list_row(_("Accounts Receivable Account:"), 'receivables_account', 1200, true,false,false,false,$vlink3);




    $_SESSION['searchfil4'] = array("title"=>"Payment Discount", "inputflid"=>"payment_discount_account", "tabhead1"=>"Id", 
"tabhead2"=>"Payment Discount", "fld1"=>"account_code", "fld2"=>"account_name", "tbl"=>"chart_master");
$url4 = "includes/sview/search_view.php?dat=searchfil4";
$vlink4= viewer_link(_("Search"), $url4, "", "", ICON_VIEW);


gl_all_accounts_list_row(_("Prompt Payment Discount Account:"), 'payment_discount_account',4500,false,false,false,false,$vlink4);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>
