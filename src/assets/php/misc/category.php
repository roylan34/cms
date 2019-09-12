<?php
/**
* 07/25/2017
*
* This file contains the json data format for dropdown list category.
*
*/

require_once '../database.php';
require_once '../utils.php';

$db = Database::getInstance();
$db->selectQuery('id,cat_name','tbl_category ORDER BY id');
$res = $db->getFields();

print Utils::jsonEncode($res);