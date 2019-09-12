<?php
/**
* 04/04/2015
*
* This file contains the json data format of all branch list.
*
*/

require_once '../core.php';
require_once '../database.php';
require_once '../utils.php';


if(Utils::getIsset('action')){
	$action      = Utils::getValue('action');
    $db 		 = Database::getInstance();

	switch ($action) {
		case 'account_manager':
				$db = Database::getInstance();
				$db->selectQuery('ca.id, CONCAT(ac.firstname," ",ac.lastname) AS fullname','tbl_accounts ac
									INNER JOIN tbl_client_accounts ca ON ca.account_id = ac.id
									WHERE ac.status = 1 ORDER BY ac.id ASC');
				$res = $db->getFields();

				print Utils::jsonEncode($res);

			break;		
		case 'account_manager_assign':

				$db = Database::getInstance();
				$db->selectQuery('id, CONCAT(firstname," ",lastname) AS fullname','tbl_accounts 									
								  WHERE account_type IN (2,3) AND status = 1 ORDER BY id ASC');
				$res = $db->getFields();
				print Utils::jsonEncode($res);

			break;
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}

