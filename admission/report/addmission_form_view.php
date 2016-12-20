<?php
/**********************************************************************/
$page_security = 'SS_SMS_APLCNT_LST_VW';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");

page(_($help_context = "Application Form View"), true);

if (!isset($_GET['Applicantid'])) {
    die("<BR>" . _("This page must be called with a Performa Invoice to review."));
} else {
    $applicantid = $_GET['Applicantid'];
}

$sql_ex = get_sql_for_stform_view($applicantid);

    /////
    $stock_img_link = "";
	$check_remove_image = false;
	if (file_exists($sql_ex['photo_upload']))
	{
	 // 31/08/08 - rand() call is necessary here to avoid caching problems. Thanks to Peter D.
		$stock_img_link .= "<img id='item_img' alt = '[".$sql_ex['photo_upload']."]' src='".$sql_ex['photo_upload'].
			"?nocache=".rand()."'"." height='100' width='100' border='1' >";
		$check_remove_image = true;
	} 
	else 
	{
		$stock_img_link .=  '<img  src='.company_path().'/images/sabuj.jpg height="50" width="50"  />';
	}
      
       // display_error($sql_ex["photo_upload"]);

// outer table
 /*-----------------main table start----------------------*/  
br();
 start_table(TABLESTYLE1);
    start_row();
      label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
        end_row();
        start_row();
  
        labelheader_cell('APPLICATION FORM FOR 2014','width=95%','colspan=4 style="font-size:18px"');
           //label_cells("Applicant Image", $sql_ex['photo_upload'],'width=20%');
        
       
        end_row();
end_table();
br();
  start_table(TABLESTYLE_NOBORDER,'width=70%');
   label_cell($stock_img_link ,'align=right ');
  end_table();
  
br();
//  start_table(TABLESTYLE2,'width=80%');
//  start_row();
//    labelheader_cell('APPLICATION FORM FOR 2014','width=95%','colspan=4 style="font-size:18px"');
//  end_row();
//  end_table();
  
  br();
   start_table(TABLESTYLE_NOBORDER);
  
   end_table();
   
   
   //--------------------------------------------------------------------------------------------------------------------------
   
  start_table(TABLESTYLE2,'width=90%');
   start_row('background-color:none');
            labelheader_cell( 'Applicant Personal Info','colspan=8 style="text-align:left;"');
         end_row();
  start_row();
             label_cells( '<b>Candidate ID</b>',$applicantid,'width=15%');
             label_cells( '<b>Present Address</b>',$sql_ex['pre_address'],'width=15%');
          
             label_cells( '<b>Mother Tongue</b>',$sql_ex['mother_tongue']);
              
              
               
  end_row();
   start_row();
        
          
               label_cells( '<b>Candidate Name</b>',$sql_ex['name'],'width=15%');
              
             if($sql_ex['blood_group']==1){ 
               
                 $bg = 'A+';
               label_cells( '<b>Blood Group</b>',$bg,'width=15%');
                 } 
          
             elseif($sql_ex['blood_group']==2){ 
                 $bg = 'A-';
                label_cells( '<b>Blood Group</b>',$bg,'width=15%');  
                 } 
              
             elseif($sql_ex['blood_group']==3){ 
                 $bg = 'B+';
                  label_cells( '<b>Blood Group</b>',$bg,'width=15%');
                 }
             elseif($sql_ex['blood_group']==4){
                 $bg = 'B-';
                  label_cells( '<b>Blood Group</b>',$bg,'width=15%');
                 } 
                 elseif($sql_ex['blood_group']==5){
                 $bg = 'O+';
                  label_cells( '<b>Blood Group</b>',$bg,'width=15%');
                 } 
                 elseif($sql_ex['blood_group']==6){
                 $bg = 'O-';
                  label_cells( '<b>Blood Group</b>',$bg,'width=15%');
                 } 
                
                 elseif($sql_ex['blood_group']==7){
                 $bg = 'AB+';
                  label_cells( '<b>Blood Group</b>',$bg,'width=15%');
                 } 
                 elseif($sql_ex['blood_group']==8){
                 $bg = 'AB-';
                  label_cells( '<b>Blood Group</b>',$bg,'width=15%');
                 } 
             label_cells( '<b>Permanent Address</b>',$sql_ex['per_address'],'width=15%');
           
             label_cells( '<b>Nationality</b>',$sql_ex['nationality']);
  end_row();
  start_row();
      label_cells( '<b>Class</b>',$sql_ex['class_name'],'width=15%');
            
             label_cells( '<b>City</b>',$sql_ex['city'],'width=15%');
             label_cells( '<b>Mobile</b>',$sql_ex['mobile']);
            // display_error($sql_ex['per_address']);
            
             //display_error($sql_ex['country']);
          
             
  end_row();
  start_row();
    label_cells( '<b>Group</b>',$sql_ex['group_name'],'width=15%');
    label_cells( '<b>Country</b>',$sql_ex['country'],'width=15%');
    label_cells( '<b>Phone</b>',$sql_ex['phone']);
            
           
         
  end_row();
  start_row();
         if($sql_ex['gender']==1){ $gen = 'Male';} else{ $gen = 'Female';}
             
            label_cells( '<b>Gender</b>',$gen,'width=15%');
            label_cells( '<b>Birth Place</b>',$sql_ex['birth_place'],'width=15%');
            
            
                              
  label_cells( '<b>Email</b>',$sql_ex['email']);
             
             
  end_row();
  start_row();
  
   label_cells( '<b>Date of Birth</b>',$sql_ex['dob'],'width=15%');
   
   label_cells( '<b>Religion</b>',$sql_ex['religion']);
   label_cells( '<b>Hobby</b>',$sql_ex['hobby']);
  end_row();
  
  
  start_row();
   label_cells( '<b>Extra Curriculum Activity</b>',$sql_ex['extra_curriculum'],null,'colspan=5');
  end_row();
  end_table();
   
  br();
  start_table(TABLESTYLE2,'width=90%');
   start_row('background-color:none');
            labelheader_cell( 'Applicant Previous Education Details','colspan=6 style="text-align:left;"', 'width=90%');
   end_row();
  start_row();
  //display_error($sql_ex['institute_name']);
             labelheader_cell( '<b>Institute Name</b>');
             labelheader_cell( '<b>Class</b>','width=15%');
             labelheader_cell( '<b>CGPA</b>');
             labelheader_cell( '<b>Out Of</b>');
             labelheader_cell( '<b>Year</b>','width=15%');
             labelheader_cell( '<b>Grade</b>','width=15%');
 end_row();
      start_row();
             label_cell($sql_ex['institute_name'],'align=center');
             label_cell($sql_ex['cls_name'],'align=center');
             label_cell($sql_ex['cgpa'],'align=center');
             label_cell($sql_ex['out_of'],'align=center');
             label_cell($sql_ex['year'],'align=center');
             label_cell($sql_ex['grade'],'align=center');
  end_row();
end_table();
  
  
  br();
  start_table(TABLESTYLE2,'width=90%');
   start_row('background-color:none');
            labelheader_cell( 'Applicant Parent Details','colspan=8 style="text-align:left;"');
         end_row();
  start_row();
             label_cells( '<b>Father Name</b>',$sql_ex['father_name'],'width=15%');
             label_cells( '<b>Present Address</b>',$sql_ex['persent_add'],'width=15%');
             label_cells( '<b>Mobile</b>',$sql_ex['mobile'],'width=15%');
            
             
  end_row();
   start_row();
             label_cells( '<b>Father Occupation</b>',$sql_ex['f_occupation'],'width=15%');
             label_cells( '<b>Permanent Address</b>',$sql_ex['permanent_add'],'width=15%');
             label_cells( '<b>Alternate Phone</b>',$sql_ex['alternate_phone'],'width=15%');
  end_row();
  start_row();
             label_cells( '<b>Mother Name</b>',$sql_ex['mother_name'],'width=15%');
             label_cells( '<b>Country</b>',$sql_ex['country'],'width=15%');    
             label_cells( '<b>Email</b>',$sql_ex['email'],'width=15%');
            
   end_row();
   start_row();
            label_cells( '<b>Mother Occupation</b>',$sql_ex['m_occupation'],'width=15%');
            label_cells( '<b>City</b>',$sql_ex['city'],'width=15%');
    
   end_row();
   end_table();
  
  br(); 
  
   start_table(TABLESTYLE2, "width=90%"); // outer table
  //echo '<br/>';
  start_row();
       labelheader_cell('Declaration');
  end_row();
  
  start_row();
           label_cell('We hereby certify that the information given in the Registration Form is complete and accurate.
           We understand and agree that misrepresentation or omission of facts will justify the denial of admission,
           the cancellation of admission or expulsion.We do hereby consent to abide by school rule and regulation');
  end_row();
  end_table(1);
  //end_outer_table();

 // end_outer_table();
  
  br(1);
  br(1);
  br(1);
   
end_page(true, false, false, ST_BOM, $style_no);