<?php 
   
//phpinfo();
        $un = 'root';
		$pw = 'VirtualRollCall';
		$dbName = 'VIRTUAL_ROLL_CALL';
		$address = 'localhost';
		$db_connection = new mysqli($address, $un, $pw, $dbName);

		if ($db_connection->connect_errno > 0) {
//            die("Connection error: " . mysqli_connect_errno());
			die('Unable to connect to database[' . $db_connection->connect_error . ']');
		} else {
		
		echo "Connected";
		}
		
        
     
		
?>