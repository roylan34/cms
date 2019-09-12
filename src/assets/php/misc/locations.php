<?php
/**
* 01/25/2017
*
* This file contains the json data format of all branch list.
*
*/
require_once '../core.php';
require_once '../database.php';
require_once '../utils.php';

$db = Database::getInstance();
$db->selectQuery('id,branch_name,status','tbl_location ORDER BY id');
print Utils::jsonEncode($db->getFields());