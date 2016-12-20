<?php

$page_security = 'SS_SMS_APLCNT_LST_VW';
$path_to_root="../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/library/includes/ui/library_ui_lists.inc");
include_once($path_to_root . "/library/includes/db/library_db.php");


page(_($help_context = "Details Data About A Book"), true);


if (!isset($_GET['book_id'])) {
    die("<BR>" . _("This page must be called with a book data to review."));
} else {
    $bid = $_GET['book_id'];
}
//--------------------------------------------------


$sql_ex = get_sql_for_book_view($bid);

    
    $stock_img_link = "";
	$check_remove_image = false;
	if (file_exists($sql_ex['photo_upload']))
	{
	 // 31/08/08 - rand() call is necessary here to avoid caching problems. Thanks to Peter D.
		$stock_img_link .= "<img style='border:1px solid #000000;' id='item_img' alt = '[".$sql_ex['photo_upload']."]' src='".$sql_ex['photo_upload'].
			"?nocache=".rand()."'"." height='100' width='100' border='1'>";
		$check_remove_image = true;
	} 
	else 
	{
		$stock_img_link .= '<img  src='.company_path().'/images/sabuj.jpg height="100" width="100"  border="1"  />';
	}
      
//

// outer table
 /*-----------------main table start----------------------*/  
br();


 start_table(TABLESTYLE1);
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['coy_name'],'align=center style="font-size:16px"'); 
    end_row();
    start_row();
        label_cell($_SESSION['SysPrefs']->prefs['postal_address'],'colspan=3 align=center style="font-size:15px"');
    end_row();
    start_row();   
        labelheader_cell('Details Data About A Book
            ','width=95%','colspan=4 style="font-size:18px"');
    end_row();
end_table();
br();
  start_table(TABLESTYLE_NOBORDER,'width=70%');
   label_cell($stock_img_link ,'align=right ' );
  end_table();
  
br();
  start_table(TABLESTYLE_NOBORDER,'width=80%');

  start_row();
             label_cell( 'Book ID','width=20%');
             label_cell( ':','width=10%');
             label_cell( $_GET['book_id']);
  end_row();
   start_row();
   
             label_cell('Book Name');
             label_cell( ':','width=10%');
             label_cell($sql_ex['book_name']);
  end_row();
  start_row();
   
             label_cell('Author Name');
             label_cell( ':','width=10%');
             label_cell($sql_ex['auth_name']);
  end_row();
  start_row();
   
             label_cell( 'Publication');
             label_cell( ':','width=10%');
             label_cell($sql_ex['publication']);
  end_row();
  start_row();
             label_cell( 'Book Type');
             label_cell( ':','width=10%');
             label_cell($sql_ex['book_type']);
             
  end_row();
    start_row();
             label_cell( 'Edition');
             label_cell( ':','width=10%');
             label_cell($sql_ex['edition']);
  end_row();
  
   start_row();
             label_cell( 'Entry Date');
             label_cell( ':','width=10%');
             label_cell(sql2date($sql_ex['entry_date']));
  end_row();
   start_row();
             label_cell( 'ISBN/ISSN');
             label_cell( ':','width=10%');
             label_cell($sql_ex['isbn']);
  end_row();
   start_row();
             label_cell( 'Source');
             label_cell( ':','width=10%');
             label_cell($sql_ex['source']);
  end_row();
   start_row();
             label_cell( 'Number Of Copy');
             label_cell( ':','width=10%');
             label_cell($sql_ex['num_books']);
  end_row();
   start_row();
             label_cell( 'Special Feature');
             label_cell( ':','width=10%');
             label_cell($sql_ex['feature']);
  end_row();
  start_row();
             label_cell( 'Cost');
             label_cell( ':','width=10%');
             label_cell($sql_ex['cost']);
  end_row();
  start_row();
             label_cell( 'Key Word');
             label_cell( ':','width=10%');
             label_cell($sql_ex['key_word']);
  end_row();
  end_table();
   
  br();
  
  $sch = get_sql_for_lib_book($bid);
 
  start_table(TABLESTYLE2,'width=80%');
   start_row('background-color:none');
            labelheader_cell( 'Book Type','align=center');
            labelheader_cell( 'Location','align=center');
            labelheader_cell( 'Copy','align=center');
            labelheader_cell( 'Status','align=center');
            labelheader_cell( 'Due Date','align=center');
         end_row();
  start_row();
              label_cell( $sch['book_type'],'align=center');
              label_cell( $sch['location_name'],'align=center');
              label_cell( $sch['num_books'],'align=center');
              if($sch['available_books']>0){
                  label_cell('In Stock','align=center');
              }
              else{
                  label_cell('Out of Stock','align=center');
              }
              
              label_cell( sql2date($sch['due_date']),'align=center');
            
  end_row();
  end_table();
  br();
  
  
  br(1);
  br(1);
  br(1);
   
end_page(true, false, false, ST_BOM, $style_no);
?>
