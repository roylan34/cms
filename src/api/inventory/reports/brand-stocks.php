<?php
/**
* 03/15/2017
*
* This file is contains reports table of Brand Stocks.
*
*/ 

require_once '../../database.php';
require_once '../../utils.php';

$search ="";
$db = Database::getInstance();
if(Utils::getIsset('id_branch') && Utils::getValue('id_branch')) { $search ="AND im.id_branch = '".$db->escapeString(Utils::getValue('id_branch'))."'";   }

	$db->selectQuery("  (SELECT COUNT(*) FROM tbl_invnt_machines WHERE id_brand = im.id_brand) AS total_brand,
						b.brand_name,
						m.model_name,
						CONCAT_WS(' ',cat.cat_name,t.type_name) AS cat_type,
						SUM( im.id_condition = 1 ) AS bn,
						SUM( im.id_condition = 2 ) AS rf","tbl_invnt_machines im
						LEFT JOIN tbl_model m ON im.model = m.id
						LEFT JOIN tbl_category cat ON m.id_category = cat.id
						LEFT JOIN tbl_type t ON m.id_type = t.id
						LEFT JOIN tbl_brands b ON im.id_brand = b.id
						LEFT JOIN tbl_invnt_status s ON im.id_status = s.id
						WHERE im.is_delete = 0 AND (im.id_status = 0 OR s.status_type = 'IN')
						".$search."
						GROUP BY im.model
						ORDER BY m.model_name");
	$row = $db->getFields(); //Get all rows
	$data = array();

	// $res = array_filter($row['aaData'], function($num){
	//        return (!empty($num['model_name']));
	// }); //Remove empty Model's

	   if($db->getNumRows() > 0){
			foreach ($row['aaData'] as $key => $value) {
				$data[$value['brand_name']][$value['cat_type']][$key]['model_name'] = $value['model_name'];
				$data[$value['brand_name']][$value['cat_type']][$key]['bn'] = $value['bn'];
				$data[$value['brand_name']][$value['cat_type']][$key]['rf'] = $value['rf'];
			}
	   }
	print Utils::jsonEncode($data); 