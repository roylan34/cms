<?php 
/**
* 01/25/2017
* Developed by: Delsan Web Development Team
*
* This file contains a class FOR DATABASE CONNECTIVITy.
*
*/

Class Database{

	private static $instance;
	public $row_count   = 0;
	public $last_insert_id = 0;
	public $fields    	 = array();
    protected $conn      = null;

	protected $dbHost = "localhost";
	protected $dbUser = "root";
	protected $dbPass = "";
	protected $dbName = "dbmif";

	 function __construct($dbParams = array()){ // @dbParams is use to override the default settings in database.

 		if(count($dbParams) > 0)
 		{
			$this->dbName = $dbParams['db_name'];
	        $this->dbHost = $dbParams['db_host'];
	        $this->dbUser = $dbParams['db_user'];
	        $this->dbPass = $dbParams['db_pass'];
		}
		
	        $this->conn = new mysqli($this->dbHost, $this->dbUser,$this->dbPass,$this->dbName);
			if($this->conn->connect_errno){
				die("Failed to connect:". $this->conn->connect_error);
			}
			//change character set to utf8
			if(!$this->conn->set_charset('utf8')){
				printf("Error loading character set utf8 %s\n",$this->conn->connect_error);
				exit();
			}
	}

	static public function getInstance($dbParams = array()){
	
		if(self::$instance == null){
			self::$instance = new self($dbParams);
		}
		return self::$instance;
	}

	public function selectQuery($select_field, $table_name){

		$qry ="SELECT {$select_field} FROM {$table_name}";

		if($res = $this->conn->query($qry))
		{
			if($res->num_rows > 0){
				$this->row_count = $res->num_rows; // Count all rows.

				while ($row = $res->fetch_assoc()) {
					$this->fields['aaData'][] = $row;
				}
			}
			else { $this->emptyFields(); } //return empty data.
		}
		else{
			trigger_error('Something wrong of query: '. $qry);
		}
	}

	public function updateQuery($table_name,$fields,$cond){
		
		$qry = "UPDATE {$table_name} SET {$fields} WHERE {$cond}";

		if($this->conn->query($qry) === TRUE){
			$this->fields['aaData'][] = "success";
		}else{
			trigger_error('Something wrong of query: '. $qry);
		}
	}

	function updateMultipleQuery($table_name, $update_values){
			// Start of the query
			$qry = "UPDATE $table_name SET ";

			// Columns we will be updating.
			 foreach($update_values as $id => $values){
			    foreach($values as $column_key => $column_val){
			        $columns[$column_key] = $column_key . "= CASE ";
			    }
			}

			// Build up each columns CASE statement.
			foreach($update_values as $id => $values){
			    foreach($values as $column_key => $column_val){
			        $columns[$column_key] .= "WHEN `id`='" . $id . "' THEN '". $this->escapeString($column_val). "' ";
			    }
			}

			// Add a default case, here we are going to use whatever value was already in the field.
			foreach($columns as $column_name => $query_part){
			  $columns[$column_name] .= " ELSE `$column_name` END ";
			}

			// Build the WHERE part. Since we keyed our update_values off the database keys, this is pretty easy.
			$where = " WHERE `id`='" . implode("' OR `id`='", array_keys($update_values)) . "'";

			// Join the statements with commas, then run the query.
			$qry .= implode(', ',$columns) . $where;

			if($this->conn->query($qry) === TRUE){
				$this->fields['aaData'][] = "success";
			}else{
				trigger_error('Something wrong of query: '. $qry);
			}

	}

	public function insertQuery($table_name, $fields, $values){

		$qry = "INSERT INTO {$table_name} ({$fields}) VALUES ({$values})";
		if($this->conn->query($qry) === TRUE){
			$this->last_insert_id = $this->conn->insert_id;
			$this->fields['aaData'][] = "success";
		}else{
			trigger_error('Something wrong of query: '. $qry);
		}
	}

	function insertMultipleByUniqueQuery($table_name, $fields, $uniq_arr_val, $values){
	    if(is_array($uniq_arr_val)){
	        foreach($uniq_arr_val as $row) {
	            $query_values[] = "('".$row."', $values)";
	        }
	     	$qry = "INSERT INTO {$table_name} ({$fields}) VALUES ". implode(',',$query_values);
		     	if($this->conn->query($qry) === TRUE){
					$this->fields['aaData'][] = "success";
				}else{
					trigger_error('Something wrong of query: '. $qry);
				}
	    }
	    else{
	      trigger_error('Argument must an array.');
	    }

	}

	function insertMultipleQuery($table_name, $fields, $prepend_arr_val, $array_val){
	    foreach($array_val as $row){
	      if(is_array($prepend_arr_val) && count($prepend_arr_val) > 0)
	            array_unshift($row,implode("','",$prepend_arr_val));
	      
	       $values[] = "('".implode("','",$row)."')";
	    }

	    $qry = "INSERT INTO {$table_name} ({$fields}) VALUES ".implode(",", $values)."";
    		if($this->conn->query($qry) === TRUE){
				$this->fields['aaData'][] = "success";
			}else{
				trigger_error('Something wrong of query: '. $qry);
			}

	}

	public function deleteQuery($table_name, $fields){
		$qry = 'DELETE FROM '.$table_name.' WHERE '.$fields;
		if($this->conn->query($qry) === TRUE){
			$this->fields['aaData'][] = "success remove";
		}else{
			trigger_error('Something wrong of query: '. $qry);
		}
	}

	public function customQuery($custom){
		$qry = $custom;
		if($this->conn->query($qry) === TRUE){
			$this->fields['aaData'][] = "success";
		}else{
			trigger_error('Something wrong of query: '. $qry);
		}
	}
	public function selectQuery2($custom){

		$qry = $custom;
		if($res = $this->conn->query($qry))
		{
			if($res->num_rows > 0){
				$this->row_count = $res->num_rows; // Count all rows.

				while ($row = $res->fetch_assoc()) {
					$this->fields['aaData'][] = $row;
				}
			}
			else { $this->emptyFields(); } //return empty data.
		}
		else{
			trigger_error('Something wrong of query: '. $qry);
		}
	}
	public function storProc($storedProc_name){

		$qry ="CALL {$storedProc_name}";

		if($res = $this->conn->query($qry))
		{
			if($res->num_rows > 0){
				$this->row_count = $res->num_rows; // Count all rows.

				while ($row = $res->fetch_assoc()) {
					$this->fields['aaData'][] = $row;
				}
			}
			else { $this->emptyFields(); } //return empty data.
		}
		else{
			trigger_error('Something wrong of query: '. $qry);
		}
	}

	public function getNumRows(){
		return $this->row_count;
	}

	public function getFields(){
		return $this->fields;
	}

	public function getLastId(){
		return (int)$this->last_insert_id;
	}

	public function escapeString($string){
		return $this->conn->real_escape_string($string);
	}

	public function emptyFields(){
		$this->fields['aaData'] = array();
	}

	public function __clone(){
		throw new Exception("Can't clone the instance of this db.");
		
	}

}

