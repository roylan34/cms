<?php
/**
* 01/25/2017
* Developed by: Delsan Web Development Team
*
* This file contains the json data format of all company list.
*
*/ 

require_once '../database.php';
require_once '../utils.php';

$search="";
$has_user = "";
$limit = "";
$totalData =0;
$totalFiltered =0;
$status = '';
$qry_status = '';
$conn = Database::getInstance(); //For Searching.
if(Utils::getValue('form_no'))			{ $search .="AND m.form_no ='".$conn->escapeString(Utils::getValue('form_no'))."'"; }
if(Utils::getValue('company'))			{ $search .="AND c.company_name LIKE '%".$conn->escapeString(Utils::getValue('company'))."%'"; }
if(Utils::getValue('date_requested'))	{ $search .="AND m.date_requested LIKE '%".$conn->escapeString(Utils::getValue('date_requested'))."%'"; }
if(Utils::getValue('date_delivery'))	{ $search .="AND mt.5th_delivery_date ='".$conn->escapeString(Utils::getValue('date_delivery'))."'"; }
if(Utils::getValue('requested_by'))     { $search .="AND (CONCAT_WS(' ',ac.firstname,ac.lastname)) LIKE '%".$conn->escapeString(Utils::getValue('requested_by'))."%' "; }
if(Utils::getValue('serialnum'))		{ $search .="AND ms1.s1_serialnum = '".$conn->escapeString(Utils::getValue('serialnum'))."'"; }

switch (Utils::getValue('action_view')) {
	case 'current':
			if(Utils::getValue('status'))
			{ 
				$status = Utils::getValue('status'); // If has status search, query according to given paramater.
				$qry_status = ' WHEN (mt.1st_approver IS NULL && "1st_approver" = "'.$status.'") THEN CONCAT("APPROVER-1","|","status-gray")
							WHEN (mt.2nd_approver IS NULL && "2nd_approver" = "'.$status.'") THEN CONCAT("APPROVER-2","|","status-green")
							WHEN (mt.3rd_approver IS NULL && "3rd_approver" = "'.$status.'") THEN CONCAT("ENGINEERING","|","status-yellow")
							WHEN (mt.4th_approver IS NULL && "4th_approver" = "'.$status.'") THEN CONCAT("ACCOUNTING","|","status-red")
							WHEN (mt.5th_approver IS NULL && "5th_approver" = "'.$status.'") THEN CONCAT("LOGISTICS","|","status-blue")';
				switch ($status) {
					case '1st_approver':
						$search .="AND mt.1st_approver IS NULL"; 
						break;
					case '2nd_approver':
						 $search .="AND (mt.1st_approver IS NOT NULL && mt.2nd_approver IS NULL)"; 
						break;
					case '3rd_approver':
						$search .="AND (mt.1st_approver IS NOT NULL && mt.2nd_approver IS NOT NULL && mt.3rd_approver IS NULL)"; 
						break;
					case '4th_approver':
						$search .="AND (mt.1st_approver IS NOT NULL && mt.2nd_approver IS NOT NULL && mt.3rd_approver IS NOT NULL && mt.4th_approver IS NULL)"; 
						break;
					case '5th_approver':
						$search .="AND (mt.1st_approver IS NOT NULL && mt.2nd_approver IS NOT NULL && mt.3rd_approver IS NOT NULL && mt.4th_approver IS NOT NULL && mt.5th_approver IS NULL)"; 
						break;
					default:
						throw new Exception($status." not exist.", 1);			
						break;
				}
				
			}
			else{
				$qry_status = '	WHEN mt.1st_approver IS NULL THEN CONCAT("APPROVER-1","|","status-gray")
							WHEN mt.2nd_approver IS NULL THEN CONCAT("APPROVER-2","|","status-green")
							WHEN mt.3rd_approver IS NULL THEN CONCAT("ENGINEERING","|","status-yellow")
							WHEN mt.4th_approver IS NULL THEN CONCAT("ACCOUNTING","|","status-red")
							WHEN mt.5th_approver IS NULL THEN CONCAT("LOGISTICS","|","status-blue")';
			}


			if(Utils::getValue('id_user')){
				$branch_cur = ""; 
				$id_user = Utils::getValue('id_user');
				$id_branch = (Utils::getValue('id_branch') ? Utils::getValue('id_branch') : "NULL" );
						$conn->selectQuery('acct.acc_mrf_flags','tbl_account_type acct INNER JOIN tbl_accounts ac ON acct.id = ac.account_type WHERE ac.id ='.$id_user.'');
						$user_type = $conn->getFields(); // get user type.
						$user_type = ($conn->getNumRows() > 0  && !Utils::isEmpty($user_type['aaData'][0]['acc_mrf_flags']) ? $user_type['aaData'][0]['acc_mrf_flags'] : '');
						$conn->fields = null;

						if($user_type == "requestor"){ //If requestor get only the user id.
							$branch_cur  = " AND m.id_user_requestor =".$id_user." AND m.id_branch IN(".$id_branch.")";							
						}
						//HINTS: If Branch ALL selected display all branch has assigned else the selected branch.
						if($user_type == "approver" || $user_type == "preparer"){ //if approver or preparer, check if user assigned as approver in specific branch.
							$branch_cur  = " AND m.id_branch IN(".$id_branch.")";
						}
						// if($user_type == "requestor,preparer" ){ //if approver or preparer, check if user assigned as approver in specific branch.
						// 	if($id_branch){
						// 		$filter  = "AND (m.id_user_requestor = ".$id_user." OR m.id_branch IN(".$id_branch."))";
						// 		$search .= "AND (m.id_user_requestor = ".$id_user." OR m.id_branch IN(".$id_branch."))";
						// 	}
						// 	else{
						// 		$filter  = "AND m.id_user_requestor =".$id_user."";
						// 		$search .= "AND m.id_user_requestor =".$id_user."";
						// 	}
						// }

			}

			 if(!empty($user_type)){
					 $requestData= $_REQUEST;
					// storing  request (ie, get/post) global array to a variable  
					$conn->selectQuery('*','tbl_mrf m LEFT JOIN tbl_mrf_request_tracker mt ON m.id = mt.id_mrf WHERE m.id > 0 AND mt.flag_completion ="not complete" AND (mt.1st_id_status IN(1,2) AND mt.2nd_id_status IN(1,2)) AND mt.is_cancel="no" '.$branch_cur.'');
					$totalData = $conn->getNumRows(); //getting total number records without any search.
					$conn->row_count = 0;
					$conn->fields = null;

					if( !empty($search) ) { // if there is a search parameter.

					$conn->selectQuery('m.id, m.form_no, m.id_company, c.company_name, m.date_requested','tbl_mrf m 
								INNER JOIN tbl_company c ON m.id_company = c.id
								LEFT JOIN tbl_accounts ac ON m.id_user_requestor = ac.id
								LEFT JOIN tbl_mrf_request_tracker mt ON m.id = mt.id_mrf
					  			WHERE m.id > 0 AND mt.flag_completion ="not complete" AND (mt.1st_id_status IN(1,2) AND mt.2nd_id_status IN(1,2)) AND mt.is_cancel="no" '.$branch_cur.' '.$search.' ');

						$conn->fields = null;
						$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
					}
					else{
						$totalFiltered = $totalData;
					}
					
					if(intval($requestData['length']) >= 1 ) { $limit = 'LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }

					$conn->selectQuery('m.id, m.form_no, m.id_company, c.company_name, m.date_requested, CONCAT(ac.firstname," ", ac.lastname) AS requested_by, br.branch_name,
										 (CASE 
										 	'.$qry_status.'
											ELSE "APPROVED"
										END ) AS status_approval,
										(SELECT COUNT(*) FROM tbl_mrf_comments cm WHERE cm.id_mrf = m.id AND cm.id_user_from != '.$id_user.' ) AS no_received_message,
										(SELECT GROUP_CONCAT(DISTINCT CAST(id_user_from AS CHAR(10)) SEPARATOR ",") AS user_from FROM tbl_mrf_comments WHERE id_mrf = m.id) AS id_user_from, 
										DATEDIFF(DATE_FORMAT(NOW(), "%y-%m-%d"), m.date_requested) AS age','tbl_mrf m 
										INNER JOIN tbl_company c ON m.id_company = c.id
										LEFT JOIN tbl_mrf_branch_approver ba ON m.id_branch = ba.id_branch
										LEFT JOIN tbl_accounts ac ON m.id_user_requestor = ac.id
										LEFT JOIN tbl_mrf_request_tracker mt ON m.id = mt.id_mrf
										LEFT JOIN tbl_branch br ON m.id_branch = br.id
					  					WHERE m.id > 0 AND mt.flag_completion ="not complete" AND (mt.1st_id_status IN(1,2) AND mt.2nd_id_status IN(1,2)) AND mt.is_cancel="no" '.$branch_cur.' '.$search.' ORDER BY m.id DESC '.$limit.'');
					$row = $conn->getFields(); //Get all rows

					if($conn->getNumRows() > 0 ){
						$data = array();
						$nestedData=array(); 
							foreach($row['aaData'] as $index=>$value) { // preparing an array
								$nestedData[$index] = $value;
							}
							$data = $nestedData; 
							
						$json_data = array(
									"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
									"recordsTotal"    => intval( $totalData ),  // total number of records
									"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
									"records"         => $data   // data array,
									);
					} 
					else{ 
						$json_data = array("draw" =>  0,"recordsTotal" => 0, "recordsFiltered" => 0, "records" => array());
						$json_data['aaData'] = array(); 
					}

					 print Utils::jsonEncode($json_data);  // send data as json format.
			}
			else{
				echo "Something went wrong.";
			}
		break;

	case 'archive':
			if(Utils::getValue('id_user')){ 
				$branch_arc = "";
				$id_user = Utils::getValue('id_user');
				$status  = Utils::getValue('status');
				$id_branch = (Utils::getValue('id_branch') ? Utils::getValue('id_branch') : "NULL" );
				if($status) { //STATUS
					if($status == 2) // 2 = approve 
						$search .="AND (mt.1st_id_status = 2 AND mt.2nd_id_status = 2)"; 
					else if($status == 3)// 3 = disapprove 
						$search .="AND (mt.1st_id_status = 3 OR mt.2nd_id_status = 3)";
					else if($status == 4)// 4 = cancel 
						$search .="AND mt.is_cancel = 'yes'";	
					else
						return false;
				}
						$conn->selectQuery('acct.acc_mrf_flags','tbl_account_type acct INNER JOIN tbl_accounts ac ON acct.id = ac.account_type WHERE ac.id ='.$id_user.'');
						$user_type = $conn->getFields(); // get user type.
						$user_type = ($conn->getNumRows() > 0  && !Utils::isEmpty($user_type['aaData'][0]['acc_mrf_flags']) ? $user_type['aaData'][0]['acc_mrf_flags'] : '');
						$conn->fields = null;

						if($user_type == "requestor"){ //If requestor get only the user id.
							$branch_arc = " AND m.id_user_requestor =".$id_user." AND m.id_branch IN(".$id_branch.")";							
						}
						//HINTS: If Branch ALL selected display all branch has assigned else the selected branch.
						if($user_type == "approver" || $user_type == "preparer"){ //if approver or preparer, check if user assigned as approver in specific branch.
							$branch_arc = " AND m.id_branch IN(".$id_branch.")";
						}
						// if($user_type == "requestor,preparer" ){ //if approver or preparer, check if user assigned as approver in specific branch.
						// 	if($id_branch){
						// 		$filter  = "AND (m.id_user_requestor = ".$id_user." OR m.id_branch IN(".$id_branch."))";
						// 		$search .= "AND (m.id_user_requestor = ".$id_user." OR m.id_branch IN(".$id_branch."))";
						// 	}
						// 	else{
						// 		$filter  = "AND m.id_user_requestor =".$id_user."";
						// 		$search .= "AND m.id_user_requestor =".$id_user."";
						// 	}
						// }

			}

			 if(!empty($user_type)){
					$requestData= $_REQUEST;
					// storing  request (ie, get/post) global array to a variable  
					$conn->selectQuery('*','tbl_mrf m LEFT JOIN tbl_mrf_request_tracker mt ON m.id = mt.id_mrf WHERE m.id > 0 AND (mt.flag_completion ="complete" OR mt.1st_id_status = 3 OR mt.2nd_id_status = 3 OR mt.is_cancel = "yes") '.$branch_arc.'');
					$totalData = $conn->getNumRows(); //getting total number records without any search.
					$conn->row_count = 0;
					$conn->fields = null;
					if( !empty($search) ) { // if there is a search parameter.
						
					$conn->selectQuery('m.id, m.form_no, m.id_company, c.company_name, m.date_requested','tbl_mrf m 
								INNER JOIN tbl_company c ON m.id_company = c.id
								LEFT JOIN tbl_accounts ac ON m.id_user_requestor = ac.id
								LEFT JOIN tbl_mrf_request_tracker mt ON m.id = mt.id_mrf
								LEFT JOIN tbl_mrf_status ms ON mt.2nd_id_status = ms.id
								LEFT JOIN tbl_mrf_s1 ms1 ON m.id = ms1.id_mrf
					  			WHERE m.id > 0 AND (mt.flag_completion ="complete" OR mt.1st_id_status = 3 OR mt.2nd_id_status = 3 OR mt.is_cancel = "yes") '.$branch_arc.' '.$search.' GROUP BY ms1.id_mrf');

						$conn->fields = null;
						$totalFiltered  = $conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
					}
					else{

						$totalFiltered = $totalData;
					}
					
					if(intval($requestData['length']) >= 1 ) { $limit = 'LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }

					// (SELECT COUNT(*) FROM tbl_mrf_comments cm WHERE cm.id_mrf = m.id AND cm.id_user_from != '.$id_user.' ) AS no_received_message,
					// (SELECT GROUP_CONCAT(DISTINCT CAST(id_user_from AS CHAR(10)) SEPARATOR ",") AS user_from FROM tbl_mrf_comments WHERE id_mrf = m.id) AS id_user_from
					$conn->selectQuery('m.id, m.form_no, m.id_company, c.company_name, m.date_requested, IFNULL(mt.5th_delivery_date,"") AS delivery_date, CONCAT(ac.firstname," ",ac.lastname) AS requested_by, br.branch_name,
										(CASE 
										  WHEN is_cancel = "yes" THEN "cancelled"
										  WHEN (1st_id_status = 3 OR 2nd_id_status = 3 ) THEN "disapproved"
										   ELSE mt.flag_completion
										END) AS status','tbl_mrf m 
										INNER JOIN tbl_company c ON m.id_company = c.id
										LEFT JOIN tbl_accounts ac ON m.id_user_requestor = ac.id
										LEFT JOIN tbl_mrf_request_tracker mt ON m.id = mt.id_mrf
										LEFT JOIN tbl_branch br ON m.id_branch = br.id
										LEFT JOIN tbl_mrf_s1 ms1 ON m.id = ms1.id_mrf
					  					WHERE m.id > 0 AND (mt.flag_completion ="complete" OR mt.1st_id_status = 3 OR mt.2nd_id_status = 3 OR mt.is_cancel = "yes") '.$branch_arc.' '.$search.' GROUP BY ms1.id_mrf ORDER BY m.id DESC '.$limit.'');
					$row = $conn->getFields(); //Get all rows

					if($conn->getNumRows() > 0 ){
						$data = array();
						$nestedData=array(); 
							foreach($row['aaData'] as $index=>$value) { // preparing an array
								$nestedData[$index] = $value;
							}
							$data = $nestedData; 
							
						$json_data = array(
									"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
									"recordsTotal"    => intval( $totalData ),  // total number of records
									"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
									"records"         => $data   // data array,
									);
					} 
					else{ 
						$json_data = array("draw" =>  0,"recordsTotal" => 0, "recordsFiltered" => 0, "records" => array());
						$json_data['aaData'] = array(); 
					}

					 print Utils::jsonEncode($json_data);  // send data as json format.
					//print_r($json_data);
			}
			else{
				echo "Something went wrong.";
			}

		break;
	default:
		# code...
		break;
}




