<?php
/**
* 03/15/2017
*
* This file is contains reports table of SAP Stocks.
*
*/ 

require_once '../../database.php';
require_once '../../utils.php';

$search ="";
$db = Database::getInstance();

	$db->selectQuery("  (SELECT COUNT(*) FROM tbl_invnt_machines WHERE id_brand = im.id_brand) AS total_brand,
						b.brand_name,
						m.model_name,
						CONCAT_WS(' ',cat.cat_name,t.type_name) AS cat_type,
						SUM( im.id_condition = 1 ) AS qty ","tbl_invnt_machines_auto_import im
						LEFT JOIN tbl_model m ON im.model = m.id
						LEFT JOIN tbl_category cat ON m.id_category = cat.id
						LEFT JOIN tbl_type t ON m.id_type = t.id
						LEFT JOIN tbl_brands b ON im.id_brand = b.id
						GROUP BY im.model
						ORDER BY total_brand DESC");
	$row = $db->getFields(); //Get all rows
	$data = array();

	   if($db->getNumRows() > 0){
			foreach ($row['aaData'] as $key => $value) {
				$data[$value['brand_name']][$value['cat_type']][$key]['model_name'] = $value['model_name'];
				$data[$value['brand_name']][$value['cat_type']][$key]['qty'] = $value['qty'];
			}
	   }
	// print_r($data); 
	print Utils::jsonEncode($data); 