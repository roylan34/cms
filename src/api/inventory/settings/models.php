<?php
/**
* 08/25/2017
*
* This file is contains Add/Update/View/Validate of Models table.
*
*/ 

require_once '../../database.php';
require_once '../../utils.php';



if(Utils::getIsset('action')){
	$action     = Utils::getValue('action');
	$id         = Utils::getValue('id_model');
	$model_name = Utils::getValue('model_name');
	$brand 		= Utils::getValue('id_brand');
	$category   = Utils::getValue('id_category');
	$type    	= Utils::getValue('id_type');
	$db  = Database::getInstance();

	$search = "";
	//For action check_exist validation.
	$check_model 	 = Utils::getValue('txtSettingInvntModel');
	$old_model 	 	 = Utils::getValue('old_model');
	$action_validate = Utils::getValue('action_validate');

	switch ($action) {
		case 'add':
				$db->insertQuery('tbl_model','id_brand,model_name,id_category,id_type',
												'"'.$brand.'",
												"'.Utils::uppercase($model_name).'",
												"'.$category.'",
												"'.$type.'"');
				print Utils::jsonEncode($db->getFields());

			break;
		case 'update':
				$db->updateQuery('tbl_model','id_brand    = "'.$brand.'",
											  model_name  = "'.Utils::uppercase($model_name).'",
											  id_category = "'.$category.'",
											  id_type 	  = "'.$type.'"',
												'id = "'.$id.'"');
			 	print Utils::jsonEncode($db->getFields());
			
			break;
		case 'view-id':
				$db->selectQuery('id,id_brand,model_name,id_category,id_type','tbl_model WHERE id="'.$id.'" LIMIT 0,1');
			 	print Utils::jsonEncode($db->getFields());

			break;
		case 'view-all':
				$requestData = $_REQUEST;
				$db->selectQuery('*','tbl_model');
				$totalData = $db->getNumRows(); //getting total number records without any search.
				$db->row_count = 0;
				$db->fields = null;

				if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
					$search.=" WHERE m.model_name LIKE '%".$db->escapeString($requestData['search']['value'])."%' ";    
					$search.=" OR br.brand_name LIKE '%".$db->escapeString($requestData['search']['value'])."%' ";
					$search.=" OR cat.cat_name LIKE '%".$db->escapeString($requestData['search']['value'])."%' ";
					$search.=" OR t.type_name  LIKE '%".$db->escapeString($requestData['search']['value'])."%'";

					$db->selectQuery("m.id, br.brand_name, m.model_name, cat.cat_name, t.type_name","tbl_model m 
									LEFT JOIN tbl_brands br ON m.id_brand = br.id
									LEFT JOIN tbl_category cat ON m.id_category = cat.id
									LEFT JOIN tbl_type t ON m.id_type = t.id ".$search."");
					$db->fields = null;
					$totalFiltered  = $db->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
				}
				else{
					$totalFiltered = $totalData;
				}
				$db->selectQuery("m.id, br.brand_name, m.model_name, cat.cat_name, t.type_name","tbl_model m 
									LEFT JOIN tbl_brands br ON m.id_brand = br.id
									LEFT JOIN tbl_category cat ON m.id_category = cat.id
									LEFT JOIN tbl_type t ON m.id_type = t.id ".$search." ORDER BY m.id DESC LIMIT ".$requestData['start']." ,".$requestData['length']." ");
				$row = $db->getFields(); //Get all rows


				$data = array();
				$nestedData=array(); 
					foreach($row['aaData'] as $index=>$value) { // preparing an array
						$nestedData[$index] = $value;
					}
					$data = $nestedData; 
					
				$json_data = array(
							"draw"            => intval( $requestData['draw'] ), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
							"recordsTotal"    => intval( $totalData ),  // total number of records
							"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
							"records"         => $data   // data array,
							);

				print Utils::jsonEncode($json_data);  // send data as json format
			
			break;	
		case 'check_exist':
				$db->selectQuery('*','tbl_model WHERE model_name ="'.$check_model.'" LIMIT 0,1');
				if($db->getNumRows() > 0){
					if($action_validate == "add"){
						echo json_encode(array("<strong>Model name is already exist.</strong>"));
					}
					else{
						if(strtolower($old_model) == strtolower($check_model)){
							echo "true";
						}else{
							echo json_encode(array("<strong>Model name is already exist.</strong>"));
						}
					}
				}
				else{
					echo "true";
				}

			break;
		default:
			 throw new Exception($action." action doesn't exist.");
			break;
	}


}

