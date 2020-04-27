<?php

include  __DIR__."/config.php";  

class DbConnect{	
	private $connect;	
	public function __construct(){
		$this->connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

		$query  = "SET NAMES 'utf8mb4'";
		$query .= "SET GLOBAL max_allowed_packet=16777216";
		mysqli_query($this->connect,$query);
		
		if (mysqli_connect_errno($this->connect))
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();  
		}
	}
	
	public function getDb(){	
		return $this->connect;
	}	
} 
