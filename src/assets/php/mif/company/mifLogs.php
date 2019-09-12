<?php
/*
* This file container json format of Client Machine logs.
* 
*/

require_once '../database.php';
require_once '../utils.php';

$search = "";
$conn   = Database::getInstance();


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$conn->selectQuery('company_id, serialnumber, user_id, action, updated_at',
	'tblmif_logs WHERE company_id= "'.$requestData['company_id'].'" 
	  UNION ALL
	 SELECT company_id, serialnumber, user_id, action, updated_at FROM tblmif_archive_logs
	 WHERE company_id= "'.$requestData['company_id'].'"');
$totalData = $conn->getNumRows(); //getting total number records without any search.
$conn->row_count = 0;
$conn->fields = null;

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$search.=" AND ( CONCAT_WS(' ',ac.firstname,ac.lastname) LIKE '%".$conn->escapeString($requestData['search']['value'])."%' ";
	$search.=" OR serialnumber  LIKE '%".$conn->escapeString($requestData['search']['value'])."%' ";
	$search.=" OR action  LIKE '%".$conn->escapeString($requestData['search']['value'])."%' ";
	$search.=" OR updated_at LIKE '%".$conn->escapeString($requestData['search']['value'])."%')";

$conn->selectQuery('logss.company_id, logss.serialnumber, CONCAT(ac.firstname," ", ac.lastname) AS fullname, logss.action, logss.updated_at',
	'tblmif_logs logss  LEFT JOIN tbl_accounts ac ON logss.user_id = ac.id WHERE company_id= "'.$requestData['company_id'].'" '.$search.' 
	  UNION ALL
	 SELECT alogs.company_id, alogs.serialnumber, CONCAT(ac.firstname," ", ac.lastname) AS fullname, alogs.action, alogs.updated_at FROM tblmif_archive_logs alogs
	 LEFT JOIN tbl_accounts ac ON alogs.user_id = ac.id
	 WHERE company_id= "'.$requestData['company_id'].'" '.$search.'');

	$conn->fields = null;
	$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
}
else{
	$totalFiltered = $totalData;
}

$conn->selectQuery('logss.company_id, logss.serialnumber, CONCAT(ac.firstname," ", ac.lastname) AS fullname, logss.action, logss.updated_at, "" AS reason',
	'tblmif_logs logss  LEFT JOIN tbl_accounts ac ON logss.user_id = ac.id WHERE company_id= "'.$requestData['company_id'].'" '.$search.' 
	  UNION ALL
	 SELECT alogs.company_id, alogs.serialnumber, CONCAT(ac.firstname," ", ac.lastname) AS fullname, alogs.action, alogs.updated_at, alogs.reason FROM tblmif_archive_logs alogs
	 LEFT JOIN tbl_accounts ac ON alogs.user_id = ac.id
	 WHERE company_id= "'.$requestData['company_id'].'" '.$search.'  ORDER BY updated_at DESC LIMIT '.$requestData['start'].' ,'.$requestData['length'].'');
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



