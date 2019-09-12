<?php
/**
* 03/15/2017
*
* This file is contains Add/Update/View/Validate of Branch table.
*
*/ 

require_once '../database.php';
require_once '../utils.php';



if(Utils::getIsset('action')){
	$action     = Utils::getValue('action');
	$id         = Utils::getValue('idbranch');
	$branchname = Utils::getValue('branchname');
	$status     = Utils::getValue('status');
	$db         = Database::getInstance();

	//For action check_exist validation.
	$check_branch 	 = Utils::getValue('txtSettingsBranch');
	$old_branch 	 = Utils::getValue('old_branch');
	$action_validate = Utils::getValue('action_validate');

	switch ($action) {
		case 'add':
				$date_created = Utils::getSysDate().' '.Utils::getSysTime();  
				$db->insertQuery('tbl_location','branch_name,created_at,status','"'.Utils::uppercase($branchname).'","'.$date_created.'", "'.$status.'"');
				print Utils::jsonEncode($db->getFields());

			break;
		case 'update':
				$db->updateQuery('tbl_location','branch_name = "'.Utils::uppercase($branchname).'", status = "'.$status.'"','id = "'.$id.'"');
			 	print Utils::jsonEncode($db->getFields());

			break;
		case 'view':
				$db->selectQuery('id,branch_name,status','tbl_location WHERE id="'.$id.'" ORDER BY id');
				print Utils::jsonEncode($db->getFields());
			
			break;	
		case 'check_exist':
				$db->selectQuery('*','tbl_location WHERE branch_name ="'.$check_branch.'"');
				if($db->getNumRows() > 0){
					if($action_validate == "add"){
						echo json_encode(array("<strong>Location name is already exist.</strong>"));
					}
					else{
						if(strtolower($old_branch) == strtolower($check_branch)){
							echo "true";
						}else{
							echo json_encode(array("<strong>Location name is already exist.</strong>"));
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

