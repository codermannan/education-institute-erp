<?php
/*
 * gender list
 */
include_once($path_to_root . "/includes/ui/ui_lists.inc");

function gender_list_row($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false) {
    echo "<tr><td class='label'>$label</td>";
    gender_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}

function gender_list($label,$name, $selected_id = null, $enabled=true, $submit_on_change=false ) {
    global $gender_type;

    echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $gender_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}

function gender_list_cells($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo gender_list($name, $selected_id, $all_option, $submit_on_change);
    echo "</td>\n";
}

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


//-----------------nationality
function nationality_list($name, $selected_id = null, $all_option = false, $submit_on_change = false) {
    global $all_items;

    $sql = "SELECT id, nationality FROM " . TB_PREF . "nationality";

    return combo_input($name, $selected_id, $sql, 'id', 'nationality', array(
        'spec_option' => $all_option === false ? _("Select") : $all_option,
        'spec_id' => $all_items,
        'select_submit' => $submit_on_change
    ));
}

function nationality_list_cells($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo nationality_list($name, $selected_id, $all_option, $submit_on_change);
    echo "</td>\n";
}

function nationality_list_row($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false) {
    echo "<tr><td class='label'>$label</td>";
    nationality_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}

//------------------------------------------------------------------
function grading_list($label,$name, $selected_id = null, $enabled=true, $submit_on_change=false ) {

    $grading_type = array(''=>'Please Select', '0'=>'Theory','1'=>'Practical');
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

//--------------------------------------------------------
function attendence_list($label,$name, $selected_id = null, $enabled=true, $submit_on_change=false ) {

    $attendance_type = array(''=>'Please Select', '0'=>'Absent','1'=>'Present');
    echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $attendance_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}

//---------------------------------------------------------


//------------------------------------------------------------------
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
//function payment_head_lst_cells($name, $selected=null)
//{
//        $sql = "SELECT head_name, id FROM ".TB_PREF."sms_payment_head_setting";
//        $result = db_query($sql, "The Applicant information could not be added");
//        while($rq = mysql_fetch_array($result)){
//            $head[] = $rq['head_name'];
//        }
//	echo "<td>\n";
//	echo array_selector($name, $selected, $head);
//	echo "</td>\n";
//} 

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


function section_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $not_select=false)
{
	global $all_items;
        
           
         //$data_ms=data_retrieve("sp_order_master", "order_no", $not_select);

         $sql = "SELECT * FROM " . TB_PREF . "sms_session where class=".db_escape($_POST['pclass']);
       //  display_error($sql);
         
return combo_input($name, $selected_id, $sql, 'id','session_name',
	array(
		'spec_option' => $all_option === true ? _("Select ") : $all_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change
	) );
}

function section_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $not_select=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo section_list($name, $selected_id, $all_option, $submit_on_change, $not_select);
	echo "</td>\n";
}

function section_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $not_select=false)
{

	echo "<tr><td class='label'>$label</td>";
	section_list_cells(null, $name, $selected_id, $all_option, $submit_on_change, $not_select);
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
//-----
function assing_subject($label,$name, $searchval, $selected_id = null, $enabled=true, $submit_on_change=false) {
    $grading_type = array(''=>'Please Select', '1'=>'Assign','0'=>'Not Assign');
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $grading_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td>\n";
}