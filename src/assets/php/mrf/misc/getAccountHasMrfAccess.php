<?php
/**
* 01/25/2017
*
* This file contains the json data format of all account name active.
*
*/

require_once '../../database.php';
require_once '../../utils.php';

$data = array();


	$db = Database::getInstance();
	$db->selectQuery('ac.id, CONCAT(ac.firstname," ",ac.lastname) AS fullname, ac.account_type','tbl_accounts ac 
				INNER JOIN tbl_app_module app ON ac.id = app.account_id
				WHERE ac.status = 1 AND app.app_mrf = 1 AND ac.account_type > 0');
	
	print Utils::jsonEncode($db->getFields());