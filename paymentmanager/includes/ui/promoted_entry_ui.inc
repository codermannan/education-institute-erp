<?php
/**********************************************************************
    
***********************************************************************/
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/promoted_payment_cart.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");


//-------------------------------------------------------------------------------------

function add_to_prmotion(&$order, $item, $new_item_qty )
{

    if ($order->find_cart_item_raw($item))
         display_error(_("For Part :") . $item . " " . "This Item is already on this order. ");
	else
         $order->add_to_printing_cart_raw (count($order->line_items), $item, $new_item_qty );
}

//--------------------------------------------------------------------------------

function promoted_payment_header_raw(&$order)
{
     
         $sql="SELECT 
              ss.*,
              CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) AS name,
              sc.class_name,
              sec.session_name
              FROM " . TB_PREF . "sms_student ss
              LEFT JOIN " . TB_PREF . "sms_students_details sd ON ss.student_id = sd.student_id
              LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON ss.st_class = sc.id
              LEFT JOIN " . TB_PREF . "sms_session sec ON ss.st_section = sec.id
              WHERE ss.id=".  db_escape($_GET['rid']); 
         
        $query = db_query($sql);
        $res = db_fetch(db_query($sql, 'Could not get data.'));
       
	global $Refs,$Ajax;
        $syear = get_current_schoolyear();
	start_outer_table(TABLESTYLE,  "width=70%");
        
	table_section(1,"50%");
        
        date_row(_("Promotion Date :"), 'admission_date');
        label_row(_("Student ID :"), $_GET['studentid']);
        label_row(_('Student Name :'), $res['name']);
        table_section(2,"50%");
        label_row(_("Present Class :"), $res['class_name']);
        label_row(_("Present Section :"), $res['session_name']);
        
        $query1=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
        ORDER BY class_name ASC'));
        combo_list_row(_("Allocate Class:"), 'pclass', null, 'Select Class', true, $query1);
        
        if(list_updated('pclass')){
               $Ajax->activate('psection');
            }
            
        $querys=array(array('id','session_name','select id, session_name from '.TB_PREF.'sms_session
        WHERE class='.db_escape($_POST['pclass']).' ORDER BY session_name ASC'));
        combo_list_row(_("Allocate Section : "), 'psection', null, 'Select Session', true, $querys);
        
        hidden('stid', $_GET['studentid']);
        hidden('syear', $syear);
        hidden('rollnumber', $_GET['rollid']);
        hidden('class', $res['st_class']);
        hidden('section', $res['st_section']);
        hidden('stprtbl', $_GET['rid']);
        
	end_outer_table(1); 
}




//---------------------------------------------------------------------------------

function promoted_payment_items_raw($title, &$order)
{
	
	global $path_to_root;
        
        
//       display_error(print_r($order,true));
	display_heading($title);
        div_start('items_table');

	start_table(TABLESTYLE, "width=80%");
	$th = array(_("Sl#"), _("Payment Head"), _("Due Date"), _("Amount"), '');
	if ( count($order->line_items)) $th[] = '';
	table_header($th);
	$subtotal = 0;
	$k = 0;  //row colour counter
        $dd=1;

	$id = find_submit('Edit');
	foreach ($order->line_items as $line_no=>$stock_item)
	{
//         $dd=1;
            

		if ($id != $line_no)
		{
                 
                
    		alt_table_row_color($k);
                label_cell($dd,'align=center');
    		label_cell($stock_item->head_name);
                label_cell(sql2date($stock_item->due_date),'align=center');
                amount_cell($stock_item->amount);
                
			edit_button_cell("Edit$line_no", _("Edit"),
				_('Edit document line'));
			delete_button_cell("Delete$line_no", _("Delete"),
				_('Remove line from document'));
    		end_row();
                $total += $stock_item->amount;
                $display_sub_total = price_format($total);

                
		}
		else
		{
                    transfer_edit_item_controls($order, $line_no);
		}
                
                  $dd++;
	}
        label_row(_("Sub-total"), $display_sub_total, "colspan = 3 align=right","align=right", 2);
                label_cells(_("Amount Total"), $total, "colspan= 3 align='right'","align='right'");
        
	if ($id == -1)
//		transfer_edit_item_controls($order);

    end_table();
	div_end();

}

//---------------------------------------------------------------------------------

function transfer_edit_item_controls(&$order, $line_no=-1)
{
	global $Ajax;
	start_row();

	$id = find_submit('Edit');
	if ($line_no != -1 && $line_no == $id)
	{
 //           display_error($line_no);
//                $all_style=get_one_style_printing($order->line_items[$id]->style_id);
                
		$_POST['amount'] = qty_format($order->line_items[$id]->amount);

//		hidden('stock_id', $_POST['stock_id']);
//                
		label_cell($line_no+1);
		label_cell($order->line_items[$id]->head_name);
                label_cell(sql2date($order->line_items[$id]->due_date) );
//                $_POST['units'] = $order->line_items[$id]->units;
//                
                $Ajax->activate('items_table');
	}
	else
	{
    //	stock_costable_items_list_cells(null, 'stock_id', null, false, true);
    //    mf_item_list_cells(null, 'stock_id', null, false , false) ;
//		if (list_updated('stock_id')) {
//			$Ajax->activate('qty');
//                        $Ajax->activate('units');
//		}
//
//    	$item_info = get_item_edit_info($_POST['stock_id']);
//
//		$dec = $item_info['decimals'];
//   		$_POST['qty'] = number_format2(0, $dec);
//                $_POST['units'] = $item_info["units"];
//               
	}
   
//        $stock_id=$order->line_items[$id]->stock_id;
//        
//        label_cell('sl');
//        label_cell('name');
//        label_cell('Date');
//	small_qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);
        amount_cells(null, 'amount');
  
 //       label_cell($order->line_items[$id]->units);

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

	end_row();
}




function copy_from_cart()
{
     
        $cart = &$_SESSION['promo_pay_rec'];
       // $cart->from_loc = $_POST['from_loc'] ;
	$_POST['location'] = $cart->location;
        $_POST['print_order'] = $cart->print_order;
	$_POST['AdjDate'] = $cart->order_date;
	$_POST['required_date'] = $cart->requ_date;
	$_POST['comments'] = $cart->comments;

}


function copy_to_promo_pay_cart()
{ 
	$cart = &$_SESSION['promo_pay_rec'];
        $cart->add_dt=$_POST['admission_date'];
        $cart->pclass=$_POST['pclass'];
        $cart->psection=$_POST['psection'];
        $cart->syear =$_POST['syear'];
        $cart->class =$_POST['class'];
        $cart->section =$_POST['section'];
        $cart->stid = $_POST['stid'] ;
        $cart->rollnumber = $_POST['rollnumber'] ;
        $cart->stprtbl = $_POST['stprtbl'] ;
        $cart->payment_method = $_POST['payment_method'];
        $cart->bank_name = $_POST['bank_name'];
	$cart->branch_name = $_POST['branch_name'];
        $cart->cheque_no = $_POST['cheque_no'];
        $cart->cheque_date = $_POST['cheque_date'];
        $cart->comments=$_POST['Comments'];
   
}



function promoted_payment_options_controls()
{
    global $Ajax;
    br(2);
    display_heading(_('Payment Method'));
    div_start('mannan');
    start_table(TABLESTYLE2, "width=60%");
    payment_mothod(_("Payment Method :"), 'payment_method', '',_("Select"), true);
    
   if (list_updated('payment_method')){

         $Ajax->activate('mannan');
 
        if ($_POST['payment_method'] == 2){
        
        $query=array('id','bank_name','bank_name');
        combo_list_row(_("Bank Name:"),'bank_name', null,'Select Bank', null,$query);
        text_row(_("Branch Name :"), 'branch_name', null);
        text_row(_("Cheque No :"), 'cheque_no', null);
        date_row(_("Cheque Date:"), 'cheque_date','', true, 0, 0, 0, null, true);
  
    }  
    else{
        //        display_error(33); 
    }

    
        } 
   
    textarea_row(_("Comments:"), "Comments", $order->Comments, 31, 5);
    end_table(1);
    div_end();
}

//---------------------------------------------------------------------------------------------------


  
  //-------------------------------------------
  
  



?>