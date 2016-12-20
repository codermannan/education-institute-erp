<?php
/**********************************************************************
    
***********************************************************************/
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/types.inc");
include_once($path_to_root . "/includes/current_user.inc");

$_search_button = "<input %s type='submit' class='combo_submit' style='border:0;background:url($path_to_root/themes/"
	."%s/images/locate.png) no-repeat;%s' aspect='fallback' name='%s' value=' ' title='"._("Set filter")."'> ";

$_select_button = "<input %s type='submit' class='combo_select' style='border:0;background:url($path_to_root/themes/"
	."%s/images/button_ok.png) no-repeat;%s' aspect='fallback' name='%s' value=' ' title='"._("Select")."'> ";

$all_items = ALL_TEXT;

//----------------------------------------------------------------------------
//	Universal sql combo generator
//	$sql must return selector values and selector texts in columns 0 & 1
//	Options are merged with defaults.

function combo_input($name, $selected_id, $sql, $valfield, $namefield, $options=null, $post_label=null)
{
global $Ajax;

$opts = array(		// default options
	'where'=> array(),		// additional constraints
	'order' => $namefield,	// list sort order
		// special option parameters
	'spec_option'=>false, 	// option text or false
	'spec_id' => 0,		// option id
		// submit on select parameters
	'default' => '', // default value when $_POST is not set
	'multi' => false,	// multiple select
	'select_submit' => false, //submit on select: true/false
	'async' => true,	// select update via ajax (true) vs _page_body reload
		// search box parameters
	'sel_hint' => null,
	'search_box' => false, 	// name or true/false
	'type' => 0,	// type of extended selector:
		// 0 - with (optional) visible search box, search by fragment inside id
		// 1 - with hidden search box, search by option text
		// 2 - with (optional) visible search box, search by fragment at the start of id
		// 3 - TODO reverse: box with hidden selector available via enter; this
		// would be convenient for optional ad hoc adding of new item
	'search_submit' => true, //search submit button: true/false
	'size' => 8,	// size and max of box tag
	'max' => 50,
	'height' => false,	// number of lines in select box
	'cells' => false,	// combo displayed as 2 <td></td> cells
	'search' => array(), // sql field names to search
	'format' => null, 	 // format functions for regular options
	'disabled' => false,
	'box_hint' => null, // box/selectors hints; null = std see below
	'category' => false, // category column name or false
	'show_inactive' => false, // show inactive records. 
	'editable' => false // false, or length of editable entry field
);
// ------ merge options with defaults ----------
	if($options != null)
		$opts = array_merge($opts, $options);
	if (!is_array($opts['where']))  $opts['where'] = array($opts['where']);

	$search_box = $opts['search_box']===true ? '_'.$name.'_edit' : $opts['search_box'];
	// select content filtered by search field:
	$search_submit = $opts['search_submit']===true ? '_'.$name.'_button' : $opts['search_submit'];
	// select set by select content field
	$search_button = $opts['editable'] ? '_'.$name.'_button' : ($search_box ? $search_submit : false);

	$select_submit =  $opts['select_submit'];
	$spec_id = $opts['spec_id'];
	$spec_option = $opts['spec_option'];
	if ($opts['type'] == 0) {
		$by_id = true;
		$class = 'combo';
	} elseif($opts['type'] == 1) {
		$by_id = false;
		$class = 'combo2';
	} else {
		$by_id = true;
		$class = 'combo3';
	}

	$disabled = $opts['disabled'] ? "disabled" : '';
	$multi = $opts['multi'];
	
	if(!count($opts['search'])) {
		$opts['search'] = array($by_id ? $valfield : $namefield);
	}
	if ($opts['sel_hint'] === null) 
		$opts['sel_hint'] = $by_id || $search_box==false ?
			'' : _('Press Space tab for search pattern entry');

	if ($opts['box_hint'] === null)
		$opts['box_hint'] = $search_box && $search_submit != false ?
			($by_id ? _('Enter code fragment to search or * for all')
			: _('Enter description fragment to search or * for all')) :'';

	if ($selected_id == null) {
		$selected_id = get_post($name, (string)$opts['default']);
	}
	if(!is_array($selected_id))
		$selected_id = array((string)$selected_id); // code is generalized for multiple selection support

	$txt = get_post($search_box);
	$rel = '';
	$limit = '';
	if (isset($_POST['_'.$name.'_update'])) { // select list or search box change
		if ($by_id) $txt = $_POST[$name];

		if (!$opts['async'])
			$Ajax->activate('_page_body');
		else
			$Ajax->activate($name);
	}
	if (isset($_POST[$search_button])) {
		if (!$opts['async'])
			$Ajax->activate('_page_body');
		else
			$Ajax->activate($name);
	}
	if ($search_box) {
		// search related sql modifications

		$rel = "rel='$search_box'"; // set relation to list
		if ($opts['search_submit']) {
			if (isset($_POST[$search_button])) {
				$selected_id = array(); // ignore selected_id while search
				if (!$opts['async'])
					$Ajax->activate('_page_body');
				else
					$Ajax->activate($name);
			}
			if ($txt == '') {
				if ($spec_option === false && $selected_id == array())
					$limit = ' LIMIT 1';
				else
					$opts['where'][] = $valfield . "=". db_escape(get_post($name, $spec_id));
			}
			else
				if ($txt != '*') {

					foreach($opts['search'] as $i=> $s)
						$opts['search'][$i] = $s . " LIKE "
							.db_escape(($class=='combo3' ? '' : '%').$txt.'%');
					$opts['where'][] = '('. implode($opts['search'], ' OR ') . ')';
				}
		}
	}

	// sql completion
	if (count($opts['where'])) {
		$where = strpos($sql, 'WHERE')==false ? ' WHERE ':' AND ';
		$where .= '('. implode($opts['where'], ' AND ') . ')';
		$group_pos = strpos($sql, 'GROUP BY');
		if ($group_pos) {
			$group = substr($sql, $group_pos);
			$sql = substr($sql, 0, $group_pos) . $where.' '.$group;
		} else {
			$sql .= $where;
		}
	}
	if ($opts['order'] != false) {
		if (!is_array($opts['order']))
			$opts['order'] = array($opts['order']);
		$sql .= ' ORDER BY '.implode(',',$opts['order']);
	}

	$sql .= $limit;
	// ------ make selector ----------
	$selector = $first_opt = '';
	$first_id = false;
	$found = false;
	$lastcat = null;
	$edit = false;
//if($name=='stock_id')	display_notification('<pre>'.print_r($_POST, true).'</pre>');
//if($name=='curr_default') display_notification($opts['search_submit']);
	if($result = db_query($sql)) {
		while ($contact_row = db_fetch($result)) {
			$value = $contact_row[0];
			$descr = $opts['format']==null ?  $contact_row[1] :
				call_user_func($opts['format'], $contact_row);
			$sel = '';
 		 	if (get_post($search_button) && ($txt == $value)) {
 		 		$selected_id[] = $value;
 		 	}

 		 	if (in_array((string)$value, $selected_id, true)) {
				$sel = 'selected';
				$found = $value;
				$edit = $opts['editable'] && $contact_row['editable'] 
					&& (@$_POST[$search_box] == $value)
					? $contact_row[1] : false; // get non-formatted description
				if ($edit)
					break;	// selected field is editable - abandon list construction
			}
			// show selected option even if inactive 
			if (!$opts['show_inactive'] && @$contact_row['inactive'] && $sel==='') {
				continue;
			} else 
				$optclass = @$contact_row['inactive'] ? "class='inactive'" : '';

			if ($first_id === false) {
				$first_id = $value;
				$first_opt = $descr;
			}
			$cat = $contact_row[$opts['category']];
			if ($opts['category'] !== false && $cat != $lastcat){
				$selector .= "<optgroup label='".$cat."'>\n";
				$lastcat = $cat;
			}
			$selector .= "<option $sel $optclass value='$value'>$descr</option>\n";
		}
		db_free_result($result);
	}

	// Prepend special option.
	if ($spec_option !== false) { // if special option used - add it
		$first_id = $spec_id;
		$first_opt = $spec_option;
//	}
//	if($first_id !== false) {
		$sel = $found===false ? 'selected' : '';
		$optclass = @$contact_row['inactive'] ? "class='inactive'" : '';
		$selector = "<option $sel value='$first_id'>$first_opt</option>\n"
			. $selector;
	}

	if ($found===false) {
		$selected_id = array($first_id);
	}
	
	$_POST[$name] = $multi ? $selected_id : $selected_id[0];

	$selector = "<select autocomplete='off' ".($multi ? "multiple" : '')
		. ($opts['height']!==false ? ' size="'.$opts['height'].'"' : '')
		. "$disabled name='$name".($multi ? '[]':'')."' class='$class' title='"
		. $opts['sel_hint']."' $rel>".$selector."</select>\n";

	if ($by_id && ($search_box != false || $opts['editable']) ) {
		// on first display show selector list
		if (isset($_POST[$search_box]) && $opts['editable'] && $edit) {
			$selector = "<input type='hidden' name='$name' value='".$_POST[$name]."'>"
			."<input type='text' $disabled name='{$name}_text' id='{$name}_text' size='".
				$opts['editable']."' maxlength='".$opts['max']."' $rel value='$edit'>\n";
				set_focus($name.'_text'); // prevent lost focus
		} else if (get_post($search_submit ? $search_submit : "_{$name}_button"))
			set_focus($name); // prevent lost focus
		if (!$opts['editable'])
	 		$txt = $found;
		$Ajax->addUpdate($name, $search_box, $txt ? $txt : '');
	}

	$Ajax->addUpdate($name, "_{$name}_sel", $selector);

	// span for select list/input field update
	$selector = "<span id='_{$name}_sel'>".$selector." ".$post_label."</span>\n";

	 // if selectable or editable list is used - add select button
	if ($select_submit != false || $search_button) {
		global $_select_button;
	// button class selects form reload/ajax selector update
		$selector .= sprintf($_select_button, $disabled, user_theme(),
			(fallback_mode() ? '' : 'display:none;'),
			 '_'.$name.'_update')."\n";
	}
// ------ make combo ----------
	$edit_entry = '';
	if ($search_box != false) {
		$edit_entry = "<input $disabled type='text' name='$search_box' id='$search_box' size='".
			$opts['size']."' maxlength='".$opts['max'].
			"' value='$txt' class='$class' rel='$name' autocomplete='off' title='"
			.$opts['box_hint']."'"
			.(!fallback_mode() && !$by_id ? " style=display:none;":'')
			.">\n";
		if ($search_submit != false || $opts['editable']) {
			global $_search_button;
			$edit_entry .= sprintf($_search_button, $disabled, user_theme(),
				(fallback_mode() ? '' : 'display:none;'),
				$search_submit ? $search_submit : "_{$name}_button")."\n";
		}
	}
	default_focus(($search_box && $by_id) ? $search_box : $name);

	if ($search_box && $opts['cells'])
		$str = ($edit_entry!='' ? "<td>$edit_entry</td>" : '')."<td>$selector</td>";
	else
		$str = $edit_entry.$selector;
	return $str;
}

/*
	Helper function.
	Returns true if selector $name is subject to update.
*/
function list_updated($name)
{
	return isset($_POST['_'.$name.'_update']) || isset($_POST['_'.$name.'_button']);
}
//----------------------------------------------------------------------------------------------
//	Universal array combo generator
//	$items is array of options 'value' => 'description'
//	Options is reduced set of combo_selector options and is merged with defaults.

function array_selector($name, $selected_id, $items, $options=null)
{
	global $Ajax;

$opts = array(		// default options
	'spec_option'=>false, 	// option text or false
	'spec_id' => 0,		// option id
	'select_submit' => false, //submit on select: true/false
	'async' => true,	// select update via ajax (true) vs _page_body reload
	'default' => '', // default value when $_POST is not set
	'multi'=>false,	// multiple select
		// search box parameters
	'height' => false,	// number of lines in select box
	'sel_hint' => null,
	'disabled' => false
);
// ------ merge options with defaults ----------
	if($options != null)
		$opts = array_merge($opts, $options);
	$select_submit =  $opts['select_submit'];
	$spec_id = $opts['spec_id'];
	$spec_option = $opts['spec_option'];
	$disabled = $opts['disabled'] ? "disabled" : '';
	$multi = $opts['multi'];

	if ($selected_id == null) {
		$selected_id = get_post($name, $opts['default']);
	}
	if(!is_array($selected_id))
		$selected_id = array((string)$selected_id); // code is generalized for multiple selection support

	if (isset($_POST[ '_'.$name.'_update'])) {
		if (!$opts['async'])
			$Ajax->activate('_page_body');
		else
			$Ajax->activate($name);
	}

	// ------ make selector ----------
	$selector = $first_opt = '';
	$first_id = false;
	$found = false;
//if($name=='SelectStockFromList') display_error($sql);
		foreach($items as $value=>$descr) {
			$sel = '';
			if (in_array((string)$value, $selected_id, true)) {
				$sel = 'selected';
				$found = $value;
			}
			if ($first_id === false) {
				$first_id = $value;
				$first_opt = $descr;
			}
			$selector .= "<option $sel value='$value'>$descr</option>\n";
		}

	if ($first_id!==false) {
		$sel = ($found===$first_id) || ($found===false && ($spec_option===false)) ? "selected='selected'" : '';
		$selector = sprintf($first_opt, $sel).$selector;
	}
	// Prepend special option.
	if ($spec_option !== false) { // if special option used - add it
		$first_id = $spec_id;
		$first_opt = $spec_option;
		$sel = $found===false ? 'selected' : '';
		$selector = "<option $sel value='$spec_id'>$spec_option</option>\n"
			. $selector;
	}

	if ($found===false) {
		$selected_id = array($first_id);
	}
	$_POST[$name] = $multi ? $selected_id : $selected_id[0];

	$selector = "<select autocomplete='off' ".($multi  ? "multiple" : '')
		. ($opts['height']!==false ? ' size="'.$opts['height'].'"' : '')
		. "$disabled name='$name".($multi ? '[]' : '')."' class='combo' title='"
		. $opts['sel_hint']."'>".$selector."</select>\n";

	$Ajax->addUpdate($name, "_{$name}_sel", $selector);

	$selector = "<span id='_{$name}_sel'>".$selector."</span>\n";

	if ($select_submit != false) { // if submit on change is used - add select button
		global $_select_button;
		$selector .= sprintf($_select_button, $disabled, user_theme(),
			(fallback_mode() ? '' : 'display:none;'),
			 '_'.$name.'_update')."\n";
	}
	default_focus($name);

	return $selector;
}
//----------------------------------------------------------------------------------------------

function _format_add_curr($row)
{
	static $company_currency;

	if ($company_currency == null)
	{
		$company_currency = get_company_currency();
	}
	return $row[1] . ($row[2] == $company_currency ?
		'' : ("&nbsp;-&nbsp;" . $row[2]));
}

function add_edit_combo($type)
{
	global $path_to_root, $popup_editors, $use_icon_for_editkey;

	if (!isset($use_icon_for_editkey) || $use_icon_for_editkey==0)
		return "";
	// Derive theme path
	$theme_path = $path_to_root . '/themes/' . user_theme();

	$key = $popup_editors[$type][1];
	$onclick = "onclick=\"javascript:callEditor($key); return false;\"";
	$img = "<img width='12' height='12' border='0' alt='Add/Edit' title='Add/Edit' src='$theme_path/images/".ICON_EDIT."'>";
	return "<a target = '_blank' href='#' $onclick tabindex='-1'>$img</a>"; 
}

function supplier_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT supplier_id, supp_ref, curr_code, inactive FROM ".TB_PREF."suppliers ";

	$mode = get_company_pref('no_supplier_list');

	if ($editkey)
		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'supplier_id', 'supp_name',
	array(
		'format' => '_format_add_curr',
	    'order' => array('supp_ref'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Suppliers") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select supplier'),
		'show_inactive'=>$all
		));
	if ($editkey)
		$ret .= add_edit_combo('supplier');		
	return $ret;
}

function supplier_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>";
        
                echo "<td>";
		echo supplier_list($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function supplier_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo supplier_list($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
//----------------------------------------------------------------------------------------------

function customer_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT debtor_no, debtor_ref, curr_code, inactive FROM ".TB_PREF."debtors_master ";

	$mode = get_company_pref('no_customer_list');

	if ($editkey)
		set_editor('customer', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'debtor_no', 'debtor_ref',
	array(
	    'format' => '_format_add_curr',
	    'order' => array('debtor_ref'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Customers") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
		_('Select customer'),
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('customer');
	return $ret;
}

function customer_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo customer_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}

function customer_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo customer_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}

//------------------------------------------------------------------------------------------------

function customer_branches_list($customer_id, $name, $selected_id=null,
	$spec_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT branch_code, branch_ref FROM ".TB_PREF."cust_branch
		WHERE debtor_no=" . db_escape($customer_id)." ";

	if ($editkey)
		set_editor('branch', $name, $editkey);

	$where = $enabled ? array("disable_trans = 0") : array();
	$ret = combo_input($name, $selected_id, $sql, 'branch_code', 'branch_ref',
	array(
		'where' => $where,
		'order' => array('branch_ref'),
		'spec_option' => $spec_option === true ? _('All branches') : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'sel_hint' => _('Select customer branch')
	) );
	if ($editkey)
	{
		$ret .= add_edit_combo('branch');
	}	
	return $ret;
}
//------------------------------------------------------------------------------------------------

function customer_branches_list_cells($label,$customer_id, $name, $selected_id=null, 
	$all_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo customer_branches_list($customer_id, $name, $selected_id, $all_option, $enabled, 
		$submit_on_change, $editkey);
	echo "</td>\n";
}

function customer_branches_list_row($label, $customer_id, $name, $selected_id=null, 
	$all_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td>";
	customer_branches_list_cells(null, $customer_id, $name, $selected_id, 
		$all_option, $enabled, $submit_on_change, $editkey);
	echo "</tr>";
}

//------------------------------------------------------------------------------------------------

function locations_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $not_select=false)
{
	global $all_items;

	$sql = "SELECT loc_code, location_name, inactive FROM ".TB_PREF."locations";

        if($not_select){
            $sql .= " WHERE loc_code !='".$not_select."'";
        }
//        display_error($_SESSION["wa_current_user"]->other_locations);
        
        
      if($_SESSION["wa_current_user"]->other_locations!='all' && $not_select)
        $sql.=" and loc_code in('".implode("','",$_SESSION["wa_current_user"]->other_locations)."')";
        
         if($_SESSION["wa_current_user"]->other_locations!='all' && !$not_select)
        $sql.=" WHERE loc_code in('".implode("','",$_SESSION["wa_current_user"]->other_locations)."')";
        
return combo_input($name, $selected_id, $sql, 'loc_code', 'location_name',
	array(
		'spec_option' => $all_option === true ? _("Select Locations") : $all_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change
	) );
}

function locations_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $not_select=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo locations_list($name, $selected_id, $all_option, $submit_on_change, $not_select);
	echo "</td>\n";
}

function locations_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $not_select=false)
{
	echo "<tr><td class='label'>$label</td>";
	locations_list_cells(null, $name, $selected_id, $all_option, $submit_on_change, $not_select);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------

function currencies_list($name, $selected_id=null, $submit_on_change=false,$all_option=false)
{
	$sql = "SELECT curr_abrev, currency, inactive FROM ".TB_PREF."currencies";

// default to the company currency
return combo_input($name, $selected_id, $sql, 'curr_abrev', 'currency',
	array(
		'select_submit'=> $submit_on_change,
                'spec_option' => $all_option===true ?  _("Select") : $all_option,
		'default' => get_company_currency(),
		'async' => false		
	) );
}

function currencies_list_cells($label, $name, $selected_id=null, $submit_on_change=false,$all_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo currencies_list($name, $selected_id, $submit_on_change,$all_option);
	echo "</td>\n";
}

function currencies_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	currencies_list_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}

//---------------------------------------------------------------------------------------------------

function fiscalyears_list($name, $selected_id=null, $submit_on_change=false)
{

	$sql = "SELECT * FROM ".TB_PREF."fiscal_year";

// default to the company current fiscal year

return combo_input($name, $selected_id, $sql, 'id', '',
	array(
		'order' => 'begin',
		'default' => get_company_pref('f_year'),
		'format' => '_format_fiscalyears',
		'select_submit'=> $submit_on_change,
		'async' => false
	) );
}

function _format_fiscalyears($row)
{
	return sql2date($row[1]) . "&nbsp;-&nbsp;" . sql2date($row[2])
	. "&nbsp;&nbsp;" . ($row[3] ? _('Closed') : _('Active')) . "</option>\n";
}

function fiscalyears_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo fiscalyears_list($name, $selected_id);
	echo "</td>\n";
}

function fiscalyears_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class='label'>$label</td>";
	fiscalyears_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}
//------------------------------------------------------------------------------------

function dimensions_list($name, $selected_id=null, $no_option=false, $showname=' ',
	$submit_on_change=false, $showclosed=false, $showtype=1)
{
$sql = "SELECT id, CONCAT(reference,'  ',name) as ref FROM ".TB_PREF."dimensions";

$options = array(
	'order' => 'reference',
	'spec_option'=>$no_option ? $showname : false,
	'spec_id' => 0,
	'select_submit'=> $submit_on_change,
	'async' => false,
	);

	if (!$showclosed)
		$options['where'][] = "closed=0";
	if($showtype)
		$options['where'][] = "type_=".db_escape($showtype);

	return combo_input($name, $selected_id, $sql, 'id', 'ref', $options);
}

function dimensions_list_cells($label, $name, $selected_id=null, $no_option=false, $showname=null,
	$showclosed=false, $showtype=0, $submit_on_change=false)
{
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";
	echo dimensions_list($name, $selected_id, $no_option, $showname, $submit_on_change, $showclosed, $showtype);
	echo "</td>\n";
}

function dimensions_list_row($label, $name, $selected_id=null, $no_option=false, $showname=null,
	$showclosed=false, $showtype=0, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	dimensions_list_cells(null, $name, $selected_id, $no_option, $showname,
		$showclosed, $showtype, $submit_on_change);
	echo "</tr>\n";
}

//---------------------------------------------------------------------------------------------------

function stock_items_list($name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $opts=array(), $editkey = false, $post_label = false)
{
	global $all_items;

	$sql = "SELECT stock_id, s.description, c.description, s.inactive, s.editable
			FROM ".TB_PREF."stock_master s,".TB_PREF."stock_category c WHERE s.category_id=c.category_id";
 if($_SESSION["wa_current_user"]->role_categories!='all')
        $sql.=" and c.category_id in(".$_SESSION["wa_current_user"]->role_categories.")";
	if ($editkey)
		set_editor('item', $name, $editkey);

     //   $sql .=array('new'=>'New Item');
	$ret = combo_input($name, $selected_id, $sql, 'stock_id', 's.description',
	array_merge(
	  array(
		'format' => '_format_stock_items',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'search' => array("stock_id", "c.description","s.description"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>15,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('c.description','stock_id')
	  ), $opts), $post_label );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}

function _format_stock_items($row)
{
	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1];
}

function stock_items_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false, $post_label = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('cells'=>true, 'show_inactive'=>$all), $editkey, $post_label);
}

/* Combo for buyers inquiry with style for marketing in Order Management -- abiR 
 */
function inquiry_items_list($name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $opts=array(), $editkey = false)
{
	global $all_items;

	$sql = "SELECT sample_tracking_no,style FROM ".TB_PREF."mrkt_sample_master_data";

	if ($editkey)
		set_editor('item', $name, $editkey);

     //   $sql .=array('new'=>'New Item');
	$ret = combo_input($name, $selected_id, $sql, 'sample_tracking_no', 'style',
	array_merge(
	  array(
		'format' => '_format_stock_items',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'search' => array("sample_tracking_no", "style"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>15,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('style','sample_tracking_no')
	  ), $opts) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}
//
//function _format_stock_items($row)
//{
//	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1];
//}

function inquiry_items_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo inquiry_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('cells'=>true, 'show_inactive'=>$all), $editkey);
}


/*
function stock_items_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr>\n";
	stock_items_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}
*/
//---------------------------------------------------------------------------------------------------
//
// Select item via foreign code.
//
function sales_items_list($name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $type='', $opts=array())
{
	global $all_items;
	// all sales codes
	$sql = "SELECT i.item_code, i.description, c.description, count(*)>1 as kit,
			 i.inactive, if(count(*)>1, '0', s.editable) as editable
			FROM
			".TB_PREF."stock_master s,
			".TB_PREF."item_codes i
			LEFT JOIN
			".TB_PREF."stock_category c
			ON i.category_id=c.category_id
			WHERE i.stock_id=s.stock_id";

	
	if ($type == 'local')	{ // exclude foreign codes
		$sql .=	" AND !i.is_foreign"; 
	} elseif ($type == 'kits') { // sales kits
		$sql .=	" AND !i.is_foreign AND i.item_code!=i.stock_id";
	}
	$sql .= " AND !i.inactive AND !s.inactive AND !s.no_sale";
	$sql .= " GROUP BY i.item_code";

	return combo_input($name, $selected_id, $sql, 'i.item_code', 'c.description',
	array_merge(
	  array(
		'format' => '_format_stock_items',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'search' => array("i.item_code", "c.description", "i.description"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>15,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('c.description','i.item_code'),
		'editable' => 30,
		'max' => 255
	  ), $opts) );
}

function sales_items_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $editkey=false)
{
	if ($editkey)
		set_editor('item', $name, $editkey);

	if ($label != null)
		echo "<td>$label</td>\n";
	echo sales_items_list($name, $selected_id, $all_option, $submit_on_change,
		'', array('cells'=>true));
}

function sales_kits_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	return sales_items_list($name, $selected_id, $all_option, $submit_on_change,
		'kits', array('cells'=>false, 'editable' => false));
}

function sales_local_items_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";
	echo sales_items_list($name, $selected_id, $all_option, $submit_on_change,
		'local', array('cells'=>false, 'editable' => false));
	echo "</td></tr>";
}
//------------------------------------------------------------------------------------

function stock_manufactured_items_list($name, $selected_id=null,
	$all_option=false, $submit_on_change=false)
{
	return stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag= 'M'")));
}

function stock_manufactured_items_list_cells($label, $name, $selected_id=null,
				$all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
//	echo "<td>";
//	echo stock_manufactured_items_list($name, $selected_id, $all_option, $submit_on_change);
//	echo "</td>\n";
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag= 'M'"), 'cells'=>true));   
        
}

function stock_manufactured_items_list_row($label, $name, $selected_id=null,
		$all_option=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	stock_manufactured_items_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}
//------------------------------------------------------------------------------------

function stock_component_items_list($name, $parent_stock_id, $selected_id=null,
	$all_option=false, $submit_on_change=false, $editkey = false)
{
	return stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("stock_id != ".db_escape($parent_stock_id))), $editkey);
}

function stock_component_items_list_cells($label, $name, $parent_stock_id, 
	$selected_id=null, $all_option=false, $submit_on_change=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("stock_id != ".db_escape($parent_stock_id)), 'cells'=>true),
		$editkey);
}
//------------------------------------------------------------------------------------

function stock_costable_items_list($name, $selected_id=null,
	$all_option=false, $submit_on_change=false)
{
	return stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag!='D'")));
}

function stock_costable_items_list_cells($label, $name, $selected_id=null, 
	$all_option=false, $submit_on_change=false, $post_label = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag!='D'"), 'cells'=>true), false, $post_label);
}



function stock_pur_items_list_cells($label, $name, $selected_id=null, 
	$all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag='M'"), 'cells'=>true));
}


//------------------------------------------------------------------------------------
function stock_purchasable_items_list($name, $selected_id=null,	
	$all_option=false, $submit_on_change=false, $all=false, $editkey=false)
{
	return stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag!= 'M'"), 
			'show_inactive'=>$all), $editkey);
}
//
//	This helper is used in PO/GRN/PI entry and supports editable descriptions.
//
function stock_purchasable_items_list_cells($label, $name, $selected_id=null,
			$all_option=false, $submit_on_change=false, $editkey=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag!= 'M'"), 
			 'editable' => 30,
			 'cells'=>true), $editkey);
}
/*
function stock_purchasable_items_list_row($label, $name, $selected_id=null,
			$all_option=false, $submit_on_change=false, $editkey=false)
{
	echo "<tr><td class='label'>$label</td>";
	stock_purchasable_items_list_cells(null, $name, $selected_id=null,
		$all_option, $submit_on_change, $editkey);
	echo "</tr>\n";
}
*/
//------------------------------------------------------------------------------------

function stock_item_types_list_row($label, $name, $selected_id=null, $enabled=true)
{
	global $stock_types;

	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $stock_types, 
		array( 
			'select_submit'=> true, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}

function stock_units_list_row($label, $name, $value=null, $enabled=true)
{
	$result = get_all_item_units();
	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	while($unit = db_fetch($result))
		$units[$unit['abbr']] = $unit['name'];

	echo array_selector($name, $value, $units, array( 'disabled' => !$enabled) );

	echo "</td></tr>\n";
}

function stock_units_list_cells($label, $name, $value=null, $enabled=true)
{
	$result = get_all_item_units();
//	echo "<tr>";
//	if ($label != null)
//		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	while($unit = db_fetch($result))
		$units[$unit['abbr']] = $unit['name'];

	echo array_selector($name, $value, $units, array( 'disabled' => !$enabled) );

	echo "</td>";
}
//------------------------------------------------------------------------------------

function tax_types_list($name, $selected_id=null, $none_option=false, $submit_on_change=false)
{
	$sql = "SELECT id, CONCAT(name, ' (',rate,'%)') as name FROM ".TB_PREF."tax_types";

	return combo_input($name, $selected_id, $sql, 'id', 'name',
	array(
		'spec_option' => $none_option,
		'spec_id' => ALL_NUMERIC,
		'select_submit'=> $submit_on_change,
		'async' => false,
	) );
}

function tax_types_list_cells($label, $name, $selected_id=null, $none_option=false,
	$submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo tax_types_list($name, $selected_id, $none_option, $submit_on_change);
	echo "</td>\n";
}

function tax_types_list_row($label, $name, $selected_id=null, $none_option=false,
	$submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	tax_types_list_cells(null, $name, $selected_id, $none_option, $submit_on_change);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function tax_groups_list($name, $selected_id=null,
	$none_option=false, $submit_on_change=false)
{
	$sql = "SELECT id, name FROM ".TB_PREF."tax_groups";

	return combo_input($name, $selected_id, $sql, 'id', 'name',
	array(
		'order' => 'id',
		'spec_option' => $none_option,
		'spec_id' => ALL_NUMERIC,
		'select_submit'=> $submit_on_change,
		'async' => false,
	) );
}

function tax_groups_list_cells($label, $name, $selected_id=null, $none_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo tax_groups_list($name, $selected_id, $none_option, $submit_on_change);
	echo "</td>\n";
}

function tax_groups_list_row($label, $name, $selected_id=null, $none_option=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	tax_groups_list_cells(null, $name, $selected_id, $none_option, $submit_on_change);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function item_tax_types_list($name, $selected_id=null)
{
	$sql ="SELECT id, name FROM ".TB_PREF."item_tax_types";
	return combo_input($name, $selected_id, $sql, 'id', 'name', array('order' => 'id') );
}

function item_tax_types_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo item_tax_types_list($name, $selected_id);
	echo "</td>\n";
}

function item_tax_types_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class='label'>$label</td>";
	item_tax_types_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function shippers_list($name, $selected_id=null)
{
	$sql = "SELECT shipper_id, shipper_name, inactive FROM ".TB_PREF."shippers";
	return combo_input($name, $selected_id, $sql, 'shipper_id', 'shipper_name', 
		array('order'=>array('shipper_name')));
}

function shippers_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo shippers_list($name, $selected_id);
	echo "</td>\n";
}

function shippers_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class='label'>$label</td>";
	shippers_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//-------------------------------------------------------------------------------------

function sales_persons_list($name, $selected_id=null, $spec_opt=false)
{
	$sql = "SELECT sm.salesman_code, emp.name, sm.inactive FROM ".TB_PREF."salesman as sm
            left join ".TB_PREF."hcm_emp as emp on emp.vendor_no=sm.salesman_code";
        
        
	return combo_input($name, $selected_id, $sql, 'sm.salesman_code', 'emp.name', 
		array('order'=>array('emp.name'),
//			'spec_option' => $spec_opt,
                         'spec_option' => $spec_opt===true ?  _("Select") : $spec_opt,
			'spec_id' => ALL_NUMERIC));
}

function sales_persons_list_cells($label, $name, $selected_id=null, $spec_opt=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>\n";
	echo sales_persons_list($name, $selected_id, $spec_opt);
	echo "</td>\n";
}

function sales_persons_list_row($label, $name, $selected_id=null, $spec_opt=false)
{
	echo "<tr><td class='label'>$label</td>";
	sales_persons_list_cells(null, $name, $selected_id, $spec_opt);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function sales_areas_list($name, $selected_id=null)
{
	$sql = "SELECT area_code, description, inactive FROM ".TB_PREF."areas";
	return combo_input($name, $selected_id, $sql, 'area_code', 'description', array());
}

function sales_areas_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo sales_areas_list($name, $selected_id);
	echo "</td>\n";
}

function sales_areas_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class='label'>$label</td>";
	sales_areas_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function sales_groups_list($name, $selected_id=null, $special_option=false)
{
	$sql = "SELECT id, description, inactive FROM ".TB_PREF."groups";
	return combo_input($name, $selected_id, $sql, 'id', 'description', array(
		'spec_option' => $special_option===true ? ' ' : $special_option,
		'order' => 'description', 'spec_id' => 0,
	));
}

function sales_groups_list_cells($label, $name, $selected_id=null, $special_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo sales_groups_list($name, $selected_id, $special_option);
	echo "</td>\n";
}

function sales_groups_list_row($label, $name, $selected_id=null, $special_option=false)
{
	echo "<tr><td class='label'>$label</td>";
	sales_groups_list_cells(null, $name, $selected_id, $special_option);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function _format_template_items($row)
{
	return ($row[0] . "&nbsp;- &nbsp;" . _("Amount") . "&nbsp;".$row[1]);
}

function templates_list($name, $selected_id=null, $special_option=false)
{
	$sql = "SELECT sorder.order_no,	Sum(line.unit_price*line.quantity*(1-line.discount_percent)) AS OrderValue
		FROM ".TB_PREF."sales_orders as sorder, ".TB_PREF."sales_order_details as line
		WHERE sorder.order_no = line.order_no AND sorder.type = 1 GROUP BY line.order_no";
	return combo_input($name, $selected_id, $sql, 'order_no', 'OrderValue', array(
		'format' => '_format_template_items',
		'spec_option' => $special_option===true ? ' ' : $special_option,
		'order' => 'order_no', 'spec_id' => 0,
	));
}

function templates_list_cells($label, $name, $selected_id=null, $special_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo templates_list($name, $selected_id, $special_option);
	echo "</td>\n";
}

function templates_list_row($label, $name, $selected_id=null, $special_option=false)
{
	echo "<tr><td class='label'>$label</td>";
	templates_list_cells(null, $name, $selected_id, $special_option);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function workorders_list($name, $selected_id=null)
{
	$sql = "SELECT id, wo_ref FROM ".TB_PREF."workorders WHERE closed=0";
	return combo_input($name, $selected_id, $sql, 'id', 'wo_ref', array());
}

function workorders_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo workorders_list($name, $selected_id);
	echo "</td>\n";
}

function workorders_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class='label'>$label</td>";
	workorders_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function payment_terms_list($name, $selected_id=null)
{
	$sql = "SELECT terms_indicator, terms, inactive FROM ".TB_PREF."payment_terms";
	return combo_input($name, $selected_id, $sql, 'terms_indicator', 'terms', array());
}

function payment_terms_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo payment_terms_list($name, $selected_id);
	echo "</td>\n";
}

function payment_terms_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class='label'>$label</td>";
	payment_terms_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function credit_status_list($name, $selected_id=null)
{
	$sql ="SELECT id, reason_description, inactive FROM ".TB_PREF."credit_status";
	return combo_input($name, $selected_id, $sql, 'id', 'reason_description', array());
}

function credit_status_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo credit_status_list($name, $selected_id);
	echo "</td>\n";
}

function credit_status_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class='label'>$label</td>";
	credit_status_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------

function sales_types_list($name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	$sql = "SELECT id, sales_type, inactive FROM ".TB_PREF."sales_types";

	return combo_input($name, $selected_id, $sql, 'id', 'sales_type',
	array(
		'spec_option' => $special_option===true ? _("All Sales Types") : $special_option,
		'spec_id' => 0,
		'select_submit'=> $submit_on_change,
	//	  'async' => false,
	) );
}

function sales_types_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo sales_types_list($name, $selected_id, $submit_on_change, $special_option);
	echo "</td>\n";
}

function sales_types_list_row($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	echo "<tr><td class='label'>$label</td>";
	sales_types_list_cells(null, $name, $selected_id, $submit_on_change, $special_option);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------

function movement_types_list($name, $selected_id=null)
{
	$sql = "SELECT id, name FROM ".TB_PREF."movement_types";
	return combo_input($name, $selected_id, $sql, 'id', 'name', array());
}

function movement_types_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo movement_types_list($name, $selected_id);
	echo "</td>\n";
}

function movement_types_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class='label'>$label</td>";
	movement_types_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------
function _format_date($row)
{
	return sql2date($row['reconciled']);
}

function bank_reconciliation_list($account, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	$sql = "SELECT reconciled, reconciled FROM ".TB_PREF."bank_trans
		WHERE bank_act=".db_escape($account)." AND reconciled IS NOT NULL
		GROUP BY reconciled";
	return combo_input($name, $selected_id, $sql, 'id', 'reconciled',
	array(
		'spec_option' => $special_option,
		'format' => '_format_date',
		'spec_id' => '',
		'select_submit'=> $submit_on_change
	) );
}

function bank_reconciliation_list_cells($label,$account, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo bank_reconciliation_list($account, $name, $selected_id, $submit_on_change, $special_option);
	echo "</td>\n";
}
/*
function bank_reconciliation_list_row($label, $account, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	echo "<tr>\n";
	bank_reconciliation_list_cells($label, $account, $name, $selected_id, $submit_on_change, $special_option);
	echo "</tr>\n";
}
*/
//-----------------------------------------------------------------------------------------------

function workcenter_list($name, $selected_id=null, $all_option=false)
{
	global $all_items;

	$sql = "SELECT id, name, inactive FROM ".TB_PREF."workcentres";

	return combo_input($name, $selected_id, $sql, 'id', 'name',
	array(
		'spec_option' =>$all_option===true ? _("All Suppliers") : $all_option,
		'spec_id' => $all_items,
	) );
}

function workcenter_list_cells($label, $name, $selected_id=null, $all_option=false)
{
	default_focus($name);
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo workcenter_list($name, $selected_id, $all_option);
	echo "</td>\n";
}

function workcenter_list_row($label, $name, $selected_id=null, $all_option=false)
{
	echo "<tr><td class='label'>$label</td>";
	workcenter_list_cells(null, $name, $selected_id, $all_option);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------

function bank_accounts_list($name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
	$sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, inactive
		FROM ".TB_PREF."bank_accounts";

	return combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
	array(
		'format' => '_format_add_curr',
		'select_submit'=> $submit_on_change,
		'spec_option' => $spec_option,
		'spec_id' => '',
		'async' => false
	) );
}

function bank_accounts_list_cells($label, $name, $selected_id=null, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo bank_accounts_list($name, $selected_id, $submit_on_change);
	echo "</td>\n";
}

function bank_accounts_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	bank_accounts_list_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}
//''''''''''''''''''''''''''''''''''''''''''

function bankk_accounts_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, $spec_option=false)
{
	$sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, inactive
		FROM ".TB_PREF."bank_accounts
                WHERE ".TB_PREF."bank_accounts.account_type !=".BT_CASH;

	return combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
	array(
		'format' => '_format_add_curr',
		'select_submit'=> $submit_on_change,
		'spec_option' => $spec_option === true ? _("All Bank Account") : $spec_option,
		'spec_id' => '',
		'async' => false
	) );
}

function bankk_accounts_list_cells($label, $name, $selected_id=null, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo bankk_accounts_list($name, $selected_id, $all_option , $submit_on_change);
	echo "</td>\n";
}

function bankk_accounts_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	bankk_accounts_list_cells(null, $name, $selected_id,$submit_on_change);
	echo "</tr>\n";
}

function bankkk_accounts_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{
	global $all_items;

	
	$sql = "SELECT id, bank_account_name, bank_curr_code, inactive
		FROM ".TB_PREF."bank_accounts
                WHERE account_type !=".BT_CASH;

	$mode = get_company_pref('no_customer_list');

	if ($editkey)
		set_editor('customer', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
	array(
	    'format' => '_format_add_curr',
	    'order' => array('id'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Bank Account") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
		_('Select customer'),
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('customer');
	return $ret;
}

function bankkk_accounts_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo bankkk_accounts_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}

function bankkk_accounts_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo bankkk_accounts_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}

//-----------------------------------------------------------------------------------------------

//function cash_accounts_list_row($label, $name, $selected_id=null, $submit_on_change=false)
//{
//	$sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, inactive
//		FROM ".TB_PREF."bank_accounts
//		WHERE ".TB_PREF."bank_accounts.account_type=".BT_CASH;
//
//	if ($label != null)
//		echo "<tr><td class='label'>$label</td>\n";
//	echo "<td>";
//	echo combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
//	array(
//		'format' => '_format_add_curr',
//		'select_submit'=> $submit_on_change,
//		'async' => true
//	) );
//	echo "</td></tr>\n";
//}
//-----------------------------------------------------------------------------------------------
//''''''''''''''''''''''''''''''''''''''''''

function cash_accounts_list($name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
	$sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, inactive
		FROM ".TB_PREF."bank_accounts
                WHERE ".TB_PREF."bank_accounts.account_type =".BT_CASH;

	return combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
	array(
		'format' => '_format_add_curr',
		'select_submit'=> $submit_on_change,
		'spec_option' => $spec_option,
		'spec_id' => '',
		'async' => false
	) );
}

function cash_accounts_list_cells($label, $name, $selected_id=null, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo cash_accounts_list($name, $selected_id, $submit_on_change);
	echo "</td>\n";
}

function cash_accounts_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	cash_accounts_list_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}
//;;;;;;;;;;;;;;;;;;;;;;;;
function pos_list_row($label, $name, $selected_id=null, $spec_option=false, $submit_on_change=false)
{
	$sql = "SELECT id, pos_name, inactive FROM ".TB_PREF."sales_pos";

	default_focus($name);
	echo '<tr>';
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo combo_input($name, $selected_id, $sql, 'id', 'pos_name',
	array(
		'select_submit'=> $submit_on_change,
		'async' => true,
		'spec_option' =>$spec_option,
		'spec_id' => -1,
		'order'=> array('pos_name')
	) );
	echo "</td></tr>\n";

}
//-----------------------------------------------------------------------------------------------
// Payment type selector for current user.
//
function sale_payment_list($name, $category, $selected_id=null, $submit_on_change=true)
{
	$sql = "SELECT terms_indicator, terms, inactive FROM ".TB_PREF."payment_terms";
	
	if ($category == PM_CASH) // only cash
			$sql .= " WHERE days_before_due=0 AND day_in_following_month=0";
	if ($category == PM_CREDIT) // only delayed payments
			$sql .= " WHERE days_before_due!=0 OR day_in_following_month!=0";

	return combo_input($name, $selected_id, $sql, 'terms_indicator', 'terms',
	array(
		'select_submit'=> $submit_on_change,
		'async' => true
	) );

}

function sale_payment_list_cells($label, $name, $category, $selected_id=null, $submit_on_change=true)
{
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo sale_payment_list($name, $category, $selected_id, $submit_on_change);

	echo "</td>\n";
}
//-----------------------------------------------------------------------------------------------

function class_list($name, $selected_id=null, $submit_on_change=false)
{
	$sql = "SELECT cid, class_name FROM ".TB_PREF."chart_class";

	return combo_input($name, $selected_id, $sql, 'cid', 'class_name',
	array(
		'select_submit'=> $submit_on_change,
		'async' => false
	) );

}

function class_list_cells($label, $name, $selected_id=null, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo class_list($name, $selected_id, $submit_on_change);
	echo "</td>\n";
}

function class_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	class_list_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------
function stock_categories_list($name, $selected_id=null, $spec_opt=false, $submit_on_change=false, $condition = false)
{
	$sql = "SELECT category_id, description, inactive FROM ".TB_PREF."stock_category c";
        
        if($condition){
            $sql .= " WHERE dflt_mb_flag='".$condition."'";
        }
if($_SESSION["wa_current_user"]->role_categories!='all' && $condition)
        $sql.=" and c.category_id in(".$_SESSION["wa_current_user"]->role_categories.")";
        
         if($_SESSION["wa_current_user"]->role_categories!='all' && !$condition)
        $sql.=" WHERE c.category_id in(".$_SESSION["wa_current_user"]->role_categories.")";
         
	return combo_input($name, $selected_id, $sql, 'category_id', 'description',
 	array('order'=>'category_id',
		'spec_option' => $spec_opt,
		'spec_id' => -1,
 		'select_submit'=> $submit_on_change,
 		'async' => true
 	));
}

function stock_categories_list_cells($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false, $condition = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo stock_categories_list($name, $selected_id, $spec_opt, $submit_on_change, $condition);
	echo "</td>\n";
}

function stock_categories_list_row($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false, $condition = false)
{
	echo "<tr><td class='label'>$label</td>";
	stock_categories_list_cells(null, $name, $selected_id, $spec_opt, $submit_on_change, $condition);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------

function gl_account_types_list($name, $selected_id=null, $all_option=false, $all=true)
{
	global $all_items;

	$sql = "SELECT id, name FROM ".TB_PREF."chart_types";

	return combo_input($name, $selected_id, $sql, 'id', 'name',
	array(
		'format' => '_format_account',
		'order' => array('class_id', 'id', 'parent'),
		'spec_option' =>$all_option,
		'spec_id' => $all_items
	) );
}

function gl_account_types_list_cells($label, $name, $selected_id=null, $all_option=false, $all=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo gl_account_types_list($name, $selected_id, $all_option, $all);
	echo "</td>\n";
}

function gl_account_types_list_row($label, $name, $selected_id=null, $all_option=false,	$all=false)
{
	echo "<tr><td class='label'>$label</td>";
	gl_account_types_list_cells(null, $name, $selected_id, $all_option,
		$all);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------



function gl_account_types_id($name, $selected_id = null, $spec_option = false, $submit_on_change = false, $show_inactive = false, $editkey = false) {
    global $all_items;

   
    $sql = "SELECT id,concat(name,' ',id) as name FROM ".TB_PREF."chart_types";   // 

    $ret = combo_input($name, $selected_id, $sql, 'id', 'name', array(
        'order' => array('name'),
        'search_box' => TRUE,
        'size' => 10,
        'spec_option' => $spec_option === true ? _("Select Order") : $spec_option,
        'spec_id' => $all_items,
        'search' => array("id", "name"),
        'search_submit' => get_company_pref('no_item_list') != 0,
        'select_submit' => $submit_on_change,
        'async' => false,
        'category' => 2,
    ));

    return $ret;
}

function gl_account_types_id_cells($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false, $show_inactive = false, $editkey = false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo gl_account_types_id($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey);
    echo "</td>\n";
}

function gl_account_types_id_rows($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false, $show_inactive = false, $editkey = false) {
    global $path_to_root;

    echo "<tr><td class='label'>$label</td><td nowrap>";
    echo gl_account_types_id($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey);
    echo "</td>\n</tr>\n";
}




//-----------------------------------------------------------------------------------------------
function gl_all_accounts_list($name, $selected_id=null, $skip_bank_accounts=false,
	$cells=false, $all_option=false, $submit_on_change=false, $all=false, $post_label=false)
{
    
	if ($skip_bank_accounts)
		$sql = "SELECT chart.account_code, chart.account_name, type.name, chart.inactive, type.id
			FROM (".TB_PREF."chart_master chart,".TB_PREF."chart_types type) "
			."LEFT JOIN ".TB_PREF."bank_accounts acc "
			."ON chart.account_code=acc.account_code
				WHERE acc.account_code  IS NULL
			AND chart.account_type=type.id";
	else
		$sql = "SELECT chart.account_code, chart.account_name, type.name, chart.inactive, type.id
			FROM ".TB_PREF."chart_master chart,".TB_PREF."chart_types type
			WHERE chart.account_type=type.id";

	return combo_input($name, $selected_id, $sql, 'chart.account_code', 'chart.account_name',
	array(
		'format' => '_format_account',
		'spec_option' => $all_option===true ?  _("Use Item Sales Accounts") : $all_option,
		'spec_id' => '',
		'type' => 2,
		'order' => array('type.class_id','type.id','account_code'),
		'search_box' => $cells,
			'search_submit' => false,
			'size' => 12,
			'max' => 10,
			'cells' => true,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'category' => 2,
		'show_inactive' => $all
	), $post_label);

}

function _format_account($row)
{
		return $row[0] .  "&nbsp;&nbsp;&nbsp;&nbsp;" . $row[1];
}

function gl_all_accounts_list_cells($label, $name, $selected_id=null, 
	$skip_bank_accounts=false, $cells=false, $all_option=false, 
	$submit_on_change=false, $all=false, $post_label = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo gl_all_accounts_list($name, $selected_id, 
		$skip_bank_accounts, $cells, $all_option, $submit_on_change, $all, $post_label);
	echo "</td>\n";
}

function gl_all_accounts_list_row($label, $name, $selected_id=null, 
	$skip_bank_accounts=false, $cells=false, $all_option=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	gl_all_accounts_list_cells(null, $name, $selected_id, 
		$skip_bank_accounts, $cells, $all_option, $submit_on_change);
	echo "</tr>\n";
}

function yesno_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	$items = array();
	$items['0'] = strlen($name_no) ? $name_no : _("No");
	$items['1'] = strlen($name_yes) ? $name_yes : _("Yes");

	return array_selector($name, $selected_id, $items, 
		array( 
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function yesno_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo yesno_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}

function yesno_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	yesno_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------------------

function languages_list($name, $selected_id=null, $all_option=false)
{
	global $installed_languages;

	$items = array();
	if ($all_option)
		$items[''] = $all_option;
	foreach ($installed_languages as $lang)
			$items[$lang['code']] = $lang['name'];
	return array_selector($name, $selected_id, $items);
}

function languages_list_cells($label, $name, $selected_id=null, $all_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo languages_list($name, $selected_id, $all_option);
	echo "</td>\n";
}

function languages_list_row($label, $name, $selected_id=null, $all_option=false)
{
	echo "<tr><td class='label'>$label</td>";
	languages_list_cells(null, $name, $selected_id, $all_option);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------------------

function bank_account_types_list($name, $selected_id=null)
{
	global $bank_account_types;

	return array_selector($name, $selected_id, $bank_account_types);
}

function bank_account_types_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo bank_account_types_list($name, $selected_id);
	echo "</td>\n";
}

function bank_account_types_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class='label'>$label</td>";
	bank_account_types_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------------------
function payment_person_types_list($name, $selected_id=null, $submit_on_change=false)
{
	global $payment_person_types;

	$items = array();
	foreach ($payment_person_types as $key=>$type)
	{
		if ($key != PT_WORKORDER)
			$items[$key] = $type;
	}		
	return array_selector($name, $selected_id, $items, 
		array( 'select_submit'=> $submit_on_change ) );
}

function payment_person_types_list_cells($label, $name, $selected_id=null, $related=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo payment_person_types_list($name, $selected_id, $related);
	echo "</td>\n";
}

function payment_person_types_list_row($label, $name, $selected_id=null, $related=null)
{
	echo "<tr><td class='label'>$label</td>";
	payment_person_types_list_cells(null, $name, $selected_id, $related);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------------------

function wo_types_list($name, $selected_id=null)
{
	global $wo_types_array;
	
	return array_selector($name, $selected_id, $wo_types_array, 
		array( 'select_submit'=> true, 'async' => true ) );
}

function wo_types_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class='label'>$label</td><td>\n";
	echo wo_types_list($name, $selected_id);
	echo "</td></tr>\n";
}

//------------------------------------------------------------------------------------------------

function dateformats_list_row($label, $name, $value=null)
{
	global $dateformats;

	echo "<tr><td class='label'>$label</td>\n<td>";
	echo array_selector( $name, $value, $dateformats );
	echo "</td></tr>\n";
}

function dateseps_list_row($label, $name, $value=null)
{
	global $dateseps;

	echo "<tr><td class='label'>$label</td>\n<td>";
	echo array_selector( $name, $value, $dateseps );
	echo "</td></tr>\n";
}

function thoseps_list_row($label, $name, $value=null)
{
	global $thoseps;

	echo "<tr><td class='label'>$label</td>\n<td>";
	echo array_selector( $name, $value, $thoseps );
	echo "</td></tr>\n";
}

function decseps_list_row($label, $name, $value=null)
{
	global $decseps;

	echo "<tr><td class='label'>$label</td>\n<td>";
	echo array_selector( $name, $value, $decseps );
	echo "</td></tr>\n";
}

function themes_list_row($label, $name, $value=null)
{
	global $path_to_root;

	$path = $path_to_root.'/themes/';
	$themes = array();
	$themedir = opendir($path);
	while(false !== ($fname = readdir($themedir)))
	{
		if($fname!='.' && $fname!='..' && $fname!='CVS' && is_dir($path.$fname))
		{
			$themes[$fname] =  $fname;
		}
	}
	ksort($themes);

	echo "<tr><td class='label'>$label</td>\n<td>";
	echo array_selector( $name, $value, $themes );
	echo "</td></tr>\n";
}

function pagesizes_list_row($label, $name, $value=null)
{
	global $pagesizes;

	$items = array();
	foreach ($pagesizes as $pz)
		$items[$pz] = $pz;

	echo "<tr><td class='label'>$label</td>\n<td>";
	echo array_selector( $name, $value, $items );
	echo "</td></tr>\n";
}

function systypes_list($name, $value=null, $spec_opt=false, $submit_on_change=false, $exclude=array())
{
	global $systypes_array;

	$systypes = array_diff_key($systypes_array, array_flip($exclude));
	return array_selector($name, $value, $systypes, 
		array( 
			'spec_option'=> $spec_opt,
			'spec_id' => ALL_NUMERIC,
			'select_submit'=> $submit_on_change,
			'async' => false,
			)
	);
}

function systypes_list_cells($label, $name, $value=null, $submit_on_change=false, $exclude=array())
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo systypes_list($name, $value, false, $submit_on_change, $exclude);
	echo "</td>\n";
}

function systypes_list_row($label, $name, $value=null, $submit_on_change=false, $exclude=array())
{
	echo "<tr><td class='label'>$label</td>";
	systypes_list_cells(null, $name, $value, $submit_on_change, $exclude);
	echo "</tr>\n";
}

function journal_types_list_cells($label, $name, $value=null, $submit_on_change=false)
{
	global $systypes_array;

	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";

	$items = $systypes_array;

	// exclude quotes, orders and dimensions
	foreach (array(ST_PURCHORDER, ST_SALESORDER, ST_DIMENSION, ST_SALESQUOTE, ST_LOCTRANSFER) as $excl)
			unset($items[$excl]);
	
	echo array_selector($name, $value, $items, 
		array( 
			'spec_option'=> _("All"),
			'spec_id' => -1,
			'select_submit'=> $submit_on_change,
			'async' => false
			)
	);
	echo "</td>\n";
}

function cust_allocations_list_cells($label, $name, $selected=null)
{
	global $all_items;

	if ($label != null)
		label_cell($label);
	echo "<td>\n";
	$allocs = array( 
		$all_items=>_("All Types"),
		'1'=> _("Sales Invoices"),
		'2'=> _("Overdue Invoices"),
		'3' => _("Payments"),
		'4' => _("Credit Notes"),
		'5' => _("Delivery Notes")
	);
	echo array_selector($name, $selected, $allocs);
	echo "</td>\n";
}

function supp_allocations_list_cell($name, $selected=null)
{
	global $all_items;

	echo "<td>\n";
	$allocs = array( 
		$all_items=>_("All Types"),
		'1'=> _("Invoices"),
		'2'=> _("Overdue Invoices"),
		'3' => _("Payments"),
		'4' => _("Credit Notes"),
		'5' => _("Overdue Credit Notes")
	);
	echo array_selector($name, $selected, $allocs);
	echo "</td>\n";
}

function supp_transactions_list_cell($name, $selected=null)
{
	global $all_items;

	echo "<td>\n";
	$allocs = array( 
		$all_items=>_("All Types"),
		'6'=>_("GRNs"),
		'1'=> _("Invoices"),
		'2'=> _("Overdue Invoices"),
		'3' => _("Payments"),
		'4' => _("Credit Notes"),
		'5' => _("Overdue Credit Notes")
	);

	echo array_selector($name, $selected, $allocs);
	echo "</td>\n";
}

function policy_list_cells($label, $name, $selected=null)
{
	if ($label != null)
		label_cell($label);
	echo "<td>\n";
	echo array_selector($name, $selected, 
				array( '' => _("Automatically put balance on back order"),
			 		'CAN' => _("Cancel any quantites not delivered")) );
	echo "</td>\n";
}

function policy_list_row($label, $name, $selected=null)
{
	echo "<tr><td class='label'>$label</td>";
	policy_list_cells(null, $name, $selected);
	echo "</tr>\n";
}

function credit_type_list_cells($label, $name, $selected=null, $submit_on_change=false)
{
	if ($label != null)
		label_cell($label);
	echo "<td>\n";
	echo array_selector($name, $selected, 
				array( 'Return' => _("Items Returned to Inventory Location"),
			 		'WriteOff' => _("Items Written Off")),
				array( 'select_submit'=> $submit_on_change ) );
	echo "</td>\n";
}

function credit_type_list_row($label, $name, $selected=null, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	credit_type_list_cells(null, $name, $selected, $submit_on_change);
	echo "</tr>\n";
}

function number_list($name, $selected, $from, $to, $no_option=false)
{
	$items = array();
	for ($i = $from; $i <= $to; $i++)
		$items[$i] = "$i";

	return array_selector($name, $selected, $items,
				array(	'spec_option' => $no_option,
						'spec_id' => ALL_NUMERIC) );
}

function number_list_cells($label, $name, $selected, $from, $to, $no_option=false)
{
	if ($label != null)
		label_cell($label);
	echo "<td>\n";
	echo number_list($name, $selected, $from, $to, $no_option);
	echo "</td>\n";
}

function number_list_row($label, $name, $selected, $from, $to, $no_option=false)
{
	echo "<tr><td class='label'>$label</td>";
	echo number_list_cells(null, $name, $selected, $from, $to, $no_option);
	echo "</tr>\n";
}

function print_profiles_list_row($label, $name, $selected_id=null, $spec_opt=false,
	$submit_on_change=true)
{
	$sql = "SELECT profile FROM ".TB_PREF."print_profiles"
		." GROUP BY profile";
	$result = db_query($sql, 'cannot get all profile names');
	$profiles = array();
	while($myrow=db_fetch($result)) {
		$profiles[$myrow['profile']] = $myrow['profile'];
	}

	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $profiles, 
		array( 'select_submit'=> $submit_on_change,
			'spec_option'=>$spec_opt,
			'spec_id' => ''
		 ));

	echo "</td></tr>\n";
}



function printers_list($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	static $printers; // query only once for page display

	if (!$printers) {
		$sql = "SELECT id, name, description FROM ".TB_PREF."printers";	
		$result = db_query($sql, 'cannot get all printers');
		$printers = array();
		while($myrow=db_fetch($result)) {
			$printers[$myrow['id']] = $myrow['name'].'&nbsp;-&nbsp;'.$myrow['description'];
		}
	}
	return array_selector($name, $selected_id, $printers, 
		array( 'select_submit'=> $submit_on_change,
			'spec_option'=>$spec_opt,
			'spec_id' => ''
		 ));
}

//------------------------------------------------------------------------------------------------

function quick_entries_list($name, $selected_id=null, $type=null, $submit_on_change=false)
{
	$where = false;
	$sql = "SELECT id, description FROM ".TB_PREF."quick_entries";
	if ($type != null)
		$sql .= " WHERE type=$type";

	return combo_input($name, $selected_id, $sql, 'id', 'description',
		array(
			'spec_id' => '',
			'order' => 'description',
			'select_submit'=> $submit_on_change,
			'async' => false
		) );

}

function quick_entries_list_cells($label, $name, $selected_id=null, $type, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";	
	echo quick_entries_list($name, $selected_id, $type, $submit_on_change);
	echo "</td>";
}

function quick_entries_list_row($label, $name, $selected_id=null, $type, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	quick_entries_list_cells(null, $name, $selected_id, $type, $submit_on_change);
	echo "</tr>\n";
}


function quick_actions_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	global $quick_actions;
	
	echo "<tr><td class='label'>$label</td><td>";
	echo array_selector($name, $selected_id, $quick_actions, 
		array( 
			'select_submit'=> $submit_on_change
		) );
	echo "</td></tr>\n";
}

function quick_entry_types_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	global $quick_entry_types;
		
	echo "<tr><td class='label'>$label</td><td>";
	echo array_selector($name, $selected_id, $quick_entry_types, 
		array( 
			'select_submit'=> $submit_on_change
			) );
	echo "</td></tr>\n";
}

function record_status_list_row($label, $name) {
	return yesno_list_row($label, $name, null, 	_('Inactive'), _('Active'));
}

function class_types_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	global $class_types;
	
	echo "<tr><td class='label'>$label</td><td>";
	echo array_selector($name, $selected_id, $class_types, 
		array( 
			'select_submit'=> $submit_on_change
		) );
	echo "</td></tr>\n";
}

//------------------------------------------------------------------------------------------------

function security_roles_list($name, $selected_id=null, $new_item=false, $submit_on_change=false,
	$show_inactive = false)
{
	global $all_items;

	$sql = "SELECT id, role, inactive FROM ".TB_PREF."security_roles";

return combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		'spec_option'=>$new_item ? _("New role") : false,
		'spec_id' => '',
		'select_submit'=> $submit_on_change,
		'show_inactive' => $show_inactive
	) );
}

function security_roles_list_cells($label, $name, $selected_id=null, $new_item=false, $submit_on_change=false,
	$show_inactive = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo security_roles_list($name, $selected_id, $new_item, $submit_on_change, $show_inactive);
	echo "</td>\n";
}

function security_roles_list_row($label, $name, $selected_id=null, $new_item=false, $submit_on_change=false,
	$show_inactive = false)
{
	echo "<tr><td class='label'>$label</td>";
	security_roles_list_cells(null, $name, $selected_id, $new_item, $submit_on_change, $show_inactive);
	echo "</tr>\n";
}

function tab_list_row($label, $name, $selected_id=null)
{
	global $installed_extensions;
	
	$tabs = array();
	foreach ($_SESSION['App']->applications as $app) {
		$tabs[$app->id] = access_string($app->name, true);
	}
	echo "<tr>\n";
	echo "<td class='label'>$label</td><td>\n";
	echo array_selector($name, $selected_id, $tabs);
	echo "</td></tr>\n";
}

//-----------------------------------------------------------------------------------------------

function tag_list($name, $height, $type, $multi=false, $all=false, $spec_opt = false)
{
	// Get tags
	global $path_to_root;
	include_once($path_to_root . "/admin/db/tags_db.inc");
	$results = get_tags($type, $all);

	while ($tag = db_fetch($results))
		$tags[$tag['id']] = $tag['name'];
	
	if (!isset($tags)) {
		$tags[''] = $all ? _("No tags defined.") : _("No active tags defined.");
		$spec_opt = false;
	}
	return array_selector($name, null, $tags,
		array(
			'multi' => $multi,
			'height' => $height,
			'spec_option'=> $spec_opt,
			'spec_id' => -1,
		) );
}

function tag_list_cells($label, $name, $height, $type, $mult=false, $all=false, $spec_opt = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>\n";
	echo tag_list($name, $height, $type, $mult, $all, $spec_opt);
	echo "</td>\n";
	
}

function tag_list_row($label, $name, $height, $type, $mult=false, $all=false, $spec_opt = false)
{
	echo "<tr><td class='label'>$label</td>";
	tag_list_cells(null, $name, $height, $type, $mult, $all, $spec_opt);
	echo "</tr>\n";	
}

//---------------------------------------------------------------------------------------------
//	List of sets of active extensions 
//
function extset_list($name, $value=null, $submit_on_change=false)
{
	global $db_connections;

	$items = array();
	foreach ($db_connections as $comp)
		$items[] = sprintf(_("Activated for '%s'"), $comp['name']);
	return array_selector( $name, $value, $items,
		array(
			'spec_option'=> _("Available and/or installed"),
			'spec_id' => -1,
			'select_submit'=> $submit_on_change,
			'async' => true
		));
}

function crm_category_types_list($name, $selected_id=null, $filter=array(), $submit_on_change=true)
{

	$sql = "SELECT id, name, type, inactive FROM ".TB_PREF."crm_categories";

	$multi = false;
	$groups = false;
	$where = array();
	if (@$filter['class']) {
		$where[] = 'type='.db_escape($filter['class']);
	} else
		$groups = 'type';
	if (@$filter['subclass']) $where[] = 'action='.db_escape($filter['subclass']);
	if (@$filter['entity']) $where[] = 'entity_id='.db_escape($filter['entity']);
	if (@$filter['multi']) { // contact category selector for person
		$multi = true;
	}

	return combo_input($name, $selected_id, $sql, 'id', 'name',
	 	array(
	 		'multi' => $multi,
	 		'height' => $multi ? 5:1,
			'category' => $groups,
	 		'select_submit'=> $submit_on_change,
 			'async' => true,
 			'where' => $where
	 	));
}

function crm_category_types_list_row($label, $name, $selected_id=null, $filter=array(), $submit_on_change=true)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo crm_category_types_list($name, $selected_id, $filter, $submit_on_change);
	echo "</td></tr>\n";
}

function payment_type_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	global $pterm_types;
	
	echo "<tr><td class='label'>$label</td><td>";
	echo array_selector($name, $selected_id, $pterm_types, 
		array( 
			'select_submit'=> $submit_on_change
		) );
	echo "</td></tr>\n";
}

function coa_list_row($label, $name, $value=null)
{
	global $path_to_root, $installed_extensions;

	$path = $path_to_root.'/sql/';
	$coas = array();
	$sqldir = opendir($path);
	while (false !== ($fname = readdir($sqldir)))
	{
		if (is_file($path.$fname) && substr($fname,-4)=='.sql' && @($fname[2] == '_'))
		{
			$ext = array_search_value($fname, $installed_extensions, 'sql');
			if ($ext!=null) {
				$descr = $ext['name'];
			} elseif ($fname == 'en_US-new.sql') { // two standard COAs
				$descr = _("Standard new company American COA (4 digit)");
			} elseif ($fname == 'en_US-demo.sql') {
				$descr = _("Standard American COA (4 digit) with demo data");
			} else
				$descr = $fname;

			$coas[$fname] =  $descr;
		}
	}
	ksort($coas);

	echo "<tr><td class='label'>$label</td>\n<td>";
	echo array_selector( $name, $value, $coas );
	echo "</td></tr>\n";
}

function payment_services($name)
{
	global $payment_services;

	$services = array_combine(array_keys($payment_services), array_keys($payment_services));

	return array_selector($name, null, $services, array(
			'spec_option'=> _("No payment Link"),
			'spec_id' => '',
		));
}

function app11_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo app_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

function app_list($name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $opts=array(), $editkey = false)
{
	global $all_items;
 $sql = "SELECT emp_code,name
			FROM
			".TB_PREF."emp where active='1'";

	set_editor('item', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'emp_code', 'name',
	array_merge(
	  array(
		'format' => '_format_stock_items',
		'spec_option' => $all_option===true ?  _("All Employee") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'search' => array("emp_code", "name"),
	    'search_submit' => get_company_pref('no_item_list')!=0,
	 	'size'=>10,
	 	'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('name','emp_code')
	     ), $opts) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}
/*-----------------------------------nayan-----------------------------------------*/


//-------------------------------------Color combo......//

function dye_color_list($name, $selected_id=null, $spec_option=array(), $submit_on_change=false, 
	$st_id, $po_id)
{ //  display_error($_POST['styleid']);
//display_error($po_id);
	global $all_items;

//$spec_option=array('default','Option','disabled');
	$sql = "SELECT id, color_name FROM ".TB_PREF."dyeing_color ";
        $query=db_query($sql);
        $colors[$spec_option[0]]=$spec_option[1];
        $disabled=$spec_option[2]?true:false;
        while($result=db_fetch($query)){
            $colors[$result['id']]=$result['color_name'];
        }
        
 echo array_selector($name, $selected_id, $colors,array('select_submit'=> $submit_on_change
     ,'disabled' => $disabled));

}

function  dye_color_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $po_id)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo  dye_color_list($name, $selected_id, $all_option, $submit_on_change,
		$po_id);
	echo "</td>\n";
}


function  dye_color_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $st_id, $po_id, $show_inactive=false, $editkey = false)
{
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo  dye_color_list($name, $selected_id, $all_option, $submit_on_change,$st_id,$po_id,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}




function sr_po_list($name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $opts=array(), $editkey = false)
{   
	global $all_items;
	$sql = "SELECT id, po_head FROM ".TB_PREF."mf_po where style_id=".db_escape($_POST['styleid']);

	if ($editkey)
		set_editor('item', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'po_head', 'id',
	array_merge(
	  array(
		'format' => '_format_stock_items',
		'spec_option' => $all_option===true ?  _("All Size") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'search' => array("id", "po_head"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>10,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('po_head','id')
	  ), $opts) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}

function sr_po_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo sr_po_list($name, $selected_id, $all_option, $submit_on_change,
		array('cells'=>true, 'show_inactive'=>$all), $editkey);
}

function sr_po_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr>\n";
	sr_po_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}


function sr_co_list($name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $opts=array(), $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, color_head FROM ".TB_PREF."mf_color";

	if ($editkey)
		set_editor('item', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'color_head', 'id',
	array_merge(
	  array(
		'format' => '_format_stock_items',
		'spec_option' => $all_option===true ?  _("All Size") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'search' => array("id", "color_head"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>10,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('color_head','id')
	  ), $opts) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}

function sr_co_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo sr_co_list($name, $selected_id, $all_option, $submit_on_change,
		array('cells'=>true, 'show_inactive'=>$all), $editkey);
}

function sr_co_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr>\n";
	sr_co_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}


/*----------------------Workcenter-------------------------*/


function work_center_list($name, $selected_id=null, $all_option=false, $submit_on_change=false,$change=false)
{
	global $all_items;
       // display_error($selected_id);
        if($change!=false){
            $condition=' and id!='.$change;            
        }else{
            
            $condition=NULL;
        }
        
	$sql = "SELECT id, wcname FROM ".TB_PREF."mf_workcenter where status=1 ".$condition;

return combo_input($name, $selected_id, $sql, 'id', 'wcname',
	array(
		//'spec_option' => $all_option === true ? _("Select Style") : $all_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change
	) );
}

function work_center_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change,$change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo work_center_list($name, $selected_id, $all_option, $submit_on_change,$change);
	echo "</td>\n";
}

function work_center_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false,$change=false)
{
	echo "<tr><td class='label'>$label</td>";
	work_center_list_cells(null, $name, $selected_id, $all_option, $submit_on_change,$change);
	echo "</tr>\n";
}
/*----------------------Workcenter-------------------------*/

/*----------------------style sheet-------------------------*/

function sr_style_list($name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $opts=array(), $editkey = false)
{   
	global $all_items;
	$sql = "SELECT id, style_head FROM ".TB_PREF."mf_style";

	if ($editkey)
		set_editor('item', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'style_head', 'id',
	array_merge(
	  array(
		'format' => '_format_stock_items',
		'spec_option' => $all_option===true ?  _("All Size") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'search' => array("id", "style_head"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>10,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('style_head','id')
	  ), $opts) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}

function sr_style_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo sr_style_list($name, $selected_id, $all_option, $submit_on_change,
		array('cells'=>true, 'show_inactive'=>$all), $editkey);
}

function sr_style_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr>\n";
	sr_style_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}
/*----------------------style sheet-------------------------*/

function style_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	global $all_items;

	$sql = "SELECT st.id, st.style_head FROM ".TB_PREF."mf_style st";

        if (isset($_POST['buyer_id']) && $_POST['buyer_id'] != ''&& $_POST['po_id'] == '')
            $sql .=" WHERE st.buyer_id=".db_escape($_POST['buyer_id']);

        if (isset($_POST['po_id']) && $_POST['po_id'] != '' && $_POST['buyer_id'] == '')
            $sql .=" LEFT JOIN " . TB_PREF . "mf_po po ON po.style_id = st.id 
            WHERE po.id=".db_escape($_POST['po_id']);
        
        if (isset($_POST['buyer_id']) && $_POST['buyer_id'] != '' && isset($_POST['po_id']) && $_POST['po_id'] != '')
            $sql .=" LEFT JOIN " . TB_PREF . "mf_po po ON po.style_id = st.id 
            WHERE st.buyer_id=".db_escape($_POST['buyer_id'])." AND po.id=".db_escape($_POST['po_id']);

return combo_input($name, $selected_id, $sql, 'id', 'style_head',
	array(
		'spec_option' => $all_option === true ? _("Select Style") : $all_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change
	) );
}

function style_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";
	echo style_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

function style_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	style_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}
/*----------------------style id-------------------------*/

function buyer_lc_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $buyer_id)
{
	global $all_items;

	//$sql = "SELECT id, style_head FROM ".TB_PREF."mf_style";
$sql = "SELECT itm.lc_no as id, itm.lc_no as lcno FROM " . TB_PREF . "mf_style st
    left join " . TB_PREF . "mf_bom bom on bom.style_id = st.id
    left join " . TB_PREF . "mf_items itm on itm.trans_no = bom.trans_no
        where st.buyer_id=" . db_escape($buyer_id).'group by itm.lc_no';
        
return combo_input($name, $selected_id, $sql, 'id', 'lcno',
	array(
		'spec_option' => $all_option === true ? _("Select Style") : $all_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change
	) );
}

function buyer_lc_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $buyer_id)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo buyer_lc_list($name, $selected_id, $all_option, $submit_on_change, $buyer_id);
	echo "</td>\n";
}

function buyer_lc_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $buyer_id)
{
	echo "<tr><td class='label'>$label</td>";
	buyer_lc_list_cells(null, $name, $selected_id, $all_option, $submit_on_change, $buyer_id);
	echo "</tr>\n";
}




function buyer_article_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	global $all_items;


$sql = "SELECT elm.lc_no as id, elm.lc_no as lcno FROM " . TB_PREF . "exp_lc_master elm";
    
 if($_POST['buyer_id'])
  $sql .= "  where elm.buyer_id =" . db_escape($_POST['buyer_id']);
 else
  $sql .= "  where elm.buyer_id > 0";
 
        
return combo_input($name, $selected_id, $sql, 'id', 'lcno',
	array(
		'spec_option' => $all_option === true ? _("Select Style") : $all_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change
	) );
}

function buyer_article_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";
	echo buyer_article_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

function buyer_article_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	buyer_article_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}


//-------------------------------------buyer combo...


function buyer_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
        //display_error($msg);
	global $all_items;

	$sql = "SELECT em.exporter_no, em.name FROM ".TB_PREF."exporter_master em";
        
        if (isset($_POST['style_id']) && $_POST['style_id'] != ''&& $_POST['po_id'] == '')
        $sql .=" LEFT JOIN " . TB_PREF . "mf_style st ON em.exporter_no = st.buyer_id 
            WHERE st.id=".db_escape($_POST['style_id']);

        if (isset($_POST['po_id']) && $_POST['po_id'] != '' && $_POST['style_id'] == '')
            $sql .=" LEFT JOIN " . TB_PREF . "mf_style st ON em.exporter_no = st.buyer_id 
                 LEFT JOIN " . TB_PREF . "mf_po po ON po.style_id = st.id 
            WHERE po.id=".db_escape($_POST['po_id']);
        
        if (isset($_POST['style_id']) && $_POST['style_id'] != '' && isset($_POST['po_id']) && $_POST['po_id'] != '')
            $sql .=" LEFT JOIN " . TB_PREF . "mf_style st ON em.exporter_no = st.buyer_id 
                 LEFT JOIN " . TB_PREF . "mf_po po ON po.style_id = st.id 
            WHERE st.id=".db_escape($_POST['style_id'])." AND po.id=".db_escape($_POST['po_id']);
        
          if (isset($_POST['bomId']) && $_POST['bomId'] != '')
        $sql .=" LEFT JOIN " . TB_PREF . "mf_style st ON em.exporter_no = st.buyer_id 
                 LEFT JOIN " . TB_PREF . "mf_bom bom ON bom.style_id = st.id 
            WHERE bom.id=".db_escape($_POST['bomId']);
        
        

return combo_input($name, $selected_id, $sql, 'exporter_no', 'name',
	array(
		'spec_option' => $all_option === true ? _("Select ") : $all_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change
	) );
}

function buyer_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";
	echo buyer_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

function buyer_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	buyer_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}


//////////---------------buyer combo end----------////////////////

/*----------------------style sheet-------------------------*/




function pro_category_list($name, $selected_id=null, $all_option=false, $submit_on_change=false,$type=null)
{
	global $all_items;

        //$maxcat = get_bom_max_category($_POST['styleid'], $_POST['po_id']);
        //$catid = $maxcat['cat'] + 1;
        
	$sql = "SELECT id, category_head FROM ".TB_PREF."mf_pro_category";
if($type){
    $sql .= " WHERE cat_type=".db_escape($type);
}
        
return combo_input($name, $selected_id, $sql, 'id', 'category_head',
	array(
		'spec_option' => $all_option === true ? _("Select Category") : $all_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change
	) );
}

function pro_category_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false,$type=null)
{
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";
	echo pro_category_list($name, $selected_id, $all_option, $submit_on_change,$type);
	echo "</td>\n";
}

function pro_category_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false,$type=null)
{
	echo "<tr><td class='label'>$label</td>";
	pro_category_list_cells(null, $name, $selected_id, $all_option, $submit_on_change,$type);
	echo "</tr>\n";
}

/*------------------------------nayan-------------------------------------------*/
function free_combo_list_cells($label, $name, $selected_id, $items, $options)
{
    //<option value="$key">$value</option>
    //$items=array($key=>$value)
    
    /*
     $options= array(		// default options
	'spec_option'=>false, 	// option text or false
	'spec_id' => 0,		// option id
	'select_submit' => false, //submit on select: true/false
	'async' => true,	// select update via ajax (true) vs _page_body reload
	'default' => '', // default value when $_POST is not set
	'multi'=>false,	// multiple select
		// search box parameters
	'height' => false,	// number of lines in select box
	'sel_hint' => null,
	'disabled' => false
);
     */
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
        echo array_selector($name, $selected_id, $items, $options);
	echo "</td>\n";
}

function free_combo_list_row($label, $name, $selected_id, $items, $options=false)
{
	echo "<tr><td class='label'>$label</td>";
	free_combo_list_cells(null, $name, $selected_id, $items, $options);
        
	echo "</tr>\n";
}




function combo_list($name, $selected_id=null, $all_option=false, $submit_on_change=false,$query=false)
{ 
  //  display_error($query);exit;
        global $all_items;
        //<option value="field1">field2</option>
        //$query=array('field1','field2','table','where','value',tablejoinarray())
        //tablejoinarray=array('join type','joinning table','primary_id','foreign_id');
        //if $query[0] is an array use $query=array(array('value','option','sql'))
        if(!is_array($query[0])){
            
            $option_value=$query[0];
            $option=$query[1];
	$sql = "SELECT DISTINCT ".$query[0].", ".$query[1]." FROM ".TB_PREF.$query[2]." m";
        
        if($query[5])
            $sql .=" ".$query[5][0]." JOIN ".TB_PREF.$query[5][1]." ON m.".$query[0]."=".TB_PREF.$query[5][1].".".$query[5][2];
        
        if($query[3])
        $sql .=" WHERE ".$query[3]."=".$query[4];
        //display_error($sql);
       }
        else{
            $sql=$query[0][2];
            $option_value=$query[0][0];
            $option=$query[0][1];
//            display_error(print_r($sql,true));
        }
        
        //exit();
return combo_input($name, $selected_id, $sql, $option_value, $option,
	array(
                'order' => $query[6],
		'spec_option' => $all_option === true ? _("Select Cost Center") : $all_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change
	) );
}

function combo_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false,$query=false)
{
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";
	echo combo_list($name, $selected_id, $all_option, $submit_on_change,$query);
	echo "</td>\n";
}

function combo_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $query=false)
{
	echo "<tr><td class='label'>$label</td>";
	combo_list_cells(null, $name, $selected_id, $all_option, $submit_on_change,$query);
	echo "</tr>\n";
}


function double_combo_list($name, $selected_id = null, $spec_option = false, $submit_on_change = false, $show_inactive = false, $editkey = false,$query=false) {
    global $all_items;

   if(!is_array($query[0])){
            
            $option_value=$query[0];
            $option=$query[1];
	$sql = "SELECT DISTINCT ".$query[0].", ".$query[1]." FROM ".TB_PREF.$query[2]." m";
        
        if($query[5])
            $sql .=" ".$query[5][0]." JOIN ".TB_PREF.$query[5][1]." ON m.".$query[0]."=".TB_PREF.$query[5][1].".".$query[5][2];
        
        if($query[3])
        $sql .=" WHERE ".$query[3]."=".$query[4];

       }
        else{
            $sql=$query[0][2];
             $option_value=$query[0][0];
            $option=$query[0][1];
        }
  

    $ret = combo_input($name, $selected_id, $sql, $option_value, $option, array(
        //'order' => array($query[6]),
        'search_box' => TRUE,
        'size' => 10,
        'spec_option' => $spec_option === true ? _("Select Order") : $spec_option,
        'spec_id' => $all_items,
        'search' => array($option_value, $option),
        'search_submit' => get_company_pref('no_item_list') != 0,
        'select_submit' => $submit_on_change,
        'async' => false,
       
    ));
//display_error($sql);
    return $ret;
    
//   }
}

function double_combo_list_cells($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false, $show_inactive = false, $editkey = false,$query=false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo double_combo_list($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey,$query);
    echo "</td>\n";
}

function double_combo_list_rows($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false, $show_inactive = false, $editkey = false,$query=false) {
    global $path_to_root;

    echo "<tr><td class='label'>$label</td><td nowrap>";
    echo double_combo_list($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey,$query);
    echo "</td>\n</tr>\n";
}

function digital_time($label,$hr_name,$mm_name,$options_hr=false,$options_mm=false,$hr_range=false){
    
    if($hr_range){
        $hr=$hr_range[0];
        $hr_end=$hr_range[1];        
        }else{
            $hr=0;
            $hr_end=23;
        }
    $hour=array();
for($hr;$hr<=$hr_end;$hr++){
    if($hr<10)
    $hour[$hr]="0".$hr;
    else
        $hour[$hr]=$hr;    
}

$mins=array();
for($mn=0;$mn<=59;$mn++){
    if($mn<10)
    $mins[$mn]="0".$mn;
    else
        $mins[$mn]=$mn;    
}
    
   return label_row($label,array_selector( $hr_name, $_POST[$hr_name], $hour,$options_hr).':'
            .array_selector( $mm_name, $_POST[$mm_name], $mins,$options_mm));
}


/*------------------------------nayan-------------------------------------------*/




function item_cost_list_row($label, $name, $selected_id=null, $enabled=true)
{
	global $cost_approved_status;

	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $cost_approved_status, 
		array( 
			'select_submit'=> false, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}


function wc_st_list_row($label, $name, $selected_id=null, $enabled=true)
{
	global $wc_status;

	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $wc_status, 
		array( 
			'select_submit'=> false, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}

//--------------------- order combo----------------

function order_type_row($label, $name, $selected_id=null, $enabled=true, $submit_on_change=false )
{
	global $order_type;

	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $order_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}



function po_order_type_row($label, $name, $selected_id=null, $enabled=true)
{
	global $po_order_type;

	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $po_order_type, 
		array( 
			'select_submit'=> false, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}


//--------------------- Contractor buyer  combo----------------

function con_buy_type_row($label, $name, $selected_id=null, $enabled=true, $submit_on_change=false )
{
	global $con_buy_type;

	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $con_buy_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}


//--------------------- Store Requisition Type ----------------

function stor_requi_type_row($label, $name, $selected_id=null, $enabled=true, $submit_on_change=false )
{
	global $stor_requi_type;

	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $stor_requi_type, 
		array( 
			'select_submit'=> $submit_on_change, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}



function lc_type_list_row($label, $name, $selected_id=null, $enabled=true, $submit_on_change=false )
{
	global $lc_type;

	echo "<tr>";
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $lc_type, 
		array( 
			'select_submit'=> $submit_on_change,  
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}


function qty_type_list_cells($label, $name, $selected_id=null, $enabled=true, $submit_on_change=false )
{
	global $qty_type;

//	echo "<tr>";
//	if ($label != null)
//		echo "<td class='label'>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $qty_type, 
		array( 
			'select_submit'=> $submit_on_change,  
			'disabled' => !$enabled) );
	echo "</td>";
}

//-------------------------------------Color combo......//
//
//function color_item_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
//	$st_id, $po_id)
//{ //  display_error($_POST['styleid']);
////display_error($po_id);
//	global $all_items;
//
//
//	$sql = "SELECT id, color_head FROM ".TB_PREF."mf_color WHERE style_id=". db_escape($_POST['styleid']);
//
//	
//	$ret = combo_input($name, $selected_id, $sql, 'id', 'color_head',
//	array(
//	
//	    'order' => array('color_head'),
//		'type' => 1,
//		'size' => 20,
//		'spec_option' => $spec_option === true ? _("All Color") : $spec_option,
//		'spec_id' => $all_items,
//		'select_submit'=> $submit_on_change,
//		'async' => false,
//		'show_inactive' => $show_inactive
//	) );
//	if ($editkey)
//		$ret .= add_edit_combo('project');
//	return $ret;
//}
//
//function  color_item_list_cells($label, $name, $selected_id=null, $all_option=false, 
//	$submit_on_change=false, $po_id)
//{
//	if ($label != null)
//		echo "<td>$label</td>\n";
//	echo "<td nowrap>";
//	echo  color_item_list($name, $selected_id, $all_option, $submit_on_change,
//		$po_id);
//	echo "</td>\n";
//}
//
//
//function  color_item_list_row($label, $name, $selected_id=null, $all_option = false, 
//	$submit_on_change=false, $st_id, $po_id, $show_inactive=false, $editkey = false)
//{
//	global $path_to_root;
//
//	echo "<tr><td class='label'>$label</td><td nowrap>";
//	echo  color_item_list($name, $selected_id, $all_option, $submit_on_change,$st_id,$po_id,
//		$show_inactive, $editkey);
//	echo "</td>\n</tr>\n";
//}





//---------------------------- for  Dyeing Machine--------------------------------------

 

function dyeing_color_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$prev_maching, $status)
{
	global $all_items;

	$sql = "SELECT DISTINCT dm.color as cid, dc.color_name as cnm  FROM ".TB_PREF."dyeing_machine dm
            LEFT JOIN ".TB_PREF."dyeing_color as dc on dc.id=dm.color
            WHERE dm.machine_name=".  db_escape($prev_maching)  ." AND dm.status=".db_escape($status)  ." AND dm.color!=0";



	$ret = combo_input($name, $selected_id, $sql, 'cid', 'cnm',
	array(
	
	    'order' => array('cnm'),
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("Select") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false
	) );

	return $ret;
}

function dyeing_color_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $prev_maching, $status)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo dyeing_color_list($name, $selected_id, $all_option, $submit_on_change,
		$prev_maching, $status);
	echo "</td>\n";
}

function dyeing_color_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $prev_maching, $status )
{
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo dyeing_color_list($name, $selected_id, $all_option, $submit_on_change,
		$prev_maching, $status );
	echo "</td>\n</tr>\n";
}






function stock_category_item_list($name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $opts=array(), $editkey = false)
{
	global $all_items;

	//$sql = "SELECT id, size_head FROM ".TB_PREF."mf_size WHERE colid=".$_POST['color_id'];
        
$sql = "SELECT stock_id, description FROM ".TB_PREF."stock_master WHERE category_id =".$_POST['category_id'];

	if ($editkey)
		set_editor('item', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'description', 'stock_id',
	array_merge(
	  array(
		'format' => '_format_stock_items',
		'spec_option' => $all_option===true ?  _("All Size") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'search' => array("stock_id", "description"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>10,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('description','stock_id')
	  ), $opts) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}

function stock_category_item_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo stock_category_item_list($name, $selected_id, $all_option, $submit_on_change,
		array('cells'=>true, 'show_inactive'=>$all), $editkey);
}

function stock_category_item_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr>\n";
	stock_category_item_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}


     
function mf_items_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $opts=array(), $editkey = false)
{
	global $all_items;

	$sql = "SELECT itm.item_code as code, itm.item_name as item FROM " . TB_PREF . "mf_bom bom 
    LEFT JOIN " . TB_PREF . "mf_items itm ON itm.trans_no = bom.trans_no
        WHERE bom.id=" . db_escape($_POST['bomId']);

        
	if ($editkey)
		set_editor('item', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'item', 'code',
	array_merge(
	  array(
		'format' => '_format_stock_items',
		'spec_option' => $all_option===true ?  _("All Size") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'search' => array("code", "item"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>10,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('code', 'item')
	  ), $opts) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
}

function mf_items_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo mf_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('cells'=>true, 'show_inactive'=>$all), $editkey);
}

function mf_items_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr>\n";
	mf_items_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}


     
function employee_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $opts=array(), $editkey = false)
{
	global $all_items;


$sql = "SELECT vendor_no, name FROM ".TB_PREF."hcm_emp";
  
        
	if ($editkey)
		set_editor('item', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'name', 'vendor_no',
	array_merge(
	  array(
		'format' => '_format_stock_items',
		'spec_option' => $all_option===true ?  _("All Employee") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'search' => array("vendor_no", "name"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>10,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('name', 'vendor_no')
	  ), $opts) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
}

function employee_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo employee_list($name, $selected_id, $all_option, $submit_on_change,
		array('cells'=>true, 'show_inactive'=>$all), $editkey);
}

function employee_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr>\n";
	employee_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}



//-------------------------------------Manager Name combo...


function manager_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	global $all_items;

	$sql = "SELECT he.vendor_no, he.name FROM ".TB_PREF."hcm_emp he";
        
        if($_POST['buyer_id']){
            $sql .= " INNER JOIN ".TB_PREF."exporter_master em ON he.vendor_no = em.merchent_id
                WHERE em.exporter_no=".db_escape($_POST['buyer_id']);
        }
        
//print_r($sql);
//display_error($sql);
return combo_input($name, $selected_id, $sql, 'vendor_no', 'name',
	array(
		'spec_option' => $all_option === true ? _("Select Manager") : $all_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change
	) );
}

function manager_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo manager_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

function manager_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	manager_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}


//-------------------------------------PO list...

function po_no_item_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false)
{
	global $all_items;
        
	$sql = "SELECT id, po_head FROM ".TB_PREF."mf_po";
        
        if($_POST['styleid']){
            $sql .= " WHERE style_id=".$_POST['styleid'];
        }
        
	$ret = combo_input($name, $selected_id, $sql, 'id', 'po_head',
	array(
	
	    'order' => array('po_head'),
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All PO") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('project');
	return $ret;
}

function  po_no_item_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false)
{
	if ($label != null)
		echo "<td class='label'>$label</td>\n";
	echo "<td nowrap>";
	echo  po_no_item_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

function  po_no_item_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo  po_no_item_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n</tr>\n";
}



//-------------------------------------Color combo...

function colour_item_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false)
{   
//    display_error($st_id);
//    display_error($po_id);
	global $all_items;
//, , 

	$sql = "SELECT id, color_head FROM ".TB_PREF."mf_color";
        
	
        $sql .= " GROUP BY color_head";
        
	$ret = combo_input($name, $selected_id, $sql, 'id', 'color_head',
	array(
	
	    'order' => array('color_head'),
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Color") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('project');
	return $ret;
}

function  colour_item_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo  colour_item_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}


function  colour_item_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo  colour_item_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}


function size_set_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false)
{   

	global $all_items;


	$sql = "SELECT id, size_head FROM ".TB_PREF."mf_size";
//        if($_POST['color_id']){
//        $sql .= "WHERE colid=".$_POST['color_id']
//                . " AND po_id=".$_POST['po_id']. " AND style_id=".$_POST['styleid'];
//        }
//	$sql = "SELECT id, color_head FROM ".TB_PREF."mf_color 
//            WHERE style_id=". db_escape($_POST['styleid'])." 
//                AND po_id=".db_escape($_POST['po_id']);
   $sql .= " GROUP BY size_head";
	
	$ret = combo_input($name, $selected_id, $sql, 'id', 'size_head',
	array(
	
	    'order' => array('size_head'),
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Color") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('project');
	return $ret;
}

function  size_set_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo size_set_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}


function  size_set_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo  size_set_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}

//=========================================  ASMA.....    ==================================
function so_list($name, $selected_id=null, $submit_on_change=false, $special_option=false)
{

$sql = "SELECT DISTINCT e.vendor_no,e.name FROM ".TB_PREF."hcm_emp e  WHERE e.is_so = '1' AND e.inactive= '0'"; // AND e.inactive = '0'
	return combo_input($name, $selected_id, $sql, 'e.vendor_no', 'e.name',
	array(
		'spec_option' => $special_option===false ? _("Sales Officer") : $special_option,
		'spec_id' => 0,
		'select_submit'=> $submit_on_change,
		  'async' => false,
	) );
}

function so_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo so_list($name, $selected_id, $submit_on_change, $special_option);
	echo "</td>\n";
}

function so_list_row($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	echo "<tr><td class='label'>$label</td>";
	so_list_cells(null, $name, $selected_id, $submit_on_change, $special_option);
	echo "</tr>\n";
}

function sr_list($name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
     //e join ".TB_PREF."salesman as sm on e.emp_code=sm.salesman_code
//    $sql = "SELECT DISTINCT e.emp_code,e.name FROM ".TB_PREF."hcm_emp e join ". TB_PREF."salesman as sm on e.emp_code=sm.salesman_code WHERE e.is_so = '2' AND e.inactive= '0'";  // AND e.inactive = '0'

$sql = "SELECT DISTINCT e.vendor_no,e.name FROM ".TB_PREF."hcm_emp e  WHERE e.is_so = '2' AND e.inactive= '0'"; // AND e.inactive = '0'

    
    return combo_input($name, $selected_id, $sql, 'e.vendor_no', 'e.name',
	array(
		'spec_option' => $special_option===false ? _("Sales Representative") : $special_option,
		'spec_id' => 0,
		'select_submit'=> $submit_on_change,
	//	  'async' => false,
	) );
}

function sr_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo sr_list($name, $selected_id, $submit_on_change, $special_option);
	echo "</td>\n";
}

function sr_list_row($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	echo "<tr><td class='label'>$label</td>";
	sr_list_cells(null, $name, $selected_id, $submit_on_change, $special_option);
	echo "</tr>\n";
}
//-----------------------------------------------------------------------------------------------------


function supplier_list_id($name, $selected_id = null, $spec_option = false, $submit_on_change = false, $show_inactive = false, $editkey = false) {
    global $all_items;

//    $sql = "SELECT supplier_id,supp_name FROM " . TB_PREF . "suppliers WHERE dimension_id=".db_escape($_SESSION["wa_current_user"]->dimension_id);
    $sql = "SELECT supplier_id,supp_name FROM " . TB_PREF . "suppliers";

    $ret = combo_input($name, $selected_id, $sql, 'supplier_id', 'supp_name', array(
        'order' => array('supp_name'),
        'search_box' => TRUE,
        'size' => 8,
        'spec_option' => $spec_option === true ? _("Select Supplier") : $spec_option,
        'spec_id' => $all_items,
        'search' => array("supplier_id", "supp_name"),
        'search_submit' => get_company_pref('no_item_list') != 0,
        'select_submit' => $submit_on_change,
        'async' => false,
        'category' => 2,
    ));

    return $ret;
}

function supplier_list_id_cells($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false, $show_inactive = false, $editkey = false) {
    if ($label != null)
        echo "<td class='label'>$label</td>\n";
    echo "<td nowrap>";
    echo supplier_list_id($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey);
    echo "</td>\n";
}

function supplier_list_id_rows($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false, $show_inactive = false, $editkey = false) {
    global $path_to_root;

    echo "<tr><td class='label'>$label</td><td nowrap>";
    echo supplier_list_id($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey);
    echo "</td>\n</tr>\n";
}

function stock_purchable_items_cells($label, $name, $selected_id=null, 
	$all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag='B'"), 'cells'=>true));
}

//-----------------------------session wise combos---------------------

function session_stock_items_list($name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $opts=array(), $editkey = false)
{
//    display_error($_SESSION["wa_current_user"]->role_categories['cat_id']);
	global $all_items;
//        display_error(print_r($_SESSION["wa_current_user"]->role_categories['cat_id'],true));
	$sql = "SELECT stock_id, s.description, c.description, s.inactive, s.editable
			FROM ".TB_PREF."stock_master s,".TB_PREF."stock_category c 
                            WHERE s.category_id=c.category_id ";
         if($_SESSION["wa_current_user"]->role_categories!='all')
        $sql.=" and c.category_id in(".$_SESSION["wa_current_user"]->role_categories.")";

	if ($editkey)
		set_editor('item', $name, $editkey);

     //   $sql .=array('new'=>'New Item');
	$ret = combo_input($name, $selected_id, $sql, 'stock_id', 's.description',
	array_merge(
	  array(
		'format' => '_format_stock_items',
		'spec_option' => $all_option===true ?  _("All Items") : $all_option,
		'spec_id' => $all_items,
		'search_box' => true,
		'search' => array("stock_id", "c.description","s.description"),
		'search_submit' => get_company_pref('no_item_list')!=0,
		'size'=>15,
		'select_submit'=> $submit_on_change,
		'category' => 2,
		'order' => array('c.description','stock_id')
	  ), $opts) );
	if ($editkey)
		$ret .= add_edit_combo('item');		
	return $ret;
	  
}

function session_stock_costable_items_list_cells($label, $name, $selected_id=null, 
	$all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo session_stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag!='D'"), 'cells'=>true));
}

function combo_list_row1($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $query=false)
{
	echo "<td class='label'>$label";
	combo_list_cells(null, $name, $selected_id, $all_option, $submit_on_change,$query);
	echo "</td>\n";
}
function combo_input_modified($name, $selected_id, $sql, $valfield, $namefield,
	$options=null)
{
global $Ajax;
//display_error(print_r($name,true)); 
//display_error(print_r($selected_id,true)); 
//display_error(print_r($sql,true)); 
//display_error(print_r($valfield,true)); 
//display_error(print_r($namefield,true)); 
//display_error(print_r($options,true)); 
$opts = array(		// default options
	'where'=> array(),		// additional constraints
	'order' => $namefield,	// list sort order
		// special option parameters
	'spec_option'=>false, 	// option text or false
	'spec_id' => 0,		// option id
		// submit on select parameters
	'default' => '', // default value when $_POST is not set
	'multi' => false,	// multiple select
	'select_submit' => false, //submit on select: true/false
	'async' => true,	// select update via ajax (true) vs _page_body reload
		// search box parameters
	'sel_hint' => null,
	'search_box' => false, 	// name or true/false
	'type' => 0,	// type of extended selector:
		// 0 - with (optional) visible search box, search by fragment inside id
		// 1 - with hidden search box, search by option text
		// 2 - with (optional) visible search box, search by fragment at the start of id
		// 3 - TODO reverse: box with hidden selector available via enter; this
		// would be convenient for optional ad hoc adding of new item
	'search_submit' => true, //search submit button: true/false
	'size' => 8,	// size and max of box tag
	'max' => 50,
	'height' => false,	// number of lines in select box
	'cells' => false,	// combo displayed as 2 <td></td> cells
	'search' => array(), // sql field names to search
	'format' => null, 	 // format functions for regular options
	'disabled' => false,
	'box_hint' => null, // box/selectors hints; null = std see below
	'category' => false, // category column name or false
	'show_inactive' => false, // show inactive records. 
	'editable' => false // false, or length of editable entry field
);
// ------ merge options with defaults ----------
	if($options != null)
		$opts = array_merge($opts, $options);
	if (!is_array($opts['where']))  $opts['where'] = array($opts['where']);

	$search_box = $opts['search_box']===true ? '_'.$name.'_edit' : $opts['search_box'];
	// select content filtered by search field:
	$search_submit = $opts['search_submit']===true ? '_'.$name.'_button' : $opts['search_submit'];
	// select set by select content field
	$search_button = $opts['editable'] ? '_'.$name.'_button' : ($search_box ? $search_submit : false);

	$select_submit =  $opts['select_submit'];
	$spec_id = $opts['spec_id'];
	$spec_option = $opts['spec_option'];
	if ($opts['type'] == 0) {
		$by_id = true;
		$class = 'combo';
	} elseif($opts['type'] == 1) {
		$by_id = false;
		$class = 'combo2';
	} else {
		$by_id = true;
		$class = 'combo3';
	}

	$disabled = $opts['disabled'] ? "disabled" : '';
	$multi = $opts['multi'];
	
	if(!count($opts['search'])) {
		$opts['search'] = array($by_id ? $valfield : $namefield);
	}
	if ($opts['sel_hint'] === null) 
		$opts['sel_hint'] = $by_id || $search_box==false ?
			'' : _('Press Space tab for search pattern entry');

	if ($opts['box_hint'] === null)
		$opts['box_hint'] = $search_box && $search_submit != false ?
			($by_id ? _('Enter code fragment to search or * for all')
			: _('Enter description fragment to search or * for all')) :'';

	if ($selected_id == null) {
		$selected_id = get_post($name, (string)$opts['default']);
	}
	if(!is_array($selected_id))
		$selected_id = array((string)$selected_id); // code is generalized for multiple selection support

	$txt = get_post($search_box);
	$rel = '';
	$limit = '';
	if (isset($_POST['_'.$name.'_update'])) { // select list or search box change
		if ($by_id) $txt = $_POST[$name];

		if (!$opts['async'])
			$Ajax->activate('_page_body');
		else
			$Ajax->activate($name);
	}
	if (isset($_POST[$search_button])) {
		if (!$opts['async'])
			$Ajax->activate('_page_body');
		else
			$Ajax->activate($name);
	}
	if ($search_box) {
		// search related sql modifications

		$rel = "rel='$search_box'"; // set relation to list
		if ($opts['search_submit']) {
			if (isset($_POST[$search_button])) {
				$selected_id = array(); // ignore selected_id while search
				if (!$opts['async'])
					$Ajax->activate('_page_body');
				else
					$Ajax->activate($name);
			}
			if ($txt == '') {
				if ($spec_option === false && $selected_id == array())
					$limit = ' LIMIT 1';
				else
					$opts['where'][] = $valfield . "=". db_escape(get_post($name, $spec_id));
			}
			else
				if ($txt != '*') {

					foreach($opts['search'] as $i=> $s)
						$opts['search'][$i] = $s . " LIKE "
							.db_escape(($class=='combo3' ? '' : '%').$txt.'%');
					$opts['where'][] = '('. implode($opts['search'], ' OR ') . ')';
                                       
				}
                                
		}
	}

	// sql completion
	if (count($opts['where'])) {
		$where = strpos($sql, 'WHERE')==false ? ' WHERE ':' AND ';
		$where .= '('. implode($opts['where'], ' AND ') . ')';
		$group_pos = strpos($sql, 'GROUP BY');
		if ($group_pos) {
			$group = substr($sql, $group_pos);
			$sql = substr($sql, 0, $group_pos) . $where.' '.$group;
		} else {
			$sql .= $where;
		}
	}
	if ($opts['order'] != false) {
		if (!is_array($opts['order']))
			$opts['order'] = array($opts['order']);
		$sql .= ' ORDER BY '.implode(',',$opts['order']);
	}

	$sql .= $limit;
	// ------ make selector ----------
	$selector = $first_opt = '';
	$first_id = false;
	$found = false;
	$lastcat = null;
	$edit = false;
//if($name=='stock_id')	display_notification('<pre>'.print_r($_POST, true).'</pre>');
//if($name=='curr_default') display_notification($opts['search_submit']);
	if($result = db_query($sql)) {
		while ($contact_row = db_fetch($result)) {
			$value = $contact_row[0];
			$descr = $opts['format']==null ?  $contact_row[1] :
				call_user_func($opts['format'], $contact_row);
			$sel = '';
 		 	if (get_post($search_button) && ($txt == $value)) {
 		 		$selected_id[] = $value;
 		 	}

 		 	if (in_array((string)$value, $selected_id, true)) {
				$sel = 'selected';
				$found = $value;
				$edit = $opts['editable'] && $contact_row['editable'] 
					&& (@$_POST[$search_box] == $value)
					? $contact_row[1] : false; // get non-formatted description
				if ($edit)
					break;	// selected field is editable - abandon list construction
			}
			// show selected option even if inactive 
			if (!$opts['show_inactive'] && @$contact_row['inactive'] && $sel==='') {
				continue;
			} else 
				$optclass = @$contact_row['inactive'] ? "class='inactive'" : '';

			if ($first_id === false) {
				$first_id = $value;
				$first_opt = $descr;
			}
			$cat = $contact_row[$opts['category']];
			if ($opts['category'] !== false && $cat != $lastcat){
				$selector .= "<optgroup label='".$cat."'>\n";
				$lastcat = $cat;
			}
			$selector .= "<option $sel $optclass value='$value'>$descr</option>\n";
		}
		db_free_result($result);
	}

	// Prepend special option.
	if ($spec_option !== false) { // if special option used - add it
		$first_id = $spec_id;
		$first_opt = $spec_option;
//	}
//	if($first_id !== false) {
		$sel = $found===false ? 'selected' : '';
		$optclass = @$contact_row['inactive'] ? "class='inactive'" : '';
		$selector = "<option $sel value='$first_id'>$first_opt</option>\n"
			. $selector;
	}

	if ($found===false) {
		$selected_id = array($first_id);
	}
	
	$_POST[$name] = $multi ? $selected_id : $selected_id[0];

	$selector = "<select autocomplete='off' ".($multi ? "multiple" : '')
		. ($opts['height']!==false ? ' size="'.$opts['height'].'"' : '')
		. "$disabled name='$name".($multi ? '[]':'')."' class='$class' title='"
		. $opts['sel_hint']."' $rel>".$selector."</select>\n";

	if ($by_id && ($search_box != false || $opts['editable']) ) {
		// on first display show selector list
		if (isset($_POST[$search_box]) && $opts['editable'] && $edit) {
			$selector = "<input type='hidden' name='$name' value='".$_POST[$name]."'>"
			."<input type='text' $disabled name='{$name}_text' id='{$name}_text' size='".
				$opts['editable']."' maxlength='".$opts['max']."' $rel value='$edit'>\n";
				set_focus($name.'_text'); // prevent lost focus
		} else if (get_post($search_submit ? $search_submit : "_{$name}_button"))
			set_focus($name); // prevent lost focus
		if (!$opts['editable'])
	 		$txt = $found;
		$Ajax->addUpdate($name, $search_box, $txt ? $txt : '');
	}

	$Ajax->addUpdate($name, "_{$name}_sel", $selector);

	// span for select list/input field update
	$selector = "<span id='_{$name}_sel'>".$selector."</span>\n";

	 // if selectable or editable list is used - add select button
	if ($select_submit != false || $search_button) {
		global $_select_button;
	// button class selects form reload/ajax selector update
		$selector .= sprintf($_select_button, $disabled, user_theme(),
			(fallback_mode() ? '' : 'display:none;'),
			 '_'.$name.'_update')."\n";
	}
// ------ make combo ----------
	$edit_entry = '';
	if ($search_box != false) {
            
		$edit_entry = "<input $disabled type='text' name='$search_box' id='$search_box' size='".
			$opts['size']."' maxlength='".$opts['max'].
			"' value='$txt' class='$class' rel='$name' autocomplete='off' title='"
			.$opts['box_hint']."'"
			.(!fallback_mode() && !$by_id ? " style=display:none;":'')
			.">\n";
		if ($search_submit != false || $opts['editable']) {
			global $_search_button;
			$edit_entry .= sprintf($_search_button, $disabled, user_theme(),
				(fallback_mode() ? '' : 'display:none;'),
				$search_submit ? $search_submit : "_{$name}_button")."\n";
		}
	}
	default_focus(($search_box && $by_id) ? $search_box : $name);
        display_error($txt);

	if ($search_box && $opts['cells'])
		$str = ($edit_entry!='' ? "<td>$edit_entry</td>" : '')."<td>$selector</td>";
	else
		$str = $edit_entry.$selector;
	return $str;
}

function digital_time_hcm1($hr_name,$mm_name,$options_hr=false,$options_mm=false,$hr_range=false){
    
    if($hr_range){
        $hr=$hr_range[0];
        $hr_end=$hr_range[1];        
        }else{
            $hr=0;
            $hr_end=23;
        }
    $hour=array();
for($hr;$hr<=$hr_end;$hr++){
    if($hr<10){
        $hr="0".$hr;
    $hour[$hr]=$hr;
    }
    else
        $hour[$hr]=$hr;    
}

$mins=array();
for($mn=0;$mn<=59;$mn++){
    if($mn<10){
        $mn="0".$mn;
    $mins[$mn]=$mn;
    }
    else
        $mins[$mn]=$mn;    
}
    
   return array_selector( $hr_name, $_POST[$hr_name], $hour,$options_hr).':'
            .array_selector( $mm_name, $_POST[$mm_name], $mins,$options_mm);
}


function double_combo_list_nayan($name, $selected_id = null, $spec_option = false, $submit_on_change = false, $show_inactive = false, $editkey = false,$query=false) {
    global $all_items;

   if(!is_array($query[0])){
            
            $option_value=$query[0];
            $option=$query[1];
	$sql = "SELECT DISTINCT ".$query[0].", ".$query[1]." FROM ".TB_PREF.$query[2]." m";
        
        if($query[5])
            $sql .=" ".$query[5][0]." JOIN ".TB_PREF.$query[5][1]." ON m.".$query[0]."=".TB_PREF.$query[5][1].".".$query[5][2];
        
        if($query[3])
        $sql .=" WHERE ".$query[3]."=".$query[4];

       }
        else{
            $sql=$query[0][2];
             $option_value=$query[0][0];
            $option=$query[0][1];
        }
  

    $ret = combo_input_modified($name, $selected_id, $sql, $option_value, $option, array(
        //'order' => array($query[6]),
        'search_box' => TRUE,
        'size' => 10,
        'spec_option' => $spec_option === true ? _("Select Order") : $spec_option,
        'spec_id' => $all_items,
        'search' => array($option_value, $option),
        'search_submit' => get_company_pref('no_item_list') != 0,
        'select_submit' => $submit_on_change,
        'async' => false,
        
       
    ));
//display_error($sql);
    return $ret;
    
//   }
}
function double_combo_list_cells_nayan($label, $name, $selected_id = null, $all_option = false, $submit_on_change = false, $show_inactive = false, $editkey = false,$query=false) {
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo double_combo_list_nayan($name, $selected_id, $all_option, $submit_on_change, $show_inactive, $editkey,$query);
    echo "</td>\n";
}
//used syear

function get_current_schoolyear(){
	$date = date2sql(Today());
	$sql = "SELECT id,begin FROM ".TB_PREF."fiscal_year WHERE '$date' >= begin AND '$date' <= end";
	
	$result = db_fetch(db_query($sql, "could not get all sms_school years"));
        return $result['id'];
}
function get_next_schoolyear(){
	
        $ndate = date('Y-m-d',date(strtotime("+1 year")));
        
	$sql = "SELECT id,begin FROM ".TB_PREF."fiscal_year WHERE '$ndate' >= begin AND '$ndate' <= end";
	
	$result = db_fetch(db_query($sql, "could not get all sms_school years"));
        return $result['id'];
}


