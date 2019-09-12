<?php
/**
* 03/18/2017
*
* This file contains the json data format of total machines.
* 
*/

require_once '../database.php';
require_once '../utils.php';

$branch = Utils::getValue('branch');
$branchMachine = ($branch == '1' ? 'WHERE status_machine = 0 AND company_id > 0 ' : 'WHERE branches IN ('.$branch.') AND status_machine = 0 AND company_id > 0');
$branchCompany = ( $branch == '1' ? '' : ' WHERE id IN (SELECT DISTINCT id_company FROM tbl_company_branches WHERE id_branches IN ('.$branch.'))');

//In and Out
$date_from 	= ( Utils::getValue('dateFrom') ? Utils::getValue('dateFrom') : Utils::getSysDate() );
$date_to 	= ( Utils::getValue('dateTo')   ? Utils::getValue('dateTo')   : Utils::getSysDate() );

$db = Database::getInstance();
$db->selectQuery('count(id) as total_mif','tblmif '.$branchMachine.'');
$resMachine = $db->getFields();//Total MIF count.
$db->fields = null;

$db->selectQuery('SUM(STATUS =1 ) active,  SUM(STATUS =0 ) blocked','tbl_company'.$branchCompany.'');
$resClient = $db->getFields(); //Total client count.
$db->fields = null;

$db->selectQuery('COUNT(*) AS total_in','tblmif WHERE status_machine = 0 AND company_id > 0 AND ( date_in BETWEEN "'.$date_from.'" AND "'.$date_to.'" )');
$total_in = $db->getFields();//Total IN MIF.
$db->fields = null;

$db->selectQuery('COUNT(*) AS total_out','tblmif WHERE status_machine > 0 AND company_id > 0 AND ( date_out BETWEEN "'.$date_from.'" AND "'.$date_to.'" )');
$total_out = $db->getFields();//Total OUT MIF.


$total   = number_format($resMachine['aaData'][0]['total_mif']);
$active  = number_format($resClient['aaData'][0]['active']);
$blocked = number_format($resClient['aaData'][0]['blocked']);
$total_in   = number_format($total_in['aaData'][0]['total_in']);
$total_out  = number_format($total_out['aaData'][0]['total_out']);

print Utils::jsonEncode(array($total, $active, $blocked, $total_in, $total_out ));