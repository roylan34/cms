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

    public function add($action, $id, ?int $cat = null){
        $this->conn->insertQuery($this->table,'id_contract, category, action',
                                ''.$id.',
                                '.$cat.',
                                "'.$action.'"');
        $res = $this->conn->getFields();
        return $res;
    }
    public function getLogs($id){
        $this->conn->selectQuery("logs.action, logs.created_at, cat.cat_name","{$this->table} logs 
                                LEFT JOIN tbl_categories cat ON logs.category = cat.id
                                WHERE logs.id_contract={$id}");
        $res = $this->conn->getFields();
        return $res;
    }
}
?>