<?php
require_once '../../utils.php';


function getCompany($selected_accmngr,$conn){ //This func is used to retain the no. of machines when searching.
  $res = array();
	if(!Utils::isEmpty($selected_accmngr)){
		$conn->fields = null;
		$conn->selectQuery('company','tbl_client_accounts WHERE id='.$selected_accmngr.'');
		$resultComp = $conn->getFields();
		if($conn->getNumRows() > 0)
			$data = $resultComp['aaData'][0]['company'];
			if(empty($data)){
				return $res;
			}else{
		    	$data = explode(',',$data); 
		    	return $data;
		    }
	}
return $res;
}

function companyLogs($company_id,$user_id,$action,$conn){
	$date_now = Utils::getSysDate().' '.Utils::getSysTime();  
	if(!Utils::isEmpty($company_id)){
		$conn->insertQuery('tbl_company_logs','company_id,user_id,action,updated_at',
									  '"'.$company_id.'",
									  "'.(int)$user_id.'",
									  "'.Utils::uppercase($action).'",
									  "'.$date_now.'"');
	}
	return false;
}
