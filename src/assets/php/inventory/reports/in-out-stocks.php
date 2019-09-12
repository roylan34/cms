<?php
/**
* 03/15/2017
*
* This file is contains reports table of IN/OUT Stocks.
*
*/ 

require_once '../../database.php';
require_once '../../utils.php';

$search ="";
$db = Database::getInstance();
if(Utils::getIsset('id_branch') && Utils::getValue('id_branch')) { $search ="AND m.id_branch = ".$db->escapeString(Utils::getValue('id_branch'))."";   }

	$db->selectQuery("  x.brand_name, x.model, x.total_in, x.total_out ,(x.total_in + x.total_out) AS over_all_total, x.total_brand",
					   "(SELECT 					   		
					   		(SELECT COUNT(*) FROM tbl_invnt_machines WHERE id_brand = m.id_brand) AS total_brand,
							b.brand_name, 
							mo.model_name AS model, 
							SUM(s.status_type = 'IN' OR m.id_status = 0) AS total_in,
							SUM(s.status_type = 'OUT' AND m.id_status != '') AS total_out
							 FROM tbl_invnt_machines m
							LEFT JOIN tbl_brands b ON m.id_brand = b.id
							LEFT JOIN tbl_model mo ON m.model = mo.id
							LEFT JOIN tbl_invnt_status s ON m.id_status = s.id
							WHERE m.is_delete = 0
							  ".$search."
							GROUP BY m.model
						) X ORDER BY X.model");
	$row = $db->getFields(); //Get all rows
	$data = array();

	   if($db->getNumRows() > 0){
			foreach ($row['aaData'] as $key => $value) {
				$data[$value['brand_name']][$key]['model_name'] = $value['model'];
				$data[$value['brand_name']][$key]['total_in'] = $value['total_in'];
				$data[$value['brand_name']][$key]['total_out'] = $value['total_out'];
				$data[$value['brand_name']][$key]['over_all_total'] = $value['over_all_total'];

			}
	   }
	
		//print_r($data); 
	print Utils::jsonEncode($data); 
	