<?php
/**
* 01/25/2017
*
* This file contains the json data format of all client.
*
*/
require_once '../../database.php';
require_once '../../utils.php';



if(Utils::getIsset('action')){
	$action     = Utils::getValue('action');
	$id         = Utils::getValue('idclient');
	$account_id = Utils::getValue('account_id');
	$old_account_id = Utils::getValue('old_account_id');
	$user 		= Utils::getValue('username');
	$pass 		= Utils::getValue('pass');
	$idemp 		= Utils::getValue('idemp');
	$fname 		= Utils::getValue('fname');
	$lname 		= Utils::getValue('lname');
	$mname 		= Utils::getValue('mname');
	$position 	= Utils::getValue('position');
	$companies 	= Utils::getValue('companies');
	$old_companies = Utils::getValue('old_companies');
	$status 	   = Utils::getValue('status');
	$id_emp 	   = Utils::getValue('id_emp');
	$date_created  = Utils::getSysDate().' '.Utils::getSysTime();  
	$db         = Database::getInstance();


	//For action check_exist validation.
	// $check_username  = Utils::getValue('txtClientUsername');
	$check_username  = Utils::getValue('slctAcctMngrName');
	$old_username 	 = Utils::getValue('old_username');
	$action_validate = Utils::getValue('action_validate');

	switch ($action) {
		case 'add':	
			if(checkNameExist(null, $account_id, "add", $db) === true) {
				$res['aaData']['isNameExist'] = 'true';
				exit(Utils::jsonEncode($res));
			}
				if(!empty($companies)){//Check if company is already taken.
							$db->fields = null;
							$db->selectQuery('company_name,id_client_mngr','tbl_company WHERE id IN ('.$companies.')');
							$resultCompany = $db->getFields();	
							$arr_filter_company = array_filter($resultCompany['aaData'], function($r) use ($id){
							    return ($r['id_client_mngr'] != 0 && $r['id_client_mngr'] != $id);
							});

							$fetch_val_idclient = array();
							foreach($resultCompany['aaData'] as $key => $value){
							    $fetch_val_idclient[] = $value['id_client_mngr'];
							}
							$get_companyname_exist = array();
							foreach ($arr_filter_company as $key => $value) {
								$get_companyname_exist[] = $value['company_name'];
							}

							$arr_companies = explode(',',$companies);
							if(count($arr_companies) == 1){  //count if $selected_idcompany is only one value.
							    if(in_array(0,$fetch_val_idclient) || $id == $fetch_val_idclient[0]){
										$acc_flag = 0;
							    }else{
							        	$acc_flag = 1;
							    }    
							}
							else{
							    if(count(array_unique($fetch_val_idclient)) == 1){//Check if all array values are the same, 1 = TRUE.
										$acc_flag = 0;
							    }else{
							        
							        if(count($arr_filter_company) == 0){
										$acc_flag = 0;
							        }else{
							        	$acc_flag = 1;
							        }							        
							    }
							}

								if($acc_flag == 0){
									$db->insertQuery('tbl_client_accounts','account_id, idemp, created_at',
										  '"'.$account_id.'",
										  "'.$id_emp.'",
										  "'.$date_created.'"');
									$res = $db->getFields();

									$last_id = $db->getLastId();//Get the last inserted id of client account.
									$db->updateQuery('tbl_company','id_client_mngr = "'.$last_id.'"','id IN ('.$companies.')'); 

									$res['aaData']['has_value_accmngr'] = 0;
									$res['aaData']['isNameExist'] = 'false';
									print Utils::jsonEncode($res);

							    }
							    else{
							    	$res['aaData']['exist_company'] = implode('<br>',$get_companyname_exist);
							    	$res['aaData']['has_value_accmngr'] = 1;
							    	$res['aaData']['isNameExist'] = 'false';
							    	print Utils::jsonEncode($res);
							   }


					}else{
							$db->insertQuery('tbl_client_accounts','account_id, idemp, created_at',
								  '"'.$account_id.'",
								  "'.$id_emp.'",
								  "'.$date_created.'"');
							$res = $db->getFields();
							$res['aaData']['has_value_accmngr'] = 0;
							$res['aaData']['isNameExist'] = 'false';
							print Utils::jsonEncode($res);
					}

			break;
		case 'update': 
				if(checkNameExist($old_account_id, $account_id, "update", $db) === true) {
					$res['aaData']['isNameExist'] = 'true';
					exit(Utils::jsonEncode($res));
				}

					if(!empty($companies)){//Check if company is already taken.
						$db->fields = null;
						$db->selectQuery('company_name,id_client_mngr','tbl_company WHERE id IN ('.$companies.')');
						$resultCompany = $db->getFields();	
						$arr_filter_company = array_filter($resultCompany['aaData'], function($r) use ($id){//Filter id_client_mngr not equal to $id.
						    return ($r['id_client_mngr'] != 0 && $r['id_client_mngr'] != $id);
						});

						$fetch_val_idclient = array();
						foreach($resultCompany['aaData'] as $key => $value){
						    $fetch_val_idclient[] = $value['id_client_mngr'];
						}
						$get_companyname_exist = array();
						foreach ($arr_filter_company as $key => $value) {
							$get_companyname_exist[] = $value['company_name'];
						}

						$arr_companies = explode(',',$companies);
						if(count($arr_companies) == 1){  //count if $selected_idcompany is only one value.
						    if(in_array(0,$fetch_val_idclient) || $id == $fetch_val_idclient[0]){
									$acc_flag = 0;
						    }else{
						        	$acc_flag = 1;
						    }    
						}
						else{
						    if(count(array_unique($fetch_val_idclient)) == 1){//Check if all array values are the same, 1 = TRUE.
									$acc_flag = 0;
						    }else{
						        
						        if(count($arr_filter_company) == 0){
									$acc_flag = 0;
						        }else{
						        	$acc_flag = 1;
						        }							        
						    }
						}

						if($acc_flag == 0){
							$old_companies = explode(',', $old_companies);
							if(count($arr_companies) < count($old_companies)){//Removing id_client, Options refer to $arr_filter_company.
								$res_diff_companies = array_diff($old_companies, $arr_companies);
								$db->updateQuery('tbl_company','id_client_mngr = null','id IN ('.implode(',',$res_diff_companies).')');
							}

							$db->updateQuery('tbl_company','id_client_mngr = "'.$id.'"','id IN ('.$companies.')'); //Assign new id_client.
			        		$db->fields = null;
			        		$db->updateQuery('tbl_client_accounts','account_id = "'.$account_id.'", idemp= "'.$id_emp.'" ','id = "'.$id.'"');

				        	 $res['aaData']['has_value_accmngr'] = 0;

					    }
					    else{
					    	$res['aaData']['exist_company'] = implode('<br>',$get_companyname_exist);
					    	$res['aaData']['has_value_accmngr'] = 1;
					   	}

							$res['aaData']['isNameExist'] = 'false';
							print Utils::jsonEncode($res);

					}else{

						if(!Utils::isEmpty($old_companies)){//Removing id_client, Options refer to $arr_filter_company.
							$db->updateQuery('tbl_company','id_client_mngr = null','id IN ('.$old_companies.')');
						}

							$db->fields = null;
			        		$db->updateQuery('tbl_client_accounts','account_id = "'.$account_id.'", idemp= "'.$id_emp.'"'
							     ,'id = "'.$id.'"');

							$res['aaData']['has_value_accmngr'] = 0;
							$res['aaData']['isNameExist'] = 'false';
							print Utils::jsonEncode($res);					
					}
					

			break;
		case 'view-all': 
				$db->fields = null;
				// $db->selectQuery('sc.id, CONCAT(ac.firstname," ",ac.middlename," ",ac.lastname) AS fullname, sc.company, ac.status, sc.created_at','tbl_client_accounts sc 
				// 					LEFT JOIN tbl_accounts ac ON sc.account_id = ac.id
				// 				 	ORDER BY sc.id');
				// $resultClient = $db->getFields();

				// if($db->getNumRows() > 0 ){
				// 	foreach ($resultClient as $key => $value) {
				// 	$count_val = count($value);
				// 	for ($i=0; $i < $count_val; $i++) { 
				// 		  $data['aaData'][$i] = $value[$i];
				// 		  $data['aaData'][$i]['company'] = getListofCompany($value[$i]['company'],$db);
				// 	}
				//   }
				// } 
				// else { $data['aaData'] = array(); }

				$db->selectQuery('sc.id, CONCAT(ac.firstname," ",ac.middlename," ",ac.lastname) AS fullname, (SELECT GROUP_CONCAT(company_name SEPARATOR "<br>") AS company FROM tbl_company
								WHERE STATUS = 1 && id_client_mngr = sc.id) AS company, ac.status, sc.created_at','
								tbl_client_accounts sc 
								LEFT JOIN tbl_accounts ac ON sc.account_id = ac.id
				 				ORDER BY sc.id');
				$resultClient = $db->getFields();

				if($db->getNumRows() > 0 ){
					$data = $resultClient;
				} 
				else { 
					$data['aaData'] = array();
				}

				print Utils::jsonEncode($data);
			
			break;
		case 'view-id': 
				$db->selectQuery('sc.id, sc.idemp, sc.account_id, CONVERT(GROUP_CONCAT(com.id SEPARATOR ",") USING "utf8") AS company, sc.created_at',
								'tbl_client_accounts sc 
								LEFT JOIN tbl_company com ON sc.id = com.id_client_mngr
								WHERE com.id_client_mngr = "'.$id.'"');
				print Utils::jsonEncode($db->getFields());
			
			break;		
		case 'check_exist': 

			break;
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}




function getListofCompany($strCompany,$db){
	if(is_string($strCompany) && !empty($strCompany)){
		$list = '';
		$db->fields = null;
		$db->selectQuery('company_name','tbl_company WHERE id IN ('.$strCompany.')');
		$res = $db->getFields();
		$countcomp = count($res['aaData']);
		for ($i=0; $i < $countcomp ; $i++) { 
			$list .= $res['aaData'][$i]['company_name'].'<br>';
		}
		return $list;
	}
	return '';
}

//Check if Account Manager ID is exist.
function checkNameExist($old_account_id, $new_account_id, $action_validate, $db){
	$db->fields = null;
	$db->selectQuery('ca.account_id','tbl_client_accounts ca 
						INNER JOIN tbl_accounts ac ON ca.account_id = ac.id 
						WHERE ca.account_id ="'.$new_account_id.'" AND ac.status = 1');
	$resName = $db->getFields();
	if(count($resName['aaData']) > 0){
		if ($action_validate == "add"){
			return true;
		}
		else{
			if ($old_account_id == $new_account_id){
				return false;	
			}
			// elseif ($resName['aaData'][0]['account_id'] == $old_account_id) {
			// 	return false;
			// }
			else{
				return true;								
			}
		}
	}
	else{
		// echo "not exist";
		return false;

	}
}




