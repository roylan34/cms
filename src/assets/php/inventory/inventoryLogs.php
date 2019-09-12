<?php
/*
* This file container json format of Inventory logs.
* 
*/

require_once '../database.php';
require_once '../utils.php';

$search = "";
$conn   = Database::getInstance();

$requestData= $_REQUEST;
$conn->selectQuery("sl.*","tbl_invnt_status_logs sl
						 LEFT JOIN tbl_invnt_status s ON sl.id_status = s.id 
						 WHERE sl.id_machine = ".$requestData['id_machine']."");
$totalData = $conn->getNumRows(); //getting total number records without any search.
$conn->row_count = 0;
$conn->fields = null;

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$search.=" AND ( CONCAT_WS(' ',ac.firstname,ac.lastname) LIKE '%".$conn->escapeString($requestData['search']['value'])."%' ";
	$search.=" OR sl.id_machine  LIKE '%".$conn->escapeString($requestData['search']['value'])."%' ";
	$search.=" OR s.status_name  LIKE '%".$conn->escapeString($requestData['search']['value'])."%' ";
	$search.=" OR c.company_name  LIKE '%".$conn->escapeString($requestData['search']['value'])."%' ";
	$search.=" OR sl.date_in_out LIKE '%".$conn->escapeString($requestData['search']['value'])."%')";

$conn->selectQuery('sl.id, sl.id_machine, s.status_name, sl.date_in_out, CONCAT(ac.firstname," ", ac.lastname) AS fullname, c.company_name, sl.remarks',
				    'tbl_invnt_status_logs sl
					LEFT JOIN tbl_invnt_status s ON sl.id_status = s.id
					LEFT JOIN tbl_accounts ac ON sl.id_user = ac.id
					LEFT JOIN tbl_company c ON sl.id_company = c.id
					WHERE sl.id_machine= "'.$requestData['id_machine'].'" '.$search.'');

	$conn->fields = null;
	$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
}
else{
	$totalFiltered = $totalData;
}

$conn->selectQuery('sl.id, sl.id_machine, sl.id_status, s.status_name, s.status_type, sl.date_in_out, CONCAT(ac.firstname," ", ac.lastname) AS fullname, c.company_name, sl.remarks',
				    'tbl_invnt_status_logs sl
					LEFT JOIN tbl_invnt_status s ON sl.id_status = s.id
					LEFT JOIN tbl_accounts ac ON sl.id_user = ac.id
					LEFT JOIN tbl_company c ON sl.id_company = c.id
					WHERE sl.id_machine= "'.$requestData['id_machine'].'" '.$search.' ORDER BY sl.id DESC LIMIT '.$requestData['start'].' ,'.$requestData['length'].'');
$row = $conn->getFields(); //Get all rows


$data = array();
$nestedData=array(); 
	foreach($row['aaData'] as $index=>$value) { // preparing an array
		$nestedData[$index] = $value;
	}
	$data = $nestedData; 
	

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),// for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  //total number of records
			"recordsFiltered" => intval( $totalFiltered ), //total number of records after searching, if there is no searching then totalFiltered = totalData
			"records"         => $data   // data array,
			);

print Utils::jsonEncode($json_data);  // send data as json format



