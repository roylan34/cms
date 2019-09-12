<?php
/**
* 06/28/2017
*
* This file is to update the data of accounts by id.
*
*/ 

require_once '../database.php';
require_once '../utils.php';



if(Utils::getIsset('action')){
	$action     = Utils::getValue('action');
	$id  		= Utils::getValue('idaccount');
	$username  	= Utils::getValue('username');
 	$pass 		= Utils::getValue('pass');
 	$old_pass 	= Utils::getValue('txtOldPassword');
	$fname  	= Utils::getValue('fname'); 
	$mname   	= Utils::getValue('mname');
	$lname   	= Utils::getValue('lname');
	$email   	= Utils::getValue('email');
	$pm_type   	= Utils::getValue('pm_type');
	$app_mif    = Utils::getValue('app_mif');  
	$app_pm     = Utils::getValue('app_pm');  
	$location   = Utils::getValue('location');  
	$app_invt   = Utils::getValue('app_invt');  
	$branch     = Utils::getValue('branch');  
	$branch_pm  = Utils::getValue('branch_pm');  
	$app_mrf    = Utils::getValue('app_mrf');  
	$branch_mrf = Utils::getValue('branch_mrf');  
	$accrole   	= Utils::getValue('accrole');  
	$status   	= Utils::getValue('status');   	
	$acc_type_mrf = Utils::getValue('acc_type_mrf');

	$action_mif   = Utils::getValue('action_mif');  
	$action_pm    = Utils::getValue('action_pm');  
	$action_invnt = Utils::getValue('action_invnt');  
	$action_mrf   = Utils::getValue('action_mrf');    
	 
	$date_created = Utils::getSysDate().' '.Utils::getSysTime();   
	$db = Database::getInstance();

	//For action check_exist validation.
	$newUsername = Utils::getValue('txtUsername');
	$actionVali  = Utils::getValue('actionVali');
	$oldUsername = Utils::getValue('oldUsername');

	switch ($action) {
		case 'add':
			  if(hasLocationAll($location) == false){
				$db->insertQuery('tbl_accounts','username, password, firstname, middlename, lastname, email, location, branches, branch_pm, branches_mrf, accountrole, status, account_type, pm_type, created_at',
									  '"'.$username.'",
									  "'.Utils::encrypt($pass).'",
									  "'.Utils::ucFirstLetter($fname).'",
									  "'.Utils::ucFirstLetter($mname).'",
									  "'.Utils::ucFirstLetter($lname).'",
									  "'.strtolower($email).'",
									  "'.$location.'",
									  "'.$branch.'",
									  "'.$branch_pm.'",
									  "'.$branch_mrf.'",
									  "'.Utils::ucFirstLetter($accrole).'",
									  "'.$status.'",
									  "'.$acc_type_mrf.'",
									  "'.$pm_type.'",
									  "'.$date_created.'"');
                      $last_id = $db->getLastId();
                      //Add to this table to restrict app module can access.
                      $db->insertQuery('tbl_app_module','account_id, app_mif, app_pm, app_inventory, app_mrf',
									  '"'.$last_id.'",
									  "'.$app_mif.'",
									  "'.$app_pm.'",
									  "'.$app_invt.'",
									  "'.$app_mrf.'"');

                       //Add to this table to restrict adding or updating.
                      $db->insertQuery('tbl_app_action','id_account, app_mif, app_pm, app_invnt, app_mrf',
									  '"'.$last_id.'",
									  "'.$action_mif.'",
									  "'.$action_pm.'",
									  "'.$action_invnt.'",
									  "'.$action_mrf.'"');

                      $res = $db->getFields();
				      $res['aaData']['check_location'] = 0;

				  }else{
				  	  $res['aaData']['check_location'] = 1;
				  }
				print Utils::jsonEncode($res);

			break;
		case 'update':
				if(hasLocationAll($location) == false){
					if(Utils::isEmpty($pass)){
						$db->updateQuery('tbl_accounts','username    = "'.$username.'", 
												     firstname   = "'.$fname.'",
												     middlename  = "'.$mname.'",
												     lastname    = "'.$lname.'",
												     email    	 = "'.$email.'",
												     location  	 = "'.$location.'",
												     branches  	 = "'.$branch.'",
												     branch_pm   = "'.$branch_pm.'",
												     pm_type     = "'.$pm_type.'",
												     branches_mrf= "'.$branch_mrf.'",
												     accountrole = "'.$accrole.'",
												     account_type = "'.$acc_type_mrf.'",
												     status  	 = "'.$status.'"'
												     ,'id = "'.$id.'"');
					}else {
							$db->updateQuery('tbl_accounts','username    = "'.$username.'", 
														 password    = "'.Utils::encrypt($pass).'",
													     firstname   = "'.$fname.'",
													     middlename  = "'.$mname.'",
													     lastname    = "'.$lname.'",
													     email    	 = "'.$email.'",
													     location  	 = "'.$location.'",
													     branches  	 = "'.$branch.'",
													     pm_type     = "'.$pm_type.'",
													     branch_pm   = "'.$branch_pm.'",
													     branches_mrf= "'.$branch_mrf.'",
													     accountrole = "'.$accrole.'",
													     account_type = "'.$acc_type_mrf.'",
													     status  	 = "'.$status.'"'
													     ,'id = "'.$id.'"');
					}
                       //Update to this table to restrict app module can access.
                       $db->updateQuery('tbl_app_module','app_mif    	= "'.$app_mif.'", 
													      app_pm        = "'.$app_pm.'",
													      app_inventory = "'.$app_invt.'",
													      app_mrf 		= "'.$app_mrf.'"'
													     ,'account_id = "'.$id.'"');

                       //Update to this table to restrict adding or updating.
                       $db->updateQuery('tbl_app_action','app_mif    = "'.$action_mif.'", 
													      app_pm     = "'.$action_pm.'",
													      app_invnt  = "'.$action_invnt.'",
													      app_mrf    = "'.$action_mrf.'"'
													     ,'id_account = "'.$id.'"');



						$res = $db->getFields();
						$res['aaData']['check_location'] = 0;
				}else{
					    $res['aaData']['check_location'] = 1;
				}

			    print Utils::jsonEncode($res);


			break;
		case 'view-all':
					$db->selectQuery('m.*, br.branch_name','tbl_accounts m LEFT JOIN tbl_branch br ON m.branches = br.id');
					$res = $db->getFields();
					$data = array();
						foreach ($res['aaData'] as $key => $val){
							$data['aaData'][] = array(
													'id' => $val['id'],
													'username' => $val['username'],
													'password' => $val['password'],
													'firstname' => Utils::ucFirstLetter($val['firstname']),
													'middlename' => Utils::ucFirstLetter($val['middlename']),
													'lastname' => Utils::ucFirstLetter($val['lastname']),
													'location' => getListofCompany(strtoupper($val['location']),$db),
													'branch' => strtoupper($val['branch_name']),
													'accountrole' => Utils::ucFirstLetter($val['accountrole']),
													'status' => $val['status'],
													'created_at' => $val['created_at']);
						}
					 print Utils::jsonEncode($data);
			
			break;
		case 'view-id':
				$db->selectQuery('a.*, app.app_mif, app.app_inventory, app.app_mrf, app.app_pm, tac.app_mif AS action_mif, tac.app_invnt AS action_invnt, tac.app_mrf AS action_mrf, tac.app_pm AS action_pm','tbl_accounts a 
									LEFT JOIN tbl_app_module app ON a.id = app.account_id 
									LEFT JOIN tbl_app_action tac ON a.id = tac.id_account
									WHERE a.id = "'.$id.'"');
				$res = $db->getFields();
					foreach ($res['aaData'] as $key => $val) {
						$data['aaData'][] = array(
												'id' => $val['id'],
												'username' => $val['username'],
												'firstname' => Utils::ucFirstLetter($val['firstname']),
												'middlename' => Utils::ucFirstLetter($val['middlename']),
												'lastname' => Utils::ucFirstLetter($val['lastname']),
												'email' => $val['email'],
												'accountrole' => Utils::ucFirstLetter($val['accountrole']),
												'pm_type' => $val['pm_type'],
												'app_mif' => $val['app_mif'],
												'app_pm' => $val['app_pm'],
												'app_inventory' => $val['app_inventory'],
												'app_mrf' => $val['app_mrf'],
												'action_mif' => $val['action_mif'],
												'action_pm' => $val['action_pm'],
												'action_invnt' => $val['action_invnt'],
												'action_mrf' => $val['action_mrf'],
												'location' => strtoupper($val['location']),
												'branch' => $val['branches'],
												'branch_pm' => $val['branch_pm'],
												'branch_mrf' => $val['branches_mrf'],
												'status' => $val['status'],
												'account_type' => $val['account_type'],
												'created_at' => $val['created_at']);
					}

			 print Utils::jsonEncode($data);
			
			break;		
		case 'check_exist':
				$db->selectQuery('username','tbl_accounts WHERE username = "'.$newUsername.'"');
				$res = $db->getFields();
				if($db->getNumRows()){
					if($action == 'add') {// if action is add display the error message. Else echo true.
						echo json_encode("<strong>Username is already exist.<strong>");
					}else{
						if(strtolower($oldUsername) == strtolower($newUsername)){
							echo "true";
						}else{
							echo json_encode("<strong>Username is already exist.<strong>");
						}
					}
				}
				else {
					echo "true"; // true = not exist.
				}

			break;

		case 'change_password':
			//Update user password.
			if(!Utils::isEmpty($pass)){
				$db->updateQuery('tbl_accounts','password    = "'.Utils::encrypt($pass).'"'
							     	,'id = "'.$id.'"');
				$res = array('status' => 'success');
			}
			else{
				$res = array('status' => 'failed');
			}

			 print Utils::jsonEncode($res);

			break;
		case 'check_password':
				$db->selectQuery('COUNT(*) AS check_password','tbl_accounts WHERE id = "'.$id.'" AND password ="'.Utils::encrypt($old_pass).'"');
				$res = $db->getFields();
				if($res['aaData'][0]['check_password'] > 0){
					echo "true";
				}
				else {
					echo json_encode('<strong>Incorrect password.<strong>');
				}

			break;
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}


 function getListofCompany($strBranches,$db){
	if(is_string($strBranches) && !empty($strBranches)){
		$list = '';
		$db->fields = null;
		$db->selectQuery('branch_name','tbl_location WHERE id IN ('.$strBranches.')');
		$res = $db->getFields();
		$countbranch = count($res['aaData']);
		for ($i=0; $i < $countbranch ; $i++) { 
			$list .= $res['aaData'][$i]['branch_name'].'<br>';
		}
		return $list;
	}
	return '';
}

function hasLocationAll($location){ //Check if location has ALL.
	$loc_exp = explode(',',$location);
	$count_loc = count($loc_exp);

	if($count_loc > 1 ){
	 if(!in_array(1,$loc_exp)){
	       return false; //Add/Update
	  }
	  else{
	     return true; //Cant Add/Update
	  }
	}
	else{
	  return false; //Add/Update
	}
}


