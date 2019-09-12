<?php
/**
* 07/26/2017
*
* This file is contains Add/Update/View of Schedule.
*
*/ 

require_once '../database.php';
require_once '../utils.php';



if(Utils::getIsset('action')){
	//For inputs
	$action     	= Utils::getValue('action');
	$id_sched 		= Utils::getValue('id_pm');
	$pmnumber 		= Utils::getValue('pmnumber');
	$company  		= Utils::getValue('company');
	$sched_date  	= Utils::getValue('sched_date');
	$technician 	= Utils::getValue('technician');
	$old_technician = Utils::getValue('old_technician');
	$contact_name  	= Utils::getValue('contact_name');
	$contact_no 	= Utils::getValue('contact_no');
	$email  		= Utils::getValue('email');
	$department  	= Utils::getValue('department');
	$branch  		= Utils::getValue('branch');
	$user_id  		= Utils::getValue('user_id');
	$date_entered = Utils::getSysDate();
	$time_entered = Utils::getSysTime();

	 //Technician Adding/Deleting
	$expld_technician  	= (!empty($technician) 	   ? explode(',',$technician)     : array());
	$expl_old_technician= (!empty($old_technician) ? explode(',',$old_technician) : array());

	$db = Database::getInstance();
	$search ="";

	switch ($action) {
		case 'add':
		        $add_pm_no 			= generatePmNo($db);
				$db->fields = null;

				$db->insertQuery('tbl_pm_schedule','pm_number, company_id, schedule_date, date_entered, time_entered, contact_name, contact_number, email_address,
													department, created_by, branch  ',
												'"'.$add_pm_no.'",
												"'.$company.'",
												"'.$sched_date.'",
												"'.$date_entered.'",
												"'.$time_entered.'",
												"'.$contact_name.'",
												"'.$contact_no.'",
												"'.$email.'",
												"'.$department.'",
												"'.$user_id.'",
												"'.$branch.'"');


				if(count($expld_technician) > 0){
					$db->insertMultipleByUniqueQuery('tbl_pm_technician','technician, pm_number, created_at', 
								Utils::filterEmptyArr($expld_technician),
									  '"'.$add_pm_no.'",
									  "'.$date_entered.'"'); 
				}

          		print Utils::jsonEncode($db->getFields());
			break;
		case 'edit':
					if($id_sched){
						$db->updateQuery('tbl_pm_schedule','company_id = "'.$company.'", 
														schedule_date = "'.$sched_date.'",
														contact_name = "'.$contact_name.'",
														contact_number  = "'.$contact_no.'",
														email_address = "'.$email.'",
														department = "'.$department.'"'
										    			,'id = "'.$id_sched.'"');

						 //Add Technician
						if(count($expld_technician) >= count($expl_old_technician)){
						    foreach($expld_technician AS $key => $val_selected){
						        if(!in_array($val_selected, $expl_old_technician)){
						        	$db->insertQuery('tbl_pm_technician','pm_number, technician, created_at',
															  '"'.$pmnumber.'",
															  "'.$val_selected.'",
															  "'.$date_entered.'"');
						       }
						    }
						}
						//Delete Technician
						if(count($expl_old_technician) >= count($expld_technician)){
						      foreach($expl_old_technician AS $key => $val_selected){
						        if(!in_array($val_selected, $expld_technician)){
						        	$db->deleteQuery('tbl_pm_technician','pm_number ="'.$pmnumber.'" AND technician ='.$val_selected.'');
						       }
						    }
						}
						
				 		print Utils::jsonEncode($db->getFields());
				 	}
			break;
		case 'view-id':
					$db->selectQuery("ps.id, ps.pm_number, ps.company_id, ps.schedule_date, GROUP_CONCAT(pt.technician) AS technician, CONCAT(ps.date_entered,' ', ps.time_entered) AS date_entered,
									  ps.contact_name, ps.contact_number, ps.email_address, ps.department"," tbl_pm_schedule ps
									  LEFT JOIN tbl_pm_technician pt on ps.pm_number = pt.pm_number
									WHERE ps.id = ".$id_sched." LIMIT 1");
					 print Utils::jsonEncode($db->getFields());
					
			break;	
		case 'update_cancel':
					if($id_sched){
						$is_cancel = checkIsCancel($pmnumber, $db);
						if($is_cancel['aaData']['result'] == 'true'){
							$db->updateQuery('tbl_pm_schedule','status = "cancel"','id = "'.$id_sched.'"');
						}

				 		print Utils::jsonEncode($is_cancel);	
				 	}
			break;
		case 'update_close':
					if($id_sched){
						$is_done = checkIsDone($pmnumber, $db);
						if($is_done['aaData']['result'] == 'true'){
							$db->updateQuery('tbl_pm_schedule','status = "close"','id = "'.$id_sched.'"');
						}

				 		print Utils::jsonEncode($is_done);	
				 	}
			break;		
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}

function generatePmNo($db){
	$db->selectQuery('*','tbl_pm_schedule');
	$res = intval($db->getNumRows()) + 1;

	date_default_timezone_set('Asia/Manila');
	$info = getdate();
	$date = $info['mday'];
	$month = $info['mon'];
	$year = $info['year'];

	return 'PM'.$year.$date.$month.'-'.$res;
}


function checkIsCancel($pm_num,$db){
	$res = null;
	if(!Utils::isEmpty($pm_num)){
		// $db->selectQuery("COUNT(*) AS total_pending ","tbl_pm_machines  
		// 	WHERE pm_number = '".$pm_num."' || (time_in = '' AND time_out ='') ");
		// $res = $db->getFields();

		$db->selectQuery2("SELECT IF( 
						(SELECT COUNT(*) FROM tbl_pm_machines WHERE pm_number = '".$pm_num."' ) 
						= 
						(SELECT COUNT(*) FROM tbl_pm_machines WHERE pm_number = '".$pm_num."' AND 
						((time_in = '0000-00-00 00:00:00' AND time_out = '0000-00-00 00:00:00') 
						|| 
						(time_in IS NULL AND time_out IS NULL))
						), 'yes', 'no') AS can_cancel");
		$fetchRes = $db->getFields();
		if($fetchRes['aaData'][0]['can_cancel'] == 'yes'){ 

			$res = array('aaData' => array(
				'result' =>  'true',
				'message' => 'Request has been successfully cancelled see at Archived.'		
			));
		}else{
			$res = array('aaData' => array(
				'result' =>  'false',
				'message' => "Unable to cancel request because already in-progress."
			));
		}
	}
	return $res;

}

function checkIsDone($pm_num, $db){
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
				'message' => 'PM Close see at Archived.'
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