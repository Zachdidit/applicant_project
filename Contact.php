<?php 

require_once("AbstractModel.php");
Class Contact extends AbstractModel
{
	protected $_table = "contacts";
	protected $_pk = "id";
	
	protected $name="";
	protected $email="";
	
	private $dbCon;
	
	function __construct() {
		$host = "localhost";
		$password = "test";
		$user = "testUser";
		$database = "test";
		$port = "3306";
		if (!$this->dbCon){
			$this->dbCon = new mysqli($host, $user, $password, $database, $port);
		
			if (!$this->dbCon)
			{
				die("Connection error: " . mysqli_connect_error());
				return false;
			}else
			{				
				return true;
			}
		}else{
			return true;
		}
		
		
	}
	
	
	
	function __destruct() {
		$this->dbCon->close();
	}

	public function save(){
	
		if($this->_pk == null){			
			$query = "INSERT INTO $this->_table (name, email) VALUES('$this->name','$this->email')";
			$result =  $this->dbCon->query($query);
			$this->_pk = mysqli_insert_id($this->dbCon);
		}else  {			
			$query = "UPDATE $this->_table SET name = '$this->name', email = '$this->email' WHERE ID = $this->_pk";
			$result =  $this->dbCon->query($query);			
		}
		if (!$result){
			echo("[Save]Query Failed");
			echo(" Error Discription: " . $this->dbCon->error);
		}
		return $result;
		
	}
	// Load a new primay key
	public function load($id){
		$this->_pk = $id;		
			$query = "SELECT * FROM $this->_table WHERE ID = $this->_pk";			
			$result = $this->dbCon->query($query);
			if (!$result){
			echo("[Load]Query Failed");
			echo(" Error Discription: " . $this->dbCon->error);
			}
			$result = mysqli_fetch_assoc($result);
			 $this->name = $result['name'];
			 $this->email = $result['email'];	
		return $this;
	}
	// Delete function
	public function delete($id=false){
		print_r("\nDeleting");
		if($id == false){
			$id = $this->_pk;
		}
		$query = "DELETE FROM $this->_table WHERE id = $id";
		$result = $this->dbCon->query($query);
			if (!$result){
				echo("[Delete]Query Failed");
				echo(" Error Discription: " . $this->dbCon->error);
			}
		return $this;
	}
	
	public function getData($key=false){
				
		switch ($key){
			case false:
			 $arrData['id'] = $this->_pk;
			 $arrData['name'] = $this->name;
			 $arrData['email'] = $this->email;
			 return $arrData;
			 break;
			case "name":
				return $this->name;
				break;
			case "id":
				return $this->_pk;
				break;
			case "email":
				return $this->email;
				break;
			default:
				return "Invalid key provided";
				break;
				
		}
	}
	
	
	public function setData($arr, $value=false){
		if(is_array($arr)){
			$this->_pk = $arr['id'];
			$this->name = $arr['name'] ;
			$this->email = $arr['email'];
		}else{
			switch ($arr){
				case "name":
					$this->name = $value;
					break;
				case "id":
					$this->_pk = $value;
					break;
				case "email":
					$this->email = $value;
					break;
				default:
					echo "Invalid key provided";
					break;		
			}
		}
		return $this;
	}
}

?>
