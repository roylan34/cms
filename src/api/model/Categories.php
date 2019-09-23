<?php

require 'core/database.php';

class Categories{


    private $table = "tbl_categories";
    protected $conn = null;


    function __construct(){
        $db = Database::getInstance();
        if($db == null){
            throw new Exception("Failed to connect database.");
        }
        $this->conn = $db;
    }

    function getAllCategories(){

        $this->conn->selectQuery('id, cat_name', $this->table);
        return $this->conn->getFields();
    }
}

?>