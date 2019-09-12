<?php
/**
* 01/25/2017
*
* This file contains the json data format of all machine list.
*
*/ 
require_once '../../core.php';
require_once '../../database.php';
require_once '../../utils.php';

$search="";
$limit = "";
$conn = Database::getInstance(); 
if(Utils::getValue('company_id'))     { $company_id ="AND company_id =".Utils::getValue('company_id').""; }
if(Utils::getValue('serialnum'))      { $search  .="AND m.serialnumber ='".Utils::getValue('serialnum')."'"; }
if(Utils::getValue('brand'))          { $search  .="AND m.brand =".Utils::getValue('brand').""; }

switch (Utils::getValue('action_view')) {
    case 'current':
			$requestData= $_REQUEST;
			// storing  request (ie, get/post) global array to a variable  
			$conn->selectQuery('*','tblmif WHERE status_machine = 0 '.$company_id.' ');
			$totalData = $conn->getNumRows(); //getting total number records without any search.
			$conn->row_count = 0;
			$conn->fields = null;

			if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

                $conn->selectQuery('m.*','tblmif m WHERE m.status_machine = 0 '.$company_id.' '.$search.'');

				$conn->fields = null;
				$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			}
			else{
				$totalFiltered = $totalData;
			}
			
			if(intval($requestData['length']) >= 1 ) { $limit = 'LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }
			$conn->selectQuery('m.id,
                                m.company_id,
                                com.company_name,
                                m.client_category,
                                m.brand, 
                                m.model, 
                                (SELECT GROUP_CONCAT(t.toner_code SEPARATOR "<br>") AS toner_code FROM tbl_toner_model tm
                                LEFT JOIN tbl_toner t ON tm.toner_id = t.id
                                WHERE tm.model = m.model) as toner_code, 
                                m.category, 
                                m.type, 
                                m.serialnumber, 
                                m.page_count, 
                                m.location_area, 
                                m.department, 
                                m.no_of_user,
                                m.remarks, 
                                m.date_installed, 
                                m.unit_owned_by, 
                                m.billing_type,
                                m.status_machine,
                                b.branch_name AS branches,
                                br.brand_name AS brand_name,
                                com.date_last_visit,
                                CONCAT(ac.firstname," ",ac.lastname) AS account_manager','tblmif m 
								LEFT JOIN tbl_location b ON m.branches = b.id 
								LEFT JOIN tbl_brands br ON m.brand = br.id
								LEFT JOIN tbl_company com ON m.company_id = com.id
								LEFT JOIN tbl_client_accounts ca ON com.id_client_mngr = ca.id
								LEFT JOIN tbl_accounts ac ON ca.account_id = ac.id
								WHERE m.status_machine = 0 '.$company_id.' '.$search.' ORDER BY com.company_name ASC '.$limit.'');
			$row = $conn->getFields(); //Get all rows

			if($conn->getNumRows() > 0 ){
				$data = array();
				$nestedData=array(); 
					foreach($row as $index=>$value) { // preparing an array
						$nestedData[$index] = $value;
					}
					$data = $nestedData; 
					
				$json_data = array(
							"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
							"recordsTotal"    => intval( $totalData ),  // total number of records
							"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
							"records"         => $data['aaData']   // data array,
							);
			} 
			else{ 
				$json_data = array("draw" =>  0,"recordsTotal" => 0, "recordsFiltered" => 0, "records" => array());
			}

				print Utils::jsonEncode($json_data);  // send data as json format.
		break;
	case 'archive':
        //Code
		break;
	
	default:
		echo "Empty action view";
		break;
}
