<?php
$page_security = 'SS_SMS_APLCNT_PAYMNT_RCV';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/sms/includes/ui/sms_ui.inc");
include_once($path_to_root . "/sms/includes/db/sms_db.php");
//include_once($path_to_root . "/sms/includes/db/applicant_db.inc");

page(_("Mobile No Entry"));

simple_page_mode(true);
//----------------------------------------------------------------------------------
$Ajax;
if($_POST['id'])
    $selected_id=$_POST['id'];

if(list_updated('zone'))
$Ajax->activate('_page_body');

if(list_updated('area'))
$Ajax->activate('_page_body');

if(list_updated('district'))
$Ajax->activate('_page_body');

if(list_updated('thana'))
$Ajax->activate('_page_body');
//-------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['name']) == 0) 
	{
		$input_error = 1;
		display_error(_("Name cannot be empty."));
		set_focus('name');
	}

        if (strlen($_POST['address']) == 0) 
	{
		$input_error = 1;
		display_error(_("Address cannot be empty."));
		set_focus('address');
	}
        
        if (strlen($_POST['zone']) == 0) 
	{
		$input_error = 1;
		display_error(_("Zone cannot be empty."));
		set_focus('zone');
	}
        
        if (strlen($_POST['thana']) == 0) 
	{
		$input_error = 1;
		display_error(_("Thana cannot be empty."));
		set_focus('thana');
	}
        
        if (strlen($_POST['district']) == 0) 
	{
		$input_error = 1;
		display_error(_("District cannot be empty."));
		set_focus('district');
	}
        
        if (strlen($_POST['mobile']) == 0) 
	{
		$input_error = 1;
		display_error(_("Mobile cannot be empty."));
		set_focus('mobile');
	}
                if (strlen(trim($_POST['mobile'])) != 11 ) 
	{
		$input_error = 1;
		display_error(_("Enter a Valid Mobile Number"));
		set_focus('mobile');
	}
        $pattern="/^01[5-9]{1}[0-9]{1}[0-9]{1}[0-9]{6}/";
        $subject=$_POST['mobile'];
        if(!preg_match($pattern, $subject)){
         $input_error = 1;
    display_error(_("Enter a Valid Mobile Number"));
		set_focus('mobile');
        }
       
           if (strlen($_POST['area']) == 0) 
	{
		$input_error = 1;
		display_error(_("Area cannot be empty."));
		set_focus('area');
	}
      
        if($input_error != 1)
        {
        
    	if ($selected_id != -1) 
    	{ 
            
		    update_mobile_no($selected_id,$_POST['name'],$_POST['address'],$_POST['zone'],$_POST['thana'],$_POST['district'],$_POST['mobile'],$_POST['area']);    		
                   
                    display_notification(_('Selected data has been updated'));
    	} 
    	else 
    	{
		    add_mobile_no($_POST['name'],$_POST['address'],$_POST['zone'],$_POST['thana'],$_POST['district'],$_POST['mobile'],$_POST['area']);
                    display_notification(_('Mobile no  has been added'));
    	}
		$Mode = 'RESET';
        }         
	
}

//---------------------------------------------------------------------------------- 

if ($Mode == 'Delete')
{

	delete_data($selected_id,'sms_mobile');
		display_notification(_('Selected data has been deleted'));
	
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//----------------------------------------------------------------------------------

$sql = get_data_for_mobile_entry();
$result = db_query($sql, "could not get exam name");
//display_error('');

start_form();
start_table(TABLESTYLE, "width=60%");
$th = array(_('Serial No'), _('Name'),_('Address'),_('Zone'),('Attach Fire Station'),('District'),('Mobile No'),'','');
table_header($th);
$k = 0; //row colour counter
$sl = 1;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
       // display_error($myrow['name']);
       label_cell($sl);
	label_cell($myrow["name"]);
        label_cell($myrow["address"]);
        label_cell($myrow["zone_name"]);
        //label_cell($myrow["grade_type"]);
        label_cell($myrow["thana_n"]);
        label_cell($myrow["district_n"]);
        label_cell($myrow["mobile_no"]);
        
 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
	end_row();
        $sl++;
}


end_table();
end_form();
echo '<br>';
//----------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
    //display_error($myrow["school_year"]);
 	if ($Mode == 'Edit') {
                $myrow = data_retrieve("sms_mobile", "id", $selected_id);
                display_error($myrow["zone"]);
		$_POST['name'] = $myrow["name"];               
		$_POST['address']  = $myrow["address"];
                $_POST['zone']  = $myrow["zone"]; 
                $_POST['area']  =  $myrow["area"];
                $_POST['district']  = $myrow["district"]; 
                $_POST['thana']  = $myrow["thana"];
                $_POST['mobile']  = $myrow["mobile_no"]; 
                         
	}
	hidden('id', $selected_id);
}


text_row(_('Name:'), 'name', null, 35);

textarea_row(_('Address'), 'address', null, 25, 2);

$query=array('id','zone_name','sms_zone_setup');
combo_list_row(_("Zone:"),'zone', $_POST['zone'],'Select Zone', true, $query);

$query=array('id','area','sms_area','zone',$_POST['zone']);
combo_list_row(_("Area:"),'area', $_POST['area'],'select area', true, $query);

$query=array('id','district_n','sms_district','area_id',$_POST['area']);
combo_list_row(_("District:"),'district', $_POST['district'], 'Select district', true, $query);

$query=array('id','thana_n','sms_thana_setup','district_n',$_POST['district']);
combo_list_row(_("Thana:"),'thana', $_POST['thana'], 'Select thana', true, $query);

text_row(_('Mobile No:'),'mobile', null, 35);




end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

end_form();

end_page();
?>
