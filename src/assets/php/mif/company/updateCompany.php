<?php
/**
* 01/25/2017
*
* This file is to update the data of company by id.
*
*/ 

require_once '../../core.php';
require_once '../../database.php';
require_once '../../utils.php';
require_once 'misc.php';


if(Utils::getIsset('comp_id')){
$id             = Utils::getValue('comp_id');
$company        = Utils::getValue('comp_name');
$category       = Utils::getValue('category'); 
$address        = Utils::getValue('address'); 
$branch         = Utils::getValue('branches');
$location       = Utils::getValue('main_location');
$contactno      = Utils::getValue('contact_no'); 
$id_accmngr     = Utils::getValue('id_client_mngr');
// $id_oldaccmngr  = Utils::getValue('oldaccmngr');
$id_oldbranch   = Utils::getValue('old_id_branch');
$status         = Utils::getValue('status');
$user_id        = Utils::getValue('user_id');
$last_visit     = Utils::getValue('date_last_visit');
$client_service = Utils::getValue('client_service');
$sap_code       = Utils::getValue('sap_code');
$delsan_comp    = Utils::getValue('delsan_comp');
$lat            = Utils::getValue('lat');
$lng            = Utils::getValue('lng');


	//Company branch Adding/Deleting
	$selected_branch_exp = (!empty($branch) ? explode(',',$branch) : array());
	$old_branch_exp = (!empty($id_oldbranch) ? explode(',',$id_oldbranch) : array());

	$conn = Database::getInstance();
	$conn->selectQuery('set_default','tbl_machine_status WHERE set_default = 1 LIMIT 0,1');//Get the default status, to set the default status_machine in tblmif.
	$res_status = $conn->getFields();

	if($status == 0){//If status 0 = Blocked, all machine belong to that idcompany will update status_machine from the default status fetched.
		$conn->updateQuery('tblmif','status_machine = "'.$res_status['aaData'][0]['set_default'].'"','company_id = "'.$id.'"');
	}
	// else{// 1 = Active, empty the status. //Commented 06/06/2017
	// 	$conn->updateQuery('tblmif','status_machine = null','company_id = "'.$id.'"');
	//}

  $conn->fields = null;//Clear
    //Update table tbl_company
	$conn->updateQuery('tbl_company','company_name    = "'.Utils::uppercase($company).'",
									     client_category = "'.Utils::uppercase($category).'",
									     address 		     = "'.Utils::uppercase($address).'",
									     contact_no		   = "'.$contactno.'",
									     id_client_mngr  = "'.(int)$id_accmngr.'",
									     status  		     = "'.(int)$status.'",
                       main_location   = "'.(int)$location.'",
                       date_last_visit = "'.$last_visit.'",
                       sap_code        = "'.$sap_code.'",
                       delsan_company  = "'.$delsan_comp.'",
                       latitude        = "'.$lat.'",
                       longitude       = "'.$lng.'"'
									     ,'id = "'.$id.'"');

  companyLogs($id,$user_id,'UPDATE',$conn);//Logs Insert action.

//Adding/Deleting Company branch
if(count($selected_branch_exp) >= count($old_branch_exp)){
    foreach($selected_branch_exp AS $key => $val_selected){
        if(!in_array($val_selected, $old_branch_exp)){
        	$conn->insertQuery('tbl_company_branches','id_company, id_branches',
									  '"'.(int)$id.'",
									  "'.(int)$val_selected.'"');
       }
    }
}else{
      foreach($selected_branch_exp AS $key => $val_selected){
        if(!in_array($val_selected, $old_branch_exp)){
         	$conn->insertQuery('tbl_company_branches','id_company, id_branches',
						  '"'.(int)$id.'",
						  "'.(int)$val_selected.'"');
       }
    }
}

if(count($old_branch_exp) <= count($selected_branch_exp)){
    foreach($old_branch_exp AS $key => $val_selected){
        if(!in_array($val_selected, $selected_branch_exp)){
        	$conn->deleteQuery('tbl_company_branches','id_company ="'.$id.'" AND id_branches ='.(int)$val_selected.'');//Delete the record 
            // echo "Delete:" . $val_selected;
       }
    }
}else{
    foreach($old_branch_exp AS $key => $val_selected){
        if(!in_array($val_selected, $selected_branch_exp)){
        	$conn->deleteQuery('tbl_company_branches','id_company ="'.$id.'" AND id_branches ='.(int)$val_selected.'');//Delete the record 
             //echo "Delete:" . $val_selected;
       }
    }
}


//Add/Update tbl_client_accounts
// if(Utils::isEmpty($id_oldaccmngr) || $id_oldaccmngr == 0){
// 	$companies = getCompany($id_accmngr, $conn);

//    if(count($companies) == 0){
//    	   if(!empty($id_accmngr)){
// 	   		$conn->updateQuery('tbl_client_accounts','company= "'.$id.'"','id = "'.$id_accmngr.'"');
// 	   	}
//    }else{
//    		if(!in_array($id, $companies)){
//    	   		array_push($companies, $id);
//    	   		$conn->updateQuery('tbl_client_accounts','company= "'.(count($companies) > 0 ? implode(',',$companies): '' ).'"','id = "'.$id_accmngr.'"');
//    	   }else{
//    	   		$companies = array_filter($companies, function($e) use ($id){
// 	   			return ($e != $id); // use old_client_id to update
// 	   		});
// 	   		$conn->updateQuery('tbl_client_accounts','company= "'.(count($companies) > 0 ? implode(',',$companies): '' ).'"','id = "'.$id_oldaccmngr.'"');
//    	   }
//    }
// }
// else{
//    if(!Utils::isEmpty($id_accmngr)){
//    		$companies = getCompany($id_accmngr, $conn);
//    		$rm_companies = getCompany($id_oldaccmngr, $conn);

//    	   if(!in_array($id, $companies)){
//    	   		array_push($companies, $id); //Update
//    	   		$conn->updateQuery('tbl_client_accounts','company= "'.(count($companies) > 0 ? implode(',',$companies): '' ).'"','id = "'.$id_accmngr.'"');

//    	   		$rm_companies = array_filter($rm_companies, function($e) use ($id){
// 	   			return ($e != $id); // use old_client_id to update
// 	   		});
// 	   		$conn->updateQuery('tbl_client_accounts','company= "'.(count($rm_companies) > 0 ? implode(',',$rm_companies): '' ).'"','id = "'.$id_oldaccmngr.'"');
//    	   }
//    }else{
//    			$rm_companies = getCompany($id_oldaccmngr, $conn); //Uncomment 09/29/2017
// 	   		$companies = array_filter($rm_companies, function($e) use ($id){
// 	   			return ($e != $id); // use old_client_id to update
// 	   		});
//         $conn->fields = null;
// 	   		$conn->updateQuery('tbl_client_accounts','company= "'.(count($companies) > 0 ? implode(',',$companies): '' ).'"','id = "'.$id_oldaccmngr.'"');
//    }
// }

	print Utils::jsonEncode($conn->getFields());
}

