<?php
/**
* 01/25/2017
*
* This file contains the json data format of all model list.
*
*/

require_once '../database.php';
require_once '../utils.php';


$db = Database::getInstance();

$search = "";
if(Utils::getValue('model')) { $search = 'WHERE model LIKE "%'.Utils::getValue('model').'%"'; }

$db->selectQuery('DISTINCT model','tblmif '.$search.' LIMIT 0,10');
$res = $db->getFields();
$data = array();

foreach ($res['aaData'] as $key => $value) {
	$data[$key] = $value['model'];
}


print Utils::jsonEncode($data);