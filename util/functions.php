<?php

/**
 * Methods are used frequently.
 * @author GGCoke
 * 2012-2-18 14:38:29
 */

/**
 * Get an instance of MySQL connection. $global_do_conn is a global variable.
 * @author GGCoke
 * @global type $global_do_conn
 */
function require_icg_conn() {
    global $global_do_conn;
    require_once (ABSPATH . "util/DB.class.php");

    if (isset($global_do_conn))
	return;

    $db = new DB(DB_DRIVER, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_DEBUG);
    $global_do_conn = $db->get_connection();
    
    /* Set the result fetch mode as ASSOC, ie using the name of coloum insdead of number.  */
    $global_do_conn->SetFetchMode(ADODB_FETCH_ASSOC);
    
    /** Get instance of connection failed. */
    if (is_null($global_do_conn)) {
	die("Failed getting connection. Please review the configuration.");
    }
}

/**
 * Set timezone of the system. Default is UTC
 * @author GGCoke
 */
function set_timezone() {
    if (defined('TIMEZONE')) {
	date_default_timezone_set(TIMEZONE);
    } else {
	date_default_timezone_set('UTC');
    }
}

/**
 * Get an array of result from ADOResultSet.
 * @param ADOResultSet $rs
 * @return array Return null if the count of the result if zero.
 */
function get_array_from_resultset($rs){
    if (!$rs || $rs->RecordCount() == 0)
	return null;
    
    $result = array();
    $column_count = $rs->FieldCount();
    $rs->Move(0);
    while ($row = $rs->FetchRow()){
	$array_of_row = array();
	for ($i = 0; $i < $column_count; $i++){
	    $column_name = $rs->FetchField($i)->name;
              //ADD BY Song jingwei
            if($column_name=='Chromosome') {
	  	  if($row[$column_name][0]=='0'){
                     $row[$column_name]=substr($row[$column_name],1);
                  } 
	      }  
	    $array_of_row[$column_name] = $row[$column_name];
	}
	array_push($result, $array_of_row);
    }
    return $result;
}

//end of script