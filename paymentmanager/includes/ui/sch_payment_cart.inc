<?php

/* * ********************************************************************

 * ********************************************************************* */
include_once($path_to_root . "/includes/prefs/sysprefs.inc");
//include_once($path_to_root . "/inventory/includes/inventory_db.inc");

class sch_payment_cart {

    var $line_items;
    var $order_no;
    var $units;
    var $quantity;
    var $line_no;
    
    var $due_date;
    var $head_name;
    var $amount;

    function sch_payment_cart($type = null) {

        $this->clear_sch_payment_items();
    }

    // --------------- line item functions

    function add_to_sch_payment_cart_raw($line_no,$due_date,$head_name,$amount,$id,$disstatus,$ratio) {

       // if (isset($id) && $id != "") { //&& isset($qty)
            $this->line_items[] = new line_item($line_no,$due_date,$head_name,$amount,$id,$disstatus,$ratio);

            return true;
//        } else {
//            // shouldn't come here under normal circumstances
//            display_error("unexpected - adding an invalid item ", "", true);
//        }

//        return false;
    }

    function find_cart_item_raw($item) {

        foreach ($this->line_items as $line_no => $line) {
            if ($line->stock_id == $item)               // because,bundle is known as Item for this transction
                return $this->line_items[$line_no];
        }
        return null;
    }

    function update_cart_item($line_no, $qty) {
//		$this->line_items[$line_no]->part_id = $part;
        $this->line_items[$line_no]->amount = $qty;
    }

    function remove_from_cart($line_no) {
        array_splice($this->line_items, $line_no, 1);
    }

    function count_items() {
        return count($this->line_items);
    }

    // ------------ common functions

    function clear_sch_payment_items() {
        unset($this->line_items);
          $this->due_date = '';
          $this->head_name = '';
          $this->amount = 0;
          $this->disstatus = '';
          $this->ratio = 0;
    }

}

//--------------------------------------------------------------------------------------------

class line_item {

    var $due_date;
    var $head_name;
    var $amount;
    var $disstatus;
    var $ratio;
    var $line_no;
    var $id;

    function line_item($line_no,  $due_date, $head_name, $amount,$id,$disstatus,$ratio) {

        $this->due_date = $due_date;
        $this->head_name = $head_name;
        $this->amount = $amount;
        $this->disstatus = $disstatus;
        $this->ratio = $ratio;
        $this->line_no = $line_no;
        $this->id = $id;
    }

}

?>
