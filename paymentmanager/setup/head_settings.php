<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_STDNT_HEAD_NAME_SETNG';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_("Head Settings Entry"));

simple_page_mode(true);
$syear = get_current_schoolyear();
$tday = Today();
//----------------------------------------------------------------------------------
if($_POST['id'])
    $selected_id=$_POST['id'];

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

        if (strlen($_POST['head_name']) == 0) 
	{
		$input_error = 1;
		display_error(_("head name cannot be empty."));
		set_focus('exam_name');
	}
        
        if (strlen($_POST['payment']) == 0) 
	{
		$input_error = 1;
		display_error(_("The payment cannot be empty."));
		set_focus('exam_name');
	}
        if ($_POST['due_date'] == $tday AND $_POST['payment']== 1){
		$input_error = 1;
		display_error(_("Due date cannot be today."));
		set_focus('due_date');
	}
        if (strlen($_POST['class_id']) == 0) 
	{
		$input_error = 1;
		display_error(_("The class cannot be empty."));
		set_focus('exam_name');
	}
        
       if (strlen($_POST['amount']) == 0) 
	{
		$input_error = 1;
		display_error(_("The amount cannot be empty."));
		set_focus('exam_name');
	}
        
        if($input_error != 1)
        {
            global $selected_id, $Mode;

	  $ok = true;

    	if ($selected_id != -1) 
    	{ 
               
		    update_head_settings($selected_id,$syear, $_POST['head_name'], $_POST['payment'],$_POST['student_type'],$_POST['class_id'],input_num('amount'),date2sql($_POST['due_date']));    		
			display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{
            $re= "SELECT school_year, head_name,student_type,st_class FROM " . TB_PREF . "sms_payment_head_setting WHERE school_year="
                    . db_escape($syear) . "AND head_name=" . db_escape($_POST['head_name']) . "AND student_type=" . db_escape($_POST['student_type']) . "AND st_class=" . db_escape($_POST['class_id']) ;
            
            $tr= db_query($re);
           $final = db_fetch($tr);
           
           if($syear == $final['school_year'] && $_POST['head_name'] == $final['head_name'] &&  $_POST['student_type'] == $final['student_type'] && $_POST['class_id'] == $final['st_class'])
           {
         
                display_notification('Head name exits alresdy');
           }
         else
         {
           add_head_settings($syear,$_POST['head_name'],$_POST['payment'],$_POST['student_type'],$_POST['class_id'],  input_num('amount'),  date2sql($_POST['due_date']));
                   
			display_notification(_('Head setings  has been added'));
         }
    	}
		$Mode = 'RESET';
	
}
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{
        global $selected_id;
        
        if($selected_id!=''){
           
            $tblrq = "SELECT * FROM ".TB_PREF."sms_tbl_receivable WHERE head_id=".$selected_id;
            $recres = db_query($tblrq);
            $recrows = db_num_rows($recres);
            
            $trquery = "SELECT * FROM ".TB_PREF."sms_transaction WHERE head_id=".$selected_id;
            $trres = db_query($trquery);
            $trrows = db_num_rows($trres);
            
            if($recrows>0 OR $trrows>0){
                
                display_notification(_('Selected head can not be deleted,because it has been used another table as foreign key'));
            }
            else{
               delete_data($selected_id,'sms_payment_head_setting');
		display_notification(_('Selected data has been deleted'));
            }
//         
        }
	
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
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
function status($row) 
{   
    if($row['student_type']==1){
        return 'New Student';
    }
    elseif($row['student_type']==2){
        return 'Old Student';
    }
    else{
        return 'Both';
    }
}
function payment_terms($row) 
{   
    if($row['no_of_payment']==1){
        return 'One Time';
    }
    elseif($row['no_of_payment']==2){
        return 'Half Yearly';
    }
    elseif($row['no_of_payment']==3){
        return 'Quarterly';
    }
    elseif($row['no_of_payment']==12){
        return 'Monthly';
    }
    else{
        return 'Payment term has not been initialized';
    }
}
//-------------------------------
function duedate($row) 
{   
    if($row['due_date']=='0000-00-00'){
        return ' ';
    }
    else{
        return $row['due_date'];
    }
}

//............................................................

if (!@$_GET['popup'])
    start_form();
start_table(TABLESTYLE_NOBORDER);

start_row();

 $query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_cells(_("Class:"),'class', null, 'Select Class', false, $query);

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
br();

$ec=sms_payment_setting($hs);

$cols = array(
    _("Sl#")=>array('align'=>'center'),
    _("Head Name")=>array('align'=>'center'),
    _("Payment Terms")=>array('fun'=>'payment_terms','align'=>'center'),
    _("Due Date")=>array('fun'=>'duedate','align'=>'center'),
    _("Student Type")=>array('fun'=>'status','align'=>'center'),
    _("Class")=>array('align'=>'center'),
    _("Amount")=>array('align'=>'center'),
    array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
    array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_payment_head_setting', $ec, $cols);

$table->width = "60%";

display_db_pager($table);
echo '<br>';

//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
            
                $myrow = data_retrieve("sms_payment_head_setting", "id", $selected_id);
		$_POST['head_name']  = $myrow["head_name"];
                $_POST['payment']  = $myrow["no_of_payment"];
                $_POST['student_type']  = $myrow["student_type"];
                $_POST['class_id']  = $myrow["st_class"];
                $_POST['amount']  = $myrow["amount"];
	}
	hidden('selected_id', $selected_id);
}

$query=array(array('id','head_name','select id, head_name from '.TB_PREF.'sms_payment_head
       ORDER BY head_name ASC'));
combo_list_row(_("Head Name:"), 'head_name', $_POST['head_name'], 'Select Head Name', false, $query);

$items = array('0'=>'Please Select','1'=>'One Time','2'=>'Half Yearly','3'=>'Quarterly','12'=>'Monthly');
free_combo_list_row(_("Payment Terms:"), 'payment',$_POST['payment'], $items, array('select_submit'=>true));

if (get_post('payment')==1) {
  $Ajax->activate('_page_body');
  date_row(_("Due Date:"), 'due_date');
}
payment_head_lst_cells(_("Student Type :"),'student_type',null);

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_row(_("Class:"), 'class_id', $_POST['class_id'], 'Select Class', false, $query);
        
amount_row(_("Amount:"), 'amount', null, '', $bank_currency);     

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);


  if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>

