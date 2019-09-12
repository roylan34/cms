<?php
/**
* 01/25/2017
*
* This file contains auto generating form no.
*
*/

require_once '../../database.php';
require_once '../../utils.php';

$db = Database::getInstance();
$db->selectQuery('*','tbl_mrf');
$res = intval($db->getNumRows()) + 1;

date_default_timezone_set('Asia/Manila');
$info = getdate();
$date = $info['mday'];
$month = $info['mon'];
$year = $info['year'];

print Utils::jsonEncode(array('FRM'.$year.$date.$month.'-'.$res));




