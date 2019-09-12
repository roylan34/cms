<?php
/**
* 01/25/2017
*
* This file contains the json data format of all active Technicians.
*
*/

require_once '../../database.php';
require_once '../../utils.php';

$data = array();
$branch = Utils::getValue('branch');

if($branch){
	$db = Database::getInstance();
	$db->selectQuery('ac.id, CONCAT(ac.firstname," ", ac.lastname) AS technician','tbl_accounts ac 
				LEFT JOIN tbl_app_module am ON ac.id = am.account_id
				WHERE ac.pm_type = "TECHNICIAN" AND ac.status = 1 AND am.app_pm = 1 AND ac.branch_pm = "'.$branch.'"');
	
	print Utils::jsonEncode($db->getFields());
}