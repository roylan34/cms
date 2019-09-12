<?php
/**
* 07/17/2017
*
* This file contains the json data format for dropdown list of inventory brands.
*
*/

require_once '../database.php';
require_once '../utils.php';


if(Utils::getIsset('action')){
	$action      = Utils::getValue('action');
    $db 		 = Database::getInstance();

	switch ($action) {
		case 'branch':
				$db->selectQuery('id,branch_name','tbl_branch ORDER BY id');
				print Utils::jsonEncode($db->getFields());

			break;
		case 'type':
				$db->selectQuery('id,type_name','tbl_type ORDER BY id');
				print Utils::jsonEncode($db->getFields());

			break;
		case 'condition':
				$db->selectQuery('id,acronym_name,acronym_name_def','tbl_invnt_condition ORDER BY id');
				print Utils::jsonEncode($db->getFields());
		break;	
		case 'status':
				$status_type = Utils::getValue('status_type');

				$db->selectQuery('id,status_name,status_type','tbl_invnt_status WHERE status_type="'.$status_type.'" ORDER BY id');
				print Utils::jsonEncode($db->getFields());
		break;
		case 'models':

				$db->selectQuery('id,model_name,id_category,id_type','tbl_model ORDER BY id DESC');
				print Utils::jsonEncode($db->getFields());
		break;
		case 'client_location_by_company':
				$id_company  = Utils::getValue('id_company');

				$db->selectQuery('GROUP_CONCAT(CAST(id_branches as CHAR(10)) SEPARATOR ",") as id_branches','tbl_company_branches WHERE id_company = "'.$id_company.'" GROUP BY id_company');
				print Utils::jsonEncode($db->getFields());
		break;
	    case 'model_by_brand':
				$id_brand  = Utils::getValue('id_brand');

				$db->selectQuery('GROUP_CONCAT(CAST(id as CHAR(10)) SEPARATOR ",") as id_model','tbl_model WHERE id_brand = "'.$id_brand.'"');
				print Utils::jsonEncode($db->getFields());
		break;
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}

