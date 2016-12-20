<?php
/*********************************************************************************************/
include_once($path_to_root . "/includes/prefs/sysprefs.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");

class cut_items_cart
{
    var $trans_type;
    var $line_items;
  //  var $lines_on_order = 0;  
  //  var $order_no;
	
    function cut_items_cart($type)
    {
        $this->trans_type = $type;
        $this->clear_cuting_items();
    }

    function add_to_yarn_cart($line_no, $stock_id, $qty, $yarn_description, $description=null)
    { 
        if (isset($stock_id) && $stock_id != "" && isset($qty))
        {
            $this->line_items[] = new line_item($line_no,$stock_id, $qty, $yarn_description, $description);
            return true;
        }
        else
        {     
              // shouldn't come here under normal circumstances
                display_error("unexpected - adding an invalid item or null quantity", "", true);
        }
        return false;
    }

    function find_cart_itemp($stock_id)
    { 
        foreach($this->line_items as $line_no=>$line) 
        {
            if ($line->stock_id == $stock_id)
                return $this->line_items[$line_no];
        }
        return null;
    }

    function update_cart_item($line_no, $qty)
    {
        $this->line_items[$line_no]->quantity = $qty;
    }

    function remove_from_cart($line_no)
    {
        array_splice($this->line_items, $line_no, 1);
    }

    function count_items()
    {
        return count($this->line_items);
    }

    function clear_knit_yarn_items()
    {
        unset($this->line_items);
        $this->line_items = array();
       // $this->lines_on_order = 0;  
       // $this->order_no = 0;
    }
}
//--------------------------------------------------------------------------------------------
class line_item
{
    var $catname;
    var $stock_id;
    var $item_description;
    var $units;
    var $quantity;
    var $line_no;
    var $yarn_description;

    function line_item ($line_no,$stock_id, $yarn_description, $qty, $description=null)
    {
        $item_row = get_item_name($stock_id);

        if ($item_row == null)
            display_error("invalid item added to order : $stock_id", "");

        if ($description == null)
            $this->item_description = $item_row["description"];
        else
            $this->item_description = $description;
        
        $this->yarn_description = $yarn_description;
        $this->catname = $item_row["cat_name"];
        $this->line_no = $line_no;
        $this->stock_id = $stock_id;
        $this->quantity = $qty;
        $this->units = $item_row["units"];
    }
}
?>