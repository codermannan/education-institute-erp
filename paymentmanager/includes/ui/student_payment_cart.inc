<?php

/* * ********************************************************************

 * ********************************************************************* */
include_once($path_to_root . "/includes/prefs/sysprefs.inc");
include_once($path_to_root . "/paymentmanager/includes/db/payment_db.inc");


class print_sr_cart_raw {

    var $line_items;

    function print_sr_cart_raw($type = null) {

        $this->clear_printing_raw_items();
    }

    // --------------- line item functions

    function add_to_student_cart_raw($line_no, $ptype, $stid, $syear){
        
        if (isset($ptype) && $ptype != "") { //&& isset($qty)
            $this->line_items[] = new line_item($line_no,$ptype, $stid, $syear);

            return true;
        } else {
            // shouldn't come here under normal circumstances
            display_error("unexpected - adding an invalid item ", "", true);
        }

        return false;
    }

    function find_payment_cart_item_raw($ptype) {
        
        foreach ($this->line_items as $line_no => $line) {
            
            if ($line->tbl_id == $ptype)               // because,bundle is known as Item for this transction
                return $this->line_items[$line_no];
        }
        return null;
    }

    function update_cart_item($line_no, $part, $qty) {
//		$this->line_items[$line_no]->part_id = $part;
        $this->line_items[$line_no]->quantity = $qty;
    }

    function remove_from_cart($line_no) {
        array_splice($this->line_items, $line_no, 1);
    }

    function count_items() {
        return count($this->line_items);
    }

    // ------------ common functions

    function clear_printing_raw_items() {
        unset($this->line_items);
        $this->line_items = array();
        $this->lines_on_order = 0;
        $this->tbl_id = 0;
        $this->head_id = '';
        $this->due_date = '';
    }

}

//--------------------------------------------------------------------------------------------

class line_item {
    
    var $tbl_id;
    var $line_no;
    var $due_date;
    var $disstatus;
    var $ratio;
    var $head_id;
    var $realize;

    function line_item($line_no, $ptype, $stid, $syear) {
       
        $item_row = get_payment($ptype);
        
        if ($item_row == null)
            display_error("There are no items to receive payment");
        
        
        if($item_row["month"] == 2){
            $this->head_name = $item_row["head_name"].' for February '.date(Y);
        }
        elseif($item_row["month"] == 3){
            $this->head_name = $item_row["head_name"].' for March '.date(Y);
        }
        elseif($item_row["month"] == 4){
            $this->head_name = $item_row["head_name"].' for April '.date(Y);
        }
        elseif($item_row["month"] == 5){
            $this->head_name = $item_row["head_name"].' for May '.date(Y);
        }
        elseif($item_row["month"] == 6){
            $this->head_name = $item_row["head_name"].' for June '.date(Y);
        }
        elseif($item_row["month"] == 7){
            $this->head_name = $item_row["head_name"].' for July '.date(Y);
        }
        elseif($item_row["month"] == 8){
            $this->head_name = $item_row["head_name"].' for August '.date(Y);
        }
        elseif($item_row["month"] == 9){
            $this->head_name = $item_row["head_name"].' for September '.date(Y);
        }
        elseif($item_row["month"] == 10){
            $this->head_name = $item_row["head_name"].' for October '.date(Y);
        }
        elseif($item_row["month"] == 11){
            $this->head_name = $item_row["head_name"].' for November '.date(Y);
        }
        elseif($item_row["month"] == 12){
            $this->head_name = $item_row["head_name"].' for December '.date(Y);
        }
        else{
            $this->head_name = $item_row["head_name"]; 
        }
//        display_error($item_row["head_id"]);
        $this->due_date = $item_row["due_date"];
        
        if($item_row["fineamnt"]!=0){
        $this->amount = $item_row["fineamnt"];    
        }
        elseif($item_row["fineamnt"]==0){
        $this->amount = $item_row["amount"];    
        }
        $this->disstatus = $item_row["discount_status"];
        $this->ratio = $item_row["ratio"];
        
        $this->tbl_id = $item_row["id"];
        $this->head_id = $item_row["head_id"];
    }

}

?>
