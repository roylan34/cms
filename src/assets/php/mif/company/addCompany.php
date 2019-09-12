<?php
/**
* 01/25/2017
*
* This file is to add new data of company.
*
*/ 

require_once '../database.php';
require_once '../utils.php';
require_once 'misc.php';

$company   = Utils::getValue('company');
$category  = Utils::getValue('category'); 
$address   = Utils::getValue('address'); 
$branch    = Utils::getValue('branch');
$location  = Utils::getValue('location');
$contactno = Utils::getValue('contactno'); 
$accmngr   = Utils::getValue('accmngr');
$user_id   = Utils::getValue('user_id');
$date_created 		 = Utils::getSysDate().' '.Utils::getSysTime();  
$selected_branch_exp = (!empty($branch) ? explode(',',$branch) : array());
$last_visit 		 = Utils::getValue('last_visit');
$client_service		 = Utils::getValue('client_service');
$sap_code			 = Utils::getValue('sap_code');
$delsan_comp         = Utils::getValue('delsan_comp');
$lat                 = Utils::getValue('lat');
$lng                 = Utils::getValue('lng');



	$conn = Database::getInstance();
	$conn->insertQuery('tbl_company','company_name,client_category,address,contact_no,id_client_mngr, main_location, date_last_visit, sap_code, delsan_company, latitude, longitude',
									  '"'.Utils::uppercase($company).'",
									  "'.Utils::uppercase($category).'",
									  "'.Utils::uppercase($address).'",
									  "'.(int)$contactno.'",
									  "'.(int)$accmngr.'",
									  "'.$location.'",
									  "'.$last_visit.'",
									  "'.$sap_code.'",
									  "'.$delsan_comp.'",
									  "'.$lat.'",
									  "'.$lng.'"');
	
	$last_id   = $conn->getLastId();
	companyLogs($last_id,$user_id,'CREATE',$conn);//Logs Insert action.

	//Commented 07-06-2018
	// if(!empty($accmngr)){ 	//Update tbl_client_accounts company field.
	// 	$companies = getCompany($accmngr, $conn);

	// 	array_push($companies,$last_id); //Add last inserted company id to tbl_client_account.
	// 	$conn->updateQuery('tbl_client_accounts','company= "'.(count($companies) > 0 ? implode(',',$companies): '').'"','id = "'.(int)$accmngr.'"'); 
	// }

	if(count($selected_branch_exp) > 0){ //Insert Company Branches.
		foreach($selected_branch_exp AS $key => $id_branches){
	        	$conn->insertQuery('tbl_company_branches','id_company, id_branches',
										  '"'.(int)$last_id.'",
										  "'.(int)$id_branches.'"');
	    }
	}

	print Utils::jsonEncode($conn->getFields());


