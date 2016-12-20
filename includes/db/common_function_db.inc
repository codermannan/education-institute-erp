<?php

/* * ********************************************************************

 * ********************************************************************* */

//
//	General database functions common for all modules.
//
//-------------------------------------------------------------------
// Multilevel transaction control.
//

function common_str_requsition_order(&$sp_sr_obj) {
//    display_error(print_r($sp_sr_obj,true));

    
    begin_transaction();

    /* Insert to print_requisition_master  record */
    if (isset($sp_sr_obj->dyeing_order))
        $sp_sr_obj->order = $sp_sr_obj->dyeing_order;
    elseif (isset($sp_sr_obj->knit_order))
        $sp_sr_obj->order = $sp_sr_obj->knit_order;
    elseif (isset($sp_sr_obj->print_order))
        $sp_sr_obj->order = $sp_sr_obj->print_order;
    elseif (isset($sp_sr_obj->wash_order))
        $sp_sr_obj->order = $sp_sr_obj->wash_order;
    elseif (isset($sp_sr_obj->ebd_order))
        $sp_sr_obj->order = $sp_sr_obj->ebd_order;
    elseif (isset($sp_sr_obj->cut_order))
        $sp_sr_obj->order = $sp_sr_obj->cut_order;
    elseif (isset($sp_sr_obj->swi_order))
        $sp_sr_obj->order = $sp_sr_obj->swi_order;



    if (isset($sp_sr_obj->from_loc))
        $sp_sr_obj->frm_location = $sp_sr_obj->from_loc;
    if (isset($sp_sr_obj->to_loc))
        $sp_sr_obj->rcv_location = $sp_sr_obj->to_loc;


    $sql = "INSERT INTO " . TB_PREF . "str_requisition_master (trans_no, fr_location,to_location, order_no, po_no,order_item_no, mod_name, order_date, 
     	requir_date, comments) VALUES(";
    $sql .= db_escape($sp_sr_obj->tr_no) . "," .
            db_escape($sp_sr_obj->frm_location) . "," .
            db_escape($sp_sr_obj->rcv_location) . "," .
            db_escape($sp_sr_obj->order) . "," .
            db_escape($sp_sr_obj->po_no) . "," .
            db_escape($sp_sr_obj->order_item_no) . "," .
            db_escape($sp_sr_obj->mod_name) . "," .
            db_escape(date2sql($sp_sr_obj->order_date)) . ", " .
            db_escape(date2sql($sp_sr_obj->requ_date)) . ", " .
            db_escape($sp_sr_obj->comments) . ")";

    db_query($sql, "The str requisition record could not be inserted");


    /* Insert the print_requisition_details records */
    foreach ($sp_sr_obj->line_items as $line_no => $print_line) {
        $sql1 = "INSERT INTO " . TB_PREF . "str_requisition_details (trans_no, item_code, quantity) VALUES (";
        $sql1 .= db_escape($sp_sr_obj->tr_no) . ", " .
                db_escape($print_line->stock_id) . ", " .
                db_escape($print_line->quantity) . ") ";

        db_query($sql1, "One of the str requisition  order detail records could not be inserted");

    }

    commit_transaction();
    return $sp_sr_obj->tr_no;
}

function update_common_sr_order(&$sp_sr_obj) {
    // display_error(print_r($sp_sr_obj,true));
    begin_transaction();


    if (isset($sp_sr_obj->from_loc))
        $sp_sr_obj->frm_location = $sp_sr_obj->from_loc;
    if (isset($sp_sr_obj->to_loc))
        $sp_sr_obj->rcv_location = $sp_sr_obj->to_loc;

    $sql = "UPDATE " . TB_PREF . "str_requisition_master SET 
                fr_location=" . db_escape($sp_sr_obj->frm_location) . ",
                to_location=" . db_escape($sp_sr_obj->rcv_location) . ", 
		mod_name=" . db_escape($sp_sr_obj->mod_name) . ",
                order_date=" . db_escape(date2sql($sp_sr_obj->order_date)) . ",
                requir_date=" . db_escape(date2sql($sp_sr_obj->requ_date)) . ",
                comments=" . db_escape($sp_sr_obj->comments) . "
                WHERE trans_no = " . db_escape($sp_sr_obj->trans_id);



//     $sql = "INSERT INTO ".TB_PREF."str_requisition_master (trans_no, fr_location,to_location, mod_name, order_date, 
//     	requir_date, comments) VALUES(";
//     $sql .= db_escape($sp_sr_obj->tr_no) . "," .
//         db_escape($sp_sr_obj->frm_location) . "," .
//         db_escape($sp_sr_obj->rcv_location) . "," .
//         db_escape($sp_sr_obj->mod_name) . "," .
//         db_escape(date2sql( $sp_sr_obj->order_date)) . ", " .
//         db_escape(date2sql($sp_sr_obj->requ_date)) . ", " .
//         db_escape($sp_sr_obj->comments) . ")";

    db_query($sql, "The str requisition record could not be inserted");
    // display_error($sql);


    $sqlz = "DELETE FROM " . TB_PREF . "str_requisition_details WHERE trans_no=" . db_escape($sp_sr_obj->trans_id);
    db_query($sqlz, "cannot delete BOM Items");

   //  display_error($sqlz);

    /* Insert the print_requisition_details records */
    foreach ($sp_sr_obj->line_items as $line_no => $print_line) {
        $sql1 = "INSERT INTO " . TB_PREF . "str_requisition_details (trans_no, item_code, quantity) VALUES (";
        $sql1 .= db_escape($sp_sr_obj->trans_id) . ", " .
                db_escape($print_line->stock_id) . ", " .
                db_escape($print_line->quantity) . ") ";

        db_query($sql1, "One of the str requisition  order detail records could not be inserted");
       // display_error($sql1);
    }

    commit_transaction();
    return $sp_sr_obj->trans_id;
}

//
function approve_common_sr(&$sp_sr_obj) {
    // display_error(print_r($sp_sr_obj,true));
    begin_transaction();

    $sql = "UPDATE " . TB_PREF . "str_requisition_master SET 
                status=" . db_escape(1) . ",
                app_by=" . db_escape($sp_sr_obj->username) . ", 
		app_date=" . db_escape(date2sql(Today())) . ",                              
                app_note=" . db_escape($sp_sr_obj->comments) . "
                WHERE trans_no = " . db_escape($sp_sr_obj->trans_id);


    db_query($sql, "The str requisition record could not be inserted");


    /* Insert the approval_requisition_details records */
    foreach ($sp_sr_obj->line_items as $line_no => $print_line) {
        $sql1 = "INSERT INTO " . TB_PREF . "str_requisition_app_details (trans_no, item_code, quantity, or_qty, available_qty) VALUES (";
        $sql1 .= db_escape($sp_sr_obj->trans_id) . ", " .
                db_escape($print_line->stock_id) . ", " .
                db_escape($print_line->quantity) . ", " .
                db_escape($print_line->orginal_quantity) . ", " .
                db_escape($print_line->avail_qty) . ") ";

        db_query($sql1, "One of the str requisition  order detail records could not be inserted");
    }

    commit_transaction();
    return $sp_sr_obj->trans_id;
}

///
function get_common_str_requis_info($frm_loc=FALSE, $specfic_location=FALSE, $view=FALSE) {

    $sql = "SELECT sprm.trans_no,loc.location_name,sprm.order_no,mp.po_head, sprm.order_date,sprm.requir_date, sprm.status 
        FROM " . TB_PREF . "str_requisition_master sprm 
        LEFT JOIN  " . TB_PREF . "locations as loc ON loc.loc_code= sprm.to_location 
        JOIN " . TB_PREF . "mf_po as mp ON sprm.po_no=mp.id ";

    if ($view)
        $sql .= " where sprm.po_no > 0";
    else
        $sql .= " where sprm.status <> 1";

    if ($frm_loc)
        $sql .=" and sprm.fr_location=" . db_escape($frm_loc);

    if ($specfic_location) {
        $values = "(" . implode(',', $specfic_location) . ")";
        //display_error($values);
        $sql .= " and sprm.fr_location NOT IN " . $values;

//$sql .=" and sprm.fr_location in (". implode( ',',db_escape($specfic_location)).")";
    }
    $sql .=" order by sprm.id desc ";

    return $sql;
}



function get_common_str_requis_information($frm_loc=FALSE, $specfic_location=FALSE, $view=FALSE) {

    $sql = "SELECT sprm.trans_no,loc.location_name, CONCAT(st.style_head, ' - ', mp.po_head, ' - ', cat.category_head) as bomname, sprm.order_no,mp.po_head, sprm.order_date,sprm.requir_date, sprm.status 
        FROM " . TB_PREF . "str_requisition_master sprm 
        LEFT JOIN  " . TB_PREF . "locations as loc ON loc.loc_code= sprm.to_location 
        JOIN " . TB_PREF . "mf_bom as bom ON sprm.po_no=bom.po_id
        JOIN " . TB_PREF . "mf_po as mp ON sprm.po_no=mp.id
        LEFT JOIN " . TB_PREF . "mf_style st ON st.id = bom.style_id
        LEFT JOIN " . TB_PREF . "mf_pro_category cat ON cat.id = bom.cat_id";

    if ($view)
        $sql .= " where sprm.po_no > 0";
    else
        $sql .= " where sprm.status <> 1";
    
    if ($frm_loc)
        $sql .=" and sprm.fr_location=" . db_escape($frm_loc);

    if ($specfic_location) {
        $values = "(" . implode(',', $specfic_location) . ")";
        //display_error($values);
        $sql .= " and sprm.fr_location NOT IN " . $values;

//$sql .=" and sprm.fr_location in (". implode( ',',db_escape($specfic_location)).")";
    
    }
    if(isset($_POST['bom_id']) && $_POST['bom_id']!='')
    $sql .=" AND bom.id= ".db_escape($_POST['bom_id']);
    
    if (isset($_POST['order_id']) && $_POST['order_id'] != '')
        $sql .=" AND  sprm.order_no = " . db_escape($_POST['order_id']);
 
  if (isset($_POST['RefreshInquiry'])) {  
   if(isset($_POST['CuttingOrAfterDate']) && $_POST['CuttingOrAfterDate']!='')
        $sql .=" AND  sprm.order_date >= ".db_escape(date2sql($_POST['CuttingOrAfterDate']));
   
   if(isset($_POST['CuttingOrToDate']) && $_POST['CuttingOrToDate']!='')
        $sql .=" AND  sprm.order_date <= ".db_escape(date2sql($_POST['CuttingOrToDate']));
 
   if(isset($_POST['CuttingReqAfterDate']) && $_POST['CuttingReqAfterDate']!='')
        $sql .=" AND  sprm.requir_date >= ".db_escape(date2sql($_POST['CuttingReqAfterDate']));
   
   if(isset($_POST['CuttingReqToDate']) && $_POST['CuttingReqToDate']!='')
        $sql .=" AND  sprm.requir_date <= ".db_escape(date2sql($_POST['CuttingReqToDate']));  
   
  }
   
    $sql .=" order by sprm.id desc ";
    
    return $sql;
}

function approve_common_requ_order($tr_no, $user, $app_dt, $status, $approve_note) {
    $app_date = date2sql($app_dt);

    $sql = "UPDATE " . TB_PREF . "str_requisition_master SET 
                app_by=" . db_escape($user) . ",
                app_date=" . db_escape($app_date) . ", 
		status=" . db_escape($status) . ",
                app_note=" . db_escape($approve_note) . "
                WHERE trans_no = " . db_escape($tr_no);

    begin_transaction();
    db_query($sql, "The Order could not be updated");
    commit_transaction();

    return true;
}

function get_common_approve_order($mod_name) {

    $sql = "SELECT prm.trans_no,loc.location_name,loct.location_name as to_loc,
            prm.order_no,mp.po_head,prm.order_date,prm.requir_date, 
            prm.status from " . TB_PREF . "str_requisition_master prm
            LEFT  JOIN  " . TB_PREF . "locations as loc on loc.loc_code= prm.fr_location 
            LEFT  JOIN  " . TB_PREF . "locations as loct on loct.loc_code=prm.to_location
            LEFT JOIN " . TB_PREF . "mf_po as mp ON mp.id=prm.po_no
            where prm.status = 1 ";
    if ($mod_name)
        $sql .=" and prm.mod_name=" . db_escape($mod_name);

    $sql .=" order by prm.id desc ";

    return $sql;
}

function get_common_approve_order_infor($mod_name) {

    $sql = "SELECT prm.trans_no,loc.location_name,loct.location_name as to_loc,CONCAT(st.style_head, ' - ', mp.po_head, ' - ', cat.category_head) as bomname,
            prm.order_no,mp.po_head,prm.order_date,prm.requir_date, 
            prm.status from " . TB_PREF . "str_requisition_master prm
            LEFT JOIN  " . TB_PREF . "locations as loc on loc.loc_code= prm.fr_location 
            LEFT JOIN  " . TB_PREF . "locations as loct on loct.loc_code=prm.to_location
                 JOIN " . TB_PREF . "mf_bom as bom ON prm.po_no=bom.po_id
            LEFT JOIN " . TB_PREF . "mf_po as mp ON mp.id=prm.po_no
            LEFT JOIN " . TB_PREF . "mf_style st ON st.id = bom.style_id
            LEFT JOIN " . TB_PREF . "mf_pro_category cat ON cat.id = bom.cat_id
            where prm.status = 1 ";
    if ($mod_name)
        $sql .=" and prm.mod_name=" . db_escape($mod_name);

    if(isset($_POST['bom_id']) && $_POST['bom_id']!='')
    $sql .=" AND bom.id= ".db_escape($_POST['bom_id']);
    
    if (isset($_POST['order_id']) && $_POST['order_id'] != '')
        $sql .=" AND  prm.order_no = " . db_escape($_POST['order_id']);
 
  if (isset($_POST['RefreshInquiry'])) { 
   if(isset($_POST['CuttingOrAfterDate']) && $_POST['CuttingOrAfterDate']!='')
        $sql .=" AND  prm.order_date >= ".db_escape(date2sql($_POST['CuttingOrAfterDate']));
   
   if(isset($_POST['CuttingOrToDate']) && $_POST['CuttingOrToDate']!='')
        $sql .=" AND  prm.order_date <= ".db_escape(date2sql($_POST['CuttingOrToDate']));
 
   if(isset($_POST['CuttingReqAfterDate']) && $_POST['CuttingReqAfterDate']!='')
        $sql .=" AND  prm.requir_date >= ".db_escape(date2sql($_POST['CuttingReqAfterDate']));
   
   if(isset($_POST['CuttingReqToDate']) && $_POST['CuttingReqToDate']!='')
        $sql .=" AND  prm.requir_date <= ".db_escape(date2sql($_POST['CuttingReqToDate']));  
  }  
    $sql .=" order by prm.id desc ";

    return $sql;
}

function receive_order_quantity($trans_no) {

    $sql = "SELECT sum(quantity) as re_qty,sum(rec_qty) as rec_qty  FROM " . TB_PREF . "str_requisition_details             
           WHERE trans_no = " . db_escape($trans_no);
    $query = db_query($sql, "data could not be retreived");
    $result = db_fetch($query);

    return $result;
}

function receive_order_quantity_app($trans_no) {

    $sql = "SELECT sum(quantity) as re_qty,sum(rec_qty) as rec_qty  FROM " . TB_PREF . "str_requisition_app_details             
           WHERE trans_no = " . db_escape($trans_no);
    $query = db_query($sql, "data could not be retreived");
    $result = db_fetch($query);

    return $result;
}

function receive_order_app_quantity($trans_no) {

    $sql = "SELECT sum(quantity) as re_qty,sum(rec_qty) as rec_qty  FROM " . TB_PREF . "str_requisition_app_details             
           WHERE trans_no = " . db_escape($trans_no);
    $query = db_query($sql, "data could not be retreived");
    $result = db_fetch($query);

    return $result;
}

function get_common_order_details_item($trans_id = null) {

    $sql = "SELECT * from " . TB_PREF . "str_requisition_details WHERE trans_no=" . db_escape($trans_id);

    return db_query($sql, "Can't get Schedule Info");
}

function get_app_order_details_item($trans_id = null) {

    $sql = "SELECT * from " . TB_PREF . "str_requisition_app_details WHERE trans_no=" . db_escape($trans_id);

    return db_query($sql, "Can't get Schedule Info");
}

function get_common_order_details_group_item($trans_id = null, $loc) {
    $sql = "SELECT srd.*, SUM(sm.qty) AS sqty from " . TB_PREF . "str_requisition_details srd
            left join " . TB_PREF . "stock_moves  as sm on sm.stock_id=srd.item_code
            WHERE srd.trans_no=" . db_escape($trans_id) . "AND sm.loc_code=" . db_escape($loc) . " group by sm.stock_id";

   return db_query($sql, "Can't get Schedule Info");
    
    //display_error($sql);
}

function get_app_common_order_details_group_item($trans_id = null, $loc) {
    $sql = "SELECT srd.*,SUM(sm.qty) AS qty from " . TB_PREF . "str_requisition_app_details srd
            left join " . TB_PREF . "stock_moves  as sm on sm.stock_id=srd.item_code
            WHERE srd.trans_no=" . db_escape($trans_id) . "AND sm.loc_code=" . db_escape($loc) . " group by sm.stock_id";

    return db_query($sql, "Can't get Schedule Info");
}

function get_common_data_order_details_by_id($id) {
    $sql = "SELECT *  FROM " . TB_PREF . "str_requisition_details WHERE id=" . db_escape($id);

    $result = db_query($sql, "could not get details");
    return $result;
}

function get_common_data_app_order_details_by_id($id) {
    $sql = "SELECT *  FROM " . TB_PREF . "str_requisition_app_details WHERE id=" . db_escape($id);

    $result = db_query($sql, "could not get details");
    return $result;
}

function get_common_order_ms_item($tr_no) {


    $sql = "SELECT * FROM " . TB_PREF . "str_requisition_master WHERE trans_no = " . db_escape($tr_no);

    return db_query($sql, "The order cannot be retrieved");
}

function update_store_requisition($selected_id, $re_qty) {

    $sql = "UPDATE " . TB_PREF . "str_requisition_details SET rec_qty=rec_qty +" . db_escape($re_qty) . " 
                WHERE id = " . db_escape($selected_id);

    db_query($sql, "The  Order could not be updated");


    $sql1 = "SELECT prm.*, prd.* FROM " . TB_PREF . "str_requisition_master prm  
            left join " . TB_PREF . "str_requisition_details prd  on prd.trans_no=prm.trans_no
            where prd.id=" . db_escape($selected_id);

    $all_items = db_query($sql1, "Can't get  data");

    return db_fetch($all_items);
}

function update_approval_store_requisition($selected_id, $re_qty) {

    $sql = "UPDATE " . TB_PREF . "str_requisition_app_details SET rec_qty=rec_qty +" . db_escape($re_qty) . " 
                WHERE id = " . db_escape($selected_id);

    db_query($sql, "The  Order could not be updated");


    $sql1 = "SELECT prm.*, prd.* FROM " . TB_PREF . "str_requisition_master prm  
            left join " . TB_PREF . "str_requisition_app_details prd  on prd.trans_no=prm.trans_no
            where prd.id=" . db_escape($selected_id);

    $all_items = db_query($sql1, "Can't get  data");

    return db_fetch($all_items);
}

function add_stock_transfer_common($Items, $location_from, $location_to, $date_, $type, $reference, $memo_) {

    global $Refs;

    begin_transaction();
    $args = func_get_args();
    $args = (object) array_combine(array('Items', 'location_from', 'location_to',
                'date_', 'type', 'reference', 'memo_'), $args);
    $args->trans_no = 0;
    hook_db_prewrite($args, ST_LOCTRANSFER);

    $transfer_id = get_next_trans_no(ST_LOCTRANSFER);



    add_stock_transfer_item($transfer_id, $Items['stock_id'], $location_from, $location_to, $date_, $type, $reference, $Items['quantity'], null, $memo_);



    add_comments(ST_LOCTRANSFER, $transfer_id, $date_, $memo_);

    $Refs->save(ST_LOCTRANSFER, $transfer_id, $reference);
    add_audit_trail(ST_LOCTRANSFER, $transfer_id, $date_);

    $args->trans_no = $transfer_id;
    hook_db_postwrite($args, ST_LOCTRANSFER);

    commit_transaction();

//	return $transfer_id;
}

/* ------------------------------------------stock adjustment-------------------------------------------- */

function add_stock_adjustment_common($items, $act_qty, $location, $date_, $type, $increase, $reference, $memo_, $order_id) {
    global $Refs;

    begin_transaction();
    $args = func_get_args();
    $args = (object) array_combine(array('items', 'location', 'date_', 'type', 'increase',
                'reference', 'memo_'), $args);
    $args->trans_no = 0;
    hook_db_prewrite($args, ST_INVADJUST);

    $adj_id = get_next_trans_no(ST_INVADJUST);
    if (!$increase)
        $act_qty = -$act_qty;

    add_stock_adjustment_item($adj_id, $items, $location, $date_, $type, $reference, $act_qty, null, $memo_, $order_id);

    add_comments(ST_INVADJUST, $adj_id, $date_, $memo_);

    $Refs->save(ST_INVADJUST, $adj_id, $reference);
    add_audit_trail(ST_INVADJUST, $adj_id, $date_);

    $args->trans_no = $adj_id;
    hook_db_postwrite($args, ST_INVADJUST);
    commit_transaction();
}

function insert_data($table, $data) {
    $fields = implode(",", array_keys($data));
    $values = implode(",", array_values($data));

    $sql = "INSERT INTO " . TB_PREF . $table . " (" . $fields . ")
		VALUES (" . $values . ")";

    db_query($sql, "could not add");

    return true;
}

function update_data($table, $data, $condition) {


    $cols = array();

    foreach ($data as $key => $val) {
        $cols[] = "$key = $val";
    }

    $where = array();
    $num = count($condition);
    foreach ($condition as $conkey => $conval) {
        $where[] = "$conkey = $conval ";
        if ($num > 1) {
            $where[] .="AND ";
            $num = $num - 1;
        }
    }


    $sql = "UPDATE " . TB_PREF . $table . " SET " . implode(', ', $cols) . " WHERE " . implode('', $where);


    db_query($sql, "could not update");
    if (db_num_affected_rows() > 0)
        return true;
    else
        return false;
}

function data_delete($table, $condition) {
    
    begin_transaction();

    $where = array();
    $num = count($condition);
    foreach ($condition as $conkey => $conval) {
        $where[] = "$conkey = '$conval' ";
        if ($num > 1) {
            $where[] .="AND ";
            $num = $num - 1;
        }
    }
    $sql = "DELETE FROM " . TB_PREF . $table . "  WHERE " . implode('', $where);
    db_query($sql, "cannot delete");
    commit_transaction();
}

function data_retrieve_condition($table, $field=false, $condition=false, $order=false) {
    //$condition=associative array $key=col name, $value= value
    //$field= array(field1,field2);
    $field_item = $field ? implode(',', $field) : ' * ';

    $where = array();
    $num = count($condition);
    foreach ($condition as $conkey => $conval) {
        $where[] = "$conkey = '$conval' ";
        if ($num > 1) {
            $where[] .="AND ";
            $num = $num - 1;
        }
    }

    $sql = "SELECT " . $field_item . " FROM " . TB_PREF . $table;

    if ($condition)
        $sql .= " WHERE " . implode('', $where);

    if ($order)
        $sql .= " ORDER BY " . $order;
//display_error($sql);
    return db_query($sql, "could not get Data");
}

function data_retrieve($table, $field, $item_id) {

    $sql = "SELECT * FROM " . TB_PREF . $table . " WHERE " . $field . "=" . db_escape($item_id) . " LIMIT 1";

    $result = db_query($sql, "could not get Data");

    return db_fetch($result);
}

function get_sql_for_table($table, $fields, $condition=false, $orderby=false) {
    //$join=array('jointable'=>'maintable.id=jointable.id');
    /* --------------------------info----------------------- */
    //$fields=array('table_field1','table_field2');
    //$condition=array('table_field1'=>$value1,'table_field2'=>$value2);
    //$orderby=array('table_field','asc');
    $field_item = implode(',', $fields);
    $sql = "SELECT " . $field_item . " FROM " . TB_PREF . $table;

//      if($join){
//          foreach($join as $join_table => $joining){
//          "LEFT JOIN ".TB_PREF.$join_table." on ".$joining." " ;
//      }
//      
//      }

    if ($condition) {
        $where = array();

        $num = count($condition);
        foreach ($condition as $conkey => $conval) {
            $where[] = "$conkey = $conval ";
            if ($num > 1) {
                $where[] .="AND ";
                $num = $num - 1;
            }
        }

        $sql .= " WHERE " . implode('', $where);
    }

    if ($orderby)
        $sql .= " order by " . $orderby[0] . " " . $orderby[1];
    //display_error($sql);
    return $sql;
}

function get_common_location_name($loc_code) {

    $sql = "SELECT *  FROM " . TB_PREF . "locations WHERE loc_code=" . db_escape($loc_code);

    $result = db_query($sql, "could not get location");
    return db_fetch($result);
}
