<?php
/**
* 01/25/2017
*
* This file contains the json data format of all account type by flag.
*
*/

require_once '../../database.php';
require_once '../../utils.php';

$data = array();


	$db = Database::getInstance();
	$db->selectQuery('*','tbl_account_type ORDER BY acc_mrf_flags');
	$res = $db->getFields();

	foreach ($res['aaData'] as $key => $value) {
		$data[$value['acc_mrf_flags']][$key]['id'] = $value['id'];
		$data[$value['acc_mrf_flags']][$key]['dept'] = $value['acc_name'];
		$data[$value['acc_mrf_flags']][$key]['mif_flag'] = $value['acc_mif_flags'];
	}
	 
	print Utils::jsonEncode($data);