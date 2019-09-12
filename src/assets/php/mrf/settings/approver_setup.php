<?php
/**
* 08/25/2017
*
* This file is contains Approver set-up table.
*
*/ 

require_once '../../database.php';
require_once '../../utils.php';

if(Utils::getIsset('action')){
	$action     = Utils::getValue('action');
	$id         = Utils::getValue('id_setup');
	$approver_1 = Utils::getValue('approver_1'); 
	$approver_2 = Utils::getValue('approver_2');
	$approver_2_2 = Utils::getValue('approver_2_2');
	$approver_3 = Utils::getValue('approver_3');
	$approver_4 = Utils::getValue('approver_4');
	$approver_4_2 = Utils::getValue('approver_4_2');
	$approver_5 = Utils::getValue('approver_5');
	$approver_5_2 = Utils::getValue('approver_5_2');
	$id_user = Utils::getValue('id_user');
	$id_branch = Utils::getValue('branch');

	$db  = Database::getInstance();

	switch ($action) {
		case 'update':

					$db->updateQuery('tbl_mrf_branch_approver','1st_approver = "'.$approver_1.'",
															2nd_approver = "'.$approver_2.'",
															2nd_approver_2 = "'.$approver_2_2.'",
															3rd_approver = "'.$approver_3.'",
															4th_approver = "'.$approver_4.'",
															4th_approver_2 = "'.$approver_4_2.'",
															5th_approver = "'.$approver_5.'",
															5th_approver_2 = "'.$approver_5_2.'"',
												     	  'id = "'.$id.'"');
				  print Utils::jsonEncode($db->getFields());
			
			break;
		case 'view-id':
				$db->selectQuery('ma.*, br.branch_name','tbl_mrf_branch_approver ma
					INNER JOIN tbl_branch br ON ma.id_branch = br.id
				    WHERE ma.id='.$id.' LIMIT 0,1');
			 	print Utils::jsonEncode($db->getFields());

			break;
		case 'view-all':
				$db->selectQuery('m.id, br.branch_name, 
								IFNULL(CONCAT(ac1.firstname, " ", ac1.lastname),"N/A") AS 1st_approver,
								IFNULL(CONCAT(ac2.firstname, " ", ac2.lastname),"N/A") AS 2nd_approver,
								IFNULL(CONCAT(ac2_2.firstname, " ", ac2_2.lastname),"N/A") AS 2nd_approver_2,
								IFNULL(CONCAT(ac3.firstname, " ", ac3.lastname),"N/A") AS 3rd_approver,
								IFNULL(CONCAT(ac4.firstname, " ", ac4.lastname),"N/A") AS 4th_approver,
								IFNULL(CONCAT(ac4_2.firstname, " ", ac4_2.lastname),"N/A") AS 4th_approver_2,
								IFNULL(CONCAT(ac5.firstname, " ", ac5.lastname),"N/A") AS 5th_approver,
								IFNULL(CONCAT(ac5_2.firstname, " ", ac5_2.lastname),"N/A") AS 5th_approver_2',
					 			'tbl_mrf_branch_approver  m
									LEFT JOIN tbl_accounts ac1 ON m.1st_approver = ac1.id
									LEFT JOIN tbl_accounts ac2 ON m.2nd_approver = ac2.id
									LEFT JOIN tbl_accounts ac2_2 ON m.2nd_approver_2 = ac2_2.id
									LEFT JOIN tbl_accounts ac3 ON m.3rd_approver = ac3.id
									LEFT JOIN tbl_accounts ac4 ON m.4th_approver = ac4.id
									LEFT JOIN tbl_accounts ac4_2 ON m.4th_approver_2 = ac4_2.id
									LEFT JOIN tbl_accounts ac5 ON m.5th_approver = ac5.id
									LEFT JOIN tbl_accounts ac5_2 ON m.5th_approver_2 = ac5_2.id
									LEFT JOIN tbl_branch br ON m.id_branch = br.id');
				print Utils::jsonEncode($db->getFields());  // send data as json format
			
			break;
		case 'check-is-active': //PENDING
				$db->selectQuery('ac.status, tac.app_mrf ','tbl_accounts ac LEFT JOIN tbl_app_action tac ON ac.id = tac.id_account WHERE ac.id ="'.$id_approver.'" LIMIT 0,1');
				$acc = $db->getFields();
				if($db->getNumRows() > 0){
					if(intval($acc['aaData'][0]['status']) == 0){
						echo json_encode(array("Can't assign as approver due to this account is INACTIVE."));
					}
					else if($acc['aaData'][0]['app_mrf'] == 'r'){
						echo json_encode(array("Can't assign as approver due to this account has READ only action."));
					}
					else{
						echo "true";
					}
				}
				else{
					echo json_encode(array("ID of approver not exist."));
				}
			
			break;	
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}


function checkIfAlreadyAssign($id_branch,$id_user,$db){
	$status = null;
	if(!Utils::isEmpty($id_branch)){
		$db->selectQuery("COUNT(*) AS already_approver ","tbl_mrf_branch_approver  
			WHERE  id_branch = ".$id_branch." AND (1st_approver = ".$id_user." OR 2nd_approver = ".$id_user." OR 2nd_approver_2= ".$id_user." OR 3rd_approver = ".$id_user." OR 4th_approver = ".$id_user." OR 5th_approver = ".$id_user.")");
		$res = $db->getFields();

		if($res['aaData'][0]['already_approver'] == 0){ // If 0 means not yet approver.
			$status = array('aaData' => array(
				'result' =>  'true',
				'message' => ''
			));
		}else{
			$status = array('aaData' => array(
				'result' =>  'false',
				'message' => "Name selected already assigned."
			));
		}
	}
	return $status;

}

