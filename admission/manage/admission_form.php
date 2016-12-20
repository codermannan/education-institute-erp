<?php
/**********************************************************************
 developed by M007
***********************************************************************/
$page_security = 'SS_SMS_ADMNSN_PRCS';
$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "Admission Form"), @$_REQUEST['popup'], false, "", $js); 


if(list_updated('grelation'))
$Ajax->activate('_page_body');

if (list_updated('datasearch'))
    $Ajax->activate('_page_body');

 if(list_updated('class'))
$Ajax->activate('_page_body');
  
$user = $_SESSION['wa_current_user']->username;
$syear = get_current_schoolyear();

$new_item = get_post('pro_id')=='' || get_post('cancel') || get_post('clone');
//--------------------------------------------------------------------------------------------

if (list_updated('pro_id')) {
	$_POST['NewProID'] = $pro_id = get_post('pro_id');
        clear_data();
	$Ajax->activate('details');
	$Ajax->activate('controls');
    
}

$upload_plan_img = "";
if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') 
{
	$pro_id = $_POST['NewProID'];
	$result = $_FILES['pic']['error'];
 	$upload_plan_img = 'Yes'; 
	$filename = company_path().'/stpicture/';
       
	if (!file_exists($filename))
	{
	    mkdir($filename);
	}
        if($_FILES['pic']['type'] != "application/pdf"){
	$filename .= "/".$pro_id.".jpg";
	
	if ((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false)
		$imagetype = $type;
	else
		$imagetype = false;
	if ($imagetype != IMAGETYPE_GIF && $imagetype != IMAGETYPE_JPEG && $imagetype != IMAGETYPE_PNG)
	{	//File type Check
                 
            display_warning( _('Only graphics files can be uploaded'));
            $upload_plan_img ='No';
               
	}	
	elseif (@strtoupper(substr(trim($_FILES['pic']['name']), @in_array(strlen($_FILES['pic']['name']) - 3)), array('JPG','PNG','GIF')))
	{
            display_warning(_('Only graphics files are supported - a file extension of .jpg, .png or .gif is expected'));
            $upload_plan_img ='No';
	} 
	elseif ( $_FILES['pic']['size'] > ($max_image_size * 1024)) 
	{ //File Size Check
		display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
		$upload_plan_img ='No';
	} 
	elseif (file_exists($filename))
	{
	    $result = unlink($filename);
            if (!$result) 
                {
                    display_error(_('The existing image could not be removed'));
                    $upload_plan_img ='No';
                }
	}
	
	if ($upload_plan_img == 'Yes')
	{
		$result  =  move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
	}
        }
        else{
            $filename .= "/".$pro_id.".pdf";
            $result  =  move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
         
        }
        
	$Ajax->activate('details');
}


function clear_data()
{
	unset($_POST['f_name']);
	unset($_POST['m_name']);
	unset($_POST['l_name']);
	unset($_POST['class_id']);
	unset($_POST['group_id']);
	unset($_POST['dob']);
	unset($_POST['gender']);
	unset($_POST['blood_group']);
	unset($_POST['pre_address']);
        unset($_POST['per_address']);
        unset($_POST['city']);
        unset($_POST['country']);
        unset($_POST['siblings']);
        unset($_POST['birth_place']);
	unset($_POST['phone']);
        unset($_POST['mobile']);
        unset($_POST['email']);
        unset($_POST['nationality']);
        unset($_POST['mother_tongue']);
	unset($_POST['religion']);
        unset($_POST['hobby']);
        unset($_POST['exca']);
        unset($_POST['pic']);
        unset($_POST['inst_name']);
        unset($_POST['class']);
        unset($_POST['st_group']);
        unset($_POST['year']);
        unset($_POST['grade']);       

}
//--------------------------------------------------------------------------------------------
if (isset($_POST['addupdate'])) 
{

	$input_error = 0;
	if ($upload_plan_img == 'No')
		$input_error = 1;
        
        
	if (strlen($_POST['NewProID']) == 0){
		$input_error = 1;
		display_error( _('The applicant id  must be entered.'));
		set_focus('NewProID');
	} 
        elseif (strlen($_POST['f_name']) == 0){
		$input_error = 1;
		display_error( _('Applicant first name must be entered.'));
		set_focus('f_name');
	} 
        elseif (strlen($_POST['class_id']) == 0){
		$input_error = 1;
		display_error( _('Applicant class must be selected.'));
		set_focus('class_id');
	}
	elseif (strlen($_POST['dob']) == 0){
		$input_error = 1;
		display_error( _('Applicant date of birth must be selected'));
		set_focus('dob');
	}
        elseif (strlen($_POST['gender']) == 0){
		$input_error = 1;
		display_error( _('Applicant gender must be selected'));
		set_focus('gender');
	}
        elseif (strlen($_POST['blood_group']) == 0){
		$input_error = 1;
		display_error( _('Applicant blood group must be selected'));
		set_focus('blood_group');
	}
         elseif (strlen($_POST['pre_address']) == 0){
		$input_error = 1;
		display_error( _('Applicant present address must be entered.'));
		set_focus('pre_address');
	}
        elseif (strlen($_POST['per_address']) == 0){
		$input_error = 1;
		display_error( _('Applicant permanent address must be entered.'));
		set_focus('per_address');
	}
        elseif (strlen($_POST['studentcat']) == 0){
		$input_error = 1;
		display_error( _('Student Category must be selected.'));
		set_focus('studentcat');
	}
         elseif (strlen($_POST['mobile']) == 0 ) {
		$input_error = 1;
		display_error( _('Applicant mobile must be entered.'));
		set_focus('mobile');
	}
        else if(strlen($_POST['phone'])==0){
            
            $input_error=1;
            display_error(_('Phone Number must be entered.'));
            set_focus('phone');
            
        }
         elseif (strlen($_POST['email']) == 0) 
	{
		$input_error = 1;
		display_error( _('Applicant email must be entered.'));
		set_focus('email');
	}

        $pattern="/^01[5-9]{1}[0-9]{1}[0-9]{1}[0-9]{6}/";
        $subject=$_POST['mobile'];
        if(!preg_match($pattern, $subject)){
         $input_error = 1;
            display_error(_("Enter a Valid Mobile Number"));
		set_focus('mobile');
        }
   
       $email=$_POST['email'];
        $regex='/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';
        if(!preg_match($regex, $email)){
            $input_error = 1;
		display_error(_("Enter a Valid Email"));
		set_focus('email');
        }        
    if ($input_error != 1)
	{                        
	    if (!$new_item) 
		{ 
    		    update_applicant(                                    
                                    $_POST['re_id'],
                                    $_POST['appl_id'],
                                    $_POST['f_name'],
                                    $_POST['m_name'], 
                                    $_POST['l_name'],
                                    $_POST['class_id'], 
                                    $_POST['group_id'], 
                                    $_POST['dob'],
                                    $_POST['gender'],
                                    $_POST['blood_group'],
                                    $_POST['pre_address'],
                                    $_POST['per_address'],
                                    $_POST['city'],
                                    $_POST['country'],
                                    $_POST['siblings'],
                                    $_POST['birth_place'],
                                    $_POST['certificate_no'],
                                    $_POST['phone'],
                                    $_POST['mobile'],
                                    $_POST['email'],
                                    $_POST['nationality'],
                                    $_POST['mother_tongue'],
                                    $_POST['religion'],
                                    $_POST['hobby'],
                                    $_POST['exca'],
                                    $filename,
                                    $_POST['frm_price'],
                                    $_POST['discount'],
                                    $_POST['studentcat']
                                    );
			set_focus('pro_id');
			$Ajax->activate('pro_id'); // in case of status change
			display_notification(_("Applicant Information has been updated."));
		} 
		else 
		{ //it is a NEW part
			add_applicant($syear,    
                        $_POST['NewProID'],
                        $_POST['f_name'],
                        $_POST['m_name'], 
                        $_POST['l_name'],
                        $_POST['class_id'], 
                        $_POST['group_id'], 
                        $_POST['dob'],
			$_POST['gender'],
                        $_POST['blood_group'],
                        $_POST['pre_address'],
                        $_POST['per_address'],
                        $_POST['city'],
                        $_POST['country'],
                        $_POST['siblings'],
                        $_POST['birth_place'], 
                        $_POST['certificate_no'],
                        $_POST['phone'],
			$_POST['mobile'],
                        $_POST['email'],
                        $_POST['nationality'],
                        $_POST['mother_tongue'],
                        $_POST['religion'],
                        $_POST['hobby'],
                        $_POST['exca'],
                        $filename,
                        $_POST['frm_price'],
                        $_POST['discount'],
                        $_POST['studentcat'],
                        $user);
			display_notification(_("Applicant personal information has been added."));
			set_focus('NewProID');
		}

	}
}
//---------insert parent info---------//
if (isset($_POST['addparentinfo'])) 
{

	$input_error = 0;
        
	if (strlen($_POST['father_name']) == 0) 
	{
		$input_error = 1;
		display_error( _('Applicant father name  must be entered.'));
		set_focus('father_name');
	} 
        elseif (strlen($_POST['mother_name']) == 0) 
	{
		$input_error = 1;
		display_error( _('Applicant mother name must be entered.'));
		set_focus('mother_name');
	} 
        elseif (strlen($_POST['father_occp']) == 0) 
	{
		$input_error = 1;
		display_error( _('Applicant father occupation must be entered.'));
		set_focus('father_occp');
	}
	elseif (strlen($_POST['mother_occp']) == 0) 
	{
		$input_error = 1;
		display_error( _('Applicant mother occupation must be entered.'));
		set_focus('mother_occp');
	}
        elseif (strlen($_POST['pmobile']) == 0) 
	{
		$input_error = 1;
		display_error( _('Applicant parent mobile number must be entered.'));
		set_focus('pmobile');
	}
        elseif (strlen($_POST['pemail']) == 0) 
	{
		$input_error = 1;
		display_error( _('Applicant parent email must be entered.'));
		set_focus('pemail');
	}
       
	if ($input_error != 1)
	{
                $sql = "SELECT * FROM ".TB_PREF."sms_stud_parent_details WHERE applicant_id=".db_escape($_POST['NewAppID']);
                $result = db_query($sql, "could not get applicant");
                
                $chkk = mysql_num_rows($result);
		if (!$new_item && $chkk>0)
		{ 
			update_parent_info(                                    
                                    $_POST['NewAppID'],
                                    $_POST['father_name'],
                                    $_POST['mother_name'], 
                                    $_POST['father_occp'],
                                    $_POST['mother_occp'],                                   
                                    $_POST['grelation'], 
                                    $_POST['g_name'],
                                    $_POST['g_phone'],
                                    $_POST['g_address'],                                   
                                    $_POST['pdepartment'],
                                    $_POST['designation'],
                                    $_POST['income_source'],
                                    input_num('yr_income', 0),
                                    $_POST['pcity'],
                                    $_POST['pphone'],
                                    $_POST['pmobile'],
                                    $_POST['pemail'],
                                    $_POST['f_education'],
                                    $_POST['m_education']);
                                    
		
			set_focus('father_name');
			$Ajax->activate('father_name'); // in case of status chang
			display_notification(_("Parent information has been updated."));
		} 
		else 
		{ //it is a NEW part
                   
			add_applicant_parent_info(    
                        $_POST['NewAppID'],
                        $_POST['father_name'],
                        $_POST['mother_name'], 
                        $_POST['father_occp'],                        
			$_POST['mother_occp'],                        
                        $_POST['grelation'], 
                        $_POST['g_name'],
                        $_POST['g_phone'],
                        $_POST['g_address'],			
                        $_POST['pdepartment'],
                        $_POST['designation'],
                        $_POST['income_source'],
                        input_num('yr_income', 0),
                        $_POST['pphone'],
                        $_POST['pmobile'],
                        $_POST['pemail'],
                        $_POST['f_education'],
                        $_POST['m_education']); 
			display_notification(_("Applicant Parent information has been added."));
			set_focus('father_name');
		}

	}
}
//---------insert parent info---------//

if (isset($_POST['addupreedu'])) 
{

	$input_error = 0;

       
	if ($input_error != 1)
	{
                
                $sql = "SELECT * FROM ".TB_PREF."sms_stud_edu_details WHERE applicant_id=".db_escape($_POST['NewAppID']);
               
                $result = db_query($sql, "could not get applicant");
                
                $chk = mysql_num_rows($result);
		if (!$new_item && $chk>0)
		{ /*so its an existing one */
			update_pre_education(                                    
                                    $_POST['NewAppID'],
                                    $_POST['inst_name'],
                                    $_POST['class'], 
                                    $_POST['group_id'],
                                    $_POST['edu_year'], 
                                    $_POST['grade'], 
                                    $_POST['cgpa'], 
                                    $_POST['outof'],
                                    $_POST['folupdate'],
                                    $_POST['smsalert'],
                                    $_POST['email_notification'],
                                    $_POST['smsalert'],
                                    $_POST['email_notification']
                                );
                        
                        set_focus('inst_name');
			$Ajax->activate('inst_name');
         
			display_notification(_("Applicant previous education history has been updated."));
		} 
		else 
		{ //it is a NEW part
       
			add_applicant_pre_education(    
                        $_POST['NewAppID'],
                        $_POST['inst_name'],
                        $_POST['class'],        
                        $_POST['group_id'], 
                        $_POST['edu_year'],        
                        $_POST['grade'],
			$_POST['cgpa'], 
                        $_POST['outof'], 
                        $_POST['folupdate'],
			$_POST['smsalert'],
                        $_POST['email_notification']
                        );
			display_notification(_("Applicant Previous Education information has been added."));
			set_focus('inst_name');
		}
                 clear_data();
		$Ajax->activate('_page_body');
	}
}

////--------------------------------------------------------------------------------------------
function applicant_settings(&$pro_id) 
{
    
	global $SysPrefs, $path_to_root, $new_item, $pic_height;
        
        clear_data();
 
	start_outer_table(TABLESTYLE2);
       
	table_section(1);

	table_section_title(_("Applicant Information"));
//echo $pic_height;
	//------------------------------------------------------------------------------------
	$can_id = 'C'.time();
        if ($new_item) 
	{
                label_row(_("Applicant ID:"),$can_id);
                hidden('NewProID', $can_id);
		$_POST['inactive'] = 0;
                
	} 
	else 
	{ 
            

			$myrow = get_app_data($_POST['pro_id'],'sms_students_details','applicant_id');
                       
                        $_POST['re_id']	           = $myrow['id'];
                        $_POST['NewProID']         = $myrow["applicant_id"];
			$_POST['f_name']           = $myrow["first_name"];
			$_POST['m_name']           = $myrow["middle_name"];
			$_POST['l_name']           = $myrow["last_name"];
			$_POST['class']            = $myrow["class"];
			$_POST['group']            =  $myrow['group'];
                        $_POST['dob']              = sql2date($myrow["dob"]);
			$_POST['gender']           = $myrow['gender'];
			$_POST['blood_group']      = $myrow['blood_group'];
			$_POST['pre_address']	   = $myrow['pre_address'];
			$_POST['per_address']	   = $myrow['per_address'];      
			$_POST['city']	           = $myrow['city'];
                        $_POST['country']	   = $myrow['country'];
                        $_POST['siblings']	   = $myrow['siblings'];
                        $_POST['birth_place']	   = $myrow['birth_place'];
                        $_POST['certificate_no']   = $myrow['certificate_no'];
                        $_POST['phone'] 	   = $myrow['phone'];
                        $_POST['mobile']	   = $myrow['mobile'];
                        $_POST['email']	           = $myrow['email'];
                        
                        $_POST['nationality']	   = $myrow['nationality'];
                        $_POST['mother_tongue']	   = $myrow['mother_tongue'];
                        $_POST['religion']	   = $myrow['religion'];
                        
                        $_POST['hobby']  	   = $myrow['hobby'];
                        $_POST['extra_curriculum'] = $myrow['extra_curriculum'];
                        $_POST['photo_upload']	   = $myrow['photo_upload'];
                        $_POST['school_year']      = $myrow['school_year'];
                        
                        $myr = get_app_data($myrow["applicant_id"],'sms_app_payment','applicant_id');
                        $_POST['discount']	= $myr['discount'];
                        
                        $mysch = get_app_data($myrow["applicant_id"],'sms_student_scholarship','applicant_id');
                        $_POST['student_cat']      = $mysch['student_cat'];

		label_row(_("Applicant ID:"),$_POST['NewProID']);
		hidden('re_id', $_POST['re_id']);
                hidden('NewProID', $can_id);
		set_focus('description');
	}
    
 
 //-------------------------------------
 $syear = get_current_schoolyear();
$sql = "SELECT price FROM 0_sms_form_price_setting WHERE school_year =" .db_escape($syear);
$info = db_fetch(db_query($sql));    
$form_price = $info['price'];

text_row(_("First Name:"), 'f_name', $_POST['f_name'], 50, 50);

text_row(_("Middle Name:"), 'm_name', $_POST['m_name'], 50, 50);

text_row(_("Last Name:"), 'l_name', $_POST['l_name'], 50, 50);

$query=array(array('id','class_name','select id, class_name from '.TB_PREF.'sms_create_stud_class
       ORDER BY class_name ASC'));
combo_list_row(_("Admitted Class:"), 'class_id', $_POST['class'], 'Select Class', false, $query);
   
$query=array('id','group_name','sms_applicant_group');
combo_list_row(_("Group:"), 'group_id', $_POST['group'], 'Select Group', false, $query); 
 
date_row(_("Date of Birth :"), 'dob');

$gitems =array('1'=>'Male','2'=>'Female','3'=>'Other');
free_combo_list_row(_("Gender :"), 'gender', $_POST['gender'], $gitems,'Please Select');

blood_list(_("Blood Group:"), 'blood_group', $_POST['blood_group'],_('Select'));

textarea_row(_("Present Address:"), 'pre_address', $_POST['pre_address'], 30,3);

textarea_row(_("Permanent Address:"), 'per_address', $_POST['per_address'], 30,3);

text_row(_("City:"), 'city', $_POST['city'], 45, 50);

$query=array(array('id','country_name','select id, country_name from '.TB_PREF.'country
       ORDER BY country_name ASC'));
combo_list_row(_("Country:"), 'country', $_POST['country'], 'Select Country', false, $query);

$siblings = array('0'=>'None','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8',
            '9'=>'9','10'=>'10');
free_combo_list_row(_("No Of Siblings :"), 'siblings',null, $siblings);
        

table_section(2);

	table_section_title(_("Applicant Information"));
            if(get_post(studentcat)){
                $disratio = data_retrieve('sms_student_category', 'id', $_POST['studentcat']);
            }
            $scquery=array('id','cat_name','sms_student_category');
            combo_list_row(_("Student Category :"), 'studentcat', $_POST['student_cat'], 'Select Category', false, $scquery,$disratio['ratio']);
            
            label_row(_("Form Price :"),'Tk. '. $form_price);
            hidden('frm_price', $form_price);
            hidden('appl_id', $_POST['NewProID']);
            
            text_row(_("Discount (%) :"), 'discount', $_POST['discount'], 50, 50);
            
            text_row(_("Birth Place:"), 'birth_place', $_POST['birth_place'], 50, 50);
            
            text_row(_("Birth Certificate no:"), 'certificate_no', $_POST['certificate_no'], 50, 50);
            
            text_row(_("Phone:"), 'phone', $_POST['phone'], 50);
            
            text_row(_("Mobile:"), 'mobile', $_POST['mobile'], 50);
            
            text_row(_("Email:"), 'email', $_POST['email'], 50, 50);
          
            $query=array('id','nationality','nationality');
            combo_list_row(_("Nationality:"), 'nationality', $_POST['nationality'], 'Select Nationality', false, $query);
            
            text_row(_("Mother Tongue:"), 'mother_tongue', $_POST['mother_tongue'], 50, 50);
            
            $query=array('id','description','religion');
            combo_list_row(_("Religion:"), 'religion', $_POST['religion'], 'Select Religion', false, $query);
            
            textarea_row(_("Hobby:"), 'hobby', $_POST['hobby'], 30,3);
            
            textarea_row(_("Extra Curriculum Activities:"), 'exca', $_POST['extra_curriculum'], 30,3);
            
            file_row(_("Photo Upload (.jpg)") . ":", 'pic', 'pic');
            
            
    	end_outer_table(1);
	
	div_start('controls');
	if (!isset($_POST['NewProID']) || $new_item) 
	{
		submit_center('addupdate', _("Insert Applicant Info"), true, '', 'default');
	} 
	else 
	{
		submit_center_first('addupdate', _("Update Applicant Info"), '', 
			@$_REQUEST['popup'] ? true : 'default');
		submit_return('select', get_post('pro_id'), 
			_("Select this items and return to document entry."), 'default');
		submit_center_last('cancel', _("Cancel"), _("Cancel Edition"), 'cancel');
	}

	div_end();
}
////-------------------------------------------------------------------------------------------- 
function parent_settings(&$pro_id) 
{
    
	global $SysPrefs, $path_to_root, $new_item, $pic_height;
        
//        clear_data();
 
	start_outer_table(TABLESTYLE2);
       
	table_section(1);

	table_section_title(_("Parent Information"));

			$row = get_app_data($_POST['pro_id'],'sms_students_details','applicant_id');
                        $_POST['NewAppID'] = $row["applicant_id"];

                        $getParent = get_app_data($_POST['NewAppID'],'sms_stud_parent_details','applicant_id');
			$_POST['father_name'] = $getParent["father_name"];
			$_POST['mother_name'] = $getParent["mother_name"];
			$_POST['f_occupation'] = $getParent["f_occupation"];
			$_POST['m_occupation'] = $getParent["m_occupation"];
                        $_POST['m_education'] = $getParent["m_education"];
                        $_POST['f_education'] = $getParent["f_education"];
                        $_POST['grelation'] = $getParent["lg_relation"];
			$_POST['g_name'] = $getParent["lg_name"];
                        $_POST['g_phone'] = $getParent["lg_phone"];
                        $_POST['g_address'] = $getParent["lg_address"];			
                        $_POST['pdepartment'] = $getParent["patent_department"];
                        $_POST['designation'] = $getParent["patent_designation"];
                        $_POST['income_source'] = $getParent["income_source"];
                        $_POST['yr_income'] = price_format($getParent["yearly_income"]);
			$_POST['pcity'] = $getParent["city"];
                        $_POST['pphone'] = $getParent["alternate_phone"];
                        $_POST['pmobile'] = $getParent["mobile"];
                        $_POST['email'] = $getParent["email"];
			
hidden('NewAppID', $_POST['NewAppID']);


text_row(_("Father's Name:"), 'father_name', $_POST['father_name'], 35, 50);

text_row(_("Mother's Name:"), 'mother_name', $_POST['mother_name'], 35, 50);

text_row(_("Father's Occupation:"), 'father_occp', $_POST['f_occupation'], 35, 50);

text_row(_("Father's Educational Qualification:"), 'f_education', $_POST['f_education'], 35, 50);

text_row(_("Mother's Occupation:"), 'mother_occp', $_POST['m_occupation'], 35, 50);

text_row(_("Mother's Educational Qualification:"), 'm_education', $_POST['m_education'], 35, 50);

table_section_title(_("Legal Gardian Information"));

text_row(_("Legal Gardian in Absence of Parents:"), 'g_name', $_POST['g_name'], 35, 50);

text_row(_("Gardiant's Phone:"), 'g_phone', $_POST['g_phone'], 35, 50);


table_section(2);

	table_section_title(_("Parent Contact Information"));
            
            text_row(_("Department:"), 'pdepartment', $_POST['pdepartment'], 35);
            
            text_row(_("Designation:"), 'designation', $_POST['designation'], 35);
            textarea_row(_("Income Source:"), 'income_source', $_POST['income_source'], 30,3);
            
            amount_row("Yearly Income:", 'yr_income', NULL, $_POST['yr_income']);
            
            text_row(_("Service Location:"), 'pcity', $_POST['pcity'], 35);
            
            text_row(_("Phone:"), 'pphone', $_POST['pphone'], 35);
            
            text_row(_("Mobile:"), 'pmobile', null, 35);
            
            text_row(_("Email:"), 'pemail', $_POST['email'], 35, 50);
                  
	end_outer_table(1);
	
	div_start('controls');
	if ($parent['applicant_id'] == '') 
            {                        
                submit_center('addparentinfo', _("Insert Parent Info"), true, '', 'default');
            } 
        else 
            {
                submit_center_first('addparentinfo', _("Update Parent Info"), '', 
                        @$_REQUEST['popup'] ? true : 'default');
                submit_return('select', get_post('pro_id'), 
                        _("Select this items and return to document entry."), 'default');
                submit('delete', _("Delete Parent Info"), true, '', true);
                submit_center_last('cancel', _("Cancel"), _("Cancel Edition"), 'cancel');
            }

	div_end();
}

////-------------------------------------------------------------------------------------------- 
//
function pre_education_settings(&$pro_id) 
{
    
	global $SysPrefs, $path_to_root, $new_item, $pic_height;
        
        clear_data();
 
	start_outer_table(TABLESTYLE2);
       
	table_section(1);

	table_section_title(_("Previous Education Details"));
        
        $row = get_app_data($_POST['pro_id'],'sms_students_details','id');
                        $_POST['NewAppID'] = $row["applicant_id"];
                        
                        $getEdu = get_app_data($_POST['NewAppID'],'sms_stud_edu_details','applicant_id');
//                        display_error($getEdu["institute_name"]);
			$_POST['father_name'] = $getEdu["institute_name"];
			$_POST['class'] = $getEdu["class"];
			$_POST['st_group'] = $getEdu["st_group"];
			$_POST['year'] = $getEdu["year"];
			$_POST['grade'] = $getEdu["grade"]; 
                        $_POST['cgpa'] = $getEdu["cgpa"];
			$_POST['out_of'] = $getEdu["out_of"];
			

hidden('NewAppID', $_POST['NewAppID']);
            
text_row(_("Institute Name:"), 'inst_name', $_POST['father_name'], 45, 50);



$query=array('id','class_name','sms_create_stud_class ');
   
combo_list_cells(_("Class :"), 'class', '', 'Select Class', false, $query );
        
$query=array('id','group_name','sms_applicant_group');
        combo_list_row(_("Group:"), 'group_id', $_POST['st_group'], 'Select Group', false, $query); 

text_row(_("Education Year:"), 'edu_year', $_POST['year'], 45, 50);

text_row(_("Grade:"), 'grade', $_POST['grade'], 45, 50);

text_row(_("CGPA:"), 'cgpa', $_POST['cgpa'], 45, 50);

text_row(_("Out of:"), 'outof', $_POST['out_of'], 45, 50);


table_section(2);

	table_section_title(_("Other Information"));
  
        date_row(_("Follow Up Date :"), 'folupdate'); 
        echo '<tr>';
        check_cells(_("SMS Alert"), 'smsalert', $_POST['smsalert'],false, _('set DECLARATION'));
        echo '</tr>';
        check_cells(_("Email Notification"), 'email_notification', $_POST['email_noty'],false, _('set DECLARATION'));
            
	end_outer_table(1);
	
	div_start('controls');
	if ($edu['applicant_id'] == '') 
	{
		submit_center('addupreedu', _("Insert Pre Education"), true, '', 'default');
	} 
	else 
	{
		submit_center_first('addupreedu', _("Update Pre Education"), '', 
			@$_REQUEST['popup'] ? true : 'default');
		submit_return('select', get_post('pro_id'), 
			_("Select this items and return to document entry."), 'default');
		//submit('clone', _("Clone This Item"), true, '', true);
		submit('delete', _("Delete This Project"), true, '', true);
		submit_center_last('cancel', _("Cancel"), _("Cancel Edition"), 'cancel');
	}

	div_end();
}

////--------------------------------------------------------------------------------------------
//
// 
start_form(true);

if (db_has_stock_items()) 
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();

        $emp_query=array('applicant_id','CONCAT(first_name," ",middle_name," ",last_name)','sms_students_details');
        double_combo_list_cells(_("Select an Applicant: "), 'pro_id', $_POST['pro_id'],  _('New Applicant'), true,false,false,$emp_query);

	$new_item = get_post('pro_id')=='';
      
	end_row();
	end_table();
        $my = get_app_data($_POST['pro_id'],'sms_students_details','id');
        $parent = get_app_data($my['applicant_id'],'sms_stud_parent_details','applicant_id');
        $edu = get_app_data($my['applicant_id'],'sms_stud_edu_details','applicant_id');
//	
}
else
{
	hidden('pro_id', get_post('pro_id'));
}


div_start('details');
$pro_id = get_post('pro_id');

if (!$pro_id)
	unset($_POST['_tabs_sel']); // force settings tab for new customer


tabbed_content_start('tabs', array(
    
    'personal_info' => array(_('&Personal Info'), $pro_id),
    'parent_info' => array(_('&Parent Info'), $pro_id),
    'pre_education' => array(_('&Previous Education'), $pro_id),
));
	
	switch (get_post('_tabs_sel')) {
		default:
		case 'personal_info':
			applicant_settings($pro_id); 
			break;
        case 'parent_info':
			parent_settings($pro_id);
			break;
        case 'pre_education':
			pre_education_settings($pro_id);
			break;
               
	};
br();
tabbed_content_end();
div_end();
hidden('popup', @$_REQUEST['popup']);
end_form();
end_page(@$_REQUEST['popup']);

?>
