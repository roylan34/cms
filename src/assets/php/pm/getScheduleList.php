<?php
/**
* 06/20/2018
* Developed by: Delsan Web Development Team
*
* This file contains the json data format of Preventive Maintenance Schedules.
*
*/ 

require_once '../database.php';
require_once '../utils.php';


$search="";
$limit = "";
$searchBranch ="";
$totalData =0;
$totalFiltered =0;
$branch = Utils::getValue('branch');
$userid = Utils::getValue('userid');
$pm_type = Utils::getValue('pm_type');
$conn = Database::getInstance(); //For Searching.

if(Utils::getValue('pm_number'))		{ $search ="AND ps.pm_number ='".$conn->escapeString(Utils::getValue('pm_number'))."'"; }
if(Utils::getValue('company_name'))		{ $search ="AND com.company_name LIKE '%".$conn->escapeString(Utils::getValue('company_name'))."%'"; }
if(Utils::getValue('sched_date'))		{ $search ="AND ps.schedule_date ='".$conn->escapeString(Utils::getValue('sched_date'))."'"; }
if(Utils::getValue('technician'))		{ $search ="AND UPPER(CONCAT_WS(' ', ac.firstname, ac.lastname)) LIKE '%".$conn->escapeString(Utils::getValue('technician'))."%'"; }
if($pm_type == 'CONTROLLER' || $pm_type == 'MONITOR'){
	$search .= "AND ps.branch='".$branch."'";
	$searchBranch .= " AND ps.branch='".$branch."'";
}else{
	$search .= "AND pt.technician='".$userid."'";
	$searchBranch .= " AND pt.technician='".$userid."'";
}
$requestData= $_REQUEST;

switch (Utils::getValue('action')) {
	case 'current':
			// storing  request (ie, get/post) global array to a variable.  
			$conn->selectQuery('ps.*','tbl_pm_schedule ps 
				LEFT JOIN tbl_pm_technician pt ON ps.pm_number = pt.pm_number
				LEFT JOIN tbl_accounts ac ON pt.technician = ac.id
				WHERE (ps.status="pending" || ps.status="in-progress" || ps.status="done") > 0 '.$searchBranch.'');
			$totalData = $conn->getNumRows(); //getting total number records without any search.
			$conn->row_count = 0;
			$conn->fields = null;

			if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

				$conn->selectQuery('ps.id',' tbl_pm_schedule ps 
					LEFT JOIN tbl_company com ON ps.company_id = com.id
					LEFT JOIN tbl_pm_technician pt ON ps.pm_number = pt.pm_number
					LEFT JOIN tbl_accounts ac ON pt.technician = ac.id
					WHERE ps.id > 0 AND (ps.status="pending" || ps.status="in-progress" || ps.status="done") '.$search.' GROUP BY ps.pm_number');

				$conn->fields = null;
				$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			}
			else{
				$totalFiltered = $totalData;
			}
			
			if(intval($requestData['length']) >= 1 ) { $limit = ' LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }
				$conn->selectQuery('ps.id, ps.company_id, ps.pm_number, ps.schedule_date, GROUP_CONCAT(CONCAT(ac.firstname," ", ac.lastname) SEPARATOR "<br>") AS technician, CONCAT(ps.date_entered, " ", ps.time_entered) AS date_entered, ps.contact_name, ps.email_address, ps.department, com.company_name, ps.status',' tbl_pm_schedule ps 	
					LEFT JOIN tbl_company com ON ps.company_id = com.id
					LEFT JOIN tbl_pm_technician pt ON ps.pm_number = pt.pm_number
					LEFT JOIN tbl_accounts ac ON pt.technician = ac.id
					WHERE ps.id > 0 AND (ps.status="pending" || ps.status="in-progress" || ps.status="done") '.$search.' GROUP BY ps.id ORDER BY ps.id DESC '.$limit.' ');
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
		break;
	case 'archive':
		   // storing  request (ie, get/post) global array to a variable.  
			$conn->selectQuery('ps.*','tbl_pm_schedule ps 
				LEFT JOIN tbl_company com ON ps.company_id = com.id
				LEFT JOIN tbl_pm_technician pt ON ps.pm_number = pt.pm_number
				LEFT JOIN tbl_accounts ac ON pt.technician = ac.id
				WHERE (ps.status="cancel" || ps.status="close") > 0 '.$search.'');
			$totalData = $conn->getNumRows(); //getting total number records without any search.
			$conn->row_count = 0;
			$conn->fields = null;

			if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

				$conn->selectQuery('ps.id, ps.pm_number, ps.schedule_date, GROUP_CONCAT(CONCAT(ac.firstname," ", ac.lastname) SEPARATOR "<br>") AS technician, CONCAT(ps.date_entered, " ", ps.time_entered ) AS date_entered, ps.contact_name, ps.email_address, ps.department, com.company_name',' tbl_pm_schedule ps 
					LEFT JOIN tbl_company com ON ps.company_id = com.id
					LEFT JOIN tbl_pm_technician pt ON ps.pm_number = pt.pm_number
					LEFT JOIN tbl_accounts ac ON pt.technician = ac.id
					WHERE ps.id > 0 AND (ps.status="cancel" || ps.status="close") '.$search.' GROUP BY ps.pm_number');

				$conn->fields = null;
				$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			}
			else{
				$totalFiltered = $totalData;
			}
			
			if(intval($requestData['length']) >= 1 ) { $limit = ' LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }
				$conn->selectQuery('ps.id, ps.company_id, ps.pm_number, ps.schedule_date, GROUP_CONCAT(CONCAT(ac.firstname," ", ac.lastname) SEPARATOR "<br>") AS technician, CONCAT(ps.date_entered, " ", ps.time_entered) AS date_entered, ps.contact_name, ps.email_address, ps.department, com.company_name, ps.status',' tbl_pm_schedule ps 	
					LEFT JOIN tbl_company com ON ps.company_id = com.id
					LEFT JOIN tbl_pm_technician pt ON ps.pm_number = pt.pm_number
					LEFT JOIN tbl_accounts ac ON pt.technician = ac.id
					WHERE ps.id > 0 AND (ps.status="cancel" || ps.status="close") '.$search.' GROUP BY ps.pm_number ORDER BY ps.id DESC '.$limit.' ');
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

	break;

	default:
		echo "Empty action view";

	break;
}