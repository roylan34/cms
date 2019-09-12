<?php
/**
* 03/15/2017
*
* This file is contains Add/Update/View/Validate of Sap Customer Details table.
*
*/ 

require_once '../database.php';
require_once '../utils.php';



if(Utils::getIsset('action')){
	$action     = Utils::getValue('action');
	$db         = Database::getInstance();

	switch ($action) {
		case 'add':
			
			break;
		case 'update':
				
			break;
		case 'view-all':
				$db->selectQuery('cai.sap_code, cai.company_name, cai.client_category, cai.address, l.branch_name AS location, cai.contact_no, CONCAT(ac.firstname," ", ac.lastname) AS account_mngr','tbl_company_auto_import cai
					LEFT JOIN tbl_location l ON cai.branches = l.id
					LEFT JOIN tbl_client_accounts mngr ON cai.id_client_mngr = mngr.id
					LEFT JOIN tbl_accounts ac ON mngr.account_id = ac.id
					ORDER BY cai.company_name ASC');
				print Utils::jsonEncode($db->getFields());
					
			break;	
		case 'check_exist':

			break;
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}

