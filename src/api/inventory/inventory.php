<?php
/**
* 07/26/2017
*
* This file is contains Add/Update/View/Validate of Current/Archive Inventory table.
*
*/ 

require_once '../database.php';
require_once '../utils.php';
require_once 'misc.php';



if(Utils::getIsset('action')){
	//For inputs
	$action     = Utils::getValue('action');
	$id_machine = Utils::getValue('id_machine');
	$serialnum  = Utils::getValue('serialnum');
	$brand  	= Utils::getValue('brand');
	$model 	 	= Utils::getValue('model');
	//$type  		= Utils::getValue('type');
	//$category  	= Utils::getValue('category');
	$condi  	= Utils::getValue('bnrf');
	$location  	= Utils::getValue('location');
	$branch  	= Utils::getValue('branch');
	$date_entered = Utils::getSysDate().' '.Utils::getSysTime();

	//For inputs IN and OUT machine
	$status     = Utils::getValue('status'); 
	$company    = Utils::getValue('company');
	$client_loc = Utils::getValue('client_location');
	$date    	= Utils::getValue('date');
	$remarks   	= Utils::getValue('remarks');
	$convert_date = date_create($date);
	$status_type = Utils::getValue('status_type');
	$id_user 	= Utils::getValue('id_user');
	//For Searching
	$search ="";
	if ($status_type == 'IN') { $search .= 'AND (m.id_status = 0 OR s.status_type = "IN")'; } else { $search .= 'AND s.status_type = "OUT"'; }
    if ($branch) { $search .= 'AND m.id_branch ='.$branch.''; }

	$db         = Database::getInstance();

	//For action check_exist validation.
	$check_sn 	   	= Utils::getValue('txtCurInvntSerial');
	$old_sn 	 	= Utils::getValue('old_sn');
	$action_exist  	= Utils::getValue('action_exist');

	switch ($action) {
		case 'add':
		          $arr_serialnum = explode(',',Utils::uppercase($serialnum));
		          $is_duplicate_sn = chckDuplicateArrayVal($arr_serialnum);
		          if(count($is_duplicate_sn) > 0){ //Count if has value mean has duplicate SN.
		          		$data['aaData'][] = "Duplicate S/N: <em>" . implode(',',$is_duplicate_sn)."</em>";
		          }
		          else{
						$db->insertMultipleByUniqueQuery('tbl_invnt_machines','serialnumber,id_brand,model,id_condition,location,id_branch,date_entered', $arr_serialnum,
									  '"'.$brand.'",
									  "'.$model.'",
									  "'.$condi.'",
									  "'.$location.'",
									  "'.$branch.'", 
									  "'.$date_entered.'"');
						$data = $db->getFields();
					}
                  		print Utils::jsonEncode($data);
			break;
		case 'update':
					if($id_machine){
						$db->updateQuery('tbl_invnt_machines','serialnumber = "'.Utils::uppercase($serialnum).'", 
														id_brand = "'.$brand.'",
														model 	 = "'.$model.'",
														id_condition = "'.$condi.'",
														location  = "'.$location.'",
														id_branch = "'.$branch.'"'
										    			,'id = "'.$id_machine.'"');
				 		print Utils::jsonEncode($db->getFields());
				 	}
			break;
		case 'update_out':
					if($id_machine){

						if($status == 9){ // 9 = transfer, if that is status value update only the branch.
							$db->updateQuery('tbl_invnt_machines','id_branch = "'.$branch.'"'
											    			,'id IN ('.$id_machine.')');
							machineLogs($id_machine,$status,date_format($convert_date,'Y-m-d'),$id_user,"",$remarks,$db);
						}else{
							$db->updateQuery('tbl_invnt_machines','id_status = "'.$status.'", 
															id_company 	 	   = "'.$company.'",
															date_return_deploy = "'.date_format($convert_date,'Y-m-d').'",
															id_client_location = "'.$client_loc.'"'
											    			,'id IN ('.$id_machine.')');
						  	machineLogs($id_machine,$status,date_format($convert_date,'Y-m-d'),$id_user,$company,$remarks,$db);
					  	}

				 		print Utils::jsonEncode($db->getFields());
				 	}
			break;
		case 'update_in':
					if($id_machine){
						
						//Check the unit if from Demo unit.
						$db->selectQuery("s1.id_mrf, m.id_company, s1.s1_serialnum","tbl_mrf_s1 s1
						INNER JOIN tbl_mrf m ON s1.id_mrf = m.id
						INNER JOIN tbl_mrf_request_tracker rt ON s1.id_mrf = rt.id_mrf
						WHERE s1.s1_serialnum LIKE CONCAT('%',(SELECT serialnumber FROM tbl_invnt_machines WHERE id = ".$id_machine."),'%') AND m.id_company = '".$company."' AND m.s2_radio_id = 3 AND rt.flag_completion = 'complete' LIMIT 1");
						$data = $db->getFields();
						$checkDemo = $data['aaData'];
						if($db->getNumRows() > 0){
							//Insert table history.
							$db->insertQuery('tbl_mrf_history','id_mrf, company_id, remarks, date_created, serial_num', 
												''.$checkDemo[0]['id_mrf'].', 
												"'.$company.'", 
												"returned", 
												"'.$date_entered.'", 
												"'.$serialnum.'"');							
						}
						$db->fields = null;
						//Move to current page.
						$db->updateQuery('tbl_invnt_machines','id_status = "'.$status.'", 
														 date_return_deploy = "'.date_format($convert_date,'Y-m-d').'"'
										    			,'id = '.$id_machine.'');
						machineLogs($id_machine,$status,date_format($convert_date,'Y-m-d'),$id_user,$company,$remarks,$db);
				 		print Utils::jsonEncode($db->getFields());
				 	}
			break;
		case 'get-company-name':
					if($id_machine){
						$db->selectQuery("c.id, c.company_name, m.serialnumber","tbl_invnt_machines m 
						LEFT JOIN tbl_company c ON m.id_company = c.id
						WHERE m.id = ".$id_machine."");

					 	print Utils::jsonEncode($db->getFields());
				 	}
			break;
		case 'view-id':
					$db->selectQuery("m.*, mo.id_category, mo.id_type","tbl_invnt_machines m 
						LEFT JOIN tbl_model mo ON m.model = mo.id
						WHERE m.id = ".$id_machine."");
					 print Utils::jsonEncode($db->getFields());
					
			break;	
		case 'view-all': //Current and Archive
					$db->selectQuery("m.id, m.id_company, m.serialnumber, b.brand_name, mo.model_name as model, cat.cat_name, t.type_name, con.acronym_name, m.location, br.branch_name, m.date_entered, s.status_name, c.company_name, m.date_return_deploy, con.acronym_name_def",
						"tbl_invnt_machines m
						LEFT JOIN tbl_brands b ON m.id_brand = b.id
						LEFT JOIN tbl_model mo ON m.model = mo.id
						LEFT JOIN tbl_category cat ON mo.id_category = cat.id
						LEFT JOIN tbl_type t ON mo.id_type = t.id
						LEFT JOIN tbl_invnt_condition con ON m.id_condition = con.id
						LEFT JOIN tbl_branch br ON m.id_branch = br.id
						LEFT JOIN tbl_invnt_status s ON m.id_status = s.id
						LEFT JOIN tbl_company c ON m.id_company = c.id 
						WHERE m.is_delete = 0 ".$search." ORDER BY m.id DESC ");
					if($db->getNumRows() > 0){
						$data = $db->getFields();
					}else{
						$data['aaData'] = array();
					}

					print Utils::jsonEncode($data);
				
			break;	
		case 'check_exist':
				$db->selectQuery('*','tbl_invnt_machines WHERE serialnumber ="'.$check_sn.'"');
				$sn = $db->getFields();
				if($db->getNumRows() > 0){
					if($action_exist == "add"){
						echo json_encode(array($sn['aaData'][0]['serialnumber'] ." is already exist."));
					}
					else{
						if(strtolower($old_sn) == strtolower($check_sn)){
							echo "true";
						}else{
							echo json_encode(array($sn['aaData'][0]['serialnumber'] ." is already exist."));
						}
					}
				}
				else{
					echo "true";
				}

			break;
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}


function chckDuplicateArrayVal($arr_val){
 $out = array();
 $count_val_exist = array_count_values($arr_val);
 $i = 1;
 	foreach($count_val_exist as $key => $val){
    	if($val > 1){
      		$out[$i] = $key;
    }
    $i++;
 }
    return $out;
}

function getSerialNumberExistMif($id_machine,$db){ //For Development
	$db->selectQuery("serialnumber","tbl_invnt_machines WHERE id IN (".$id_machine.")");
	$invnt_sn = $db->getFields(); //Get S/N by id.
		foreach ($invnt_sn['aaData'] as $key => $value) {
			$invnt_res_sn[$key] = $value['serialnumber'];
		} 

    	if(!Utils::isEmpty($invnt_res_sn)){ //Fetch S/N that already exist in MIF.	
    		$db->fields = null;
    		$db->selectQuery("DISTINCT serialnumber","tblmif WHERE FIND_IN_SET(serialnumber,'".implode(',',$invnt_res_sn)."') ORDER BY serialnumber DESC");
			if($db->getNumRows() > 0){
				$mif_sn	= $db->getFields();		
					foreach ($mif_sn['aaData'] as $key => $value) {
						$mif_res_sn[$key] = $value['serialnumber'];
					} 
			}
			return implode(',',$mif_res_sn);
    	}
	
	
}