<?php
/**
* 08/25/2017
*
* This file is contains Add/Update/View/Validate of Branch table.
*
*/ 

require_once '../../database.php';
require_once '../../utils.php';

if(Utils::getIsset('action')){
	$action     = Utils::getValue('action');
	$id         = Utils::getValue('id_branch');
	$branchname = Utils::getValue('branchname');
	$date_created = Utils::getSysDate().' '.Utils::getSysTime();  
	$db  = Database::getInstance();

	//For action check_exist validation.
	$check_branch	 = Utils::getValue('txtInvntSettingBranch');
	$old_branch 	 = Utils::getValue('old_branch');
	$action_validate = Utils::getValue('action_validate');

	switch ($action) {
		case 'add':
				$db->insertQuery('tbl_branch','branch_name,created_at',
												'"'.Utils::uppercase($branchname).'",
												"'.$date_created.'"');
				print Utils::jsonEncode($db->getFields());

			break;
		case 'update':
				$db->updateQuery('tbl_branch','branch_name  = "'.Utils::uppercase($branchname).'"',
												     'id = "'.$id.'"');
			 	print Utils::jsonEncode($db->getFields());
			
			break;
		case 'view-id':
				$db->selectQuery('id,branch_name','tbl_branch WHERE id="'.$id.'" LIMIT 0,1');
			 	print Utils::jsonEncode($db->getFields());

			break;
		case 'view-all':
				$db->selectQuery('id,branch_name','tbl_branch');
				print Utils::jsonEncode($db->getFields());  // send data as json format
			
			break;	
		case 'check_exist':
				$db->selectQuery('*','tbl_branch WHERE branch_name ="'.$check_branch.'" LIMIT 0,1');
				if($db->getNumRows() > 0){
					if($action_validate == "add"){
						echo json_encode(array("<strong>Branch name is already exist.</strong>"));
					}
					else{
						if(strtolower($old_branch) == strtolower($check_branch)){
							echo "true";
						}else{
							echo json_encode(array("<strong>Branch name is already exist.</strong>"));
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
