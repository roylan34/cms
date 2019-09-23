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
		case 'brands': //Total MIF Brand
				$db->selectQuery('count(m.id) AS total_brands, br.brand_name AS brand_name, br.id AS id_brand','tblmif m LEFT JOIN tbl_brands br ON m.brand = br.id WHERE m.status_machine = 0 AND m.company_id != 0 AND br.status = 1 GROUP BY br.id ORDER BY total_brands DESC');
			    $resBrand = $db->getFields();//Get the action value either Update/Delete.

				echo Utils::jsonEncode($resBrand); 

			break;
		case 'models': //Total model by Brand
				$db->selectQuery('model, COUNT(*) AS total_machine','tblmif WHERE brand = '.$idBrand.' AND status_machine = 0 AND company_id != 0 GROUP BY model ORDER BY model');
			    $resModel = $db->getFields();

				echo Utils::jsonEncode($resModel); 

		break;	
		case 'location': //Total MIF Location
				$db->selectQuery('br.branch_name, COUNT( * ) AS total_machine_location','tblmif m LEFT JOIN tbl_location br ON br.id = m.branches WHERE m.status_machine = 0 AND m.company_id != 0  GROUP BY branches');
			    $resLocation = $db->getFields();

				echo Utils::jsonEncode($resLocation); 
			break;	
		default:
			 throw new Exception($type_chart." chart doesn't exist.");
			break;
	}


}



