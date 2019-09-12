<?php
/**
* 01/25/2017
*
* This file contains the json data format of all machine status.
*
*/

require_once '../database.php';
require_once '../utils.php';

$db = Database::getInstance();
$db->selectQuery('id,set_default,status_name','tbl_machine_status ORDER BY id');
$res = $db->getFields();

print Utils::jsonEncode($res);