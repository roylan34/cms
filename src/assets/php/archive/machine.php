<?php
/**
* 01/25/2017
*
* This file contains the json data format of all archive machine.
*
*/
require_once '../database.php';
require_once '../utils.php';



if(Utils::getIsset('action')){
	$idmachine 	= Utils::getValue('id');
	$action     = Utils::getValue('action');
	$reason 	= Utils::getValue('reason');
	$status 	= Utils::getValue('status');
	$status_action 	= Utils::getValue('status_action');
	$user_id 	  = Utils::getValue('user_id');

	//For retrieve
	// $id       	= Utils::getValue('idmachine');
	$company_id = Utils::getValue('company_id');
	$serialnum 	= Utils::getValue('serialnum');
	$brand 		= Utils::getValue('brand');
	$model 		= Utils::getValue('model');
	$category 	= Utils::getValue('category');
	$type 		= Utils::getValue('type');
	$pagecount 	= Utils::getValue('pagecount');
	$location 	= Utils::getValue('location');
	$department = Utils::getValue('department');
	$nouser 	= Utils::getValue('nouser');
	$remarks 	= Utils::getValue('remarks');  
	$dateinstalled= Utils::getValue('dateinstall'); 
	$billing	= Utils::getValue('billing'); 
	$branch   	= Utils::getValue('branch'); 
	$unit_own   = Utils::getValue('unit_own'); 
	$date_now = Utils::getSysDate().' '.Utils::getSysTime();  

	// $action     = Utils::getValue('action');
	// $user_id 	= Utils::getValue('user_id');
	$db         = Database::getInstance();


	switch ($action) {
		case 'remove': 
				$db->selectQuery('set_default','tbl_machine_status WHERE id = "'.$status.'" LIMIT 1');
			    $res = $db->getFields();//Get the action value either Update/Delete.

				if($res['aaData'][0]['set_default'] == 1){ //soft delete
					$db->updateQuery('tblmif','status_machine = '.$status.', reason= "'.$reason.'", date_in= null, date_out= "'.$date_now.'"','id = '.$idmachine.''); //Temporary store the record to Archive.
					$db->insertQuery('tblmif_archive_logs','company_id,serialnumber,reason,machine_status,user_id,updated_at', //Archive Logs
									  '(SELECT company_id  FROM tblmif WHERE id = '.$idmachine.' LIMIT 0,1),
									  (SELECT serialnumber FROM tblmif WHERE id = '.$idmachine.' LIMIT 0,1),
									  "'.$reason.'",
									  "'.$status.'",
									  "'.$user_id.'",
									  "'.$date_now.'"');
				}
				else if($res['aaData'][0]['set_default'] == 2)//hard Delete
				{
					//$db->customQuery('INSERT INTO tblmif_archive SELECT * FROM tblmif WHERE id ='.$idmachine.' LIMIT 1'); //Replicate the data to tbl_archive if status is 2 = REMOVE
					$db->updateQuery('tblmif','status_machine = '.$status.', reason= "'.$reason.'", date_in= null, date_out= "'.$date_now.'", can_retrieve ='.$status_action.'','id = '.$idmachine.'');//Update the record after the replication data.
					$db->insertQuery('tblmif_archive_logs','company_id,serialnumber,reason,machine_status,user_id,updated_at', //Archive Logs
					  '(SELECT company_id  FROM tblmif WHERE id = '.$idmachine.' LIMIT 0,1),
					   (SELECT serialnumber FROM tblmif WHERE id = '.$idmachine.' LIMIT 0,1), 
					  "'.$reason.'",
					  "'.$status.'",
					  "'.$user_id.'",
					  "'.$date_now.'"');
					//$db->deleteQuery('tblmif','id ="'.$idmachine.'"');//Delete the record //Commented 04/19/2018
				}else{
					throw new Exception('Invalid status value: '. $status);
				}
				$db->fields = null; //Empty result;

				//Sync in PM module if machine remove in Current page that has status In-progress and PM Done.
				$db->selectQuery("GROUP_CONCAT(CONCAT('\"',pm.serialnumber, '\"')) AS serialnumber, pm.company_id","tbl_pm_machines pm LEFT JOIN tbl_pm_schedule ps ON pm.pm_number = ps.pm_number
					WHERE pm.serialnumber = (SELECT serialnumber FROM tblmif WHERE id = '".$idmachine."' LIMIT 1)
					AND pm.is_delete ='no' AND ps.status IN ('in-progress', 'done')");
			    $resPM = $db->getFields();
			    $serialnum = $resPM['aaData'][0]['serialnumber'];
			    $comp_id   = $resPM['aaData'][0]['company_id'];
			    if($serialnum != '' && $comp_id != ''){
			    	$db->updateQuery('tbl_pm_machines','is_delete = "yes"','serialnumber IN ('.$serialnum.') AND company_id='.$comp_id.' '); 
			    }
				print Utils::jsonEncode($res); //json result of remove.

			break;
		case 'view-all': //Merged the table of tblmif and tblmif_archive.
				// $db->selectQuery('m.*,c.company_name,b.branch_name,br.brand_name','tblmif_archive m
				// 					LEFT JOIN tbl_company c ON m.company_id = c.id
				// 					LEFT JOIN tbl_location b ON m.branches = b.id
				// 					LEFT JOIN tbl_brands br on m.brand = br.id
				// 					  UNION ALL
				// 					SELECT m.*,c.company_name,b.branch_name,br.brand_name FROM tblmif m 
				// 					LEFT JOIN tbl_company c ON m.company_id = c.id
				// 					LEFT JOIN tbl_location b ON m.branches = b.id
				// 					LEFT JOIN tbl_brands br on m.brand = br.id
				// 					WHERE m.status_machine != 0 || m.company_id = 0 ORDER BY id DESC');

				//Only table tblmif
				$db->selectQuery('m.*,c.company_name,b.branch_name,br.brand_name, ms.set_default AS is_hard_delete','tblmif m
									LEFT JOIN tbl_company c ON m.company_id = c.id
									LEFT JOIN tbl_location b ON m.branches = b.id
									LEFT JOIN tbl_brands br ON m.brand = br.id
									LEFT JOIN tbl_machine_status ms ON m.status_machine = ms.id
									WHERE m.status_machine != 0 || m.company_id = 0 ORDER BY id DESC');
				$resultArchiveMac = $db->getFields();
				print Utils::jsonEncode($resultArchiveMac);
			break;	
		case 'retrieve': //Merged the table of tblmif and tblmif_archive.
						$db->selectQuery('status','tbl_company WHERE id = "'.$company_id.'" LIMIT 0,1');//Check if company is blocked.
				        $resComp = $db->getFields();

						if($resComp['aaData'][0]['status'] == 1){
							$db->insertQuery('tblmif_archive_logs','company_id,serialnumber,reason,machine_status,user_id,updated_at', //Archive Logs
							  '(SELECT company_id  FROM tblmif WHERE id = '.$idmachine.' LIMIT 0,1),
							   (SELECT serialnumber FROM tblmif WHERE id = '.$idmachine.' LIMIT 0,1),
							   null,
							   0,
							   "'.$user_id.'",
							   "'.$date_now.'"');

							$db->updateQuery('tblmif','company_id = "'.$company_id.'",
							     brand 		 	= "'.$brand.'",
							     category 		= "'.Utils::upperCase($category).'",
							     type 		 	= "'.Utils::upperCase($type).'",	
							     page_count 	= "'.$pagecount.'",				     
							     model 		 	= "'.Utils::upperCase($model).'",
							     serialnumber 	= "'.Utils::upperCase($serialnum).'",
							     location_area 	= "'.Utils::upperCase($location).'",
							     department 	= "'.Utils::upperCase($department).'",
							     no_of_user 	= "'.$nouser.'",
							     remarks 		= "'.Utils::upperCase($remarks).'",
							     date_installed = "'.$dateinstalled.'",
							     billing_type 	= "'.Utils::upperCase($billing).'",
							     branches  		= "'.$branch.'",
							     unit_owned_by  = "'.Utils::upperCase($unit_own).'",
							     status_machine = 0,
							     reason         = null,
							     date_in  		= "'.$date_now.'",
							     date_out		= null'
							     ,'id = "'.$idmachine.'"');
				     		
				     		$db->fields = null;
							$res['aaData'][0]['status'] = 1;
						}
						else{
							$res['aaData'][0]['status'] = 0;
						}

						print Utils::jsonEncode($res);
			break;
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}



