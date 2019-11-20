<?php 

class Renew{

private $table = "tbl_renewal_history";
protected $conn = null;

    public function __construct(){

        $db = Database::getInstance();
        if($db == null){
            throw new Exception("Failed to connect database.");
        }
        $this->conn = $db;
    }

    public function add($data){
        $this->conn->insertQuery($this->table,'id_contract, category, valid_from, valid_to, status',
									    '"'.$data['id'].'",
									    "'.$data['category'].'",
                                        "'.$data['valid_from'].'",
                                        "'.$data['valid_to'].'",
                                        "RENEW"');
            $last_id = $this->conn->getLastId();
            $res = $this->conn->getFields();
            $res['last_id'] = $last_id;

            return $res;
    }
    public function emptyFields(){
        $this->conn->fields = null;
    }
    public function updateAttachmentName($id, $name){
        $this->conn->updateQuery($this->table, "attachment='{$name}'", "id={$id}");
        return $this->conn->getFields();
    }
    
}

?>