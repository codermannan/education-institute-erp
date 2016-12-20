<?php
/*
 * gender list
 */
include_once($path_to_root . "/includes/ui/ui_lists.inc");

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