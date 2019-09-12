<?php
/**
* 07/26/2017
*
* This file is contains Add/Update/View/Validate of Current/Archive MRF table.
*
*/ 

require_once '../database.php';
require_once '../utils.php';
require_once '../phpemailer/emailFunction.php';

if(Utils::getIsset('action')){
	$db = Database::getInstance();

	//For inputs
	$action     = Utils::getValue('action');
	$id_mrf 		= Utils::getValue('id_mrf');
	$id_user 		= Utils::getValue('id_user');

	$s1_row_data  	= Utils::getValue('s1_row_data');
	$s1_row_toner_data = Utils::getValue('s1_row_toner_data');
	$s1_row_del_data = Utils::getValue('s1_row_del_data');
	$s1_row_toner_data = Utils::getValue('s1_row_toner_data');

	$s2_row_data  	= Utils::getValue('s2_row_data');
	$s2_radio_id  	 = Utils::getValue('s2_radio_id');
	$s2_radio_nodays = Utils::getValue('s2_radio_nodays');
	$s2_radio_others = Utils::getValue('s2_radio_others');
	$s2_row_del_data = Utils::getValue('s2_row_del_data');
	$s1_row_toner_del_data = Utils::getValue('s1_row_toner_del_data');

	$s3_id_purpose 	= Utils::getValue('s3_id_purpose');

	$date_request  = Utils::getValue('date_request');
	$date_delivery = Utils::getValue('date_delivery');
	$id_company    = Utils::getValue('id_company');
	$ship_to    = $db->escapeString(Utils::getValue('ship_to'));
	$bill_to    = $db->escapeString(Utils::getValue('bill_to'));
	$contact_p  = $db->escapeString(Utils::getValue('contact_p'));
	$dept  		= $db->escapeString(Utils::getValue('dept'));
	$tel_no  	= Utils::getValue('tel_no');
	$id_user_requestor  = Utils::getValue('id_user_requestor');
	$id_status  = Utils::getValue('id_status');
	$id_branch  = Utils::getValue('branch');
	$attachment_name  = Utils::getValue('attachment_name');
	$date_entered = Utils::getSysDate().' '.Utils::getSysTime();
	
	switch ($action) {
		case 'add':
				$add_form_no = generateFormNo($db);
				$db->fields = null;
				//Step 3 and 4
				$db->insertQuery('tbl_mrf','form_no,id_company,date_requested,ship_to,bill_to,contact_person,department,tel_no, s2_radio_id, s2_radio_nodays, s2_radio_others, s3_id_purpose, id_user_requestor, id_branch, attachment, created_at',
												'"'.$add_form_no.'",
												"'.$id_company.'",
												"'.$date_entered.'",
												"'.$ship_to.'",
												"'.$bill_to.'",
												"'.$contact_p.'",
												"'.$dept.'",
												"'.$tel_no.'",
												"'.$s2_radio_id.'",
												"'.$s2_radio_nodays.'",
												"'.$s2_radio_others.'",
												"'.$s3_id_purpose.'",
												"'.$id_user_requestor.'",
												"'.$id_branch.'",
												"'.$attachment_name.'",
												"'.$date_entered.'"');
				//Get the last inserted and pass to insertMultipleQuery.
				$last_id_mrf = $db->getLastId(); 

			    //Step 2
				// if($s2_row_data){
				if(count($s2_row_data) > 0 && $s2_row_data){
					$db->insertMultipleQuery("tbl_mrf_s2","id_mrf, s2_id_brand, s2_id_model, s2_serialnum, s2_contact_p, s2_dept_branch", array($last_id_mrf),$s2_row_data);
				}

				// //Step 1
				if ($s1_row_data) {
					foreach ($s1_row_data as $s1_key => $s1_val) {
								$db->insertQuery('tbl_mrf_s1','id_mrf, s1_quantity, s1_bn_rf, s1_serialnum, s1_id_brand, s1_id_model, s1_accessories',
									'"'.$last_id_mrf.'",
									"'.$s1_val['s1_quantity'].'",
									"'.$s1_val['s1_bn_rf'].'",
									"'.$s1_val['s1_serialnum'].'",
									"'.$s1_val['s1_brand'].'",
									"'.$s1_val['s1_id_model'].'",
									"'.$s1_val['s1_accessories'].'"');

						$last_s1_id_mrf = array($db->getLastId()); //Get the last inserted.

						if(is_array($s1_row_toner_data) && array_key_exists($s1_key, $s1_row_toner_data)) //Insert data of toner's.
							// $db->insertMultipleQuery("tbl_mrf_s1_toner","id_mrf_s1,s1_toner_qty,s1_toner_monocolor,s1_toner_model,s1_toner_price,s1_toner_yield,s1_toner_rate",$last_s1_id_mrf,$s1_row_toner_data[$s1_key]['insert']);
							$db->insertMultipleQuery("tbl_mrf_s1_toner","id_mrf_s1,s1_toner_qty,s1_toner_monocolor,s1_toner_model,s1_toner_price,s1_toner_yield, s1_toner_total, s1_toner_type, s1_toner_rate",$last_s1_id_mrf,$s1_row_toner_data[$s1_key]['insert']);

					}	

				}
				//Insert last id to request tracker table.
				$db->insertQuery('tbl_mrf_request_tracker','id_mrf','"'.$last_id_mrf.'"');
				$res = $db->getFields();
			
				if($res['aaData'][0] == 'success'){
					print Utils::jsonEncode($res);	//Print success status.

					$requestor_name = Utils::getValue('fullname');
					$email    = Utils::getValue('email');
					$db->fields = null;
					$db->selectQuery("m.*, c.company_name, br.branch_name","tbl_mrf  m
										LEFT JOIN tbl_company c ON m.id_company = c.id
										LEFT JOIN tbl_branch br ON m.id_branch = br.id
										WHERE m.id = '".$last_id_mrf."' AND m.id_branch='".$id_branch."'");
					$mrf_info = $db->getFields(); //Get the info after request created.		

					/* ----- Email Area ----- */
					$cc = array();
					$requestor = array();
					$requestorMailSubject = "MRF NO: ".$add_form_no;
					
					/******************* REQUESTOR EMAIL *********************/
						$requestor[] = array( $email => $requestor_name);
						$mailBody ="<h4>MACHINE REQUEST DETAILS</h4>";		
						$mailBody .="Date Requested: <b> ".$mrf_info['aaData'][0]['date_requested']." </b><br/>";																	
						$mailBody .="Company Name: <b> ".$mrf_info['aaData'][0]['company_name']." </b><br/>";	
						$mailBody .="Ship to address: <b> ".$mrf_info['aaData'][0]['ship_to']." </b><br/>";	
						$mailBody .="Bill to address: <b> ".$mrf_info['aaData'][0]['bill_to']." </b><br/>";	
						$mailBody .="Contact Person: <b> ". (Utils::isEmpty($mrf_info['aaData'][0]['contact_person']) ? 'N/A' : $mrf_info['aaData'][0]['contact_person'] )." </b><br/>";
						$mailBody .="<b>Your MRF has been submitted successfully...</b><br/>";	
					     _EMAIL($requestor,$cc,$requestorMailSubject,$mailBody,"");	

					$approver_recipients = getApproversEmail($id_branch,$db);
					if(count($approver_recipients) > 0){
						/************* APPROVER EMAIL ********/
						$approverMailSubject ="NEW MRF NO: ".$add_form_no;
						$mailBody = "<h4>NEW MACHINE REQUEST!!!</h4>";
						$mailBody .="Requested By: <b>".$requestor_name."</b><br/>";
						$mailBody .="Date Requested: <b> ".$mrf_info['aaData'][0]['date_requested']." </b><br/>";
						$mailBody .="Branch: <b> ".$mrf_info['aaData'][0]['branch_name']." </b><br/>";	
						$mailBody .="Form No: <b>".$add_form_no."</b><br/>";
						$mailBody .="Company Name: <b>".$mrf_info['aaData'][0]['company_name']."</b><br/>";
						$mailBody .="Ship to address: <b> ".$mrf_info['aaData'][0]['ship_to']." </b><br/>";	
						$mailBody .="Bill to address: <b> ".$mrf_info['aaData'][0]['bill_to']." </b><br/>";	
						$mailBody .="Contact Person: <b> ". (Utils::isEmpty($mrf_info['aaData'][0]['contact_person']) ? 'N/A' : $mrf_info['aaData'][0]['contact_person'] )." </b><br/>";
						 _EMAIL($approver_recipients,$cc,$approverMailSubject,$mailBody,"");
						//_EMAIL(array(array('roelan.eroy@delsanonline.com'=> 'roelan.eroy@delsanonline.com')),$cc,$approverMailSubject,$mailBody,"");
					
					}
				}
			 	// print Utils::jsonEncode($res);	//Print success status.

			break;
		case 'update': 
					if(!Utils::isEmpty($id_mrf)){

						//Update row values in Step 1 form.
						if(array_key_exists('update', $s1_row_data) && count($s1_row_data['update']) > 0 ){ 

							foreach ($s1_row_data['update'] as $k_s1_u => $v_s1_u) {
								 $db->updateQuery('tbl_mrf_s1','s1_serialnum = "'.$v_s1_u['s1_serialnum'].'", 
								 				s1_quantity     = "'.$v_s1_u['s1_quantity'].'",
								 				s1_bn_rf     	= "'.$v_s1_u['s1_bn_rf'].'",
												s1_id_brand     = "'.$v_s1_u['s1_brand'].'",
												s1_id_model 	= "'.$v_s1_u['s1_id_model'].'",
												s1_accessories 	= "'.$v_s1_u['s1_accessories'].'"'
								    			,'id = "'.$k_s1_u.'"');

								 //Toner update.
								 if(is_array($v_s1_u["s1_toner"]) && array_key_exists('update', $v_s1_u["s1_toner"]) && count($v_s1_u['s1_toner']["update"]) > 0){
								 	 $db->updateMultipleQuery("tbl_mrf_s1_toner",$v_s1_u["s1_toner"]["update"]); 
								 }
								 //Toner insert.
								 if(is_array($v_s1_u["s1_toner"]) && array_key_exists('insert', $v_s1_u["s1_toner"]) && count($v_s1_u['s1_toner']["insert"]) > 0){
 	 								$db->insertMultipleQuery("tbl_mrf_s1_toner","id_mrf_s1,s1_toner_qty,s1_toner_monocolor,s1_toner_model,s1_toner_price,s1_toner_yield, s1_toner_total, s1_toner_type, s1_toner_rate", array($k_s1_u), $v_s1_u["s1_toner"]["insert"]);
								 }
								
							}
					 	}
					   
						//Insert row values in Step 1 form.
						if(array_key_exists('insert', $s1_row_data) && count($s1_row_data['insert']) > 0 ){ 
							
							foreach ($s1_row_data['insert'] as $k_s1_a => $v_s1_a) {
								$db->insertQuery('tbl_mrf_s1','id_mrf, s1_quantity, s1_bn_rf, s1_serialnum,s1_id_brand,s1_id_model,s1_accessories',
									'"'.$id_mrf.'",
									"'.$v_s1_a['s1_quantity'].'",
									"'.$v_s1_a['s1_bn_rf'].'",
									"'.$v_s1_a['s1_serialnum'].'",
									"'.$v_s1_a['s1_brand'].'",
									"'.$v_s1_a['s1_id_model'].'",
									"'.$v_s1_a['s1_accessories'].'"');

							$last_s1_id_mrf = $db->getLastId(); //Get the last inserted.

								if(is_array($v_s1_a["s1_toner"]) && array_key_exists('insert', $v_s1_a["s1_toner"]) && count($v_s1_a['s1_toner']["insert"]) > 0){ //Insert data of toner's.
									$db->insertMultipleQuery("tbl_mrf_s1_toner","id_mrf_s1,s1_toner_qty,s1_toner_monocolor,s1_toner_model,s1_toner_price,s1_toner_yield,s1_toner_total,s1_toner_type, s1_toner_rate",array($last_s1_id_mrf),$v_s1_a["s1_toner"]["insert"]);
								}	
						 	}
						}

					 	 //Step 2 Insert
					 	 if($s2_row_data && array_key_exists('insert', $s2_row_data) && count($s2_row_data['insert']) > 0 ){ 
					 	 	$db->insertMultipleQuery("tbl_mrf_s2","id_mrf, s2_id_brand,s2_id_model,s2_serialnum,s2_contact_p,s2_dept_branch",array($id_mrf),$s2_row_data['insert']);	
					 	 }
					 	 //Step 2 Update
					 	 if($s2_row_data && array_key_exists('update', $s2_row_data) && count($s2_row_data['update']) > 0 ){ 
					 		 $db->updateMultipleQuery("tbl_mrf_s2",$s2_row_data['update']); 
					 		
					 		 //Radio button options.
 							// $db->updateQuery('tbl_mrf_s2','s2_radio_id = "'.$s2_radio_id.'", 
								// 				s2_radio_nodays = "'.$s2_radio_nodays.'"'
								//     			,'is_deleted = 0 AND id_mrf = "'.$id_mrf.'"');
					 	 }

					 	 //Soft delete the data in Step 1 and 2 form.
					 	if(Utils::getIsset('s1_row_del_data') && count($s1_row_del_data) > 0){
					 	 	$db->updateQuery('tbl_mrf_s1','is_deleted = 1','id IN ('.implode(',', $s1_row_del_data).')');
					 	 }
					 	if(Utils::getIsset('s2_row_del_data') && count($s2_row_del_data) > 0){
					 	 	$db->updateQuery('tbl_mrf_s2','is_deleted = 1','id IN ('.implode(',', $s2_row_del_data).')');
					 	 }					 	
					 	 if(Utils::getIsset('s1_row_toner_del_data') && count($s1_row_toner_del_data) > 0){
					 	 	$db->updateQuery('tbl_mrf_s1_toner','is_deleted = 1','id IN ('.implode(',', $s1_row_toner_del_data).')');
					 	 }

					 	 //Step 3 and 4 form.
 						$db->updateQuery('tbl_mrf','s3_id_purpose = "'.$s3_id_purpose.'", 
												id_company = "'.$id_company.'",
												ship_to  = "'.$ship_to.'",
												bill_to = "'.$bill_to.'",
												contact_person = "'.$contact_p.'",
												department = "'.$dept.'",
												tel_no = "'.$tel_no.'",
												s2_radio_id = "'.$s2_radio_id.'",
												s2_radio_nodays = "'.$s2_radio_nodays.'",
												s2_radio_others = "'.$s2_radio_others.'",
												attachment = "'.$attachment_name.'"'
								    			,'id = "'.$id_mrf.'"');
				 		  print Utils::jsonEncode($db->getFields());
					 	 
				  	}
			break;
		case 'update_status': //Updating status.
				if(!Utils::isEmpty($id_branch)){
						$id_user_logged = Utils::getValue('id_user_logged');
						$us_field_name = Utils::getValue('field_name'); // prefix us = update_status
						$us_id_status = Utils::getValue('id_status');
						$dr_number = Utils::getValue('dr_number');
						$edr_number = Utils::getValue('edr_number');
						$inv_number = Utils::getValue('inv_number');
						$delivery_date = Utils::getValue('delivery_date');
						$received_by = Utils::getValue('received_by');
						$delivered_by = Utils::getValue('delivered_by');
						$form_no = Utils::getValue('form_no');
						
						$checkApprover = checkAssignedApprover($id_branch, $us_field_name, $id_user_logged, $db);

						 if($checkApprover['aaData']['result'] == 'true'){
						 	$db->fields = null;
						 	$checkApprover	= checkRequestTrack($id_mrf,$us_field_name,$db);

						 	
						 	if($checkApprover['aaData']['result'] == 'true'){
						 			switch ($us_field_name) {
						 				case '1st_approver':
						 					$sql = "1st_approver = {$id_user_logged}, 1st_date ='{$date_entered}',1st_id_status='{$us_id_status}'";
						 					break;						 				
						 				case '2nd_approver':
						 					$sql = "2nd_approver = {$id_user_logged}, 2nd_date ='{$date_entered}',2nd_id_status='{$us_id_status}'";
						 					break;
						 				case '2nd_approver_2':
						 					$sql = "2nd_approver = {$id_user_logged}, 2nd_date ='{$date_entered}',2nd_id_status='{$us_id_status}'";
						 					break;
						 				case '3rd_approver':
						 					$sql = "3rd_approver = {$id_user_logged}, 3rd_date ='{$date_entered}',3rd_id_status='{$us_id_status}'";
						 					break;
						 				case '4th_approver':
						 					$sql = "4th_approver = {$id_user_logged}, 4th_date ='{$date_entered}', 4th_dr_number ='{$dr_number}', 4th_id_status='{$us_id_status}', 4th_edr_number='{$edr_number}', 4th_inv_number='{$inv_number}'";
						 					break;
						 				case '4th_approver_2':
						 					$sql = "4th_approver = {$id_user_logged}, 4th_date ='{$date_entered}', 4th_dr_number ='{$dr_number}', 4th_id_status='{$us_id_status}', 4th_edr_number='{$edr_number}', 4th_inv_number='{$inv_number}'";
						 					break;
						 				case '5th_approver':
						 					$sql = "5th_approver = {$id_user_logged}, 5th_date ='{$date_entered}', 5th_id_status='{$us_id_status}', 5th_delivery_date = '{$delivery_date}', 5th_received_by='{$received_by}', 5th_delivered_by='{$delivered_by}', flag_completion ='complete'";
						 					break;
						 				case '5th_approver_2':
						 					$sql = "5th_approver = {$id_user_logged}, 5th_date ='{$date_entered}', 5th_id_status='{$us_id_status}', 5th_delivery_date = '{$delivery_date}', 5th_received_by='{$received_by}', 5th_delivered_by='{$delivered_by}', flag_completion ='complete'";
						 					break;		
						 				default:
						 					trigger_error($us_field_name. 'field not exist in database.');
						 					break;
						 			}
									$db->updateQuery('tbl_mrf_request_tracker',''.$sql.'',
															'id_mrf = '.$id_mrf.'');

										/******************* REQUESTOR EMAIL *********************/
									$cc = array();
									$requestorMailSubject ="MRF NO: ".$form_no;
									$requestStatus = getRequestStatus($id_mrf,$db);

									if(count($requestStatus) > 0){

										 $mailBody ="<h4>MRF STATUS APPROVAL</h4>";
										 $mailBody .="Form No: <b> ".$form_no." </b><br/>";
										 $mailBody .="1st Approver: <b> ".$requestStatus['1st_status']." </b><br/>";
										 $mailBody .="2nd Approver: <b> ".$requestStatus['2nd_status']." </b><br/>";	
										 $mailBody .="Engineering: <b> ".$requestStatus['3rd_status']." </b><br/>";	
										 $mailBody .="Accounting: <b> ".$requestStatus['4th_status']." </b><br/>";
										 $mailBody .="Logistics: <b> ".$requestStatus['5th_status']." </b><br/>";				
										// $mailBody .="Date Requested: <b> ".$mrf_info['aaData'][0]['date_requested']." </b><br/>";																	
										// $mailBody .="Company Name: <b> ".$mrf_info['aaData'][0]['company_name']." </b><br/>";	
										// $mailBody .="Ship to address: <b> ".$mrf_info['aaData'][0]['ship_to']." </b><br/>";	
										// $mailBody .="Bill to address: <b> ".$mrf_info['aaData'][0]['bill_to']." </b><br/>";	
										// $mailBody .="Contact Person: <b> ". (Utils::isEmpty($mrf_info['aaData'][0]['contact_person']) ? 'N/A' : $mrf_info['aaData'][0]['contact_person'] )." </b><br/>";
										 if($us_field_name == '5th_approver'){
										 	$mailBody .="<b>Your MRF has been completely approved.Please see at archived page.</b><br/>";	
										}
									    _EMAIL(array(array($requestStatus['email'] => $requestStatus['email'] )),$cc,$requestorMailSubject,$mailBody,"");	
									}
						 	}
					  	}

				 		 print Utils::jsonEncode($checkApprover);
				}
			break;
		case 'update_cancel': //Requestor can cancel request if not yet approved.
				if(!Utils::isEmpty($id_mrf)){
						$is_cancel = checkIsCancel($id_mrf,$db);

						if($is_cancel['aaData']['result'] == 'true'){
							$db->updateQuery('tbl_mrf_request_tracker','is_cancel = "yes"'											
								    			,'id_mrf = '.$id_mrf.'');
						}

				 		 print Utils::jsonEncode($is_cancel);
				}
			break;	

		case 'update_view_request': //1st and 2nd Approver update attachment.
				if(!Utils::isEmpty($id_mrf)){
 						$db->updateQuery('tbl_mrf','attachment = "'.$attachment_name.'"'
								    			,'id = "'.$id_mrf.'"');
				 		  print Utils::jsonEncode($db->getFields());
				}
			break;
		case 'edit_approve_data': //Engineering approver serial update.
			if(!Utils::isEmpty($id_mrf)){

				$action_approver = Utils::getValue("action_approver");
				switch ($action_approver) {
					case 'engr':
							$row_data_sn = Utils::getValue('row_data_sn');
							if(array_key_exists('update', $row_data_sn) && count($row_data_sn["update"]) > 0){
							 	$db->updateMultipleQuery("tbl_mrf_s1",$row_data_sn["update"]); 

							 	print Utils::jsonEncode($db->getFields());
							}
						break;	
					case 'acc':
							$dr_num  = Utils::getValue('dr_num');
							$edr_num = Utils::getValue('edr_num');
							$inv_num = Utils::getValue('inv_num');
							$db->updateQuery('tbl_mrf_request_tracker','4th_dr_number = "'.$dr_num.'",
												4th_edr_number = "'.$edr_num.'",
												4th_inv_number = "'.$inv_num.'"'											
								    			,'id_mrf = '.$id_mrf.'');
							print Utils::jsonEncode($db->getFields());
						break;					
					default:
					       throw new Exception($action_approver." action doesn't exist.");
						break;
				}
			 	
			}
		break;	
		case 'view-id':
		            //Step 3 and 4
					$db->selectQuery("m.*, c.company_name, CONCAT(ac.firstname,' ', ac.lastname) AS requested_by, m.attachment","tbl_mrf m 
						INNER JOIN tbl_company c ON m.id_company = c.id
						INNER JOIN tbl_accounts ac ON m.id_user_requestor = ac.id
						WHERE m.id = ".$id_mrf."");
					$res_row_data_s4 = $db->getFields();
					$db->fields = null;

					//Step 2
					$db->selectQuery("id AS s2_id, s2_id_brand, s2_id_model, s2_serialnum, s2_contact_p, s2_dept_branch","tbl_mrf_s2  
						WHERE is_deleted = 0 AND id_mrf = ".$id_mrf."");
					$res_row_data_s2 = $db->getFields();
					$db->fields = null;

					//Step 1 Toner
					$db->selectQuery("mt.id , mt.id_mrf_s1, mt.s1_toner_qty, mt.s1_toner_monocolor, mt.s1_toner_model, mt.s1_toner_price, mt.s1_toner_yield, s1_toner_total, s1_toner_type, s1_toner_rate","tbl_mrf_s1_toner mt
						INNER JOIN tbl_mrf_s1 m ON mt.id_mrf_s1 = m.id
						WHERE  mt.is_deleted = 0 AND m.id_mrf = ".$id_mrf."");
					$res_row_data_s1_toner = $db->getFields();
					$db->fields = null;
					$tableValues['aaData'] = null;
					if($db->getNumRows() > 0){
						foreach ($res_row_data_s1_toner['aaData'] as $key => $value) {
							$tableValues['aaData'][$value['id_mrf_s1']][$value['id']] = $value;
						}
					}

					//Step 1 Toner Sub
					$db->selectQuery("mt.id , mt.id_mrf_s1, mt.s1_toner_qty, mt.s1_toner_monocolor, IFNULL(t.type_name, '') AS type_name, mt.s1_toner_model, mt.s1_toner_price, mt.s1_toner_yield, s1_toner_total, s1_toner_type, IFNULL(bt.type,'') AS s1_billing_type, s1_toner_rate","tbl_mrf_s1_toner mt
						INNER JOIN tbl_mrf_s1 m ON mt.id_mrf_s1 = m.id
						LEFT JOIN tbl_type t ON mt.s1_toner_monocolor = t.id
						LEFT JOIN tbl_toner_billing_type bt ON mt.s1_toner_type = bt.id
						WHERE  mt.is_deleted = 0 AND m.id_mrf = ".$id_mrf."");
					$res_row_data_s1_toner_sub = $db->getFields();
					$db->fields = null;
					$SubTonertableValues['aaData'] = null;
					if($db->getNumRows() > 0){
						foreach ($res_row_data_s1_toner_sub['aaData'] as $key => $value) {
							$SubTonertableValues['aaData'][$value['id_mrf_s1']][$value['id']] = $value;
						}
					}

					//Step 1
					$db->selectQuery("id, s1_serialnum, IFNULL(s1_quantity,' ') as s1_quantity, s1_bn_rf, s1_id_brand, s1_id_model, s1_accessories ","tbl_mrf_s1 WHERE is_deleted = 0 AND id_mrf = ".$id_mrf."");
					$res_row_data_s1 = $db->getFields();
					$db->fields = null;

					$merge_res['aaData'][0] = array_merge($res_row_data_s4['aaData'][0], 
						array("res_row_data_s1" => $res_row_data_s1['aaData'] ),  
						array("res_row_data_s1_toner" => $tableValues['aaData'] ), 
						array("res_row_data_s2" => $res_row_data_s2['aaData'] ),
						array("res_row_data_s1_toner_sub" => $SubTonertableValues['aaData'] )
					);

					print Utils::jsonEncode($merge_res);
					
			break;
		case 'view-toner-details':

					//Get details if current user logged-in is assigned as approver.
					$db->selectQuery("1st_approver, 2nd_approver, 2nd_approver_2, 3rd_approver, 4th_approver, 4th_approver_2, 5th_approver, 5th_approver_2","tbl_mrf_branch_approver  
						WHERE id_branch = (SELECT id_branch FROM tbl_mrf WHERE id = ".$id_mrf.")");
					$is_approver = $db->getFields();
					$db->fields = null;


					//Get details of whose user already approved.
					$db->selectQuery("m.id_mrf, 
									IFNULL(CONCAT(ac1.firstname, ' ', ac1.lastname),'') AS 1st_approver,
									IFNULL(m.1st_date,'') AS 1st_date,
									IFNULL(CONCAT(ac2.firstname, ' ', ac2.lastname),'') AS 2nd_approver,
									IFNULL(m.2nd_date,'') AS 2nd_date,
									IFNULL(CONCAT(ac3.firstname, ' ', ac3.lastname),'') AS 3rd_approver,
									IFNULL(m.3rd_date,'') AS 3rd_date ,
									IFNULL(CONCAT(ac4.firstname, ' ', ac4.lastname),'') AS 4th_approver,
									IFNULL(m.4th_date,'') AS 4th_date,
									m.4th_dr_number,
									m.4th_edr_number,
									m.4th_inv_number,
									IFNULL(CONCAT(ac5.firstname, ' ', ac5.lastname),'') AS 5th_approver,
									IFNULL(m.5th_date,'') AS 5th_date,
									IFNULL(m.5th_delivery_date,'') AS 5th_delivery_date,
									IFNULL(m.5th_received_by,'') AS 5th_received_by,
									IFNULL(m.5th_delivered_by,'') AS 5th_delivered_by
									","tbl_mrf_request_tracker  m
										LEFT JOIN tbl_accounts ac1 ON m.1st_approver = ac1.id
										LEFT JOIN tbl_accounts ac2 ON m.2nd_approver = ac2.id
										LEFT JOIN tbl_accounts ac3 ON m.3rd_approver = ac3.id
										LEFT JOIN tbl_accounts ac4 ON m.4th_approver = ac4.id
										LEFT JOIN tbl_accounts ac5 ON m.5th_approver = ac5.id
									WHERE m.id_mrf = ".$id_mrf."");
					$user_approved = $db->getFields();
					$db->fields = null;

		            //Step 3 and 4
					$db->selectQuery("m.*, c.company_name, CONCAT(ac.firstname,' ', ac.lastname) AS requested_by","tbl_mrf m 
						INNER JOIN tbl_company c ON m.id_company = c.id
						INNER JOIN tbl_accounts ac ON m.id_user_requestor = ac.id
						WHERE m.id = ".$id_mrf."");
					$res_row_data_s4 = $db->getFields();
					$db->fields = null;

					//Step 2
					$db->selectQuery("ms.id, br.brand_name, mo.model_name, ms.s2_serialnum, ms.s2_contact_p, ms.s2_dept_branch","tbl_mrf_s2 ms
						LEFT JOIN tbl_brands br ON ms.s2_id_brand = br.id
						LEFT JOIN tbl_model mo ON ms.s2_id_model = mo.id  
						WHERE ms.is_deleted = 0 AND ms.id_mrf = ".$id_mrf."");
					$res_row_data_s2 = $db->getFields();
					$db->fields = null;

					//Step 1 Toner
					$db->selectQuery("mt.id, mt.id_mrf_s1, mt.s1_toner_qty, t.type_name , mt.s1_toner_model, mt.s1_toner_price, mt.s1_toner_yield, mt.s1_toner_total, IFNULL(bt.type,'') AS s1_billing_type, mt.s1_toner_rate","tbl_mrf_s1_toner mt
						INNER JOIN tbl_mrf_s1 m ON mt.id_mrf_s1 = m.id
						LEFT JOIN tbl_type t ON mt.s1_toner_monocolor = t.id
						LEFT JOIN tbl_toner_billing_type bt ON mt.s1_toner_type = bt.id
						WHERE mt.is_deleted = 0 AND m.id_mrf = ".$id_mrf."");
					$res_row_data_s1_toner = $db->getFields();
					$db->fields = null;
					$tableValues['aaData'] = array();
					if($db->getNumRows() > 0){
						foreach ($res_row_data_s1_toner['aaData'] as $key => $value) {
							$tableValues['aaData'][$value['id_mrf_s1']][] = $value;
						}
					}

					//Step 1 Toner Sub
					$db->selectQuery("mt.id , mt.id_mrf_s1, mt.s1_toner_qty, mt.s1_toner_monocolor, t.type_name, mt.s1_toner_model, mt.s1_toner_price, mt.s1_toner_yield, IFNULL(mt.s1_toner_total,'') AS s1_toner_total, mt.s1_toner_type, IFNULL(bt.type,'') AS s1_billing_type, s1_toner_rate","tbl_mrf_s1_toner mt
						INNER JOIN tbl_mrf_s1 m ON mt.id_mrf_s1 = m.id
						LEFT JOIN tbl_type t ON mt.s1_toner_monocolor = t.id
						LEFT JOIN tbl_toner_billing_type bt ON mt.s1_toner_type = bt.id
						WHERE  mt.is_deleted = 0 AND m.id_mrf = ".$id_mrf."");
					$res_row_data_s1_toner_sub = $db->getFields();
					$db->fields = null;
					$SubTonertableValues['aaData'] = null;
					if($db->getNumRows() > 0){
						foreach ($res_row_data_s1_toner_sub['aaData'] as $key => $value) {
							$SubTonertableValues['aaData'][$value['id_mrf_s1']][$value['id']] = $value;
						}
					}

					//Step 1
					$db->selectQuery("ms.id, ms.s1_serialnum, IFNULL(ms.s1_quantity,'') AS s1_quantity, IFNULL(mbr.acronym_name_def,'') AS bn_rf , br.brand_name, mo.model_name, ms.s1_accessories ","tbl_mrf_s1 ms
						LEFT JOIN tbl_brands br ON ms.s1_id_brand = br.id
						LEFT JOIN tbl_model mo ON ms.s1_id_model = mo.id
						LEFT JOIN tbl_invnt_condition mbr ON ms.s1_bn_rf = mbr.id
						WHERE ms.is_deleted = 0 AND ms.id_mrf = ".$id_mrf."");
					$res_row_data_s1 = $db->getFields();
					$db->fields = null;

					//History
					$db->selectQuery("*","tbl_mrf_history
						WHERE id_mrf = ".$id_mrf."");
					$res_history= $db->getFields();
					$db->fields = null;

					//Comments
					$db->selectQuery("IFNULL(GROUP_CONCAT(DISTINCT CAST(id_user_from AS CHAR(10)) SEPARATOR ','), 0) AS user_from,
								      IFNULL(SUM( id_user_from != ".$id_user."), 0) AS no_received_message","tbl_mrf_comments
						WHERE id_mrf = ".$id_mrf."");
					$res_comments= $db->getFields();
					$db->fields = null;

					$merge_res['aaData'][0] = array_merge($res_row_data_s4['aaData'][0], 
						array("res_row_data_s1" => $res_row_data_s1['aaData'] ),  
						array("res_row_data_s1_toner" => $tableValues['aaData'] ), 
						array("res_row_data_s2" => $res_row_data_s2['aaData'] ),
						array("is_approver" => $is_approver['aaData']),
						array("user_approved" => $user_approved['aaData']),
						array("res_row_data_s1_toner_sub" => $SubTonertableValues['aaData']),
						array("res_history" => $res_history['aaData'] ),
						array("res_comments" => $res_comments['aaData'] )
					);

					print Utils::jsonEncode($merge_res);

			break;		
		case 'check_stocks_model':
				$db->selectQuery('m.*','tbl_invnt_machines m 
					LEFT JOIN tbl_invnt_status s ON m.id_status = s.id
					WHERE m.is_delete = 0 AND (m.id_status = 0 OR s.status_type = "IN")
					AND m.model = '.$id_model.'');
				$model = $db->getFields();
				if($db->getNumRows() > 0){
					print "true"; 	
				}
				else{
					print Utils::jsonEncode(array("No stocks available."));
				}

			break;
		case 'recall_unit': 
				$status = null;
				$allowed_purpose = array(3,4); // 3 = Demo, 4 = Others
				//Check if demo unit.
				$db->selectQuery('s2_radio_id','tbl_mrf WHERE id = '.$id_mrf.' LIMIT 1');
				$checkRecall = $db->getFields();
				if(in_array($checkRecall['aaData'][0]['s2_radio_id'], $allowed_purpose)){
					//History
					$db->customQuery('INSERT INTO tbl_mrf_history (id_mrf, company_id, remarks, date_created, serial_num)
								 SELECT '.$id_mrf.', mr.id_company, "recall", NOW(), ms.s1_serialnum FROM tbl_mrf mr
								 INNER JOIN tbl_mrf_s1 ms ON mr.id = ms.id_mrf
								 WHERE mr.id = '.$id_mrf.'');
                	$resSched = $db->getFields();
                	if($resSched['aaData'][1] == 'success'){
						$db->updateQuery('tbl_mrf_request_tracker','is_cancel = "no",
									1st_approver = null,
									1st_date	 = null,
									1st_id_status= 1,
									2nd_approver = null,
									2nd_date 	 = null,
									2nd_id_status= 1,
									3rd_approver = null,
									3rd_date 	 = null,
									3rd_id_status= 1,
									4th_approver = null,
									4th_date 	 = null,
									4th_id_status= 1,
									4th_dr_number= null,
									4th_edr_number= null,
									4th_inv_number= null,
									5th_approver = null,
									5th_date 	 = null,
									5th_id_status= 1,
									5th_delivery_date = null,
									5th_received_by = null,
									5th_delivered_by = null,
									flag_completion = "not complete",
									is_cancel = "no"'											
									,'id_mrf = '.$id_mrf.'');
							$db->updateQuery('tbl_mrf','date_requested = "'.$date_entered.'"'											
									,'id = '.$id_mrf.'');
	                	$status =  array(
							'result'   =>  'true',
							 'message' =>  ''
							);
					}			
				}
				else{
					$status =  array(
						'result'   =>  'false',
						 'message' =>  'Can\'t Recall due to this Request Form is not Demo unit.'
						);	
				}
				print Utils::jsonEncode($status);
			break;			
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}

function getRequestStatus($id_mrf,$db){
	$res = null;
	if(!Utils::isEmpty($id_mrf)){
		$db->fields = null;
		$db->selectQuery("ac.email, m.date_requested,
							st1.status_name AS 1st_status,
							st2.status_name AS 2nd_status,
							st3.status_name AS 3rd_status,
							st4.status_name AS 4th_status,
							st5.status_name AS 5th_status","tbl_mrf  m
							LEFT JOIN tbl_accounts ac ON m.id_user_requestor = ac.id
							LEFT JOIN tbl_mrf_request_tracker mt ON mt.id_mrf  = m.id
							LEFT JOIN tbl_mrf_status st1 ON st1.id = mt.1st_id_status
							LEFT JOIN tbl_mrf_status st2 ON st2.id = mt.2nd_id_status
							LEFT JOIN tbl_mrf_status st3 ON st3.id = mt.3rd_id_status
							LEFT JOIN tbl_mrf_status st4 ON st4.id = mt.4th_id_status
							LEFT JOIN tbl_mrf_status st5 ON st5.id = mt.5th_id_status
							WHERE m.id = '".$id_mrf."' LIMIT 1");
		$res = $db->getFields(); 
		// $res = array_filter($res['aaData'][0]);	
		// foreach ($res as $key => $value) {
		// 	$res_filter[][$value] = $value;
		// }

	}
	return $res['aaData'][0];
}

function getApproversEmail($id_branch,$db){
	$res_filter = null;
	if(!Utils::isEmpty($id_branch)){
		$db->fields = null;
		$db->selectQuery("	IFNULL(ac1.email,'') AS 1st_email,
							IFNULL(ac2.email,'') AS 2nd_email,
							IFNULL(ac3.email,'') AS 3rd_email,
							IFNULL(ac4.email,'') AS 4th_email,
							IFNULL(ac4_2.email,'') AS 4th_email_2,
							IFNULL(ac5.email,'') AS 5th_email,
							IFNULL(ac5_2.email,'') AS 5th_email_2","tbl_mrf_branch_approver  m
							LEFT JOIN tbl_accounts ac1 ON m.1st_approver = ac1.id
							LEFT JOIN tbl_accounts ac2 ON m.2nd_approver = ac2.id
							LEFT JOIN tbl_accounts ac3 ON m.3rd_approver = ac3.id
							LEFT JOIN tbl_accounts ac4 ON m.4th_approver = ac4.id
							LEFT JOIN tbl_accounts ac4_2 ON m.4th_approver_2 = ac4_2.id
							LEFT JOIN tbl_accounts ac5 ON m.5th_approver = ac5.id
							LEFT JOIN tbl_accounts ac5_2 ON m.5th_approver_2 = ac5_2.id
							WHERE m.id_branch = '".$id_branch."'");
		$res = $db->getFields(); //Get the info after request created.	
		$res = array_filter($res['aaData'][0]);	
		foreach ($res as $key => $value) {
			$res_filter[][$value] = $value;
		}

	}
	return $res_filter;
}

function checkIsCancel($id_mrf,$db){
	$status = null;
	if(!Utils::isEmpty($id_mrf)){
		$db->selectQuery("COUNT(*) AS already_approver ","tbl_mrf_request_tracker  
			WHERE (1st_id_status > 1 OR 2nd_id_status > 1) AND id_mrf = ".$id_mrf."");
		$res = $db->getFields();

		if($res['aaData'][0]['already_approver'] == 0){ // If 0 means not yet approver.
			$status = array('aaData' => array(
				'result' =>  'true',
				'message' => 'Request has been successfuly cancelled see at Archived.'
			));
		}else{
			$status = array('aaData' => array(
				'result' =>  'false',
				'message' => "Unable to cancel request because already in process."
			));
		}
	}
	return $status;

}

function checkAssignedApprover($id_mrf_branch,$field_name, $id_user_logged,$db){
    if(!Utils::isEmpty($id_mrf_branch)){
		$db->selectQuery("".$field_name." AS approver","tbl_mrf_branch_approver WHERE id_branch =".$id_mrf_branch." LIMIT 0,1");
		$res = $db->getFields();

		if($db->getNumRows() > 0 && $res['aaData'][0]['approver'] == $id_user_logged){
			$res_approver = array('aaData' => array(
				'result' =>  'true',
				'message' => ''
			));
		}else{
			$res_approver = array('aaData' => array(
				'result' =>  'false',
				'message' => "The set-up in approving the request has been changed. </br>Your are not now the approver of this branch. </br>Please try to refresh your browser."
			));
		}
	}
	return $res_approver;
}

function checkRequestTrack($id_mrf,$field_name,$db){ //Add checking if request is cancel or not proceed to current 2nd checking.
	$res_track = array();

    if(!Utils::isEmpty($id_mrf)){
		if($field_name == '2nd_approver_2')
			$fieldname = '2nd_approver';
		else if($field_name == '4th_approver_2')
			$fieldname = '4th_approver';
		else if($field_name == '5th_approver_2')
			$fieldname = '5th_approver';
		else
			$fieldname = $field_name;

			$db->selectQuery("(CASE 
								WHEN is_cancel = 'yes' THEN 'Can’t update the request because it’s already cancelled by Requestor. Please refresh the table.'
								WHEN (1st_id_status = '3' OR 2nd_id_status = '3') THEN 'Can’t update the request because it’s already Disapproved. Please refresh the table.'
								WHEN ('".$fieldname."' = '4th_approver' AND 4th_approver IS NOT NULL) THEN 'Already approved. Please refresh the table.'
								WHEN ('".$fieldname."' = '5th_approver' AND 5th_approver IS NOT NULL) THEN 'Already approved. Please refresh the table.'
								ELSE ''
							END) AS status_message","tbl_mrf_request_tracker WHERE id_mrf = ".$id_mrf."");
			$checkStatus = $db->getFields();

		if(Utils::isEmpty($checkStatus['aaData'][0]['status_message'])){ //Check status if Disapproved or cancelled
			$input = array("1st_approver", "2nd_approver", "3rd_approver", "4th_approver","5th_approver"); //tbl_mrf_request_tracker fields.
			//$fieldname = ($field_name == '2nd_approver_2' ? '2nd_approver': $field_name); //As is the field 2nd_approver if field name is 2nd_approver_2

			$searched_key = array_search($fieldname, $input);

			if($searched_key == 0){ //skip the splicing for key zero only.
			  $res_track = array('aaData' => array(
						'result' =>  'true',
						'message' => ''
					));
			}else{
				//If result is equal to zero means not yet approve prior to the current approve.
				array_splice($input, $searched_key, count($input)); 
				$db->fields = null;
				$db->selectQuery("IFNULL(SUM(".implode("+",$input)."), 0) AS count_approve","tbl_mrf_request_tracker WHERE id_mrf =".$id_mrf." LIMIT 0,1");
				$res = $db->getFields();
				
				if($res['aaData'][0]['count_approve'] > 0 && $db->getNumRows() > 0 ){
					$res_track = array('aaData' => array(
						'result' =>  'true',
						'message' => ''
					));
				}
				else{
					$res_track = array('aaData' => array(
						'result' =>  'false',
						'message' => "Can't update right now due to some approver above not yet approve."
					));
				}
			}
		}
		else{
			$res_track = array('aaData' => array(
				'result' =>  'false',
				'message' => $checkStatus['aaData'][0]['status_message']
			));
		}
		return $res_track;
	}
	else{
		trigger_error("Argument id_mrf undefined.");
	}
}

function generateFormNo($db){
	$db->selectQuery('*','tbl_mrf');
	$res = intval($db->getNumRows()) + 1;

	date_default_timezone_set('Asia/Manila');
	$info = getdate();
	$date = $info['mday'];
	$month = $info['mon'];
	$year = $info['year'];

	return 'FRM'.$year.$date.$month.'-'.$res;
}