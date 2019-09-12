<?php
/**
* 01/25/2017
* Developed by: Delsan Web Development Team
*
* This file contains the json data format of all company list.
*
*/ 

require_once '../../core.php';
require_once '../../database.php';
require_once '../../utils.php';

$search="";
$count_mif_branch = "";
$has_branch = "";
$limit = "";
$conn = Database::getInstance(); //For Searching.
if(Utils::getValue('company'))	{ $search .=" AND c.company_name LIKE '%".$conn->escapeString(Utils::getValue('company'))."%'"; }
if(Utils::getValue('category'))	{ $search .=" AND c.client_category LIKE '%".$conn->escapeString(Utils::getValue('category'))."%'"; }
if(Utils::getValue('address'))	{ $search .=" AND c.address LIKE '%".$conn->escapeString(Utils::getValue('address'))."%'"; }
if(Utils::getValue('s_location')) { $search .=" AND c.main_location =".$conn->escapeString(Utils::getValue('s_location'))." "; }
if(Utils::getValue('s_branch')) { $search .=" AND cbr.id_branches IN (".$conn->escapeString(Utils::getValue('s_branch')).")"; }
if(Utils::getValue('contactno')){ $search .=" AND c.contact_no='".$conn->escapeString(Utils::getValue('contactno'))."'"; }
if(Utils::getValue('accmngr'))  { $search .=" AND c.id_client_mngr ='".$conn->escapeString(Utils::getValue('accmngr'))."'"; }
if(Utils::getValue('status'))   { $search .=" AND c.status ='".$conn->escapeString(Utils::getValue('status'))."'"; }
if(Utils::getValue('delsan_comp')) { $search .=" AND c.delsan_company ='".$conn->escapeString(Utils::getValue('delsan_comp'))."'"; }
if(Utils::getValue('toner_model')) { $search .=" AND tmu.toner_id ='".$conn->escapeString(Utils::getValue('toner_model'))."'"; }

if(Utils::getValue('branch'))   { 
	$count_mif_branch =" AND branches IN (".$conn->escapeString(Utils::getValue('branch')).")"; //Condition of counting no of branches.
	$has_branch =" AND cbr.id_branches IN (".$conn->escapeString(Utils::getValue('branch')).")";//Condition to get total number records.
}

switch (Utils::getValue('action_view')) {
	case 'company':
			$requestData= $_REQUEST;
			// storing  request (ie, get/post) global array to a variable  
			$conn->selectQuery('c.*','tbl_company c 
						LEFT JOIN tbl_company_branches cbr ON c.id = cbr.id_company
			  			WHERE c.status = 1 AND c.id > 0 '.$has_branch.' GROUP BY id');
			$totalData = $conn->getNumRows(); //getting total number records without any search.
			$conn->row_count = 0;
			$conn->fields = null;

			if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

			$conn->selectQuery('c.*','tbl_company c 
						LEFT JOIN tbl_company_branches cbr ON c.id = cbr.id_company
						LEFT JOIN tbl_toner_model_use tmu ON c.id = tmu.company_id
			  			WHERE c.status = 1 AND c.id > 0 '.$search.' GROUP BY id');

				$conn->fields = null;
				$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			}
			else{
				$totalFiltered = $totalData;
			}
			
			if(intval($requestData['length']) >= 1 ) { $limit = 'LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }

			$conn->selectQuery('c.*, GROUP_CONCAT(br.id) as id_branch, CONCAT(ac.firstname," ",ac.lastname) AS account_mngr_name, GROUP_CONCAT(DISTINCT br.branch_name SEPARATOR "<br>") AS branches, mbr.branch_name AS main_location, (SELECT count(*) FROM tblmif WHERE company_id = c.id AND status_machine = 0 '.$count_mif_branch.' GROUP BY company_id) as number_of_machines','tbl_company c 
						LEFT JOIN tbl_client_accounts ca ON c.id_client_mngr = ca.id
						LEFT JOIN tbl_accounts ac ON ca.account_id = ac.id
						LEFT JOIN tbl_company_branches cbr ON c.id = cbr.id_company
						LEFT JOIN tbl_location br ON cbr.id_branches = br.id
						LEFT JOIN tbl_location mbr ON c.main_location = mbr.id
						LEFT JOIN tbl_toner_model_use tmu ON c.id = tmu.company_id
			  			WHERE c.status = 1 AND c.id > 0 '.$search.' GROUP BY c.id ORDER BY IF(c.company_name RLIKE "^[a-z]", 1, 2), c.company_name '.$limit.'');
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
	case 'account_manager':
		  if (Utils::getValue('user_id')) {
		  		// $search .="AND c.id IN (".Utils::getValue('client_company_own').")"; //List of Companies handled.
		  		 $search .="AND c.id_client_mngr = (SELECT id FROM tbl_client_accounts WHERE account_id = ".Utils::getValue('user_id').")"; //List of Companies handled.

  	  			$conn->selectQuery('c.*, (SELECT count(*) FROM tblmif WHERE company_id = c.id AND status_machine = 0 '.$count_mif_branch.' GROUP BY company_id) as number_of_machines','tbl_company c 
				LEFT JOIN tbl_company_branches cbr ON c.id = cbr.id_company
				LEFT JOIN tbl_location br ON cbr.id_branches = br.id
	  			WHERE c.id > 0 '.$search.' GROUP BY c.id ORDER BY IF(c.company_name RLIKE "^[a-z]", 1, 2), c.company_name');

				$resultComp = $conn->getFields();
				if($conn->getNumRows() > 0 ){
					foreach ($resultComp as $key => $value) {
						$count_val = count($value);
						for ($i=0; $i < $count_val; $i++) { 
							  $data['aaData'][$i] = $value[$i];
						}
					 }
				} 
				else{ 
					$data['aaData'] = array(); 
				}
		  }
		  else{
		  	  $data['aaData'] = array();
		  }
		  	print Utils::jsonEncode($data); // send data as json format

		break;
	case 'archive_company':
			$requestData= $_REQUEST;
			// storing  request (ie, get/post) global array to a variable  
			$conn->selectQuery('c.*','tbl_company c 
						LEFT JOIN tbl_company_branches cbr ON c.id = cbr.id_company
						WHERE c.status = 0 AND c.id > 0 '.$has_branch.' GROUP BY id');
			$totalData = $conn->getNumRows(); //getting total number records without any search.
			$conn->row_count = 0;
			$conn->fields = null;

			if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

			$conn->selectQuery('c.*','tbl_company c 
						LEFT JOIN tbl_company_branches cbr ON c.id = cbr.id_company
						WHERE c.status = 0 AND c.id > 0 '.$search.' GROUP BY id');

				$conn->fields = null;
				$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			}
			else{
				$totalFiltered = $totalData;
			}
			
			if(intval($requestData['length']) >= 1 ) { $limit = 'LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }

			$conn->selectQuery('c.*, GROUP_CONCAT(br.id) as id_branch, CONCAT(ac.firstname," ",ac.lastname) AS account_mngr_name, GROUP_CONCAT(br.branch_name SEPARATOR "<br>") AS branches, mbr.branch_name AS main_location, (SELECT count(*) FROM tblmif WHERE company_id = c.id AND status_machine = 0 '.$count_mif_branch.' GROUP BY company_id) as number_of_machines','tbl_company c 
						LEFT JOIN tbl_client_accounts ca ON c.id_client_mngr = ca.id
						LEFT JOIN tbl_accounts ac ON ca.account_id = ac.id
						LEFT JOIN tbl_company_branches cbr ON c.id = cbr.id_company
						LEFT JOIN tbl_location br ON cbr.id_branches = br.id
						LEFT JOIN tbl_location mbr ON c.main_location = mbr.id
						WHERE c.status = 0 AND c.id > 0 '.$search.' GROUP BY id ORDER BY IF(c.company_name RLIKE "^[a-z]", 1, 2), c.company_name '.$limit.'');
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




