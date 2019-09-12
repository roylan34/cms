<?php
/**
* 01/25/2017
*
* This file contains the json data format of all company list.
*
*/

require_once '../../database.php';
require_once '../../utils.php';

$search ='';

if(Utils::getValue('search') == 'all') { 
	$search =" WHERE status = 1 ORDER BY company_name"; 
}
else { 
	$search = 'WHERE company_name LIKE "%'.Utils::getValue('company_name').'%" AND status = 1 ORDER BY company_name LIMIT 0,10'; 
}

$db = Database::getInstance();
$db->selectQuery('id,company_name, address','tbl_company '.$search.'');
$res = $db->getFields();
$data = array();

if($db->getNumRows() > 0 ){
	$data = $res['aaData'];
	// foreach ($res['aaData'] as $key => $value) {
	// 	$data[] = $value;
	// }
}else{
		$data = array( array(
				  'id' => 0,
				  'company_name' => 'NOT FOUND!'
		));
}

print Utils::jsonEncode($data);