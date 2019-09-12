<?php

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Manila');

if (PHP_SAPI == 'cli')
	die('This should only be run from a Web Browser');

require_once '../database.php';
require_once '../utils.php';
/** Include PHPExcel */
require_once '../phpexcel/PHPExcel-1.8/Classes/PHPExcel.php';

$search ="";
$db = Database::getInstance();
 if(Utils::getValue('form_no'))		{ $search = $db->escapeString(Utils::getValue('form_no')); }

//Create a stored procedure.
$db->customQuery('DROP PROCEDURE IF EXISTS getMachineDelivered');
$db->customQuery('CREATE PROCEDURE getMachineDelivered() 
BEGIN 
DECLARE VALUE TEXT;
DECLARE occurance INT DEFAULT 0;
DECLARE i INT DEFAULT 0;
DECLARE id INT DEFAULT 0;
DECLARE company VARCHAR(200);
DECLARE splitted_value VARCHAR(50);
DECLARE brand VARCHAR(25);
DECLARE model VARCHAR(25);
DECLARE delivery_date DATE;
DECLARE done INT DEFAULT 0; DECLARE cur1 CURSOR FOR SELECT com.company_name, s1.s1_serialnum, br.brand_name, mo.model_name, mt.5th_delivery_date FROM tbl_mrf_s1 s1
 LEFT JOIN tbl_mrf m ON s1.id_mrf = m.id
 LEFT JOIN tbl_company com ON m.id_company = com.id
 LEFT JOIN tbl_model mo ON s1.s1_id_model = mo.id
 LEFT JOIN tbl_brands br ON mo.id_brand = br.id
 LEFT JOIN tbl_mrf_request_tracker mt ON m.id = mt.id_mrf
 WHERE (mt.flag_completion ="complete" AND mt.1st_id_status = 2 AND mt.2nd_id_status = 2) AND 
 (s1.id_mrf IN ('.$search.') ); 
 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
 DROP TEMPORARY TABLE IF EXISTS table2;
CREATE TEMPORARY TABLE table2(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`company` VARCHAR(255) NOT NULL,
`serialnum` VARCHAR(50) NOT NULL,
`brand` VARCHAR(50) NOT NULL,
`model` VARCHAR(50) NOT NULL,
`delivery_date` DATE DEFAULT NULL
) ENGINE=MYISAM COLLATE=latin1_general_ci;
OPEN cur1;
  read_loop: LOOP
    FETCH cur1 INTO company, VALUE, brand, model, delivery_date;
    IF done THEN
      LEAVE read_loop;
    END IF;
    SET occurance = (SELECT LENGTH(VALUE)
                             - LENGTH(REPLACE(VALUE, ",", ""))
                             +1);
    SET i=1;
    WHILE i <= occurance DO
      SET splitted_value =
      (SELECT REPLACE(SUBSTRING(SUBSTRING_INDEX(VALUE, ",", i),
      LENGTH(SUBSTRING_INDEX(VALUE, ",", i - 1)) + 1), ",", ""));
      INSERT INTO table2 (company, serialnum, brand, model, delivery_date) VALUES (company, splitted_value, brand, model, delivery_date);
      SET i = i + 1;
    END WHILE;
  END LOOP;
  SELECT * FROM table2;
 CLOSE cur1;
 END;');

$resCreate = $db->getFields();
$db->fields = null;

//Data fetched from mysqli.
$db->storProc('getMachineDelivered()');
$data = $db->getFields();

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();


// Add Headers
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Company name')
            ->setCellValue('B1', 'Serial Number')
            ->setCellValue('C1', 'Brand')
            ->setCellValue('D1', 'Model')
            ->setCellValue('E1', 'Date Delivered');
//Bold Column Headers
$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

// Miscellaneous glyphs, UTF-8
for ($i=0; $i < count($data['aaData']) ; $i++) { 
	$ii = $i+2;
	$objPHPExcel->setActiveSheetIndex(0)
	            ->setCellValue('A'.$ii, $data['aaData'][$i]['company'])
	            ->setCellValue('B'.$ii, $data['aaData'][$i]['serialnum'])
	            ->setCellValue('C'.$ii, $data['aaData'][$i]['brand'])
	            ->setCellValue('D'.$ii, $data['aaData'][$i]['model'])
	            ->setCellValue('E'.$ii, $data['aaData'][$i]['delivery_date']);
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Machine Delivered');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$dateNow = Utils::getSysDate();
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename= Machine Delivered {$dateNow}.xlsx");
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
