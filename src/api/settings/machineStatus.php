<?php
/**
* 03/15/2017
*
* This file is contains Add/Update/View/Validate of Machine table.
*
*/ 

require_once '../database.php';
require_once '../utils.php';



if(Utils::getIsset('action')){
	$action      = Utils::getValue('action');
	$id          = Utils::getValue('id');
	$status_name = Utils::getValue('status_name');
	$status_action = Utils::getValue('status_action');
	$db         = Database::getInstance();

	//For action check_exist validation.
	$new_status 	 = Utils::getValue('txtSettingStatus');
	$old_status 	 = Utils::getValue('old_status');
	$action_validate = Utils::getValue('action_validate');

	switch ($action) {
		case 'add':
				$date_created = Utils::getSysDate().' '.Utils::getSysTime();  
				$db->insertQuery('tbl_machine_status','status_name,created_at,set_default','"'.Utils::uppercase($status_name).'","'.$date_created.'","'.$status_action.'"');
				print Utils::jsonEncode($db->getFields());

			break;
		case 'update':
				$db->updateQuery('tbl_machine_status','status_name = "'.Utils::uppercase($status_name).'", set_default= "'.$status_action.'"','id = "'.$id.'"');
			 	print Utils::jsonEncode($db->getFields());

			break;
		case 'view-id':
				$db->selectQuery('id,status_name,set_default','tbl_machine_status WHERE id="'.$id.'" ORDER BY id');
				print Utils::jsonEncode($db->getFields());
			
			break;	
		case 'check_exist':
				$db->selectQuery('*','tbl_machine_status WHERE status_name ="'.$new_status.'"');
				if($db->getNumRows() > 0){
					if($action_validate == "add"){
						echo json_encode(array("<strong>Status name is already exist.</strong>"));
					}
					else{
						if(strtolower($old_status) == strtolower($new_status)){
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

