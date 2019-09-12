<?php
/**
* 07/17/2017
*
* This file contains the json data format for dropdown list condition BN/RF.
*
*/

require_once '../database.php';
require_once '../utils.php';

$db = Database::getInstance();
$db->selectQuery('id,acronym_name,acronym_name_def','tbl_invnt_condition ORDER BY id');
$res = $db->getFields();

print Utils::jsonEncode($res);