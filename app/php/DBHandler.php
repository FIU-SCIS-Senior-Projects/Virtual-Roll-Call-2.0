<?php

//TO DO: Notify admin when a username already exists in the db

class DBHandler{

	//Must be updated to match production environment
	function __construct(){
		global $db_connection;
		$un = 'root';
		$pw = 'VirtualRollCall';
		$dbName = 'VIRTUAL_ROLL_CALL';
		$address = 'localhost';
		$db_connection = new mysqli($address, $un, $pw, $dbName);

		if ($db_connection->connect_errno > 0) {
			die('Unable to connect to database[' . $db_connection->connect_error . ']');
		}

	}

  //ADD NEW USER TO DATABASE
	function addUser($first_name, $last_name, $username, $password, $role) {
		global $db_connection;
		$result = ['Added' => false,'Username' => $username];
		$sql = "INSERT INTO OFFICERS (First_Name, Last_Name, Username, Password, Role) VALUES (?,?,?,?,?)"; 
		$stmt = $db_connection->prepare($sql);
		if (!$stmt->bind_param('sssss', $first_name, $last_name, $username, $password, $role)){
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!$stmt->execute()){
			return $result;
		}
    $result['Added'] = true;
		$stmt->close();
		$db_connection->close();
		return $result;
	}
  
  // RETRIEVE USER 
  function getUser($username){
    global $db_connection;
    $result = ['userID' => NULL, 'First_Name' => NULL, 'Last_Name' => NULL, 'Username' => NULL, 'Role' => NULL];
    $sql = 'SELECT userID, First_Name, Last_Name, Username, Role FROM OFFICERS WHERE Username=?';
    $stmt = $db_connection->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($result['userID'], $result['First_Name'], $result['Last_Name'], $result['Username'], $result['Role']);
    if (!$stmt->fetch()){
      return $result;
    }
    $stmt->close();
    $db_connection->close();
    return $result;
  }

	function editUser($id, $first_name, $last_name, $username, $role) {
		global $db_connection;
		$result = ["Updated" => false];
		$table = "OFFICERS";
		$sql = "UPDATE $table SET First_Name=?, Last_Name=?, Username=?, Role=?
		        WHERE userID=?";
		$stmt = $db_connection->prepare($sql);
		if( !$stmt->bind_param('ssssd', $first_name, $last_name, $username, $role, $id) )
		{
			return $result;
		}
		if (!$stmt->execute()) 
		{
			return $result;
		}
		$result["Updated"] = true;
		$stmt->close();
		$db_connection->close();
		return $result;
	}

	function removeUser($id) {
		global $db_connection;
		$result = ["Removed" => false];
		$table = "OFFICERS";
		$sql = "DELETE FROM $table
		        WHERE userID=?";
		$stmt = $db_connection->prepare($sql);
		if( !$stmt->bind_param('d', $id) )
		{
			return $result;
		}
		if (!$stmt->execute()) 
		{
			return $result;
		}
		$result["Removed"] = true;
		$stmt->close();
		$db_connection->close();
		return $result;
	}

	function loginUser($username, $password){
		global $db_connection;
        //store the result here
		$result = ['userID' => NULL, 'First_Name' => NULL, 'Last_Name' => NULL, 'Username' => NULL, 'Password' => NULL, 'Role' => NULL];
		$sql = 'SELECT userID, First_Name, Last_Name, Username, Password, Role FROM OFFICERS WHERE Username=? AND Password=?';
		$stmt = $db_connection->prepare($sql);
		$stmt->bind_param('ss', $username, $password);
		$stmt->execute();
		$stmt->bind_result($result['userID'], $result['First_Name'], $result['Last_Name'], $result['Username'], $result['Password'], $result['Role']);
		if (!$stmt->fetch()){
			return $result;
		}
		$stmt->close();
		$db_connection->close();		
		return $result;
	}

	function changePassword($id, $curr_pw, $new_pw){
		global $db_connection;
		$result = ['userID' => NULL, 'Updated' => NULL];
		$sql = 'SELECT userID FROM OFFICERS WHERE userID=? AND Password=?';
		$stmt = $db_connection->prepare($sql);
		$stmt->bind_param('ss', $id, $curr_pw);
		$stmt->execute();
		$stmt->bind_result($result['userID']);
		if (!$stmt->fetch()){
			return $result;
		}
		$stmt->close();
		$sql = 'UPDATE OFFICERS SET Password=? WHERE UserID=?';
		$stmt = $db_connection->prepare($sql);
		$stmt->bind_param('sd', $new_pw, $id);
		$stmt->execute();
		if ($stmt->affected_rows === 1){
			$result['Updated'] = true;
		}
		$stmt->close();
		$db_connection->close();
		return $result;
	}

	function getOfficers(){
		global $db_connection;
		$officers = [];
		$sql = 'SELECT userID, First_Name, Last_Name, Username, Role FROM OFFICERS';
		$stmt = $db_connection->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($userID, $First_Name, $Last_Name, $Username, $Role);
		while($stmt->fetch()){
			$tmp = ["id" => $userID,
			"firstName" => $First_Name,
			"lastName" => $Last_Name,
			"username" => $Username,
			"role" => $Role];
			array_push($officers, $tmp);
		}
		$stmt->close();
		$db_connection->close();
		return $officers;
	}

	//GET ALL DOCUMENTS FROM THE DATABASE
	function getDocuments(){
		global $db_connection;
		$documents = [];
		$sql = 'SELECT DOCUMENTS.document_ID, DOCUMENTS.Document_Name, DOCUMENTS.Category_ID, DOCUMENTS.Upload_Date, DOCUMENTS.Pinned, DOCUMENTS.Uploaded_By, CATEGORIES.category_name, DOCUMENTS.Upload_Name FROM DOCUMENTS INNER JOIN CATEGORIES ON DOCUMENTS.Category_ID = CATEGORIES.Category_ID';
		$stmt = $db_connection->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($id, $name, $catID, $date, $pinned, $uploadedBy, $cat_name, $upload_name);
		while($stmt->fetch()){
			$tmp = ["id" => $id,
			"name" => $name,
			"cat_name" => $cat_name,
			"date" => $date, 
			"pinned" => $pinned, 
			"uploadedBy" => $uploadedBy,
			"upload_name" => $upload_name];
			array_push($documents, $tmp);
		}
		$stmt->close();
		$db_connection->close();
		return $documents;
	}

        function getlogs(){
                global $db_connection;
                $logs = [];
                $sql = 'select First_Name,Last_Name,Document_Name,DOC 
from LOGS inner join DOCUMENTS on LOGS.documentid = DOCUMENTS.document_ID inner join OFFICERS on LOGS.userid = OFFICERS.userID';
                $stmt = $db_connection->prepare($sql);
                $stmt->execute();
                $stmt->bind_result($First_Name, $Last_Name, $Document_Name, $DOC);
                while($stmt->fetch()){
                        $tmp = ["First_Name" => $First_Name,
                        "Last_Name" => $Last_Name,
                        "Document_Name" => $Document_Name,
                        "DOC" => $DOC ];
                        array_push($logs, $tmp);
                }
                $stmt->close();
                $db_connection->close();
                return $logs;
        }

	//ADD DOCUMENT METADATA  TO THE DATABASE
	function addDocument($document, $category, $upload_date, $pinned, $uploaded_by, $upload_name){
		global $db_connection;
		$result = ['Added' => false];
		$sql = "INSERT INTO DOCUMENTS (Document_Name, Category_ID, Upload_Date, Pinned, Uploaded_By, Upload_Name) VALUES (?,?,?,?,?,?)"; 
		$stmt = $db_connection->prepare($sql);
		
		if (!$stmt->bind_param('sdsdss', $document, $category, $upload_date, $pinned, $uploaded_by, $upload_name))
		{
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!$stmt->execute())
		{
			return $result;
		}
		$result['Added'] = true;
		$stmt->close();
		$db_connection->close();
		return $result;
	}
    
    //GET ALL CATEGORIES FROM THE DATABASE
	function getCategories(){
		global $db_connection;
		$categories = [];
		$sql = 'SELECT category_ID, Category_Name FROM CATEGORIES';
		$stmt = $db_connection->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($id, $name);
		while($stmt->fetch()){
			$tmp = ["id" => $id,
			"name" => $name];
			array_push($categories, $tmp);
		}
		$stmt->close();
		$db_connection->close();
		return $categories;
	}

	//ADD NEW CATEGORY TO DATABASE
	function addCategory($name) {
		global $db_connection;
		$result = ['Added' => false,'name' => $name];
		$sql = "INSERT INTO CATEGORIES (Category_Name) VALUES (?)"; 
		$stmt = $db_connection->prepare($sql);
		if (!$stmt->bind_param('s', $name)){
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!$stmt->execute()){
			return $result;
		}
        $result['Added'] = true;
		$stmt->close();
		$db_connection->close();
		return $result;
	}

	//REMOVE CATEGORY FROM THE DATABASE
	function removeCategory($cat_id) {
		global $db_connection;
		$result = ["Removed" => false];
		$table = "CATEGORIES";
		$sql = "DELETE FROM $table
		        WHERE category_ID=?";
		$stmt = $db_connection->prepare($sql);
		if( !$stmt->bind_param('d', $cat_id) )
		{
			return $result;
		}
		if (!$stmt->execute()) 
		{
			return $result;
		}
		$result["Removed"] = true;
		$stmt->close();
		$db_connection->close();
		return $result;
	}
    
    //UPDATE CATEGORY IN THE DATABASE
	function updateCategory($cat_id, $cat_name) {
		global $db_connection;
		$result = ["Updated" => false];
		$sql = "UPDATE CATEGORIES SET Category_Name=? WHERE category_ID=?";
		$stmt = $db_connection->prepare($sql);
		if( !$stmt->bind_param('sd', $cat_name, $cat_id)){
			return $result;
		}
		if (!$stmt->execute()){
			return $result;
		}
		$result["Updated"] = true;
		$stmt->close();
		$db_connection->close();
		return $result;
	}

    //RESET OFFICER PASSWORD IN THE DATABASE
	function resetPassword($id, $reset_pw){
		global $db_connection;
		$result = ['userID' => $id, 'Updated' => false];
		$sql = 'UPDATE OFFICERS SET Password=? WHERE UserID=?';
		$stmt = $db_connection->prepare($sql);
		if(!$stmt->bind_param('sd', $reset_pw, $id)){
			return $result;
		}
		if(!$stmt->execute()){
			return $result;
		}
		$result["Updated"] = true;
		$stmt->close();
		$db_connection->close();
		return $result;
	}

	//GET ALL CATEGORIES FROM THE DATABASE
	function getSiteNames(){
		global $db_connection;
		$result = [];
		$sql = 'SELECT Application_Name, Department_Name FROM SETTINGS';
		$stmt = $db_connection->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($app_name, $dept_name);
		while($stmt->fetch()){
			$result = ["app_name" => $app_name, "dept_name" => $dept_name];
		}
		$stmt->close();
		$db_connection->close();
		return $result;
	}

	//UPDATE CATEGORY IN THE DATABASE
	function updateAppName($app_name) {
		global $db_connection;
		$result = ["Updated" => false];
		$sql = "UPDATE SETTINGS SET Application_Name=?";
		$stmt = $db_connection->prepare($sql);
		if( !$stmt->bind_param('s', $app_name)){
			return $result;
		}
		if (!$stmt->execute()){
			return $result;
		}
		$result["Updated"] = true;
		$stmt->close();
		$db_connection->close();
		return $result;
	}

	//UPDATE CATEGORY IN THE DATABASE
	function updateDeptName($dept_name) {
		global $db_connection;
		$result = ["Updated" => false];
		$sql = "UPDATE SETTINGS SET Department_Name=?";
		$stmt = $db_connection->prepare($sql);
		if( !$stmt->bind_param('s', $dept_name)){
			return $result;
		}
		if (!$stmt->execute()){
			return $result;
		}
		$result["Updated"] = true;
		$stmt->close();
		$db_connection->close();
		return $result;
	}

	function documentSaveLog($user_id,$document_id){
		global $db_connection;
		$sql = "insert into LOGS(DOC,documentid,userid) values(now(),?,?) ";
		$stmt = $db_connection->prepare($sql);
		$stmt->bind_param('ii',$document_id,$user_id);
		$stmt->execute();
		
	}	

}
