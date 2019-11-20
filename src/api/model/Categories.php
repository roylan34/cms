<?php

class Categories{


    private $table = "tbl_categories";
    protected $conn = null;


    public function __construct(){
        $db = Database::getInstance();
        if($db == null){
            throw new Exception("Failed to connect database.");
        }
        $this->conn = $db;
    }

    public function getListCategories(){

        $this->conn->selectQuery('id, cat_name', $this->table);
        return $this->conn->getFields();
    }
}

?>