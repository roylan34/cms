<?php
/**
* 03/15/2017
*
* This file is contains Add/Update/View/Validate of Brand table.
*
*/ 

require_once '../database.php';
require_once '../utils.php';



if(Utils::getIsset('action')){
	$action     = Utils::getValue('action');
	$id         = Utils::getValue('idbrand');
	$brandname  = Utils::getValue('brandname');
	$status 	= Utils::getValue('status');
	$db         = Database::getInstance();

	//For action check_exist validation.
	$check_brand 	 = Utils::getValue('txtSettingsBrand');
	$old_brand 	 	 = Utils::getValue('old_brand');
	$action_validate = Utils::getValue('action_validate');

	switch ($action) {
		case 'add':
				$date_created = Utils::getSysDate().' '.Utils::getSysTime();  
				$db->insertQuery('tbl_brands','brand_name,created_at,status','"'.Utils::uppercase($brandname).'","'.$date_created.'", "'.$status.'"');
				print Utils::jsonEncode($db->getFields());

			break;
		case 'update':
				$db->updateQuery('tbl_brands','brand_name = "'.Utils::uppercase($brandname).'", status = "'.$status.'"','id = "'.$id.'"');
			 	print Utils::jsonEncode($db->getFields());

			break;
		case 'view':
				$db->selectQuery('id,brand_name,status','tbl_brands WHERE id="'.$id.'" ORDER BY id');
				print Utils::jsonEncode($db->getFields());
			
			break;	
		case 'view-all':
				$db->selectQuery('id,brand_name,status','tbl_brands ORDER BY id');
				print Utils::jsonEncode($db->getFields());
			
			break;
		case 'check_exist':
				$db->selectQuery('*','tbl_brands WHERE brand_name ="'.$check_brand.'"');
				if($db->getNumRows() > 0){
					if($action_validate == "add"){
						echo json_encode(array("<strong>Brand name is already exist.</strong>"));
					}
					else{
						if(strtolower($old_brand) == strtolower($check_brand)){
							echo "true";
						}else{
							echo json_encode(array("<strong>Brand name is already exist.</strong>"));
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

