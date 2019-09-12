<?php
/**
* 07/26/2017
*
* This file is contains Add/Update/View/Validate of Current/Archive Inventory table.
*
*/ 

require_once '../database.php';
require_once '../utils.php';
require_once '../phpemailer/emailFunction.php';


if(Utils::getIsset('action')){
	//For inputs
	$action     = Utils::getValue('action');
	$id_mrf 	= Utils::getValue('id_mrf');
	$comments   = Utils::getValue('comments');
	$id_user  	= Utils::getValue('id_user');
	$date_entered = Utils::getSysDate().' '.Utils::getSysTime();

	$db         = Database::getInstance();

	switch ($action) {
		case 'add':
					$db->insertQuery('tbl_mrf_comments','id_mrf,id_user_from,comments,date_sent',
								  '"'.$id_mrf.'",
								  "'.$id_user.'",
								  "'.$comments.'",
								  "'.$date_entered.'"');
					$res = $db->getFields();	          		
	          		//Send email notification to requestor after approve comments.
	          		if($res['aaData'][0] == 'success'){

						/* ----- Email Area ----- */
						$cc = array();
						$requestor_recipient = getRequestorEmail($id_mrf,$db);
						$approver_name = getApproverName($id_user,$db);

						$date_sent = date_create($date_entered); //Create datetime object.

						if(count($requestor_recipient) > 0){
							/************* REQUESTOR EMAIL ********/
							$approverMailSubject ="[MRF] Comment Notification";
							$mailBody  = "<h4>NEW COMMENT SENT!!!</h4>";
							$mailBody .="Form No: <b>".$requestor_recipient['form_no'][0][0]."</b><br/>";
							$mailBody .="Date sent: <b>".date_format($date_sent, 'l, j F Y g:i A')."</b><br/>";
							$mailBody .="From: <b>".$approver_name['fullname']."</b><br/>";
							 _EMAIL($requestor_recipient['email'],$cc,$approverMailSubject,$mailBody,"");
							 //_EMAIL(array(array('roelan.eroy@delsanonline.com'=> 'roelan.eroy@delsanonline.com')),$cc,$approverMailSubject,$mailBody,"");
						
						}
						 print Utils::jsonEncode($res);
					}
			break;
		case 'view-id':
					$db->selectQuery("mc.id_mrf, mc.id_user_from, CONCAT(ac.firstname,' ',ac.lastname) AS from_name, comments, DATE_FORMAT(mc.date_sent,'%d %b (%h:%m %p)') AS date_sent","tbl_mrf_comments mc
						LEFT JOIN tbl_accounts ac ON mc.id_user_from = ac.id
						WHERE mc.id_mrf = ".$id_mrf." ORDER BY mc.id DESC");
					 print Utils::jsonEncode($db->getFields());
					
			break;	
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}



function getRequestorEmail($id_mrf,$db){
	$res_filter = null;
	if(!Utils::isEmpty($id_mrf)){
		$db->fields = null;
		$db->selectQuery("IFNULL(ac.email,'') AS email, m.form_no","tbl_mrf  m
							LEFT JOIN tbl_accounts ac ON m.id_user_requestor = ac.id
							WHERE m.id = '".$id_mrf."' LIMIT 0,1");
		$res = $db->getFields(); //Get the info after request created.	
		foreach ($res['aaData'][0] as $key => $value) {
			$res_filter[$key][][($key == 'form_no'? 0 : $value)] = $value;			
		}
		 return $res_filter;	
	
	}
}

function getApproverName($id_approver,$db){
	$res_filter = null;
	if(!Utils::isEmpty($id_approver)){
		$db->fields = null;
		$db->selectQuery("CONCAT(firstname,' ', lastname) AS fullname","tbl_accounts WHERE id = '".$id_approver."' LIMIT 0,1");
		$res = $db->getFields(); //Get the info after request created.	
		return $res['aaData'][0];	
	
	}
}
