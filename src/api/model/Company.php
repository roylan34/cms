<?php

class Company{


    private $table = "tbl_company_auto_import";
    protected $conn = null;


    public function __construct(){
        $db_mif = array('db_name'=> 'dbmif', 'db_host' => 'localhost', 'db_user'=> 'root', 'db_pass'=> 'delsan@1991');
        $db = Database::getInstance($db_mif);
        if($db == null){
            throw new Exception("Failed to connect database.");
        }
        $this->conn = $db;
    }

    public function getListCompany($comp_name){
        $search = (!empty($comp_name) ? "WHERE company_name LIKE '%{$comp_name}%'" : "");
        $this->conn->selectQuery('id, company_name', $this->table." $search LIMIT 10");
        return $this->conn->getFields();
    }
    public function getCompanyNameById($id){
        $search = (!empty($id) ? "WHERE id={$id}" : "");
        $this->conn->selectQuery('id, company_name', $this->table." $search LIMIT 1");
        return $this->conn->getFields();
    }
}

?>