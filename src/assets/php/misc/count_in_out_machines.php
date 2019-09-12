<?php
/**
* 04/17/2018
*
* This file contains the json data format of total IN/OUT machines.
* 
*/

require_once '../database.php';
require_once '../utils.php';

$date_from 	= ( Utils::getValue('dateFrom') ? Utils::getValue('dateFrom') : Utils::getSysDate() );
$date_to 	= ( Utils::getValue('dateTo')   ? Utils::getValue('dateTo')   : Utils::getSysDate() );

$db = Database::getInstance();
$db->selectQuery('COUNT(*) AS total_in','tblmif WHERE status_machine = 0 AND company_id > 0 AND ( date_in BETWEEN "'.$date_from.'" AND "'.$date_to.'" )');
$total_in = $db->getFields();//Total IN MIF.
$db->fields = null;

$db->selectQuery('COUNT(*) AS total_out','tblmif WHERE status_machine > 0 AND company_id > 0 AND ( date_out BETWEEN "'.$date_from.'" AND "'.$date_to.'" )');
$total_out = $db->getFields();//Total OUT MIF.

$total_in   = number_format($total_in['aaData'][0]['total_in']);
$total_out  = number_format($total_out['aaData'][0]['total_out']);

print Utils::jsonEncode(array($total_in, $total_out));