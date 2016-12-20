<?php
/*
 * gender list
 */
include_once($path_to_root . "/includes/ui/ui_lists.inc");

/*
 * blood_group
 */

function blood_list($label, $name, $selected_id=null, $enabled=true, $submit_on_change=false )
{
	global $blood_group;

	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $blood_group, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}

function payment_head_list($stid,$syear, $payment_type, $appid) {
    
       $query=array(array('tr.id','ph.head_name',
        'SELECT tr.id,ph.head_name, ph.amount
        FROM '.TB_PREF.'sms_tbl_receivable tr 
        LEFT JOIN '.TB_PREF.'sms_payment_head_setting ph on tr.head_id = ph.id     
        WHERE ( tr.student_id = '.  db_escape($stid).'
        OR tr.applicant_id = '.  db_escape($appid).')'.'
        AND tr.school_year = '.  db_escape($syear).' 
        AND tr.head_id ='. db_escape($payment_type).'
        AND tr.realize != ph.amount'    
        ));
        combo_list_cells(null, 'ptype', '', 'Select Payment Type', true, $query);
}

function get_payment_data($id) {
    
       $sql = "SELECT tr.due_date,hs.amount
            FROM " . TB_PREF . "sms_tbl_receivable tr
            LEFT JOIN " . TB_PREF . "sms_payment_head_setting hs ON tr.head_id = hs.id
            WHERE tr.id = ".db_escape($id);
    $result = db_query($sql, "an item could not be retreived");
    
    return db_fetch($result);
}

//-------------------------------------------------------------------------------------
function sms_status($label,$name, $selected_id = null, $enabled=true, $submit_on_change=false ) {

    $grading_type = array(''=>'Please Select', '1'=>'Active','0'=>'Inactive');
    echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $grading_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}
//-------------------------------------------------------------------------------------
function search_field($label,$name,$selected_id = null, $enabled=true, $submit_on_change=false) {
    
        $grading_type = array(''=>'Please Select', 'applicant_id'=>'Applicant ID','mobile'=>'Mobile');
   
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $grading_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td>\n";
}
function schedule_list_cells($label,$name, $selected_id = null, $enabled=true, $submit_on_change=false ) {

    $grading_type = array(''=>'Please Select', '1'=>'Open','2'=>'Close');
    echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $grading_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}
//used
//------------------------------------------------------------------
function classset_list($label,$name, $selected_id = null, $enabled=true, $submit_on_change=false ) {

    $classset = array(''=>'Please Select', '1'=>'Set 1','2'=>'Set 2');
    echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $classset, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}
//--temporarily used. should be find out why combi is not working
function student_class_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, class_name FROM ".TB_PREF."sms_create_stud_class";

	//$mode = get_company_pref('no_customer_list');

	if ($editkey)
		set_editor('project', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'id', 'class_name',
	array(
	
	    'order' => array('class_name'),
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Project") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('project');
	return $ret;
}

function get_student_clas($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo student_class_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}
//-------------
function app_attendence_list_cells($name, $selected=null){
      $attendence_type = array('0'=>'Absent','1'=>'Present');
	echo "<td>\n";
	echo array_selector($name, $selected, $attendence_type);
	echo "</td>\n";
} 

