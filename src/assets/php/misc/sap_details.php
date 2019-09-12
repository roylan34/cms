<?php
/**
* 04/17/2018
*
* This file contains the json data format of total IN/OUT machines.
* 
*/

require_once '../core.php';
require_once '../database.php';
require_once '../utils.php';


$db = Database::getInstance();

	//For inputs
	$action     = Utils::getValue('action');
	$sap_code     = Utils::getValue('sap_code');
	$db         = Database::getInstance();

	switch ($action) {
		case 'company_name':
				$db->selectQuery('sap_code, company_name','tbl_company_auto_import');

				print Utils::jsonEncode($db->getFields());

			break;
		case 'all':
				$db->selectQuery('cai.sap_code, cai.company_name, cai.client_category, cai.address, l.branch_name AS location, cai.contact_no, CONCAT(ac.firstname," ", ac.lastname) AS account_mngr','tbl_company_auto_import cai
					LEFT JOIN tbl_location l ON cai.branches = l.id
					LEFT JOIN tbl_client_accounts mngr ON cai.id_client_mngr = mngr.id
					LEFT JOIN tbl_accounts ac ON mngr.account_id = ac.id
					WHERE cai.sap_code = "'.$sap_code.'"
					ORDER BY cai.company_name ASC');

				print Utils::jsonEncode($db->getFields());
					
			break;	
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}
