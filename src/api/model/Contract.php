<?php 

require 'core/database.php';

class Contract extends Database{

private $table = "tbl_contracts";
protected $conn = null;

    function __construct(){

        $db = Database::getInstance();
        if($db == null){
            throw new Exception("Failed to connect database.");
        }
        $this->conn = $db;
    }
    function getCurrent(){
        $search = "";
        $limit  = "";

            $requestData= $_REQUEST;
			// storing  request (ie, get/post) global array to a variable  
			 $this->conn->selectQuery("*","{$this->table}");
			$totalData =  $this->conn->getNumRows(); //getting total number records without any search.
			 $this->conn->row_count = 0;
			 $this->conn->fields = null;

			if( !empty($search) ) { // if there is a search parameter, $requestData['search']['value'] contains search parameter.

			 $this->conn->selectQuery('*',"{$this->table} WHERE {$search}");

				 $this->conn->fields = null;
				$totalFiltered  =  $this->conn->getNumRows(); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			}
			else{
				$totalFiltered = $totalData;
			}
			
			if(intval($requestData['length']) >= 1 ) { $limit = 'LIMIT '.$requestData['start'].' ,'.$requestData['length'].''; }

			$this->conn->selectQuery('*',"{$this->table} WHERE id > 0 {$search} {$limit}");
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
				$json_data = array("draw" =>  0,"recordsTotal" => 0, "recordsFiltered" => 0, "records" => array());
			}
            $json_data['status'] = $row['status'];
			return $json_data;  // send data as json format.
    }


    
}

?>