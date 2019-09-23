<?php
/*
* This file container json format of Accounts company login logs.
* 
*/

require_once '../database.php';
require_once '../utils.php';

$search = "";
$conn   = Database::getInstance();


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$conn->selectQuery('*','tbl_accounts_login_logs');
$totalData = $conn->getNumRows(); //getting total number records without any search.
$conn->row_count = 0;
$conn->fields = null;

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$search.=" WHERE ( CONCAT_WS(' ',ac.firstname,ac.lastname) LIKE '%".$conn->escapeString($requestData['search']['value'])."%' ";    
	$search.=" OR acl.date_log  LIKE '%".$conn->escapeString($requestData['search']['value'])."%' ";
	$search.=" OR acl.ip_address  LIKE '%".$conn->escapeString($requestData['search']['value'])."%' ";
	$search.=" OR acl.description LIKE '%".$conn->escapeString($requestData['search']['value'])."%' )";

	$conn->selectQuery('acl.*, CONCAT(ac.firstname," ", ac.lastname) AS fullname','tbl_accounts_login_logs acl LEFT JOIN tbl_accounts ac ON acl.id_name = ac.id '.$search.' ');
	$conn->fields = null;
	$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
}
else{
	$totalFiltered = $totalData;
}

$conn->selectQuery("acl.*, CONCAT(ac.firstname,' ', ac.lastname) AS fullname","tbl_accounts_login_logs acl LEFT JOIN tbl_accounts ac ON acl.id_name = ac.id ".$search." ORDER BY date_log DESC LIMIT ".$requestData['start']." ,".$requestData['length']." ");
$row = $conn->getFields(); //Get all rows


$data = array();
$nestedData=array(); 
	foreach($row['aaData'] as $index=>$value) { // preparing an array
		$nestedData[$index] = $value;
	}
	$data = $nestedData; 
	

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"records"         => $data   // data array,
			);

print Utils::jsonEncode($json_data);  // send data as json format



