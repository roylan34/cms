<?php
/**
* 01/25/2017
*
* This file contains the json data format of all serialnumber list.
*
*/

require_once '../database.php';
require_once '../utils.php';


$db = Database::getInstance();

$search = "";
if(Utils::getValue('serialnumber')) { $search = 'WHERE serialnumber LIKE "%'.Utils::getValue('serialnumber').'%"'; }

$db->selectQuery('DISTINCT serialnumber','tblmif '.$search.' LIMIT 0,10');
$res = $db->getFields();
$data = array();

foreach ($res['aaData'] as $key => $value) {
	$data[$key] = $value['serialnumber'];
}


print Utils::jsonEncode($data);