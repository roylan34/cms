<?php


class Accounts{

private $table = 'tbl_accounts';
protected $conn = null;


    public function __construct(){

        $db = Database::getInstance();
        if($db==null){
            throw new Exception("Failed to connect database.");
        }
        $this->conn = $db;
    }

    public function getAccounts(){
        $this->conn->selectQuery("id, username, CONCAT(firstname,' ', lastname) AS fullname, status, user_role, email, created_at","{$this->table}");
        $res = $this->conn->getFields();
        return $res;   
    }

    public function getAccountById($id){
        $this->conn->selectQuery("id, username, firstname, lastname, status, user_role, email","{$this->table}");
        $res = $this->conn->getFields();
        return $res;   
    }

    public function add($data){
        $this->conn->insertQuery($this->table,'username, password, firstname, lastname, status, user_role, email',
                            '"'.$data['username'].'",
                            "'.$data['password'].'",
                            "'.$data['firstname'].'",
                            "'.$data['lastname'].'",
                            "'.$data['status'].'",
                            "'.$data['role'].'",
                            "'.$data['email'].'"');
        $res = $this->conn->getFields();
        return $res;
    }
    
}
?>