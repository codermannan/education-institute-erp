<?php
/**********************************************************************
    
***********************************************************************/
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/payment_entry_ui.inc");
include_once($path_to_root . "/paymentmanager/includes/ui/applicant_payment_ui_lists.inc");

//-------------------------------------------------------------------------------------

function add_to_printing_order_raw(&$order, $item, $new_item_qty )
{

    if ($order->find_cart_item_raw($item))
         display_error(_("For Part :") . $item . " " . "This Item is already on this order. ");
	else
         $order->add_to_printing_cart_raw (count($order->line_items), $item, $new_item_qty );
}

//--------------------------------------------------------------------------------

function sch_payment_header_raw(&$order){
         $sql="SELECT  CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) AS name,sd.class,
              sc.class_name,ssc.cat_name
              FROM " . TB_PREF . "sms_students_details sd
              LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON sd.class = sc.id
              LEFT JOIN " . TB_PREF . "sms_student_scholarship ss ON sd.applicant_id = ss.applicant_id
              LEFT JOIN " . TB_PREF . "sms_student_category ssc ON ss.student_cat = ssc.id
              WHERE sd.applicant_id=".  db_escape($_GET['app_id']); 
         
        $query = db_query($sql);
        $res = db_fetch(db_query($sql, 'Could not get data.'));
       
	global $Refs,$Ajax;
        $syear = get_current_schoolyear();
	start_outer_table(TABLESTYLE,  "width=70%");
        
	table_section(1,"50%");
        
        date_row(_("Admission Date :"), 'admission_date');
        label_row(_("Applicant ID :"), $_GET['app_id']);
        label_row(_('Applicant Name :'), $res['name']);
        label_row(_('Applicant Type :'), $res['cat_name']);
        
        table_section(2,"50%");
        label_row(_("Class :"), $res['class_name']);
        
        $result = db_query("SELECT cls.total_student,ss.session_name,ss.id FROM " . TB_PREF . "sms_create_stud_class cls
                LEFT JOIN " . TB_PREF . "sms_session ss ON ss.class = cls.id WHERE cls.id=".db_escape($res['class']));
        $numrw = db_num_rows($result);
       
        while($sedt = db_fetch($result)){
            $nosec = ($sedt['total_student'] / $numrw);
            $session_name[] = $sedt['session_name'];
            $session_id[] = $sedt['id'];
            
            $condition = array('school_year'=>$syear,'st_class'=>$res['class'],'st_section'=>$sedt['id']);
            $field = array('st_section');
            $secnum = db_num_rows(data_retrieve_condition("sms_student", $field, $condition));
            $section[] = $secnum;
         }
         
         if($nosec > $section[0]){
            label_row(_('Section :'), $session_name[0]);
            label_row(_('Present Student :'), $section[0]);
            hidden('psection', $session_id[0]);
         }
         elseif($nosec > $section[1]){
            label_row(_('Section :'), $session_name[1]);
            label_row(_('Present Student :'), $section[1]);
            hidden('psection', $session_id[1]);
         }
         elseif($nosec > $section[2]){
            label_row(_('Section :'), $session_name[2]);
            label_row(_('Present Student :'), $section[2]);
            hidden('psection', $session_id[2]);
         }

        hidden('appid', $_GET['app_id']);
        hidden('syear', $syear);
        hidden('class', $res['class']);
 
	end_outer_table(1); 
}




//---------------------------------------------------------------------------------

function sch_payment_items_raw($title, &$order){
	
	global $path_to_root;
        
	display_heading($title);
        div_start('items_table');

	start_table(TABLESTYLE, "width=80%");
	$th = array(_("Sl#"), _("Payment Head"), _("Due Date"), _("Discount"), _("Amount"));
	if ( count($order->line_items)) $th[] = '';
	table_header($th);
	$subtotal = 0;
	$k = 0;  //row colour counter
        $dd=1;

	$id = find_submit('Edit');
	foreach ($order->line_items as $line_no=>$stock_item){
		if ($id != $line_no){
                
    		alt_table_row_color($k);
                label_cell($dd,'align=center');
    		label_cell($stock_item->head_name);
                label_cell(sql2date($stock_item->due_date),'align=center');
                if($stock_item->disstatus=='1'){
                 $dishw = ($stock_item->amount * $stock_item->ratio)/100;
                 $discount_total += $dishw; 
                 
                amount_cell($dishw);
                }
                else{
                  amount_cell(0);  
                }
                amount_cell($stock_item->amount);
                
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
                
                $netamnt = price_format($total - $discount_total);
                
                label_row(_("Sub-total"), '<center><b>'.$display_sub_total.'</b></center>', "colspan = 4 align=right","align=right", 2);
                label_row(_("Discount(".$stock_item->ratio."%)"), '<center><b>'.price_format($discount_total).'</b></center>', "colspan = 4 align=right","align=right", 2);
                label_row(_("Special Discount"), '<center><input type="text" name="spdiscount"/></center>', "colspan = 4 align=right","align=right", 2);
                label_cells(_("Net Amount"), '<center><b>'.$netamnt.'</b></center>', "colspan= 4 align='right'","align='right'");
        
	if ($id == -1)
//		transfer_edit_item_controls($order);

    end_table();
	div_end();

}

//---------------------------------------------------------------------------------

function transfer_edit_item_controls(&$order, $line_no=-1)
{
	
}




function copy_from_cart()
{
     
        $cart = &$_SESSION['sch_pay_rec'];
       // $cart->from_loc = $_POST['from_loc'] ;
	$_POST['location'] = $cart->location;
        $_POST['print_order'] = $cart->print_order;
	$_POST['AdjDate'] = $cart->order_date;
	$_POST['required_date'] = $cart->requ_date;
	$_POST['comments'] = $cart->comments;

}


function copy_to_sch_pay_cart()
{ 
	$cart = &$_SESSION['sch_pay_rec'];
        $cart->add_dt=$_POST['admission_date'];
        $cart->syear =$_POST['syear'];
        $cart->class =$_POST['class'];
        $cart->psection =$_POST['psection'];
        $cart->app_id = $_POST['appid'] ;
        $cart->spdiscount = $_POST['spdiscount'] ;
        $cart->payment_method = $_POST['payment_method'];
        $cart->bank_account = $_POST['bank_account'];
        $cart->bank_name = $_POST['bank_name'];
	$cart->branch_name = $_POST['branch_name'];
        $cart->cheque_no = $_POST['cheque_no'];
        $cart->cheque_date = $_POST['cheque_date'];
        $cart->comments=$_POST['Comments'];
   
}



function sch_payment_options_controls()
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