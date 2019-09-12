<?php

/**
* 12/14/2018
*
* This file contains the json data of Monthly and Yearly Sales Per Account.
*
*/

require_once '../database.php';
require_once '../utils.php';

$year 	= Utils::getValue('year');
$month 	= Utils::getValue('month');
$action = Utils::getValue('action');
$conn 	= Database::getInstance();


switch ($action ) {
	case 'month':
			$conn->selectQuery('acc_manager,
							FORMAT(SUM(vat), 2) AS t_vat,
							FORMAT(SUM(gross), 2) AS t_gross,
							FORMAT(SUM(net), 2) AS t_net','tbl_sales_history_auto_import WHERE fiscal_year ='.$year.' AND month ="'.ucfirst($month).'" GROUP BY acc_manager ORDER BY acc_manager');
							$resMonth = $conn->getFields();
							print Utils::jsonEncode($resMonth['aaData']);

		break;
	case 'year':
			$conn->selectQuery('acc_manager,
							FORMAT(SUM(vat), 2) AS t_vat,
							FORMAT(SUM(gross), 2) AS t_gross,
							FORMAT(SUM(net), 2) AS t_net','tbl_sales_history_auto_import WHERE fiscal_year ='.$year.' GROUP BY acc_manager ORDER BY acc_manager');
							$resYear = $conn->getFields();
							print Utils::jsonEncode($resYear['aaData']);
		break;
	case 'list-year':
			//$conn->selectQuery('DATE_FORMAT(doc_date,"%Y") AS doc_year','tbl_sales_history_auto_import GROUP BY doc_year ORDER BY doc_year DESC');
			$conn->selectQuery('fiscal_year','tbl_sales_history_auto_import GROUP BY fiscal_year ORDER BY fiscal_year DESC');
							$listYear = $conn->getFields();
							 print Utils::jsonEncode($listYear['aaData']);
		break;
	default:
		throw new Exception("Missing action argument.");
		break;
}
