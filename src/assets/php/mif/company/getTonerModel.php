<?php
/**
* 02/28/2019
*
* This file contains the json data format of Toner model.
*
*/ 

require_once '../database.php';
require_once '../utils.php';



$toner = Database::getInstance();
$toner->selectQuery('id, toner_code','tbl_toner WHERE status = 1');
print Utils::jsonEncode($toner->getFields());