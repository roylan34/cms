<?php
/**
* 06/20/2018
* Developed by: Delsan Web Development Team
*
* This file contains the json data format of Machines for Maintenance.
*
*/ 

require_once '../database.php';
require_once '../utils.php';


$search="";
$limit = "";
$totalData =0;
$totalFiltered =0;
$enabled_add = 'false';
$enabled_update = 'true';

$conn = Database::getInstance(); 
$pm_number = Utils::getValue('pm_number'); 
$company_id = Utils::getValue('company_id');
// storing  request (ie, get/post) global array to a variable.  
$requestData= $_REQUEST;

switch (Utils::getValue('action')) {
	case 'current':

	        /*Trappings:
	        *   Check the tbl_pm_machines by @pm_number if exist.
	        *   If exist, fetch data from tbl_pm_machines by @pm_number.
	        *   If not exist, fetch data from tblmif by @company_id.
	        */
	        $conn->selectQuery('*','tbl_pm_machines WHERE id > 0 AND is_delete= "no" AND pm_number="'.$pm_number.'"');

	        $conn->fields = null;

	        if($conn->getNumRows() > 0 ){
	        		$enabled_add = 'false';
	        		$enabled_update = 'true';

					if(Utils::getValue('serialnumber'))	{ $search ="AND ps.serialnumber LIKE '%".$conn->escapeString(Utils::getValue('serialnumber'))."%'"; }
					if(Utils::getValue('brand'))	{ $search ="AND br.brand_name='".$conn->escapeString(Utils::getValue('brand'))."'"; }
					if(Utils::getValue('model'))	{ $search ="AND ps.model LIKE '%".$conn->escapeString(Utils::getValue('model'))."%'"; }
					if(Utils::getValue('location'))	{ $search ="AND ps.location_area LIKE '%".$conn->escapeString(Utils::getValue('location'))."%'"; }
					if(Utils::getValue('department'))	{ $search ="AND ps.department LIKE '%".$conn->escapeString(Utils::getValue('department'))."%'"; }

	        	//Table @tbl_pm_machines					
					$conn->selectQuery('*','tbl_pm_machines WHERE id > 0 AND is_delete="no" AND pm_number="'.$pm_number.'" GROUP BY mif_id');
					$totalData = $conn->getNumRows(); //getting total number records without any search.
					$conn->row_count = 0;
					$conn->fields = null;

					if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

						$conn->selectQuery('ps.*',' tbl_pm_machines ps 
							LEFT JOIN tbl_brands br ON ps.brand = br.id
							WHERE ps.id > 0 AND ps.is_delete="no" AND ps.company_id='.$company_id.' AND ps.pm_number="'.$pm_number.'" '.$search.' GROUP BY ps.mif_id');

						$conn->fields = null;
						$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
					}
					else{
						$totalFiltered = $totalData;
					}
					
				if(intval($requestData['length']) >= 1 ) { $limit = ' LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }
						$conn->selectQuery('ps.id, ps.serialnumber, br.brand_name, ps.model, ps.location_area, ps.department, ps.no_of_user, ps.date_installed, ps.unit_owned AS unit_owned, ps.manufacture_date,
							ps.remarks, ps.page_count, IFNULL(GROUP_CONCAT(tr.toner_code SEPARATOR "<br>")," ") AS toner_use, ps.time_in, ps.time_out ',' tbl_pm_machines ps 
							LEFT JOIN tbl_brands br ON ps.brand = br.id
							LEFT JOIN tbl_toner_model_use tmu ON (ps.pm_number = tmu.pm_number AND tmu.mif_id = ps.mif_id)
							LEFT JOIN tbl_toner tr ON tmu.toner_id = tr.id
							WHERE ps.id > 0 AND ps.is_delete="no" AND ps.company_id='.$company_id.' AND ps.pm_number="'.$pm_number.'" '.$search.' 
							GROUP BY ps.mif_id ORDER BY ps.id DESC '.$limit.' ');
						$row = $conn->getFields(); //Get all rows				

	        } else {
	        	//Table @tblmif	
	        		$enabled_add = 'true';
	        		$enabled_update = 'false';
	        		if(Utils::getValue('serialnumber'))	{ $search ="AND m.serialnumber LIKE '%".$conn->escapeString(Utils::getValue('serialnumber'))."%'"; }
	        		if(Utils::getValue('brand'))	{ $search ="AND br.brand_name='".$conn->escapeString(Utils::getValue('brand'))."'"; }
					if(Utils::getValue('model'))	{ $search ="AND m.model LIKE '%".$conn->escapeString(Utils::getValue('model'))."%'"; }
					if(Utils::getValue('location'))	{ $search ="AND m.location_area LIKE '%".$conn->escapeString(Utils::getValue('location'))."%'"; }
					if(Utils::getValue('department'))	{ $search ="AND m.department LIKE '%".$conn->escapeString(Utils::getValue('department'))."%'"; }

					$conn->selectQuery('schedule_date','tbl_pm_schedule WHERE pm_number="'.$pm_number.'"'); //Trap the listing of machines by scheduled date.
        			$resSched = $conn->getFields();

	        		// $conn->selectQuery('*','tblmif WHERE id > 0 AND status_machine= 0 AND company_id='.$company_id.'');
	        		$conn->selectQuery('m.id, m.serialnumber, br.brand_name, m.model, m.location_area, m.department, m.no_of_user, m.date_installed, m.unit_owned_by AS unit_owned,
						  	 "" AS manufacture_date, "" AS remarks, "" AS page_count, "" AS toner_use, "" AS time_in, "" AS time_out','tblmif m
								LEFT JOIN tbl_brands br ON m.brand = br.id
								WHERE NOT EXISTS (SELECT pm.serialnumber FROM tbl_pm_machines pm 
								LEFT JOIN tbl_pm_schedule ps ON pm.pm_number = ps.pm_number
								WHERE pm.company_id = '.$company_id.' AND ps.schedule_date = "'.$resSched['aaData'][0]['schedule_date'].'" AND pm.serialnumber = m.serialnumber AND pm.is_delete ="no" AND ps.status NOT IN ("cancel")) AND m.status_machine= 0 AND m.company_id = '.$company_id.'');
	        		$totalData = $conn->getNumRows(); //getting total number records without any search.
					$conn->row_count = 0;
					$conn->fields = null;

					if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

						// $conn->selectQuery('m.*',' tblmif m 
						// 	LEFT JOIN tbl_brands br ON m.brand = br.id
						// 	WHERE m.id > 0 AND m.status_machine= 0 AND m.company_id='.$company_id.' '.$search.'');

						$conn->selectQuery('m.id, m.serialnumber, br.brand_name, m.model, m.location_area, m.department, m.no_of_user, m.date_installed, m.unit_owned_by AS unit_owned,
						  	 "" AS manufacture_date, "" AS remarks, "" AS page_count, "" AS toner_use, "" AS time_in, "" AS time_out','tblmif m
								LEFT JOIN tbl_brands br ON m.brand = br.id
								WHERE NOT EXISTS (SELECT pm.serialnumber FROM tbl_pm_machines pm 
								LEFT JOIN tbl_pm_schedule ps ON pm.pm_number = ps.pm_number
								WHERE pm.company_id = '.$company_id.' AND ps.schedule_date = "'.$resSched['aaData'][0]['schedule_date'].'" AND pm.serialnumber = m.serialnumber AND pm.is_delete ="no" AND ps.status NOT IN ("cancel")) AND m.status_machine= 0 AND m.company_id = '.$company_id.' '.$search.'');

						$conn->fields = null;
						$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
					}
					else{
						$totalFiltered = $totalData;
					}
					
					if(intval($requestData['length']) >= 1 ) { $limit = ' LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }
					// 	$conn->selectQuery('m.id, m.serialnumber, br.brand_name, m.model, m.location_area, m.department, m.no_of_user, m.date_installed, m.unit_owned_by AS unit_owned,
					// 	  	 "" AS manufacture_date, "" AS remarks, "" AS page_count, "" AS toner_use, "" AS time_in, "" AS time_out',' tblmif m 
					// 		LEFT JOIN tbl_brands br ON m.brand = br.id
					// 		WHERE m.id > 0 AND m.status_machine= 0 AND m.company_id='.$company_id.' '.$search.' ORDER BY m.id DESC '.$limit.' ');

						$conn->selectQuery('m.id, m.serialnumber, br.brand_name, m.model, m.location_area, m.department, m.no_of_user, m.date_installed, m.unit_owned_by AS unit_owned,
						  	 "" AS manufacture_date, "" AS remarks, "" AS page_count, "" AS toner_use, "" AS time_in, "" AS time_out','tblmif m
								LEFT JOIN tbl_brands br ON m.brand = br.id
								WHERE NOT EXISTS (SELECT pm.serialnumber FROM tbl_pm_machines pm 
								LEFT JOIN tbl_pm_schedule ps ON pm.pm_number = ps.pm_number
								WHERE pm.company_id = '.$company_id.' AND ps.schedule_date = "'.$resSched['aaData'][0]['schedule_date'].'" AND pm.serialnumber = m.serialnumber AND pm.is_delete ="no" AND ps.status NOT IN ("cancel")) AND m.status_machine= 0 AND m.company_id = '.$company_id.' '.$search.' ORDER BY m.id DESC '.$limit.' ');
						$row = $conn->getFields(); //Get all rows

	        }

	        	if($conn->getNumRows() > 0 ){
					$data = array();
					$nestedData=array(); 
						foreach($row['aaData'] as $index=>$value) { // preparing an array
							$nestedData[$index] = $value;
							$nestedData[$index]['enabled_update'] = $enabled_update;
						}
						$data = $nestedData; 
							
					$json_data = array(
							"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
							"recordsTotal"    => intval( $totalData ),  // total number of records
							"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
							"records"         => $data,   // data array,
							"enabledMultiSelectAdd" => $enabled_add
						);
				} 
				else{ 
					$json_data = array("draw" =>  0,"recordsTotal" => 0, "recordsFiltered" => 0, "records" => array(), "enabledMultiSelectAdd" => $enabled_add);
					$json_data['aaData'] = array(); 
				}
	       	 	print Utils::jsonEncode($json_data);  // send data as json format.
	        
		break;
	case 'archive':
					if(Utils::getValue('serialnumber'))	{ $search ="AND pm.serialnumber LIKE '%".$conn->escapeString(Utils::getValue('serialnumber'))."%'"; }
					if(Utils::getValue('brand'))	{ $search ="AND br.brand_name='".$conn->escapeString(Utils::getValue('brand'))."'"; }
					if(Utils::getValue('model'))	{ $search ="AND pm.model LIKE '%".$conn->escapeString(Utils::getValue('model'))."%'"; }
					if(Utils::getValue('location'))	{ $search ="AND pm.location_area LIKE '%".$conn->escapeString(Utils::getValue('location'))."%'"; }
					if(Utils::getValue('department'))	{ $search ="AND pm.department LIKE '%".$conn->escapeString(Utils::getValue('department'))."%'"; }

	        	//Table @tbl_pm_machines					
					$conn->selectQuery('*','tbl_pm_machines WHERE id > 0 AND is_delete="no" AND pm_number="'.$pm_number.'"');
					$totalData = $conn->getNumRows(); //getting total number records without any search.
					$conn->row_count = 0;
					$conn->fields = null;

					if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

						$conn->selectQuery('pm.*',' tbl_pm_machines pm 
							LEFT JOIN tbl_brands br ON pm.brand = br.id
							WHERE pm.id > 0 AND pm.company_id='.$company_id.' AND pm.pm_number="'.$pm_number.'" '.$search.'');

						$conn->fields = null;
						$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
					}
					else{
						$totalFiltered = $totalData;
					}
					
					if(intval($requestData['length']) >= 1 ) { $limit = ' LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }
						$conn->selectQuery('pm.id, pm.serialnumber, br.brand_name, pm.model, pm.location_area, pm.department, pm.no_of_user, pm.date_installed, pm.unit_owned, pm.manufacture_date,
							pm.remarks, pm.page_count, IFNULL(GROUP_CONCAT(tr.toner_code SEPARATOR "<br>")," ") AS toner_use, pm.time_in, pm.time_out ',' tbl_pm_machines pm 
							LEFT JOIN tbl_brands br ON pm.brand = br.id
							LEFT JOIN tbl_toner_model_use tmu ON (pm.pm_number = tmu.pm_number AND tmu.mif_id = pm.mif_id)
							LEFT JOIN tbl_toner tr ON tmu.toner_id = tr.id
							WHERE pm.id > 0 AND pm.is_delete="no" AND pm.company_id='.$company_id.' AND pm.pm_number="'.$pm_number.'" '.$search.' GROUP BY pm.mif_id ORDER BY pm.id DESC '.$limit.' ');
						$row = $conn->getFields(); //Get all rows				

	        	if($conn->getNumRows() > 0 ){
					$data = array();
					$nestedData=array(); 
						foreach($row['aaData'] as $index=>$value) { // preparing an array
							$nestedData[$index] = $value;
							$nestedData[$index]['enabled_update'] = $enabled_update;
						}
						$data = $nestedData; 
							
					$json_data = array(
							"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
							"recordsTotal"    => intval( $totalData ),  // total number of records
							"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
							"records"         => $data,   // data array,
						);
				} 
				else{ 
					$json_data = array("draw" =>  0,"recordsTotal" => 0, "recordsFiltered" => 0, "records" => array());
					$json_data['aaData'] = array(); 
				}
	       	 	print Utils::jsonEncode($json_data);  // send data as json format.

	break;
	case 'current-header': //For development, display machines deleted.
	        	//Table @tblmif	
				//Do not display machines if the assigned schedule was the same.

        		$conn->selectQuery('schedule_date','tbl_pm_schedule WHERE pm_number="'.$pm_number.'"'); //Trap the listing of machines by scheduled date.
        		$resSched = $conn->getFields();
        		$conn->fields = null;

	        	if($conn->getNumRows() > 0 ){

	        		$conn->selectQuery('mif.serialnumber, br.brand_name, mif.model','tblmif mif
										LEFT JOIN tbl_brands br ON mif.brand = br.id
										WHERE NOT EXISTS (SELECT pm.serialnumber FROM tbl_pm_machines pm 
										LEFT JOIN tbl_pm_schedule ps ON pm.pm_number = ps.pm_number
										WHERE pm.company_id = '.$company_id.' AND ps.schedule_date = "'.$resSched['aaData'][0]['schedule_date'].'" AND pm.mif_id = mif.id AND pm.is_delete ="no") AND mif.status_machine= 0 AND mif.company_id = '.$company_id.'');
	        		$totalData = $conn->getNumRows(); //getting total number records without any search.
					$conn->row_count = 0;
					$conn->fields = null;

					$totalFiltered = $totalData;
					
					if(intval($requestData['length']) >= 1 ) { $limit = ' LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }
		        		$conn->selectQuery('"0" AS id, mif.serialnumber, br.brand_name, mif.model','tblmif mif
										LEFT JOIN tbl_brands br ON mif.brand = br.id
										WHERE NOT EXISTS (SELECT pm.serialnumber FROM tbl_pm_machines pm 
										LEFT JOIN tbl_pm_schedule ps ON pm.pm_number = ps.pm_number
										WHERE pm.company_id = '.$company_id.' AND ps.schedule_date = "'.$resSched['aaData'][0]['schedule_date'].'" AND pm.mif_id = mif.id AND pm.is_delete ="no") AND mif.status_machine= 0 AND mif.company_id = '.$company_id.' ORDER BY mif.id DESC '.$limit.'');
						$row = $conn->getFields(); //Get all rows

	        	if($conn->getNumRows() > 0 ){
					$data = array();
					$nestedData=array(); 
						foreach($row['aaData'] as $index=>$value) { // preparing an array
							$nestedData[$index] = $value;
							$nestedData[$index]['enabled_update'] = $enabled_update;
						}
						$data = $nestedData; 
							
					$json_data = array(
							"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
							"recordsTotal"    => intval( $totalData ),  // total number of records
							"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
							"records"         => $data,   // data array,
						);
				} 
				else{ 
					$json_data = array("draw" =>  0,"recordsTotal" => 0, "recordsFiltered" => 0, "records" => array());
					$json_data['aaData'] = array(); 
				}
	       	 	print Utils::jsonEncode($json_data);  // send data as json format.
	       	 }
	       	 else{
	       	 	print Utils::jsonEncode(array('status'  => 'error', 'message' => 'Empty schedule date'));
	       	 }

	break;

	default:
		echo "Empty action view";

	break;
}