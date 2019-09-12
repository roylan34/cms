<?php
/**
* 01/25/2017
*
* This file contains the json data format of all company list.
*
*/

require_once '../database.php';
require_once '../utils.php';

$search ='';
if(Utils::getValue('only_active') == 'true') { $search =" WHERE status = 1 ORDER BY company_name"; }

$db = Database::getInstance();
$db->selectQuery('id,company_name','tbl_company '.$search.'');
$res = $db->getFields();
$data = array();

foreach ($res['aaData'] as $key => $value) {
	$data[] = $value;
}

print Utils::jsonEncode($data);