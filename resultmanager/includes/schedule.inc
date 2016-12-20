<?php
/**********************************************************************

***********************************************************************/

include_once($path_to_root . "/dyeing/includes/printing_work_sch_curd_class.inc");
include_once($path_to_root . "/dyeing/includes/db/dyeing_db.inc");


class schedule extends printing_work_sch_curd {
	var $id;
	var $entity;
	var $sub_class;
	var $class;

	function schedule($name, $id, $class, $sub_class=null) {
           
		$fields = array('id');

		$this->work_sch_curd($name, $fields);
		$this->class = $class;
		$this->sub_class = $sub_class;
		$this->entity = $id;
	}

	function list_view() {
		br();  
		$get_line = get_dy_sch_line_info($this->entity);
                $last = $myrow = db_fetch($get_line);
                $data_fetch=get_dyeing_order_by_order_no($this->entity);

                display_heading(_("Toatal Order Quantity : " .$data_fetch['or_quantity'] )  );
                br();
             
                start_table(TABLESTYLE, "width=80%");

			$th = array(_('SL No'),_('Item'), _("Date"), _("Production Quantity"), _("Actual Quantity"), _("Spot Quantity"), _("Reject Quantity"),_("Comments"),"" );
        
			table_header($th);
			$k = 0; 
                        $row = 0;  
                        $tot_pro=0;
                        $tot_act=0;
                        $tot_spt=0;
                        $tot_rej=0;
			
			while ($last)
			{
				if ($myrow['id'] != $last['id']) {
					alt_table_row_color($k);
                                        label_cell($row, 'align=center');
        				label_cell($last["description"]);
					label_cell(sql2date($last["sc_date"]));
//                                        label_cell($last["line"]);
					label_cell($last["pro_qty"]);
					label_cell($last["act_qty"]);
					label_cell($last["spot_qty"]);
                                        label_cell($last["rej_qty"]);
                                        label_cell($last["comments"]);
                                        
                                  //      label_cell($data_fetch['or_quantity']);
                                        
                                    if($tot_act >= $data_fetch['or_quantity'])
                                       {                                       
                                        label_cell('N/A');    
                                       }
                                       else
                                       {
                                           $ll=$last['id']+1 ;
                                           
        $sql11 = "SELECT act_qty FROM ".TB_PREF."dyeing_schedule WHERE batch_no=".db_escape($last["batch_no"]). " AND id=".$ll." AND category='P'";
	$result11 = db_query($sql11, "could not get data");
	$ff11= db_fetch($result11);
                                           
                                            if($last["act_qty"]>0)
                                            {
                                             label_cell(null);    
                                            }
                                           else if($ff11['act_qty']>0)
                                            {
                                            label_cell(null); 
                                            } 
                                            else
                                            {
                                            edit_button_cell("{$this->name}Edit[{$last['id']}]", _("Edit"));
                                            }
                                        }

                //       		delete_button_cell("{$this->name}Delete[{$last['id']}]", _("Delete"));
					end_row();
					$last = $myrow;                               
				}
                               
				if ($myrow) {
				$myrow = db_fetch($get_line);
				}
                                $row++;
                                $tot_pro+=$last["pro_qty"];
                                $tot_act+=$last["act_qty"];
                                $tot_spt+=$last["spot_qty"];
                                $tot_rej+=$last["rej_qty"];
			}

                        label_cell('Total Quantity' , 'colspan=3; align=right');
                        label_cell($tot_pro);
                        label_cell($tot_act);
                        label_cell($tot_spt);
                        label_cell($tot_rej);
                        
		end_table(1);
                
                $spot_per=($tot_act*$tot_spt)/100;
                $rej_per=($tot_act*$tot_rej)/100;
                        
                start_table(TABLESTYLE,"width=20%");
                        
                    alt_table_row_color($k);
                            label_cells(_('Spot Production (%) :'), $spot_per );
                    end_row();
                    alt_table_row_color($k);
                            label_cells(_('Reject Production (%) :'), $rej_per);
                    end_row();
                        
                 end_table(1);
                        
                        br();
  
 $sch_view="<a target='_blank' "
		."href='../printing/view/working_schedule.php?or=".$this->entity."'"
		." onclick=\"javascript:openWindow(this.href,this.target); return false;\" >&nbsp;"
		. 'View This Schedule Details'
		."</a>";
display_heading( $sch_view );
	}
	
	function editor_view()
	{
		br();
                
                 $data=get_dy_line_sch($this->selected_id);
                
                
		start_outer_table(TABLESTYLE);

		table_section(1);
		table_section_title(_("Dyeing Schedule Information"));
                
                label_row('Batch No :', $data['batch_no']);
                label_row('Date :', sql2date($data['sc_date']) );
                qty_row('Production Quantity :', 'pro_qty', $data['pro_qty']);
         //       qty_row('Actual Quantity :', 'ac_qty',$data['act_qty']);
                text_row('Comments :', 'comments',$data['comments'] );
                
                hidden('ord_no', $data['batch_no']);
                
                
           end_outer_table(1);

	}

	function db_insert() {
            
         //   $dat = $this->data;
        
	//return add_line($this->entity, $_POST['linehead'], input_num('incharge_id'), input_num('capasity'), input_num('lineworker'), $_POST['gen_note'], input_num('linestatus'));
	}

	function db_update() {
        
		return update_working_schedule_pr($this->selected_id, input_num('pro_qty'), $_POST['comments'],$_POST['ord_no']);
		
	}

	function insert_check() {
		if (input_num('ac_qty')<0 ) {
			display_error(_("The Actual Quantity cannot be negative."));
			set_focus('ac_qty');
			return false;
		}
                      		
        	return true; 
	}

	function db_read() {

            return true;
	}

	function delete_check() {
            
		return true;
	}
	
	function db_delete() {
		
		//return delete_line($this->selected_id);
	}
        
    
}

?>