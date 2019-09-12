<?php
/**
* 01/16/2019
*
*/

require_once '../database.php';
require_once '../utils.php';

$db = Database::getInstance();

$comp_id   = Utils::getValue('comp_id');
$lat   = Utils::getValue('lat');
$lng   = Utils::getValue('lng');

$db->updateQuery('tbl_company','latitude    = "'.$lat.'",
						        longitude = "'.$lng.'"'
						       ,'id = "'.$comp_id.'"');

$res = $db->getFields();
print Utils::jsonEncode($res);