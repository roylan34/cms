<?php 


class Contract{

private $table = "tbl_contracts";
protected $conn = null;

    public function __construct(){

        $db = Database::getInstance();
        if($db == null){
            throw new Exception("Failed to connect database.");
        }
        $this->conn = $db;
    }

    public function add($data){
        $this->conn->insertQuery($this->table,'sap_company_id, category, valid_from, valid_to, days_to_reminds, notes, created_at',
									    '"'.$data['comp'].'",
									    "'.$data['category'].'",
									    "'.$data['valid_from'].'",
                                        "'.$data['valid_to'].'",
                                        "'.$data['days_to_reminds'].'",
                                        "'.$data['notes'].'",
                                        NOW()');
            $last_id = $this->conn->getLastId();
            $res = $this->conn->getFields();
            $res['last_id'] = $last_id;

            return $res;
    }
    public function update($data){
        if($data['id']){
            $this->conn->updateQuery($this->table, 
                                    "sap_company_id='".$data['comp']."',
                                    category   ='".$data['category']."',
                                    valid_from='".$data['valid_from']."',
                                    valid_to   ='".$data['valid_to']."',
                                    days_to_reminds ='".$data['days_to_reminds']."',
                                    notes ='".$data['notes']."'"
                                    , "id='".$data['id']."'");
            return $this->conn->getFields();
        }
    }
    public function updateRenew($data){
        if($data['id']){
            $this->conn->updateQuery($this->table, 
                                    "category   ='".$data['category']."',
                                    valid_from='".$data['valid_from']."',
                                    valid_to   ='".$data['valid_to']."',
                                    days_to_reminds ='".$data['days_to_reminds']."',
                                    notes ='".$data['notes']."',
                                    status ='RENEW'"
                                    , "id='".$data['id']."'");
            return $this->conn->getFields();
        }
    }
    public function updateStatus($id, $status){
        if($id){
            $this->conn->updateQuery($this->table, "status ='".$status."' ", "id='".$id."'");
            return $this->conn->getFields();
        }
    }
    public function getStatus($id){
        $this->conn->selectQuery("status","{$this->table} WHERE id={$id} LIMIT 1");
        $res = $this->conn->getFields();
        return $res['aaData'][0]['status'];
    }
    public function getCurrent($data){
        $search = "";
        $limit  = "";

            if($data['comp'])       { $search .= "AND comp.company_name LIKE '%".$data['comp']."%'"; }
            if($data['category'])   { $search .= "AND cnt.category LIKE '%".$data['category']."%'"; }
            if($data['valid_from']) { $search .= "AND cnt.valid_from LIKE '%".$data['valid_from']."%'"; }
            if($data['valid_to'])   { $search .= "AND cnt.valid_to LIKE '%".$data['valid_to']."%'"; }
            if($data['status'])     { 
                switch ($data['status']) {
                    case 'active':
                        $search .= "AND DATEDIFF(cnt.valid_to, CURDATE()) > cnt.days_to_reminds";
                        break;
                    case 'notify':
                        $search .= "AND DATEDIFF(cnt.valid_to, CURDATE()) BETWEEN 1 AND cnt.days_to_reminds";
                        break;
                    case 'expired':
                        $search .= "AND DATEDIFF(cnt.valid_to, CURDATE()) <= 0";
                        break;
                    
                    default:
                       throw new Exception("Action type not found.");
                        break;
                }
            }

            $requestData= $_REQUEST;
			// storing  request (ie, get/post) global array to a variable  
			 $this->conn->selectQuery("*","{$this->table} WHERE status IN ('INITIAL','RENEW') ");
			$totalData =  $this->conn->getNumRows(); //getting total number records without any search.
			 $this->conn->row_count = 0;
			 $this->conn->fields = null;

			if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

             $this->conn->selectQuery('*, comp.company_name',"{$this->table} cnt 
                                        LEFT JOIN dbmif.tbl_company_auto_import comp ON cnt.sap_company_id = comp.id
                                        WHERE cnt.status IN ('INITIAL','RENEW') {$search}");

				 $this->conn->fields = null;
				$totalFiltered  =  $this->conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			}
			else{
				$totalFiltered = $totalData;
			}
			
			if(intval($requestData['length']) >= 1 ) { $limit = 'LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }
            
            $this->conn->selectQuery('cnt.id, comp.company_name, cat.cat_name, cnt.valid_from, cnt.valid_to, cnt.status, cnt.updated_at,
                                        (
                                            CASE
                                                WHEN DATEDIFF(cnt.valid_to, CURDATE()) > cnt.days_to_reminds THEN "ACTIVE"
                                                WHEN DATEDIFF(cnt.valid_to, CURDATE()) BETWEEN 1 AND cnt.days_to_reminds THEN "NOTIFYING"
                                                WHEN DATEDIFF(cnt.valid_to, CURDATE()) <= 0 THEN "EXPIRED"
                                                ELSE ""
                                            END
                                        ) AS notify_status
                                        ',"{$this->table} cnt 
                                        LEFT JOIN dbmif.tbl_company_auto_import comp ON cnt.sap_company_id = comp.id
                                        LEFT JOIN tbl_categories cat ON cnt.category = cat.id
                                        WHERE cnt.status IN ('INITIAL','RENEW') {$search} ORDER BY cnt.id DESC {$limit}");
			$row =  $this->conn->getFields(); //Get all rows

			if( $this->conn->getNumRows() > 0 ){
				$data = array();
				$nestedData=array(); 
					foreach($row['aaData'] as $index=>$value) { // preparing an array
						$nestedData[$index] = $value;
					}
					$data = $nestedData; 
					
				$json_data = array(
							"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
							"recordsTotal"    => intval( $totalData ),  // total number of records
							"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
							"records"         => $data   // data array,
							);
			} 
			else{ 
				$json_data = array("draw" =>  0,"recordsTotal" => 0, "recordsFiltered" => 0, "records" => array(), "status" => "");
			}
			return $json_data;  // send data as json format.
    }
    public function getArchive(){
        $search = "";
        $limit  = "";

            $requestData= $_REQUEST;
			// storing  request (ie, get/post) global array to a variable  
			 $this->conn->selectQuery("*","{$this->table} WHERE status IN ('CANCEL','CLOSED') ");
			$totalData =  $this->conn->getNumRows(); //getting total number records without any search.
			 $this->conn->row_count = 0;
			 $this->conn->fields = null;

			if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

             $this->conn->selectQuery('*, comp.company_name',"{$this->table} cnt 
                                        LEFT JOIN dbmif.tbl_company_auto_import comp ON cnt.sap_company_id = comp.id
                                        WHERE cnt.status IN ('CANCEL','CLOSED')) {$search}");

				 $this->conn->fields = null;
				$totalFiltered  =  $this->conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			}
			else{
				$totalFiltered = $totalData;
			}
			
			if(intval($requestData['length']) >= 1 ) { $limit = 'LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }
            
            $this->conn->selectQuery('cnt.id, comp.company_name, cat.cat_name, cnt.valid_from, cnt.valid_to, cnt.status, cnt.created_at
                                        ',"{$this->table} cnt 
                                        LEFT JOIN dbmif.tbl_company_auto_import comp ON cnt.sap_company_id = comp.id
                                        LEFT JOIN tbl_categories cat ON cnt.category = cat.id
                                        WHERE cnt.status IN ('CANCEL','CLOSED') {$search} ORDER BY cnt.updated_at DESC {$limit}");
			$row =  $this->conn->getFields(); //Get all rows

			if( $this->conn->getNumRows() > 0 ){
				$data = array();
				$nestedData=array(); 
					foreach($row['aaData'] as $index=>$value) { // preparing an array
						$nestedData[$index] = $value;
					}
					$data = $nestedData; 
					
				$json_data = array(
							"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
							"recordsTotal"    => intval( $totalData ),  // total number of records
							"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
							"records"         => $data   // data array,
							);
			} 
			else{ 
				$json_data = array("draw" =>  0,"recordsTotal" => 0, "recordsFiltered" => 0, "records" => array(), "status" => "");
			}
			return $json_data;  // send data as json format.
    }

    public function getCurrentById($id){
        $this->conn->selectQuery('cnt.*, ren.attachment AS ren_attachment', "{$this->table} cnt 
                                LEFT JOIN tbl_renewal_history ren ON cnt.id = ren.id_contract
                                WHERE cnt.id={$id} ORDER BY ren.id DESC LIMIT 1");
        return $this->conn->getFields();
    }

    public function updateAttachmentName($id, $name){
        $this->conn->updateQuery($this->table, "attachment='{$name}'", "id={$id}");
        return $this->conn->getFields();
    }
    public function emptyFields(){
        $this->conn->fields = null;
    }
    
}

?>