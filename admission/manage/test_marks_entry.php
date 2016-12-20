<?php

/* * ********************************************************************
  developed by Mannan
 * ********************************************************************* */
$page_security = 'SS_SMS_ADMSN_MRK_ENTRY';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admission/includes/ui/admission_ui.inc");
include_once($path_to_root . "/admission/includes/db/applicant_db.inc");

if (!@$_GET['popup']) {
    $js = "";
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
    page(_($help_context = "Applicant Mark Entry"), false, false, "", $js);
}
//if (isset($_GET['order_number'])) {
//    $order_number = $_GET['order_number'];
//}
simple_page_mode(true);
//-----------------------------------------------------------------------------------

if (isset($_POST['processstmarks'])) 
{
    
    foreach($_POST['status'] as $key =>$stus){
            $chk = $stus;
            
        }
        
        
   if($chk == 1)
   {
        foreach($_POST['status'] as $key =>$stus)
            {
             
        
	if (strlen($_POST['mark'] == 0))
	{
		$input_error = 1;
               
		display_error(_("Marks must be less than 100."));
		set_focus('mark');
	}
   
        if($input_error != 1)
        { 
            
            $appid = $_POST['appid'][$key];
            $marks = $_POST['mark'][$key];
           
            add_test_marks($appid, $marks,$stus);
			display_notification(_('Status has been added'));
          
        }
        
     }
 }
   
   else foreach ($_POST['mark'] as $key => $marks)
   {
       $appid = $_POST['appid'][$key];
           
            add_test_marks($appid, $marks,$stus);
			display_notification(_('Status has been added')); 
   }
        
        
    // }

}
//-----------------------------------------------------
if (!@$_GET['popup'])
start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();
get_student_clas(_("Select Class:"), 'app_class', $_POST['app_class'], 'Select Class', true);

submit_cells('SearchOrders', _("Search"), '', _('Select documents'), 'default');
end_row();
end_table(1);
end_form();
//---------------------------------------------------------------------------------------------

start_form();
start_table(TABLESTYLE2,"width=60%");
         start_row();
             labelheader_cell( 'Status','width=5%');
             labelheader_cell( 'Applicant ID','width=5%');
             labelheader_cell( 'Applicant Name','width=6% ');
             labelheader_cell( 'Marks','width=7%'); 
         end_row();
         
           
         
if( list_updated('app_class') ){   
    $class_id = $_POST['app_class'];
    $sql = get_sql_for_mark_entry($class_id);
    
    $result = db_query($sql,"data could not be found");
 
 //$sl=1;
  while ($rep = db_fetch($result))
    {
         start_row();
             
         check_cells(null, 'status['.$rep['id'].']', '',false,  _('set DECLARATION'));
             label_cell( $rep['applicant_id'],'width=10%');
             hidden('appid['.$rep['id'].']', $rep['applicant_id']);
             label_cell( $rep['name'],'width=15% height=16');
             text_cells($rep['obtain_marks'], 'mark['.$rep['id'].']', null, 20,2);  
         end_row();
           
        // $sl++;
    }
} 
end_table();

//---------------------------------------------------------------------------------------------------

br();
div_start('controls');

        submit_center('processstmarks', _("Insert Applicant Marks"), true, '', 'default');

    div_end();

if (!@$_GET['popup']) {
    end_form();
    end_page();
}

?>