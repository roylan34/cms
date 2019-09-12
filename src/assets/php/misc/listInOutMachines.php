<?php
/**
* 06/20/2018
* Developed by: Delsan Web Development Team
*
* This file contains the json data format of Makati Branch stocks.
*
*/ 

require_once '../database.php';
require_once '../utils.php';


$search="";
$limit = "";
$totalData =0;
$totalFiltered =0;

$type = strtolower(Utils::getValue('type'));
$date_from 	= ( Utils::getValue('dateFrom') ? Utils::getValue('dateFrom') : Utils::getSysDate() );
$date_to 	= ( Utils::getValue('dateTo')   ? Utils::getValue('dateTo')   : Utils::getSysDate() );

$conn = Database::getInstance(); //For Searching.
if(Utils::getValue('serialnumber'))		{ $search ="AND m.serialnumber ='".$conn->escapeString(Utils::getValue('serialnumber'))."'"; }
if(Utils::getValue('brand'))			{ $search .="AND br.brand_name = '".$conn->escapeString(Utils::getValue('brand'))."'"; }
if(Utils::getValue('model'))			{ $search .="AND m.model LIKE '%".$conn->escapeString(Utils::getValue('model'))."%'"; }
if(Utils::getValue('category'))			{ $search .="AND com.company_name = '".$conn->escapeString(Utils::getValue('category'))."'"; }

			$requestData= $_REQUEST;
			// storing  request (ie, get/post) global array to a variable.  
			$conn->selectQuery('*',' tblmif WHERE id > 0 AND 
					(CASE 
						WHEN "'.$type.'"  = "in"  THEN date_in BETWEEN "'.$date_from.'" AND "'.$date_to.'" 
						WHEN "'.$type.'"  = "out" THEN date_out BETWEEN "'.$date_from.'" AND "'.$date_to.'" 
					END)');
			$totalData = $conn->getNumRows(); //getting total number records without any search.
			$conn->row_count = 0;
			$conn->fields = null;

			if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

			$conn->selectQuery('m.id, m.serialnumber, br.brand_name, m.model, com.company_name ','tblmif m
					LEFT JOIN tbl_brands br ON br.id = m.brand
					LEFT JOIN tbl_company com ON com.id = m.company_id
					WHERE m.id > 0 AND 
					(CASE 
						WHEN "'.$type.'"  = "in"  THEN m.date_in BETWEEN "'.$date_from.'" AND "'.$date_to.'" 
						WHEN "'.$type.'"  = "out" THEN m.date_out BETWEEN "'.$date_from.'" AND "'.$date_to.'" 
					END) '.$search.'');

				$conn->fields = null;
				$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			}
			else{
				$totalFiltered = $totalData;
			}
			
			if(intval($requestData['length']) >= 1 ) { $limit = ' LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }
				$conn->selectQuery('m.id, m.serialnumber, br.brand_name, m.model, com.company_name,( CASE 
						WHEN "'.$type.'"  = "in" THEN date_in
						WHEN "'.$type.'"  = "out" THEN date_out
					END ) as date','tblmif m
					LEFT JOIN tbl_brands br ON br.id = m.brand
					LEFT JOIN tbl_company com ON com.id = m.company_id
					WHERE m.id > 0 AND 
					(CASE 
						WHEN "'.$type.'"  = "in"  THEN m.date_in BETWEEN "'.$date_from.'" AND "'.$date_to.'" 
						WHEN "'.$type.'"  = "out" THEN m.date_out BETWEEN "'.$date_from.'" AND "'.$date_to.'" 
					END) '.$search.' '.$limit.'');
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