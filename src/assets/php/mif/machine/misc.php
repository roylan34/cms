<?php
/**
* 01/25/2017
*
* This file contains the functions of machines in individual data fetching.
*
*/ 

require_once '../database.php';
require_once '../utils.php';

function getOnlyCompanyName($comp_id,$conn){ 
	$res = '';
	if(!empty($comp_id)){
	$conn->fields = null;
	$conn->selectQuery('company_name','tbl_company WHERE id = '.(int)$comp_id.'');
	$resultMachine = $conn->getFields();
	if($conn->getNumRows() > 0)
		foreach ($resultMachine['aaData'] as $keys => $val) {
			$res = $val['company_name'];
		}
	}
	return $res;
}

/*
*Check if company is blocked.
*return boolean.
*/
function getCompanyStatus($companyId,$db){ 
	if(!empty($companyId)){
			$db->selectQuery('status','tbl_company WHERE id = "'.$companyId.'" LIMIT 0,1');
			if($db->getNumRows() == 1){
				$resComp = $db->getFields();
				 if($resComp['aaData'][0]['status'] == 1){
				 	return 1;
				 }
				 return 0;
			}else{
				return 0;
			}
			
	}
	return 0;
}

/*
*Insert machine logs.
*return boolean.
*/
function machineLogs($company_id,$machine_id,$serialnumber,$user_id,$action,$conn){
	$date_now = Utils::getSysDate().' '.Utils::getSysTime();  
	if(!Utils::isEmpty($company_id)){
		$conn->insertQuery('tblmif_logs','company_id,machine_id,serialnumber,user_id,action,updated_at',
									  '"'.$company_id.'",
									  "'.$machine_id.'",
									  "'.Utils::uppercase($serialnumber).'",
									  "'.$user_id.'",
									  "'.Utils::uppercase($action).'",
									  "'.$date_now.'"');
	}
	return false;
}
