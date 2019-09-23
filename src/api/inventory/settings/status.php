<?php
/**
* 08/25/2017
*
* This file is contains Add/Update/View/Validate of Status table.
*
*/ 

require_once '../../database.php';
require_once '../../utils.php';



if(Utils::getIsset('action')){
	$action     = Utils::getValue('action');
	$id          = Utils::getValue('id_status');
	$status_name = Utils::getValue('status_name');
	$status_type = Utils::getValue('status_type');
	$date_created = Utils::getSysDate().' '.Utils::getSysTime();  
	$db  = Database::getInstance();

	//For action check_exist validation.
	$check_status	 = Utils::getValue('txtInvntSettingStatus');
	$old_status 	 = Utils::getValue('old_status');
	$action_validate = Utils::getValue('action_validate');

	switch ($action) {
		case 'add':
				$db->insertQuery('tbl_invnt_status','status_name,status_type,created_at',
												'"'.Utils::uppercase($status_name).'",
												"'.$status_type.'",
												"'.$date_created.'"');
				print Utils::jsonEncode($db->getFields());

			break;
		case 'update':
				$db->updateQuery('tbl_invnt_status','status_name  = "'.Utils::uppercase($status_name).'",
											  		 status_type  = "'.$status_type.'"',
												     'id = "'.$id.'"');
			 	print Utils::jsonEncode($db->getFields());
			
			break;
		case 'view-id':
				$db->selectQuery('id,status_name,status_type','tbl_invnt_status WHERE id="'.$id.'" LIMIT 0,1');
			 	print Utils::jsonEncode($db->getFields());

			break;
		case 'view-all':
				$db->selectQuery('id,status_name,status_type','tbl_invnt_status');
				print Utils::jsonEncode($db->getFields());  // send data as json format
			
			break;	
		case 'check_exist':
				$db->selectQuery('*','tbl_invnt_status WHERE status_name ="'.$check_status.'" LIMIT 0,1');
				if($db->getNumRows() > 0){
					if($action_validate == "add"){
						echo json_encode(array("<strong>Status name is already exist.</strong>"));
					}
					else{
						if(strtolower($old_status) == strtolower($check_status)){
							echo "true";
						}else{
							echo json_encode(array("<strong>Status name is already exist.</strong>"));
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

