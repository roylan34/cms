<?php
/**
* 08/04/2017
*
* This file is contains functions of Inventory.
*
*/ 

require_once '../database.php';
require_once '../utils.php';


/*
*Insert machine inventory status logs.
*/
function machineLogs($id_machine,$id_status,$date_in_out,$id_user,$id_company,$remarks,$db){ 
	if(!Utils::isEmpty($id_status)){
		$ids = explode(',',$id_machine);
		if(count($ids) > 0){
			$db->insertMultipleByUniqueQuery('tbl_invnt_status_logs','id_machine,id_status,date_in_out,id_user,id_company,remarks', $ids,
							  '"'.$id_status.'",
							  "'.$date_in_out.'",
							  "'.$id_user.'",
							  "'.$id_company.'",
							  "'.$db->escapeString($remarks).'"'); 
		}

	}
	return false;
}

