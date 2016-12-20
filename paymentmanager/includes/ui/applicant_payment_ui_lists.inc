<?php
/*
 * gender list
 */
include_once($path_to_root . "/includes/ui/ui_lists.inc");

//------------------------------------------------------------------

//------------------------------------------
function payment_mothod($label,$name, $selected_id = null, $enabled=true, $submit_on_change=false ) {

    $attendence_type = array('Please Select','Cash','Cheque','Debit Card','Credit Card');
    echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $attendence_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}

//------------------------------------------------------------------

function payment_head_lst_cells($label,$name, $selected_id = null, $enabled=true, $submit_on_change=false ) {

    $grading_type = array(''=>'Please Select', '1'=>'New Student','2'=>'Old Student','3'=>'Both');
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
    
       $query=array(array('tr.id','tr.month',
        'SELECT tr.id,mon.month_name, ph.amount
        FROM '.TB_PREF.'sms_tbl_receivable tr 
        LEFT JOIN '.TB_PREF.'sms_payment_head_setting ph on tr.head_id = ph.id
        LEFT JOIN '.TB_PREF.'hcm_salary_month mon on tr.month = mon.id
        WHERE ( tr.student_id = '.  db_escape($stid).'
        OR tr.applicant_id = '.  db_escape($appid).')'.'
        AND tr.school_year = '.  db_escape($syear).' 
        AND tr.head_id ='. db_escape($payment_type).'
        AND tr.realize != ph.amount AND tr.flag != 1'
        ));
        combo_list_cells(null, 'ptype', '', 'Select Payment Type', true, $query);
}

function get_payment_data($id) {
    
       $sql = "SELECT tr.due_date,tr.fineamnt,hs.amount
            FROM " . TB_PREF . "sms_tbl_receivable tr
            LEFT JOIN " . TB_PREF . "sms_payment_head_setting hs ON tr.head_id = hs.id
            WHERE tr.id = ".db_escape($id);
       //display_error($sql);
    $result = db_query($sql, "an item could not be retreived");
    
    return db_fetch($result);
}
//----------------Month

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

//******************************************************************************************************

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
function search_field($label,$name, $selected_id = null, $enabled=true, $submit_on_change=false) {
    
 
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

function search_field_payment($label,$name, $searchval, $selected_id = null, $enabled=true, $submit_on_change=false) {
        $grading_type = array(''=>'Please Select','student_id'=>'Student Id');
    
    
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
//used to get payment
