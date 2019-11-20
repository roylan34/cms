<?php



class ContractLogs{

private $table = "tbl_contract_logs";
protected $conn = null;

    public function __construct(){

        $db = Database::getInstance();
        if($db == null){
            throw new Exception("Failed to connect database.");
        }
        $this->conn = $db;
    }

    public function add($action, $id, $cat){
        $this->conn->insertQuery($this->table,'id_contract, category, action',
                                ''.$id.',
                                '.$cat.',
                                "'.$action.'"');
        $res = $this->conn->getFields();
        return $res;
    }
}
?>