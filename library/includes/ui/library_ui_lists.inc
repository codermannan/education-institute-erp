<?php
/*
 * gender list  st.st_class =".db_escape($class)."
             AND 
 */
include_once($path_to_root . "/includes/ui/ui_lists.inc");


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