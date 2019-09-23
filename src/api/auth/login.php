<?php
/**
* 01/25/2017
*
* This file contains the json data format of login.
*
*/
require_once 'core.php';
require_once 'database.php';
require_once 'utils.php';

if(Utils::getIsset('username') || Utils::getIsset('password')){

	$username = Utils::getValue('username');
	$password = Utils::getValue('password');
	$db = Database::getInstance();

	if($username && $password){
		$db->selectQuery('a.id AS user_id,CONCAT(a.firstname," ",a.lastname) AS fullname, a.location, a.branches AS branch, app.app_mif, app.app_inventory, app.app_mrf, app.app_pm,
								(SELECT CONVERT(GROUP_CONCAT(id SEPARATOR ",") USING "utf8") FROM tbl_company WHERE id_client_mngr = ca.id) AS companies, 
								a.accountrole AS user_role, a.status, a.branches_mrf AS branch_mrf, a.account_type AS user_type, a.mrf_type AS user_mrf_flag, a.email, a.branch_pm,
								tap.app_mif As action_mif, tap.app_invnt As action_invnt, tap.app_mrf As action_mrf, tap.app_pm As action_pm, acct.acc_mif_flags AS user_mif_flag, a.pm_type',
			                   'tbl_accounts a 
			                    LEFT JOIN tbl_app_module app ON a.id = app.account_id 
			                    LEFT JOIN tbl_account_type acct ON a.account_type = acct.id
			                    LEFT JOIN tbl_app_action tap ON tap.id_account = a.id
			                    LEFT JOIN tbl_client_accounts ca ON a.id = ca.account_id
			                    WHERE a.username = "'.$db->escapeString($username).'" && a.password = "'.Utils::encrypt($password).'"');
		$result = $db->getFields();
		
		if(count($result['aaData']) == 0){
			 print Utils::jsonEncode(array('status' => 'empty'));
		}
		else 
		{
			if($result['aaData'][0]['status'] == 1){
				print Utils::jsonEncode(array('res' => $result['aaData'][0], 'status' => 'active') );
			}else {
				print Utils::jsonEncode(array('status' => 'inactive'));
			}
		}

	} 
	else 
	{ print Utils::jsonEncode(array('status' => 'empty')); }

}