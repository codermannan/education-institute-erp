<?php
/*
 * gender list
 */
include_once($path_to_root . "/includes/ui/ui_lists.inc");

//------------------------------------------------------------------

function applicant_name_list($name, $selected_id = null, $all_option = false, $submit_on_change = false) {
    global $all_items;

    $sql = "SELECT id, (first_name,middle_name,last_name) as name FROM " . TB_PREF . "students_details";

    return combo_input($name, $selected_id, $sql, 'id', 'name', array(
        'spec_option' => $all_option === false ? _("Select") : $all_option,
        'spec_id' => $all_items,
        'select_submit' => $submit_on_change
    ));
}

function applicant_name_cells($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo applicant_name_list($name, $selected_id, $all_option, $submit_on_change);
    echo "</td>\n";
}

function applicant_name_row($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false) {
    echo "<tr><td class='label'>$label</td>";
    applicant_name_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}

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
function attendence_lst_cells($name, $selected=null,$atttype)
{
        
            $attendence_type = array(''=>'Please Select', '0'=>'Absent','1'=>'Present');
     
	echo "<td>\n";
	echo array_selector($name, $selected, $attendence_type);
	echo "</td>\n";
} 

function app_attendence_list_cells($name, $selected=null)
{
      $attendence_type = array('0'=>'Absent','1'=>'Present');
	echo "<td>\n";
	echo array_selector($name, $selected, $attendence_type);
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


//used----------------Month

function month_list($name, $selected_id = null, $all_option = false, $submit_on_change = false) {
    global $all_items;

    $sql = "SELECT mon_id, month_name FROM " . TB_PREF ."hcm_salary_month";

    return combo_input($name, $selected_id, $sql, 'mon_id', 'month_name', array(
        'order' => 'id',
        'spec_option' => $all_option === false ? _("Select Month") : $all_option,
        'spec_id' => $all_items,
        'select_submit' => $submit_on_change
    ));
}

function month_list_cells($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo month_list($name, $selected_id, $all_option, $submit_on_change);
    echo "</td>\n";
}

function month_list_row($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false) {
    echo "<tr><td class='label'>$label</td>";
    month_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
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
function search_field($label,$name, $searchval, $selected_id = null, $enabled=true, $submit_on_change=false) {
    
    if($searchval == 'book_req'){
        $grading_type = array(''=>'Please Select', 'book_id'=>'Book ID','book_name'=>'Book Name', 'auth_name'=>'Auth Name','edition'=>'Edition','publication'=>'Publication');
    }
    else{
        $grading_type = array(''=>'Please Select', 'applicant_id'=>'Applicant ID','mobile'=>'Mobile');
    }
    
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $grading_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td>\n";
}


function search_field_student($label,$name, $searchval, $selected_id = null, $enabled=true, $submit_on_change=false) {
    
  
    
        $grading_type = array(''=>'Please Select', 'teacher'=>'Teacher Name',
            'class'=>'Class Name','section'=>'Section Name', 'subject'=>'Subject Name');
    
    
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $grading_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td>\n";
}


function leave_type_cells($label,$name, $selected_id = null, $enabled=true, $submit_on_change=false ) {

    $leave = array('0'=>'Select', '1'=>'Leave In Advance','2'=>'Leave Of Absence');
    echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $leave, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}


function search_field1($label,$name, $searchval, $selected_id = null, $enabled=true, $submit_on_change=false) {
    
    if($searchval == 'book_req')
        
     
    {
        $grading_type = array(''=>'Please Select', 'book_id'=>'Book ID','book_name'=>'Book Name', 'auth_name'=>'Auth Name','edition'=>'Edition','publication'=>'Publication');
    }
    else{
        $grading_type = array(''=>'Please Select', 'student_id'=>'Student ID');
    }
    
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $grading_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td>\n";
 
 
}
//-----------------------------------------------------------------------------

function  date_range($first,$last){
    $arr = array();
    $nw = strtotime($first);
    $thn = strtotime($last);
    
    while ($nw <= $thn)
    {
        $arr[] = date('Y-m-d',$nw);
        $nw = strtotime('+1 day',$nw);
        
    }
    return $arr;
}
//-----------------------------------------------------------------------------

function find_date($query_date,$delimeter){
    
    if($delimeter==1){
        // First day of the month.
        $date =  date('Y-m-01', strtotime($query_date));
    }else{
        // Last day of the month.
        $date = date('Y-m-t', strtotime($query_date));
    }
    return $date;
}