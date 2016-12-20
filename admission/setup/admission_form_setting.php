<?php
/**********************************************************************
    
***********************************************************************/
$page_security = 'SS_SMS_STDNT_HEAD_NAME_SETNG';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_("admission Settings Entry"));

simple_page_mode(true);
$syear = get_current_schoolyear();
//----------------------------------------------------------------------------------
if($_POST['id'])
    
    
    $selected_id=$_POST['id'];

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

        if (strlen($_POST['form_price']) == 0) 
	{
		$input_error = 1;
		display_error(_("price cannot be empty."));
		set_focus('form_price');
	}
 
    	if ($selected_id != -1) 
    	{ 
               
	       update_price_settings($selected_id,$syear, $_POST['form_price'] );    		
	       display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{
            $re= "SELECT school_year, price FROM " . TB_PREF . "sms_form_price_setting WHERE"
                    . " school_year=". db_escape($syear) 
                    . "AND price=" . db_escape($_POST['form_price']) ;
            
           $tr= db_query($re);
           $final = db_fetch($tr);
           
           if($syear == $final['school_year'] && $_POST['form_price'] == $final['price'])
           {
         
                display_notification('Exist already');
           }
         else
         {
           add_price_settings($syear,$_POST['form_price']);
                   
			display_notification(_('price  has been added'));
         }
    	}
		$Mode = 'RESET';
	
}
 
 
//---------------------------------------------------------------------------------- 
 
              
if ($Mode == 'Delete')
{
    if ($selected_id)
        {
       
            delete_price($selected_id)  ;           
            display_notification(_('Selected data has been deleted'));
        }        
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    $selected_id = -1;
    unset($_POST);
}
//----------------------------------------------------------------------------------
function edit_link($row) {
    
        return "<center>".button("Edit".$row["id"], _("Edit"), _("Edit"), ICON_EDIT).
        "</center>";

}

function delete_link($row) {
    submit_js_confirm("Delete".$row['id'],
                    sprintf(_("Are you sure you want to delete ?")));
	
     return  "<center>".button("Delete".$row["id"], _("Delete"), _("Delete"), ICON_DELETE).
        "</center>";
}
function school_year($row){
    
    $fyear =  data_retrieve('fiscal_year','id', $row['school_year']);
    $dpar  = date_parse($fyear['begin']);
    
    return $dpar['year'];
}
 
 
//............................................................

if (!@$_GET['popup'])
    start_form();
start_table(TABLESTYLE_NOBORDER);

//start_row();
//
// $query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
//       ORDER BY class_name ASC'));
//combo_list_cells(_("Class:"),'class', null, 'Select Class', false, $query);
//
//submit_cells('Search', _("Search"), '', '', 'default');
//end_row();

end_table();
br();

$ec=sms_price_setting();

$cols = array(
    _("#")=>array('align'=>'center'),
    _("School Year")=>array('fun'=>'school_year','align'=>'center'),
    _("Form Price")=>array('align'=>'center'),
    array('insert'=>true, 'fun'=>'edit_link', 'align'=>'center'),
    array('insert'=>true, 'fun'=>'delete_link', 'align'=>'center')
);
$table = & new_db_pager('sms_form_price_setting', $ec, $cols,null);

$table->width = "60%";

display_db_pager($table);
echo '<br>';

//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
            
                $myrow = data_retrieve("sms_form_price_setting", "id", $selected_id);
		$_POST['form_price']  = $myrow["price"];
             
          
	}
	hidden('selected_id', $selected_id);
}

 
amount_cells(_("Form Price:"), 'form_price');

 
 

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);


  if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>

