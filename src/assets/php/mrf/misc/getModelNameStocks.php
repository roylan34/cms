<?php
/**
* 01/25/2017
*
* This file contains the json data format of all model name available.
*
*/

require_once '../../database.php';
require_once '../../utils.php';

$data = array();
$model_name = Utils::getValue('model_name');
if(!Utils::isEmpty($model_name)) { 

	$db = Database::getInstance();
	$db->selectQuery('id, model_name','tbl_model WHERE model_name LIKE "%'.$model_name.'%" ORDER BY id DESC LIMIT 0,10');
	$res = $db->getFields();

	if($db->getNumRows() > 0 ){
		foreach ($res['aaData'] as $key => $value) {
			$data[] = $value;
		}
	}else{
		$data = array( array(
				  'id' => 0,
				  'model_name' => 'NOT FOUND!'
			  ));
	}
}

print Utils::jsonEncode($data);


