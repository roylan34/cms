<?php
/**
* 01/25/2017
*
* This file contains the json data format of company, filter by id.
*
*/ 
require_once '../../core.php';
require_once '../../database.php';
require_once '../../utils.php';


if(Utils::getIsset('idcompany')){

$param = Utils::getValue('idcompany');

	$complist = Database::getInstance();
	$complist->selectQuery('c.*, cai.company_name AS sap_comp_name, GROUP_CONCAT(cbr.id_branches) as id_branches','tbl_company c 
				LEFT JOIN tbl_company_branches cbr ON c.id = cbr.id_company 
				LEFT JOIN tbl_company_auto_import cai ON c.sap_code = cai.sap_code
				WHERE c.id = "'.$param.'" LIMIT 1');
	print Utils::jsonEncode($complist->getFields());
}

