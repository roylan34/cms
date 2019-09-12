<?php
/**
* 07/26/2017
*
* This file is contains Add/Update/View of PM.
*
*/ 

require_once '../database.php';
require_once '../utils.php';


if(Utils::getIsset('action')){
	$db = Database::getInstance();

	//For inputs
	$action     	= Utils::getValue('action');
	$serialnum 		= Utils::getValue('serialnum');
	$pmnumber 		= Utils::getValue('pmnumber');
	$pm_id  		= Utils::getValue('pm_id');
	$comp_id    	= Utils::getValue('comp_id');
	$manufacture  	= Utils::getValue('manufacture');
	$remarks  		= $db->escapeString(Utils::getValue('remarks'));
	$page  			= Utils::getValue('page');
	$toner  		= $db->escapeString(Utils::getValue('toner'));
	$toner_old  	= $db->escapeString(Utils::getValue('toner_old'));
	$time_in  	= Utils::getValue('time_in');
	$time_out  	= Utils::getValue('time_out');
	$is_delete  = Utils::getValue('is_delete');
	$user_id    = Utils::getValue('user_id');
	

	$date_entered = Utils::getSysDate();
	$time_entered = Utils::getSysTime();

	//Toner Adding/Deleting
	$expl_selected_toner  = (!empty($toner) 	? explode(',',$toner)     : array());
	$expl_old_toner	 	  = (!empty($toner_old) ? explode(',',$toner_old) : array());

	$search ="";

	switch ($action) {
		case 'add':
				$res = array();
				//Query Insert
				if($serialnum && array_key_exists('insert', $serialnum) && count($serialnum['insert']) > 0 ){ 
					$sn_insert = implode('","',$serialnum['insert']);	
					$db->customQuery('INSERT INTO tbl_pm_machines (pm_number, company_id, serialnumber, brand, model, location_area, department, no_of_user, date_installed, unit_owned , mif_id, created_date )
									 SELECT "'.$pmnumber.'", company_id, serialnumber, brand, model, location_area, department, no_of_user, date_installed, unit_owned_by, id, NOW() FROM tblmif
									 WHERE company_id = '.$comp_id.' AND serialnumber IN ("'.$sn_insert.'")');
	                $res = $db->getFields();
					 		if($res['aaData'][0] == 'success'){ 
					 			$db->fields = null;

					 				//Update status.
									$is_progress = checkStatus($pmnumber, $db);
									if($is_progress['aaData']['status'] == 'in-progress'){
										$db->updateQuery('tbl_pm_schedule','status = "in-progress"','pm_number = "'.$pmnumber.'"');
									}
							 		
					 		}
				}
				print Utils::jsonEncode($res);
			break;
	/*case 'add-pm':		 		
			$res = array();
				//Query update existing S/N by company
				if($serialnum && array_key_exists('update', $serialnum) && count($serialnum['update']) > 0 ){ 
					$sn_update = implode('","',$serialnum['update']);
					
					$db->updateQuery('tbl_pm_machines','is_delete = "no"','pm_number = "'.$pmnumber.'" AND id IN ("'.$sn_update.'")');
					$res = $db->getFields();
				}
				//Query Insert
				if($serialnum && array_key_exists('insert', $serialnum) && count($serialnum['insert']) > 0 ){ 	
					$sn_insert = implode('","',$serialnum['insert']);
				
					$db->customQuery('INSERT INTO tbl_pm_machines (pm_number, company_id, brand, model, serialnumber, location_area, no_of_user, date_installed, unit_owned, created_date )
									 SELECT "'.$pmnumber.'", company_id, brand, model, serialnumber, location_area, no_of_user, IF(date_installed, date_installed, NULL) AS date_installed, unit_owned_by, NOW() FROM tblmif
									 WHERE company_id = '.$company_id.' AND serialnumber IN ("'.$sn_insert.'")');
	                $res = $db->getFields();
	            }
		 		if($res['aaData'][0] == 'success' || $res['aaData'][1] == 'success'){ 
		 			$db->fields = null;

		 				//Update status.
						$is_progress = checkStatus($pmnumber, $db);
						if($is_progress['aaData']['status'] == 'in-progress'){
							$db->updateQuery('tbl_pm_schedule','status = "in-progress"','pm_number = "'.$pmnumber.'"');
						}
				 		
		 		}
				print Utils::jsonEncode($res);
			break;*/
		case 'update':
					if($pm_id){

						$brand    = Utils::getValue('brand');
						$model    = Utils::getValue('model');
						$location    = Utils::getValue('location');
						$department  = Utils::getValue('department');
						$no_of_user  = Utils::getValue('no_of_user');
						$mif_id  	 = Utils::getValue('mif_id');

						$db->updateQuery('tbl_pm_machines','brand 		= "'.$brand.'",
													    model 			= "'.$model.'",
													    location_area 	= "'.$location.'",
													    department 		= "'.$department.'",
													    no_of_user 		= "'.$no_of_user.'",
														manufacture_date = "'.$manufacture.'", 
														recent_user = "'.$user_id.'", 
														remarks 	= "'.$remarks.'",
														page_count 	= "'.$page.'",
														time_in  	= "'.$time_in.'",
														time_out 	= "'.$time_out.'"'
										    			,'id = "'.$pm_id.'"');
						//Sync update to tblmif.
						$db->updateQuery('tblmif','page_count 		= "'.$page.'",
												   brand 			= "'.$brand.'",
												   model 			= "'.$model.'",
												   location_area 	= "'.$location.'",
												   department 		= "'.$department.'",
												   no_of_user 		= "'.$no_of_user.'",
												   remarks			= "'.$remarks.'"'
										  ,'company_id = "'.$comp_id.'" AND id="'.$mif_id.'"');
				 		$resPM = $db->getFields();

				 		//Add Toner
						if(count($expl_selected_toner) >= count($expl_old_toner)){
						    foreach($expl_selected_toner AS $key => $val_selected){
						        if(!in_array($val_selected, $expl_old_toner)){
						        	$db->insertQuery('tbl_toner_model_use','pm_number, company_id, mif_id, toner_id',
															  '"'.$pmnumber.'",
															  "'.$comp_id.'",
															  "'.$mif_id.'",
															  "'.$val_selected.'"');
						       }
						    }
						}
						//Delete Toner
						if(count($expl_old_toner) >= count($expl_selected_toner)){
						      foreach($expl_old_toner AS $key => $val_selected){
						        if(!in_array($val_selected, $expl_selected_toner)){
						        	$db->deleteQuery('tbl_toner_model_use','pm_number ="'.$pmnumber.'" AND mif_id = '.$mif_id.' AND toner_id ='.$val_selected.'');
						       }
						    }
						}
						

				 		if($resPM['aaData'][0] == 'success'){ 
				 			$db->fields = null;

				 				//Update status.
								$is_done = checkStatus($pmnumber, $db);
								if($is_done['aaData']['status'] == 'done'){
									$db->updateQuery('tbl_pm_schedule','status = "done"','pm_number = "'.$pmnumber.'"');
								}
								else if($is_done['aaData']['status'] == 'in-progress'){
									$db->updateQuery('tbl_pm_schedule','status = "in-progress"','pm_number = "'.$pmnumber.'"');
								}
						 		else{  }
				 		}
				 		print Utils::jsonEncode($resPM);	
				 	}
			break;
		case 'view-id':
					$db->selectQuery("pm.company_id, pm.pm_number, pm.id, pm.serialnumber, pm.brand, pm.model, pm.manufacture_date, pm.remarks, pm.page_count, GROUP_CONCAT(tmu.toner_id) AS toner_use, pm.time_in, pm.time_out, pm.location_area, pm.department, pm.no_of_user, m.id AS mif_id "," tbl_pm_machines pm
									LEFT JOIN tblmif m ON m.id = pm.mif_id
									LEFT JOIN tbl_toner_model_use tmu ON pm.pm_number = tmu.pm_number
									WHERE pm.id = ".$pm_id." AND m.company_id = pm.company_id 
									AND tmu.pm_number = pm.pm_number AND tmu.mif_id = pm.mif_id
									LIMIT 1");
					 print Utils::jsonEncode($db->getFields());
					
			break;
		case 'remove-pm':
				$db->updateQuery('tbl_pm_machines','is_delete = "yes"'
										    			,'id = "'.$pm_id.'"');
				 		$resPM = $db->getFields();
				 		print Utils::jsonEncode($resPM);	
			break;			
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}


function checkStatus($pm_num, $db){
	$status = null;
	if(!Utils::isEmpty($pm_num)){
		$db->selectQuery("( CASE
			WHEN pm.pm_number = '".$pm_num."' && (SELECT COUNT(*) FROM tbl_pm_machines WHERE is_delete = 'no' AND pm_number = '".$pm_num."' AND ((time_in IS NULL || time_out IS NULL) || (time_in = '0000-00-00 00:00:00' || time_out = '0000-00-00 00:00:00')) ) > 0 THEN 'in-progress'
			WHEN pm.pm_number = '".$pm_num."' && (SELECT COUNT(*) FROM tbl_pm_machines WHERE is_delete = 'no' AND pm_number = '".$pm_num."' AND ((time_in IS NOT NULL && time_out IS NOT NULL) || (time_in != '0000-00-00 00:00:00' && time_out != '0000-00-00 00:00:00')) ) > 0 THEN 'done'
			ELSE 'no-pm'
			END
			) AS status","tbl_pm_schedule ps
			LEFT JOIN tbl_pm_machines pm ON ps.pm_number = pm.pm_number
			WHERE ps.pm_number = '".$pm_num."' GROUP BY ps.pm_number");
		$status = $db->getFields();

		if($status['aaData'][0]['status'] == 'done'){ 
			$status = array('aaData' => array(
				'status' => $status['aaData'][0]['status'],
				'result' =>  'true',
				'message' => 'PM Done see at Archived.'
			));
		}
		else if($status['aaData'][0]['status'] == 'in-progress'){ 
			$status = array('aaData' => array(
				'status' => $status['aaData'][0]['status'],
				'result' =>  'false',
				'message' => 'PM in-progress.'
			));
		}
		else{
			$status = array('aaData' => array(
				'status' => $status['aaData'][0]['status'],
				'result' =>  'false',
				'message' => "No machines added in PM."
			));
		}
	}
	return $status;
}