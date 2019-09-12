<?php
/**
* 01/25/2017
* Developed by: Delsan Web Development Team
*
* This file contains the json data format of company coordinates for google maps.
*
*/ 

require_once '../database.php';
require_once '../utils.php';



	$db = Database::getInstance();
	$db->selectQuery('c.company_name, c.latitude, c.longitude, (SELECT COUNT(*) FROM tblmif WHERE company_id = c.id AND status_machine = 0) AS num_of_machines',
					 'tbl_company c WHERE c.latitude != "" AND c.longitude != ""');
	$data = $db->getFields();
	print Utils::jsonEncode($data['aaData']);

