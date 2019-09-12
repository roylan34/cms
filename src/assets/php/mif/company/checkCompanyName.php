<?php
/*
* This file check if Client name is already exist.
* 
*/
require_once '../../core.php';
require_once '../../database.php';
require_once '../../utils.php';

$db = Database::getInstance();

//For action check_exist validation.
$comp_name   = Utils::getValue('comp_name');
$old_comp_name   = Utils::getValue('old_comp_name');
$action_validate = Utils::getValue('action');

$db->selectQuery('company_name','tbl_company WHERE company_name ="'.$comp_name.'" LIMIT 0,1');
if($db->getNumRows() > 0){
	if($action_validate == 'add'){
		   echo 'false';
		// echo "Company name is already exist.";
	}
	else{
		if(strtolower($old_comp_name) == strtolower($comp_name)){
			echo 'true';
		}else{
			echo 'false';
			// echo "Company name is already exist.";
		}
	}
}
else{
	echo 'true';
}