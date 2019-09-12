<?php
/**
* 03/15/2017
*
* This file is contains Add/Update/View/Validate of Toner table.
*
*/ 

require_once '../../database.php';
require_once '../../utils.php';



if(Utils::getIsset('action')){
	$action     = Utils::getValue('action');
	$tonerid    = Utils::getValue('tonerid');
	$toner  	= Utils::getValue('toner');
	$status 	= Utils::getValue('status');
	$date_created = Utils::getSysDate().' '.Utils::getSysTime();  
	$db         = Database::getInstance();

	//For action check_exist validation.
	$new_toner 	     = Utils::getValue('txtSettingsPmToner');
	$old_toner 	 	 = Utils::getValue('old_toner');
	$action_validate = Utils::getValue('action_validate');

	$selected_model = (!empty($model) ? explode(",",$model) : array());
	$old_model_exp = (!empty($old_model) ? explode(",",$old_model) : array());

	switch ($action) {
		case 'add':
				$db->insertQuery('tbl_toner','toner_code, status, created_at','"'.Utils::upperCase($toner).'", "'.$status.'","'.$date_created.'"');
				print Utils::jsonEncode($db->getFields());

			break;
		case 'update':
				$db->updateQuery('tbl_toner','toner_code = "'.$toner.'", status = "'.$status.'"','id ="'.$tonerid.'"');
			 	print Utils::jsonEncode($db->getFields());

			break;
		case 'view-id':
				$db->selectQuery('id, toner_code, status','tbl_toner WHERE id ="'.$toner.'"');
				print Utils::jsonEncode($db->getFields());
			
			break;	
		case 'view-all':
				$db->selectQuery('id, toner_code, status','tbl_toner ORDER BY id'); //Group by Toner code
				print Utils::jsonEncode($db->getFields());
			
			break;
		case 'check_exist':
				$db->selectQuery('*','tbl_toner WHERE toner_code ="'.$new_toner.'"');
				if($db->getNumRows() > 0){
					if($action_validate == "add"){
						echo json_encode(array("<strong>Toner name is already exist.</strong>"));
					}
					else{
						if(strtolower($old_toner) == strtolower($new_toner)){
							echo "true";
						}else{
							echo json_encode(array("<strong>Toner name is already exist.</strong>"));
						}
					}
				}
				else{
					echo "true";
				}

			break;
		case 'view-model':
				$db->selectQuery('DISTINCT model','tblmif ORDER BY model'); //Group by Toner code
				print Utils::jsonEncode($db->getFields());
		break;
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}

