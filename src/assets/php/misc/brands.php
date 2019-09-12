<?php
/**
* 01/25/2017
*
* This file contains the json data format for dropdown list brands.
*
*/

require_once '../core.php';
require_once '../database.php';
require_once '../utils.php';

$db = Database::getInstance();
$db->selectQuery('id,brand_name,status','tbl_brands WHERE status = 1 ORDER BY id');
$res = $db->getFields();

print Utils::jsonEncode($res);