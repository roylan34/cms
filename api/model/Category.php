<?php

class Category{


    private $table = "tbl_categories";
    protected $conn = null;


    public function __construct(){
        $db = Database::getInstance();
        if($db == null){
            throw new Exception("Failed to connect database.");
        }
        $this->conn = $db;
    }
        
    public function getCategories(){

        $this->conn->selectQuery('id, cat_name, status', $this->table);
        return $this->conn->getFields();
    }

    public function getCategoryById($id){
        $this->conn->selectQuery("id, cat_name, status","{$this->table} WHERE id={$id} LIMIT 1");
        $res = $this->conn->getFields();
        return $res;   
    }

    public function add($data){
        $this->conn->insertQuery($this->table,'cat_name, status',
                            '"'.$data['category'].'",
                            "'.$data['status'].'"');
        $res = $this->conn->getFields();
        return $res;
    }

    public function update($data){
        if($data['id']){
                $this->conn->updateQuery($this->table, 
                                        "cat_name='".$data['category']."',
                                        status='".$data['status']."'"
                                        , "id='".$data['id']."'");

            return $this->conn->getFields();
        }
    }
    
    public function getActiveCategories(){
        $this->conn->selectQuery('id, cat_name', "{$this->table} WHERE status='ACTIVE'");
        return $this->conn->getFields();
    }
}

?>