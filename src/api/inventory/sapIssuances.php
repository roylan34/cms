<?php
/**
* 06/04/2018
* Developed by: Delsan Web Development Team
*
* This file contains the json data format of stocks issues.
*
*/ 

require_once '../database.php';
require_once '../utils.php';


$search="";
$limit = "";
$totalData =0;
$totalFiltered =0;

$conn = Database::getInstance(); //For Searching.
if(Utils::getValue('company_name'))		{ $search ="AND company_name LIKE '%".$conn->escapeString(Utils::getValue('company_name'))."%'"; }
if(Utils::getValue('ref'))				{ $search .="AND ref_no = '".$conn->escapeString(Utils::getValue('ref'))."'"; }
if(Utils::getValue('date'))				{ $search .="AND doc_date = '".$conn->escapeString(Utils::getValue('date'))."'"; }
if(Utils::getValue('item'))				{ $search .="AND item_code = '".$conn->escapeString(Utils::getValue('item'))."'"; }
if(Utils::getValue('descrip'))			{ $search .="AND description = '".$conn->escapeString(Utils::getValue('descrip'))."'"; }
if(Utils::getValue('serial'))			{ $search .="AND serial = '".$conn->escapeString(Utils::getValue('serial'))."'"; }
if(Utils::getValue('trans'))			{ $search .="AND trans_type = '".$conn->escapeString(Utils::getValue('trans'))."'"; }

			$requestData= $_REQUEST;
			// storing  request (ie, get/post) global array to a variable  
			$conn->selectQuery('*',' tbl_invnt_issuances_auto_import');
			$totalData = $conn->getNumRows(); //getting total number records without any search.
			$conn->row_count = 0;
			$conn->fields = null;

			if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

			$conn->selectQuery('*','tbl_invnt_issuances_auto_import WHERE id > 0 '.$search.'');

				$conn->fields = null;
				$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			}
			else{
				$totalFiltered = $totalData;
			}
			
			if(intval($requestData['length']) >= 1 ) { $limit = ' LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }

				$conn->selectQuery('*','tbl_invnt_issuances_auto_import	WHERE id > 0 '.$search.' ORDER BY doc_date DESC '.$limit.'');
				$row = $conn->getFields(); //Get all rows

			if($conn->getNumRows() > 0 ){
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
			} 
			else{ 
				$json_data = array("draw" =>  0,"recordsTotal" => 0, "recordsFiltered" => 0, "records" => array());
				$json_data['aaData'] = array(); 
			}

				print Utils::jsonEncode($json_data);  // send data as json format.