<?php


Class AbstractModel{
	private $dbCon;
	protected $arrData;
function __construct() {
		$host = "localhost";
		$password = "test";
		$user = "testUser";
		$database = "test";
		$port = "3306";
		if (!$this->dbCon){
			try{
				$this->dbCon = new PDO("mysql:host=$host;port=$port;dbname=$database", $user, $password);
				echo 'Conencted to database';
				$this->dbCon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);								
			}catch(PDOException $e){
				echo 'Connection Failure';
				echo $e->getMessage();
			}
			try{
				// Setup data array using the db schema
				$query = $this->dbCon->query("DESCRIBE $this->_table");				
				foreach ($query->fetchAll() as $result){				
					$this->arrData[$result['Field']] = "";
					echo ("<br>".$result['Type']);										
				}
				
				
			}catch(PDOException $e){
				echo 'Unable to obtain DB Schema';
				echo $e->getMessage();
			}			
		}
	}
	
	
	
	function __destruct() {
		$this->dbCon = null;
	}
	// Commit changes
	public function save(){
		
		if($this->_pk == "id"){	
			// build our string for the insert value params and column names			
			$columnString;
			$valueString;
			$first = true;
			foreach($this->arrData as $key=>$value){			
				if($first){
					$columnString = $key;
					$valueString =  $key;
					$first = false;
				}else{
					$columnString = $columnString.','.$key;
					$valueString =  $valueString.',:'.$key;
				}				
			}
			$columnString = '('.$columnString.')';
			$valueString = '('.$valueString.')';
			if(!($columnString || $valueString)){
				return "Error during insert string creation";			
			}
			try{
				$query = $this->dbCon->prepare("INSERT INTO $this->_table (name, email) VALUES(:name,:email)");
				foreach($this->arrData as $key=>$value){		
					if($key != 'id'){
						$query->bindParam(':'.$key, $this->arrData[$key]);			
					}
				}
				$query->execute();			
				$this->_pk = $this->dbCon->lastInsertId();
				$this->arrData['id'] = $this->_pk; 
			}catch(PDOException $e){
				echo $e->getMessage();
				return $this;
			}
		}else{			
			$updateString;	
			$first = true;			
			foreach($this->arrData as $key=>$value){				
				if($key != 'id'){
					if($first){
						$updateString = $key.'=:'.$key;
						$first = false;					
					}else {
						$updateString = $updateString.','.$key.'=:'.$key;
					}
				}
			}			
			if(!$updateString){
				return "Error during update string production";
			}
			try{						
				$query = $this->dbCon->prepare("UPDATE $this->_table SET $updateString WHERE ID = :id");				
			foreach($this->arrData as $key=>$value){		
				if($key != 'id'){
					$query->bindParam(':'.$key, $this->arrData[$key]);			
				}
			}
				$query->bindParam(':id', $this->_pk, PDO::PARAM_INT);
				$query->execute();
			}catch(PDOException $e){
				echo $e->getMessage();
				return $this;
			}		
		}
	}
	// Load a new primay key
	public function load($id){		
			try{
				$query = $this->dbCon->prepare("SELECT * FROM $this->_table WHERE ID = :id");				
				$query->bindParam(':id', $id, PDO::PARAM_INT);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_ASSOC);
			}catch(PDOException $e){
				echo $e->getMessage();
				return $this;
			}
			echo '<br />';
			
			foreach ($this->arrData as $rowName => $value){
				// step through data. Use data from the select to match to the shema already pulled.
				
				foreach ($result as $qrowName => $qvalue){
					// Step through the query results				
					if($qrowName == $rowName){						
						if($qrowName == 'id'){
							$this->_pk = $qvalue;
						}
						$this->arrData[$rowName] = $qvalue;				
						break;
					}
				}
			}
			
			echo $this->_pk;			
			return $this;
			
	}
	// Delete function
	public function delete($id=false){
		print_r("\nDeleting");
		if($id == false){
			$id = $this->_pk;
		}
		try{
			$query = $this->dbCon->prepare("DELETE FROM $this->_table WHERE id = :id");
			$query->bindParam(':id', $id, PDO::PARAM_INT);
			$query->execute();			
		}catch(PDOException $e){
			echo $e->getMessage();
			return $this;
		}
		return $this;
	}
	// Select Function also returns column result if key is provided
	public function getData($key=false){
		
		if (!$key){
			return $this->arrData;
		}else{			
			$result;
			foreach ($this->arrData as $fieldName => $value){
				if ($fieldName == $key ){					
					$result = $value;
				}				
			}
			if (!$result){
				return "Invalid key provided";
			}else {
				return $result;
			}
		}		
	}
	
	// Update function
	public function setData($arr, $value=false){
		if(is_array($arr)){
		foreach ($this->arrData as $rowName => $mvalue){				
				foreach ($arr as $arowName => $avalue){					 		
					if($arowName == $rowName){
						if($arowName == 'id'){
							$this->_pk = $avalue;
						}
						$this->arrData[$rowName] = $avalue;
												
						break;
					}
				}
			}
		}else{
			$keyFound = false;
			foreach ($this->arrData as $fieldName => $avalue){
				if ($fieldName == $arr){
					$keyFound = true;			
					$this->arrData[$fieldName] = $value;
					if ($arr == 'id'){					
						$this->_pk = $value;
					}					
				}				
			}
			if (!$keyFound){
				return "Invalid key provided";
			}
		}
		return $this;
	}
	
}
?>
