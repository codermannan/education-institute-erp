<?php
/**********************************************************************
    
***********************************************************************/
function get_gl_type_no($type){
	$sql = "SELECT MAX(type_no) FROM ".TB_PREF."gl_trans WHERE type=".db_escape($type)." ORDER BY type DESC";
        
	$result = db_fetch(db_query($sql,"could not retreive the account name for $type"));
        
	return $result['MAX(type_no)']+1;
	
}

function get_gl_ref_no($type){
	$sql = "SELECT MAX(id) FROM ".TB_PREF."refs WHERE type=".db_escape($type)." ORDER BY type DESC";
        
	$result = db_fetch(db_query($sql,"could not retreive the account name for $type"));
        
	return $result['MAX(id)']+1;
	
}
