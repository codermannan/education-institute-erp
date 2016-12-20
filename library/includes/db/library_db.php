<?php
//--------------------------------------------------------
function add_location_setup($location_name,$shelf,$shelf_row,$shelf_column)
{
    $sql = "INSERT INTO " . TB_PREF . "sms_lib_location_setup (location_name, shelf, shelf_row, shelf_column) values (" . db_escape($location_name) . ",
        " . db_escape($shelf) .",". db_escape($shelf_row) ."," . db_escape($shelf_column) . ")";
    //display_error($sql);
    db_query($sql, "could not insert into table");
}

function view_location_setup()
{
    $sql = "SELECT * FROM ".TB_PREF."sms_lib_location_setup ORDER BY location_name ASC" ;
    return db_query($sql);
    
}

function update_location_setup($id,$location_name,$shelf,$shelf_row,$shelf_column){
        $sql = "UPDATE " . TB_PREF . "sms_lib_location_setup SET "
            . "location_name = " . db_escape($location_name)
            . " , " . "shelf = " . db_escape($shelf)
            . " , " . "shelf_row = " . db_escape($shelf_row)
            . " , " . "shelf_column = " . db_escape($shelf_column)
            . " WHERE id = " . db_escape($id);
 //display_error($sql);
  db_query($sql, "Location and not be updated");
    
} 

function delete_location_setup($id)
{
    $sql= "delete from " . TB_PREF . "sms_lib_location_setup
        where id= " .db_escape($id);
    db_query($sql, "cannot delete");
}


//add new books.............................................................................................
function add_book($book_name,$auth_name,$edition,$cost,$book_type,$publication,$isbn,$location,$shelf,$user,$feature,$key_word,$source,$new,$display,$active){
    
    $dt = date2sql(Today());
    $bn =$book_name;
    $bookid = $bn[0].'-'.substr(time(),6);
    
    
    $sql = "INSERT INTO " . TB_PREF . "sms_lib_book_entry (book_id, book_name, auth_name, edition, cost, book_type, 
            publication,isbn,location,shelf,entry_by,entry_date,feature,key_word,source,new,display,active,flag) values (" . db_escape($bookid) . ",
            " . db_escape($bn) .",". db_escape($auth_name) ."," . db_escape($edition) . ",
            " . db_escape($cost).",". db_escape($book_type).",". db_escape($publication).",". db_escape($isbn).",". db_escape($location).",". db_escape($shelf).",
            ". db_escape($user).",". db_escape($dt).",". db_escape($feature).",". db_escape($key_word).",". db_escape($source).",". db_escape($new).",". db_escape($display).",". db_escape($active).",". db_escape(1).")";
//    display_error($sql);
    db_query($sql, "can not insert book record");
}

function view_book_list($bookid)
{
	 $sql = "SELECT be.*, be.id as bookid, loc.* FROM ".TB_PREF."sms_lib_book_entry be
                LEFT JOIN " . TB_PREF . "sms_lib_location_setup loc ON loc.id= be.location 
                WHERE be.book_id=".  db_escape($bookid);
        
	$result = db_query($sql, "could not get book");
	return db_fetch($result);
}

function update_book($id,$book_name,$auth_name,$edition,$cost,$book_type,$publication,$isbn,$location,$shelf,$feature,$key_word,$source,$new,$display,$active ) {

    $sql = "UPDATE " . TB_PREF . "sms_lib_book_entry SET "
            . "book_name = " . db_escape($book_name)
            . " , " . "auth_name = " . db_escape($auth_name)
            . " , " . "edition = " . db_escape($edition)
            . " , " . "cost = " . db_escape($cost)
            . " , " . "book_type = " . db_escape($book_type)  
            . " , " . "publication = " . db_escape($publication)
            . " , " . "isbn = " . db_escape($isbn)
            . " , " . "location = " . db_escape($location)
            . " , " . "location = " . db_escape($shelf)
            . " , " . "feature = " . db_escape($feature)
            . " , " . "key_word = " . db_escape($key_word)
            . " , " . "source = " . db_escape($source)
            . " , " . "new = " . db_escape($new)
            . " , " . "display = " . db_escape($display)
            . " , " . "active = " . db_escape($active)
            . " WHERE id = " . db_escape($id);
 //display_error($sql);
  db_query($sql, "Book record can not be updated");
}

function delete_book($id)
{
    $sql= "delete from " . TB_PREF . "sms_lib_book_entry
        where id= " .db_escape($id);
    db_query($sql, "cannot delete");
}

//insert author
function add_author($author_name,$address,$author_image,$status)
{
    $sql = "INSERT INTO " . TB_PREF . "sms_lib_author_setup (author_name, address, image, status) values (" . db_escape($author_name) . ",
        " . db_escape($address) .",". db_escape($author_image) ."," . db_escape($status) . ")";
    //display_error($sql);
    db_query($sql, "could not insert into table");
}

function update_author($id,$author_name,$address,$author_image,$status)
{
    $sql = "UPDATE " . TB_PREF ."sms_lib_author_setup SET author_name=" . db_escape($author_name) .", address="
         . db_escape($address) . ", image= " . db_escape($author_image) . ", status=" . db_escape($status) .
         " WHERE id=" . db_escape($id);
// display_error($sql);
 db_query($sql,'selected data update failed');
}

function view_author()
{
    $sql = "SELECT image,author_name,address,status,id FROM ".TB_PREF."sms_lib_author_setup ORDER BY author_name ASC" ;
    
//    display_error($sql);
    return $sql;
    
}

function delete_author($id)
{
    $sql= "DELETE FROM " . TB_PREF . "sms_lib_author_setup
        where id= " .db_escape($id);
    db_query($sql, "cannot delete");
}

//insert publisher
function add_publisher($publisher_name,$address,$publisher_image,$status)
{
    $sql = "INSERT INTO " . TB_PREF . "sms_lib_publisher_setup (publisher_name, address, image , status) values (" . db_escape($publisher_name) . ",
        " . db_escape($address) .",". db_escape($publisher_image) ."," . db_escape($status) . ")";
//    display_error($sql);
    db_query($sql, "could not insert into table");
}

function update_publisher($id,$publisher_name,$address,$status)
{
    $sql = "UPDATE " . TB_PREF . "sms_lib_publisher_setup SET publisher_name=" . db_escape($publisher_name) .
           ", address=" . db_escape($address) . ", status=" . db_escape($status) . "WHERE id=" . db_escape($id);
   db_query($sql,'update failed');
    
}
function view_publisher()
{
    $sql = "SELECT * FROM ".TB_PREF."sms_lib_publisher_setup ORDER BY publisher_name ASC" ;
    return $sql;
    
}

function dynamic_delete($table,$id)
{
    $sql= "DELETE FROM " . TB_PREF . "$table
        where id= " .db_escape($id);
    
    //display_error($sql);
    db_query($sql, "cannot delete");
}

//insert book_type
function add_book_type($book_type,$status)
{
    $sql = "INSERT INTO " . TB_PREF . "sms_lib_book_type_setup (book_type, status) values (" . db_escape($book_type) . "," . db_escape($status) . ")";
    //display_error($sql);
    db_query($sql, "could not insert into table");
}

function max_book_request_and_fine($max_book_request,$request_duration,$fine){
     $sql = "INSERT INTO " . TB_PREF . "sms_lib_duration-number-request (id,max_request,duration_active_request) values (" . db_escape($book_type) . "," . db_escape($status) . ")";
}

function view_book_type()
{
    $sql = "SELECT * FROM ".TB_PREF."sms_lib_book_type_setup ORDER BY book_type ASC" ;
    return db_query($sql);
    
}


//add new book request.............................................................................................
function add_book_request($student_id,$class,$roll,$book_id,$date,$due_date,$time,$note){
    $sql = "INSERT INTO " . TB_PREF . "sms_lib_book_request (student_id,class, roll,book_id,date, due_date,time,note) values (" . db_escape(student_id) . ",
           ". db_escape($roll) .",". db_escape($class) ."," . db_escape($book_id) . "," . db_escape($date).",". db_escape($due_date).",
           ". db_escape($time).",". db_escape($note).")";
    display_error($sql);
    db_query($sql, "could not insert into table");
}



function get_sql_for_book_list($datasearch,$searchvalue) {
    
    $sql = "SELECT 
        be.book_id,
        be.book_name,
        bs.book_type as book,
        la.author_name,
        be.edition,
        ps.publisher_name,
        be.entry_date,
        be.source,
        be.cost,
        be.id
        FROM " . TB_PREF . "sms_lib_book_entry be
        LEFT JOIN " . TB_PREF . "sms_lib_book_type_setup bs ON be.book_type = bs.id
        LEFT JOIN " . TB_PREF ."sms_lib_author_setup la ON la.id = be.auth_name
        LEFT JOIN " . TB_PREF ."sms_lib_publisher_setup ps ON ps.id = be.publication";
//    display_error($sql);
  
    if($datasearch){
            $sql .= " WHERE $datasearch LIKE '%$searchvalue%'";
        } 

    return $sql;
       
}



function delete_book_data($tab, $key, $val) {
    begin_transaction();
    $sql1 = "DELETE FROM " . TB_PREF .$tab. " WHERE ".$key." = " . db_escape($val);;
    db_query($sql1, "cannot delete this info");
    commit_transaction();
    return true;
}
//Request list for libarian
function get_sql_for_request_list($datasearch,$searchvalue) {
    
    $sql = "SELECT 
        st.student_id,
        CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name,
        sc.class_name,
        st.roll_number,
        br.book_id,
        be.book_name,
        be.auth_name,
        be.edition,
        be.publication,
        br.date as requested_date,
        br.due_date,
        lr.date as returndate,
        br.note,
        br.id,
        br.status
        FROM " . TB_PREF . "sms_lib_book_request br
        LEFT JOIN " . TB_PREF . "sms_student st ON br.student_id = st.student_id AND st.status=1
        LEFT JOIN " . TB_PREF . "sms_students_details sd ON st.student_id = sd.student_id
        LEFT JOIN " . TB_PREF . "sms_lib_book_entry be ON br.book_id = be.book_id
        LEFT JOIN " . TB_PREF . "sms_create_stud_class sc ON st.st_class = sc.id
        LEFT JOIN " . TB_PREF . "sms_lib_book_return lr ON br.id = lr.entry_id
        ORDER BY br.id DESC "; 
//    display_error($sql);
    if($datasearch){
            $sql .= " WHERE $datasearch LIKE '%$searchvalue%'";
        } 

    return $sql;
}

//get data for file "book_request_page"
function get_data_by_key($book_id, $tbl_name, $wh) {

    $sql = "SELECT * FROM " . TB_PREF . "$tbl_name 
            WHERE $wh=" . db_escape($book_id);
    
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}

//add book .............................................................................................
function add_request($user,$name,$cls,$roll,$book_id,$note){
    
    $date = date2sql(Today());
    
    $due_date = date('Y-m-d',date( strtotime("+30 days"),strtotime($date)));
    
    
    $sql = "INSERT INTO " . TB_PREF . "sms_lib_book_request (student_id,student_name,class,roll, book_id, date, due_date, note) values (" . db_escape($user) . ",
            ". db_escape($name) .",". db_escape($cls) .",". db_escape($roll) ."," 
            . db_escape($book_id) .",". db_escape($date) ."," . db_escape($due_date) ."," . db_escape($note) . ")";
    
    db_query($sql, "can not insert book record");
    
    $sql = "SELECT num_of_requests FROM " . TB_PREF . "sms_lib_book_entry 
            WHERE book_id = '$book_id'";
   
    $result = db_fetch(db_query($sql, "could not get book_id"));
    $num_requests = $result['num_of_requests'] + 1;
   
    $sqlup = "UPDATE " . TB_PREF . "sms_lib_book_entry SET num_of_requests =". db_escape($num_requests) ." WHERE book_id = ". db_escape($book_id);
    $re = db_fetch(db_query($sqlup, "could not get book_id"));
}

//add num_books in lib........................................
function add_books_in_lib($user,$book_id, $num){
     
     $rcvdate= date2sql(Today());   
    $sqlr=  "INSERT INTO " . TB_PREF . "sms_lib_book_receive (book_id,receive_date,num_of_books,receive_by) values (" . db_escape($book_id) . "," . db_escape($rcvdate) . ",
            " . db_escape($num) .",". db_escape($user) .")";
    
    db_query($sqlr, "can not insert book record");
      
    $date = date2sql(Today());
    $sql = "SELECT num_books FROM " . TB_PREF . "sms_lib_book_entry 
            WHERE book_id = '$book_id'";
   
    $result = db_fetch(db_query($sql, "could not get book_id"));
    $booknum = $result['num_books'] + $num;
    
    $sql = "SELECT available_books FROM " . TB_PREF . "sms_lib_book_entry 
            WHERE book_id = '$book_id'";
   
    $result = db_fetch(db_query($sql, "could not get book_id"));
    $available = $result['available_books'] + $num;
     
    $sqlup = "UPDATE " . TB_PREF . "sms_lib_book_entry SET num_books =". db_escape($booknum) ." WHERE book_id = ". db_escape($book_id);
    $re = db_fetch(db_query($sqlup, "could not get book_id"));
    
    $sqlup = "UPDATE " . TB_PREF . "sms_lib_book_entry SET available_books =". db_escape($available) ." WHERE book_id = ". db_escape($book_id);
    $re = db_fetch(db_query($sqlup, "could not get book_id"));
  
}
function student_book_return($tid,$sid,$book_id,$date,$time,$note,$user){
    $date = date2sql(Today());
    $time = time();
    
     $sql = "INSERT INTO " . TB_PREF . "sms_lib_book_return (entry_id,student_id, book_id, date, time, note,user) values (" . db_escape($tid) . "," . db_escape($sid) . ",
            " . db_escape($book_id) .",". db_escape($date) ."," . db_escape($time) ."," . db_escape($note) . "," . db_escape($user) .")";

     db_query($sql, "can not insert book record");
     

        $condition = array('book_id'=>$book_id);
        $field = array('id','available_books');
        $data = db_fetch(data_retrieve_condition("sms_lib_book_entry", $field, $condition));
        
        $avbook = ($data['available_books'] +1);
        
        $sqlbe = "UPDATE " . TB_PREF . "sms_lib_book_entry SET 
                available_books = $avbook 
                 WHERE id = " . db_escape($data['id']);
//        display_error($sqlbe);
        db_query($sqlbe, "could not update");
        
           $sqlap = "UPDATE " . TB_PREF . "sms_lib_approve_list SET 
                     received_date = ".db_escape($date).",received_by=".db_escape($user).",is_returned=1
                     WHERE request_id = ".db_escape($tid);
            //display_error($sqlap);
          db_query($sqlap, "could not update");  
          
}     
function get_sql_for_book_request_log($datasearch, $datavalue,$from_date,$to_date) {
    

     $sql = "SELECT 
        sr.student_id,
        CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name,
        sr.book_id,
        sr.date as requested_date,
        sr.due_date,
        lr.date as returned_date,
        sr.status
        FROM " . TB_PREF . "sms_lib_book_request sr
        LEFT JOIN " . TB_PREF . "sms_students_details sd ON sr.student_id = sd.student_id
        LEFT JOIN " . TB_PREF . "sms_lib_book_return lr ON sr.id = lr.entry_id";
     
     if ($datavalue!='') {
        $sql .= "  WHERE sr.$datasearch =" . db_escape($datavalue);
            
    }
   
    if ($from_date!='' && $to_date!='') {
        $sql .= "  WHERE sr.$datasearch BETWEEN " . db_escape($from_date)." AND ". db_escape($to_date);           
    }
    
//    display_error($sql);
    return $sql;
}


    
    
function get_sql_for_prnt_request_log($datasearch, $datavalue,$from_date,$to_date) {

     $sql = "SELECT 
        sr.student_id,
        CONCAT(sd.first_name,' ',sd.middle_name,' ',sd.last_name) as name,
        sr.book_id,
        sr.date as requested_date,
        sr.due_date,
        lr.date as returned_date,
        sr.status
        FROM " . TB_PREF . "sms_lib_book_request sr
        LEFT JOIN " . TB_PREF . "sms_students_details sd ON sr.student_id = sd.student_id
        LEFT JOIN " . TB_PREF . "sms_lib_book_return lr ON sr.id = lr.entry_id";
     
     if ($datavalue!='') {
        $sql .= "  WHERE sr.$datasearch =" . db_escape($datavalue);
            
    }
 
   
    if ($from_date!='' && $to_date!='') {
        $sql .= "  WHERE sr.$datasearch BETWEEN " . db_escape($from_date)." AND ". db_escape($to_date);
            
    }
          
    
//    display_error($sql);
    return $sql;
}


//----------------------------------------------------------------------------------------

function add_lib_config_list($fine_day,$queue_length,$issue_period,$due_date_reminder,$hold_period,$issue_limit)
{
    $sql = "INSERT INTO " . TB_PREF . "sms_lib_config (fine_day, queue_length, issue_period, due_date_reminder,hold_period,issue_limit) values (" . db_escape($fine_day) . ",
        " . db_escape($queue_length) .",". db_escape($issue_period) ."," . db_escape($due_date_reminder) . ",". db_escape($hold_period) .",". db_escape($issue_limit) .")";
    //display_error($sql);
    db_query($sql, "could not insert into table");
}



function view_lib_config()
{
    $sql = "SELECT * FROM ".TB_PREF."sms_lib_config" ;
    return db_query($sql);
    
}

function update_lib_config_list($id,$fine_day,$queue_length,$issue_period,$due_date_reminder,$hold_period,$issue_limit){
        $sql = "UPDATE " . TB_PREF . "sms_lib_config SET "
            . "fine_day = " . db_escape($fine_day)
            . " , " . "queue_length = " . db_escape($queue_length)
            . " , " . "issue_period = " . db_escape($issue_period)
            . " , " . "due_date_reminder = " . db_escape($due_date_reminder)
              . " , " . "hold_period = " . db_escape($hold_period)
                 . " , " . "issue_limit = " . db_escape($issue_limit)
            . " WHERE id = " . db_escape($id);
 //display_error($sql);
  db_query($sql, "could not be updated");
    
} 

function get_book_data($book_id, $tbl_name, $wh) {

    $sql = "SELECT  * FROM " . TB_PREF . "$tbl_name
            WHERE $wh=" . db_escape($book_id);
    //display_error($sql);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}


//----------------------------------------------------------------------

function get_sql_for_book_view($bid) {
  
    $sql = "SELECT 
        be.*,bt.*
        FROM " . TB_PREF . "sms_lib_book_entry be ,
            " . TB_PREF . "sms_lib_book_type_setup bt
            WHERE be.book_id =" . db_escape($bid);
   

   $res = db_fetch(db_query($sql, 'Could not get student data.'));
  
    return $res;
}

function get_sql_for_lib_book($bid) {

    $sql = "SELECT be.*,bt.*,br.*,ls.*
                    FROM " . TB_PREF . "sms_lib_book_entry be," . TB_PREF . "sms_lib_book_type_setup bt,
                          " . TB_PREF . "sms_lib_book_request br," . TB_PREF . "sms_lib_location_setup ls
                    WHERE be.book_id =" . db_escape($bid);

    $res = db_fetch(db_query($sql, 'Could not get schedule data.'));
    
    return $res;
    
}



//------------------artical----------------------------------
function add_artical($type,$artical,$details,$active)
{
     $dt = date2sql(Today());
    
    $sql = "INSERT INTO " . TB_PREF . "sms_lib_artical (type, artical, detail, active,insert_date) values (" . db_escape($type) . ",
        " . db_escape($artical) .",". db_escape($details) ."," . db_escape($active) . ",". db_escape($dt) .")";
    //display_error($sql);
    db_query($sql, "could not insert into table");
}

function add_artical1($type, $trans_no, $artical, $contbtr, $name, $active,
	$filesize, $filetype, $filename, $unique_name)
{
   
	$date = date2sql(Today());
	$sql = "INSERT INTO ".TB_PREF."sms_lib_artical (type, ref_number, artical, contributor, guid_teacher, active, filesize,
            filetype, insert_date, filename, unique_name) VALUES (".db_escape($type).","
		.db_escape($trans_no).",".db_escape($artical).",".db_escape($contbtr).", ". db_escape($name) .", ".db_escape($active).", "
		.db_escape($filesize).", ".db_escape($filetype)
		.",".db_escape($date).", ".db_escape($filename).", ". db_escape($unique_name) .")";
        
       // display_error($sql);
	db_query($sql, "Artical could not be inserted");		
}

function get_sql_for_attached_artical()
{
	 $sql = "SELECT @num:=@num+1,
                slb.book_type,
                sla.ref_number,                              
                sla.artical,
                sla.contributor,               
                sla.active,
                sla.insert_date,
                he.name,                                   
                sla.is_approved,
                sla.type,                
                sla.id                  
            FROM (SELECT @num:=0) as n," . TB_PREF . "sms_lib_artical sla           
            LEFT JOIN " . TB_PREF . "sms_lib_book_type_setup slb ON sla.type = slb.id 
            LEFT JOIN " . TB_PREF . "hcm_emp he ON sla.guid_teacher = he.emp_code";
     
        if($_POST['booktype']!=''){
        $sql.=" WHERE sla.type=".  db_escape($_POST['booktype']);
        }
        $sql.="  ORDER BY sla.id asc";
        //display_error($sql);
        return $sql;
}


function get_artical_attachment($id)
{
	$sql = "SELECT * FROM ".TB_PREF."sms_lib_artical WHERE id=".db_escape($id);
	$result = db_query($sql, "Could not retrieve attachments");
	return db_fetch($result);
}

function delete_artical_attachment($id)
{
	$sql = "DELETE FROM ".TB_PREF."sms_lib_artical WHERE id = ".db_escape($id);
	db_query($sql, "Could not delete attachment");
}


function get_artical($id)
{
	$sql = "SELECT * FROM ".TB_PREF."sms_lib_artical WHERE id=".db_escape($id);
	$result = db_query($sql, "Could not retrieve attachments");
	return db_fetch($result);
}


function view_artical()
{
    $sql = "SELECT lt.book_type,la.artical,la.detail,la.active,la.insert_date 
            FROM ".TB_PREF."sms_lib_artical la,".TB_PREF."sms_lib_book_type_setup lt WHERE la.type = lt.id";
    return ($sql);
    
}

function update_artical($id,$type,$artical,$details,$active){
        $sql = "UPDATE " . TB_PREF . "sms_lib_artical SET "
            . "type = " . db_escape($type)
            . " , " . "artical = " . db_escape($artical)
            . " , " . "detail = " . db_escape($details)
            . " , " . "active = " . db_escape($active)
            . " WHERE id = " . db_escape($id);
 //display_error($sql);
  db_query($sql, "Artical  not be updated");
    
}  

function update_artical_attachment($selected_id, $filterType, $trans_no, $artical, $contributor, $guid_teacher, $active,
	$filename, $unique_name, $filesize, $filetype)
{
	$date = date2sql(Today());
	$sql = "UPDATE ".TB_PREF."sms_lib_artical SET
		type=".db_escape($filterType).",
		ref_number=".db_escape($trans_no).",
		 artical=".db_escape($artical).",
                 contributor=".db_escape($contributor).",
                 active=".db_escape($active).",
                 guid_teacher=".db_escape($guid_teacher).",";
	if ($filename != "")
	{
		$sql .= "filename=".db_escape($filename).",
		unique_name=".db_escape($unique_name).",
		filesize=".db_escape($filesize).",
		filetype=".db_escape($filetype).",";
	}	
	$sql .= "insert_date='$date' WHERE id=".db_escape($selected_id);
	db_query($sql, "Attachment could not be updated");		
}

//---------------------publish---------------------------------
function get_sql_for_publish() {
    $sql = "SELECT bt.book_type,la.artical ,la.id
     FROM " . TB_PREF . "sms_lib_artical la 
     LEFT JOIN " . TB_PREF . "sms_lib_book_type_setup bt ON la.type = bt.id";
          
    
    return $sql;
}

function get_sql_for_article_view($arid) {
  
    $sql = "SELECT 
        artical,detail
        FROM " . TB_PREF . "sms_lib_artical
            WHERE id =" . db_escape($arid);
   

   $res = db_fetch(db_query($sql, 'Could not get artical details.'));
  
    return $res;
}
