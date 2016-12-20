<?php
/**********************************************************************
    
***********************************************************************/
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/student_payment_cart.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");


//-------------------------------------------------------------------------------------

function add_to_student_payment_raw(&$order, $ptype, $stid, $syear)
{
    
    if ($order->find_payment_cart_item_raw($ptype))
         display_error(_("For Part :") . $ptype . " " . "This Item is already on this order. ");
	else
         $order->add_to_student_cart_raw (count($order->line_items), $ptype, $stid, $syear);
}

//--------------------------------------------------------------------------------

function st_display_order_header_raw(&$order,$stid, $syear, $class)
{
        global $Refs,$Ajax;
        $sql="SELECT 
              ss.*,
              CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) AS name,sd.applicant_id,
              sc.class_name,
              sec.session_name,
              ssc.cat_name
              FROM " . TB_PREF . "sms_student ss
              LEFT JOIN " . TB_PREF . "sms_students_details sd ON ss.student_id = sd.student_id
              LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON ss.st_class = sc.id
              LEFT JOIN " . TB_PREF . "sms_session sec ON ss.st_section = sec.id
              LEFT JOIN " . TB_PREF . "sms_student_scholarship sch ON sd.applicant_id = sch.applicant_id
              LEFT JOIN " . TB_PREF . "sms_student_category ssc ON sch.student_cat = ssc.id
              WHERE ss.school_year =".  db_escape($syear)." AND ss.student_id =".  db_escape($stid)." AND ss.st_class =".  db_escape($class); 
         
        $query = db_query($sql);
        $res = db_fetch(db_query($sql, 'Could not get data.'));
        
	start_outer_table(TABLESTYLE,  "width=70%");
        
	table_section(1,"50%");
        
        $sql="SELECT  phs.id, phm.head_name 
              FROM " . TB_PREF . "sms_payment_head_setting phs
              LEFT JOIN " . TB_PREF . "sms_payment_head phm ON phs.head_name = phm.id
              WHERE phs.st_class=".  db_escape($class). " AND phs.no_of_payment != 1";
        
        $query=array(array('si','sn',$sql));
        combo_list_cells(_("Payment Type :"), 'payment_type', '', 'Select Payment Type', true, $query);
        date_row(_("Payment Date :"), 'payment_date');
        label_row(_("Student ID :"), $_GET['studentid']);
        label_row(_('Student Name :'), $res['name']);
        
        table_section(2,"50%");
        label_row(_("Student Type :"), $res['cat_name']);
        label_row(_("Class :"), $res['class_name']);
        label_row(_("Section :"), $res['session_name']);
        hidden('stid', $_GET['studentid']);
        hidden('syear', $syear);
        hidden('class', $res['st_class']);
        hidden('section', $res['st_section']);
     
	end_outer_table(1); 
}




//---------------------------------------------------------------------------------

function st_display_transfer_items_raw($title, &$order, $payment_type,$stid, $syear)
{
    
	global $path_to_root;
        
	display_heading($title);
        div_start('items_table');
        //display_error(print_r($order, true));
	start_table(TABLESTYLE, "width=80%");
	$th = array(_("Sl#"), _("Payment Head"), _("Due Date"), _("Discount"), _("Amount"));
	if ( count($order->line_items)) $th[] = '';
	table_header($th);
	$subtotal = 0;
	$k = 0;  //row colour counter
        $sl = 1;
	$id = find_submit('Edit');
	foreach ($order->line_items as $line_no=>$stock_item){
                
                $disstatus = $stock_item->disstatus;
                $ratio = $stock_item->ratio;
                
		if ($id != $line_no){
    		alt_table_row_color($k);
                
                label_cell($sl,'align=center');
    		label_cell($stock_item->head_name);
                label_cell(sql2date($stock_item->due_date),'align=center');
                
                if($disstatus == '1'){
                 $dishw = ($stock_item->amount * $ratio)/100;
                 $discount_total += $dishw;
                 amount_cell($dishw);
                }
                else{
                  amount_cell(0);  
                }
                
                label_cell(price_format($stock_item->amount),'align=center');
                $total += $stock_item->amount;
                $display_sub_total = price_format($total);
              
                hidden('head_id', $stock_item->head_id);
			delete_button_cell("Delete$line_no", _("Delete"),
				_('Remove line from document'));
    		end_row();
                
		}
		else
		{
                    transfer_edit_item_controls($order, $line_no, $payment_type,$stid,$syear,$total,$display_sub_total,$disstatus,$ratio,$discount_total);
		}
           $sl++;     
	}
            
	if ($id == -1)
		transfer_edit_item_controls($order,$payment_type,$stid,$syear,$total,$display_sub_total,$disstatus,$ratio,$discount_total);

    end_table();
	div_end();
        
}

//---------------------------------------------------------------------------------

function transfer_edit_item_controls(&$order, $payment_type,$stid,$syear,$total,$display_sub_total, $disstatus, $ratio, $discount_total, $line_no=-1)
{      
	global $Ajax;
	start_row();
        
	$id = find_submit('Edit');
        
	if ($line_no != -1 && $line_no == $id){

	}
	else
	{
        label_cell('');
       
        
        $con = array('student_id'=>$stid);
        $field= array('applicant_id');
        $app = db_fetch(data_retrieve_condition('sms_students_details', $field, $con));
        
        
        payment_head_list($stid, $syear, $payment_type,$app['applicant_id']);
        
        if (list_updated('ptype')) {
                $Ajax->activate('ddate');
                $Ajax->activate('amnt');
                $Ajax->activate('dis');
        }
        
        //display_error($_POST['ptype']);
    	$data = get_payment_data($_POST['ptype']);
        
        label_cell(sql2date($data['due_date']),'align=center','ddate');
        
        if($disstatus == '1'){
           $dishw = ($data['amount'] * $ratio)/100;
           label_cell(price_format($dishw),'align=center','dis');  
        }else{
           label_cell(price_format(0),'align=center');  
        }
        
        //$discount_total += $dishw;
         
        if($data['fineamnt']!=0){
        label_cell(price_format($data['fineamnt']),'align=center','amnt');    
        }
        elseif($data['fineamnt']==0){
        label_cell(price_format($data['amount']),'align=center','amnt');    
        }
             
  }
	if ($id != -1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('LineNo', $line_no);
 		set_focus('qty');
	}
	else
	{
		submit_cells('AddItem', _("Add Item"), "colspan=2",
		    _('Add new item to document'), true);
	} 	

        label_row(_("Sub-total"), $display_sub_total, "colspan = 4 align=right","align=center", 2);
        label_row(_("Discount(%)"),price_format($discount_total), "colspan = 4 align=right","align=center", 2);
        label_row(_("Special Discount"), '<center><input type="text" name="spdiscount"/></center>', "colspan = 4 align=right","align=center", 2);
        label_cells(_("Amount Total"), ($total - $discount_total), "colspan= 4 align='right'","align='center'");
	end_row();
}




function copy_from_cart(){

}


function copy_to_student_cart()
{    
	$cart = &$_SESSION['student_st_req_raw'];
        $cart->payment_dt=$_POST['payment_date'];
        $cart->syear =$_POST['syear'];
        $cart->class =$_POST['class'];
        $cart->st_id = $_POST['stid'] ;
        $cart->spdiscount = $_POST['spdiscount'] ;
        $cart->payment_method = $_POST['payment_method'];
        $cart->bank_account = $_POST['bank_account'];
        $cart->bank_name = $_POST['bank_name'];
	$cart->branch_name = $_POST['branch_name'];
        $cart->cheque_no = $_POST['cheque_no'];
        $cart->cheque_date = $_POST['cheque_date'];
        $cart->comments=$_POST['Comments'];
   
}



function st_transfer_options_controls()
{
	  global $Ajax;
    br(2);
    display_heading(_('Payment Method'));
    div_start('mannan');
    start_table(TABLESTYLE2, "width=60%");
    payment_mothod(_("Payment Method :"), 'payment_method', '',_("Select"), true);
    
   if (list_updated('payment_method')){

         $Ajax->activate('mannan');
 
        if ($_POST['payment_method'] == 1){
            cash_accounts_list_row(_("Into:"), 'bank_account', null, true);
        }elseif($_POST['payment_method'] == 2){
            $query=array('id','bank_name','bank_name');
            bankkk_accounts_list_row(_("Bank Name:"), 'bank_name', null, true,true);
            text_row(_("Branch Name :"), 'branch_name', null);
            text_row(_("Cheque No :"), 'cheque_no', null);
            date_row(_("Cheque Date:"), 'cheque_date','', true, 0, 0, 0, null, true); 
         }
 }  
   
    textarea_row(_("Comments:"), "Comments", $order->Comments, 31, 5);
    end_table(1);
    div_end();
}

//---------------------------------------------------------------------------------------------------

?>