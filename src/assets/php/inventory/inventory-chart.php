<?php
/**
* 01/25/2017
*
* This file contains the json data format of all archive machine.
*
*/
require_once '../database.php';
require_once '../utils.php';


if(Utils::getIsset('type_chart')){
	$db = Database::getInstance();
	$type_chart = Utils::getValue('type_chart');
	$idBrand 	= Utils::getValue('idBrand');


	switch ($type_chart) {
		case 'brands': //Total Inventory Brand
				$db->selectQuery('im.id_brand, b.brand_name, SUM( im.id_condition = 1 ) AS bn, SUM( im.id_condition = 2 ) AS rf','tbl_invnt_machines im
									LEFT JOIN tbl_brands b ON im.id_brand = b.id
									LEFT JOIN tbl_invnt_status s ON im.id_status = s.id
									WHERE (im.id_status = 0 OR s.status_type = "IN") AND b.status = 1 AND im.is_delete = 0
									GROUP BY im.id_brand');
			    $resBrand = $db->getFields();
				print Utils::jsonEncode($resBrand);

			break;
		case 'models': //Total model by Brand
				$db->selectQuery('mo.model_name, COUNT(*) as total_machine','tbl_invnt_machines im
									LEFT JOIN tbl_model mo ON im.model = mo.id
									LEFT JOIN tbl_invnt_status s ON im.id_status = s.id
									WHERE (im.id_status = 0 OR s.status_type = "IN") AND im.is_delete = 0 AND im.id_brand = '.$idBrand.'
									GROUP BY im.model');
			    $resModel = $db->getFields();

				print Utils::jsonEncode($resModel); 

		break;	

		default:
			 throw new Exception($type_chart." chart doesn't exist.");
			break;
	}


}



