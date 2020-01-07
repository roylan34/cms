<?php

class Notification{


private $table = "tbl_contracts";
protected $conn = null;

    public function __construct(){

        $db = Database::getInstance();
        if($db == null){
            throw new Exception("Failed to connect database.");
        }
        $this->conn = $db;
    }

    public function getContractExpiring(){
        $expiring = array();
        $this->conn->selectQuery('comp.company_name, acc.email, cat.cat_name, cnt.valid_to, valid_from, cnt.user_id, DATEDIFF(cnt.valid_to, CURDATE()) AS for_expire',"{$this->table} cnt
                                LEFT JOIN tbl_accounts acc ON cnt.user_id = acc.id
                                LEFT JOIN tbl_categories cat ON cnt.category = cat.id
                                LEFT JOIN dbmif.tbl_company_auto_import comp ON cnt.sap_company_id = comp.id
                                WHERE DATEDIFF(cnt.valid_to, CURDATE()) = cnt.days_to_reminds AND cnt.status IN ('INITIAL','RENEW')");

        $res = $this->conn->getFields();
        if($this->conn->getNumRows() > 0){
            foreach ($res['aaData'] as $key => $value) {
                $expiring[$value['user_id']]['details'][$key]['comp']= $value['company_name'];
                $expiring[$value['user_id']]['details'][$key]['cat_name']= $value['cat_name'];
                $expiring[$value['user_id']]['details'][$key]['expiration']= $value['valid_to'];
                $expiring[$value['user_id']]['email'][][$value['email']] = $value['email'];
            }
        }
        return $expiring;
    }
}





?>