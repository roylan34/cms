<?php
/**
* 01/25/2017
*
* This file contains the json data format of branch approver.
*
*/

require_once '../../database.php';
require_once '../../utils.php';


$id_user = Utils::getValue('id_user');

if($id_user){

	$db = Database::getInstance();
	$db->selectQuery('GROUP_CONCAT(CAST(id_branch AS CHAR(10)) SEPARATOR ",") AS id_branch','tbl_mrf_branch_approver 
							WHERE (1st_approver = '.$id_user.' OR 2nd_approver = '.$id_user.' OR 2nd_approver_2 = '.$id_user.' OR
							3rd_approver = '.$id_user.' OR 4th_approver = '.$id_user.' OR 4th_approver_2 = '.$id_user.' OR
							5th_approver = '.$id_user.' OR 5th_approver_2 = '.$id_user.')');
	$res = $db->getFields();
	print Utils::jsonEncode($res);
}
else{
	trigger_error("ID user is empty");
}