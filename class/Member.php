<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1);

class Member
{
    private $ds;

    function __construct()
    {
        require_once "DataSource.php";
        $this->ds = new DataSource();
    }

    function getAllCategories($table_name="book_categories")
    {
    	$query = "SELECT * FROM ".$table_name.";";
    	$paramType = array();
    	$paramArray = array();
    	$result = $this->ds->select($query, $paramType, $paramArray);
    
    	return $result;
    }
    
    function getAlleBookCategories()
    {
    	$query = "SELECT * FROM ebook_categories;";
    	$paramType = array();
    	$paramArray = array();
    	$result = $this->ds->select($query, $paramType, $paramArray);
    
    	return $result;
    }

    function getAllMembers()
    {
    	$query = "SELECT * FROM registered_users;";
    	$paramType = array();
    	$paramArray = array();
    	$result = $this->ds->select($query, $paramType, $paramArray);
    
    	return $result;
    }

    function getAllRoles()
    {
	$query = "SELECT * FROM roles;";
    	$paramType = array();
    	$paramArray = array();
    	$result = $this->ds->select($query, $paramType, $paramArray);
    
    	return $result;
    }

    function getMemberById($memberId)
    {
        $query = "SELECT * FROM registered_users WHERE id = ?";
        $paramType = array(SQLITE3_INTEGER);
        $paramArray = array($memberId);
        $memberResult = $this->ds->select($query, $paramType, $paramArray);
        
        return $memberResult;
    }
    
    public function processLogin($username, $password) {
        $passwordHash = md5($password);
        $query = "SELECT * FROM registered_users WHERE user_name = ? AND password = ?";
        $paramType = array(SQLITE3_TEXT, SQLITE3_TEXT);
        $paramArray = array($username, $passwordHash);
	$memberResult = $this->ds->select($query, $paramType, $paramArray);
	if(!empty($memberResult)) {
            $_SESSION["userId"] = $memberResult[0]["id"];
	    $_SESSION["name"] = $memberResult[0]["display_name"];
	    $_SESSION["role"] = $memberResult[0]["role"];
	    $_SESSION["time"] = date('Y-m-d H:i:s');
	    return true;
        }
    }

    public function addUser($name, $username, $password, $role, $email) {
    	   $passwordHash = md5($password);
	   $query = "INSERT INTO registered_users ('user_name', 'display_name', 'password', 'role', 'email') VALUES (?, ?, ?, ?, ?);";
	   $paramType = array(SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT);
	   $paramArray = array($username, $name, $passwordHash, $role, $email);
	   $insertResult = $this->ds->execute($query, $paramType, $paramArray);
	   return true;
    }
    
    public function addCategory($table_name, $name) {
	   $query = "INSERT INTO ".$table_name." ('category') VALUES (?);";
	   $paramType = array(SQLITE3_TEXT);
	   $name = strtoupper(str_replace(" ", "_", $name));
	   $paramArray = array($name);
	   $insertResult = $this->ds->execute($query, $paramType, $paramArray);
	   return true;
    }

    public function removeUser($id) {
	   $query = "DELETE FROM registered_users WHERE id = ?;";
	   $paramType = array(SQLITE3_INTEGER);
	   $paramArray = array($id);
	   $insertResult = $this->ds->execute($query, $paramType, $paramArray);
	   return true;
    }

    public function removeCategory($table_name, $id) {
	   $query = "DELETE FROM ".$table_name." WHERE id = ?;";
	   $paramType = array(SQLITE3_INTEGER);
	   $paramArray = array($id);
	   $insertResult = $this->ds->execute($query, $paramType, $paramArray);
	   return true;
    }

    public function addNote($sender, $recipient, $recipientg, $note, $timestamp) {
	   $query = "INSERT INTO note ('sender', 'recipient', 'recipientg', 'text', 'date') VALUES (?, ?, ?, ?, ?);";
	   $paramType = array(SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT);
	   $paramArray = array($sender, $recipient, $recipientg, $note, $timestamp);
	   $insertResult = $this->ds->execute($query, $paramType, $paramArray);
	   
	   return true;
    }

    public function getNNotes($displayName, $role) {
    	   $query = "SELECT * FROM note WHERE (recipient = ? OR recipientg = ?) AND status = 0;";
           $paramType = array(SQLITE3_TEXT, SQLITE3_TEXT);
           $paramArray = array($displayName, $role);
	   $memberResult = $this->ds->select($query, $paramType, $paramArray);
	   if (empty($memberResult)) {
	      return 0;
	   } else {
	   	return count($memberResult);
	   }
    }

    public function getAllNotes($displayName, $role) {
    	   $query = "UPDATE note SET status = 1 WHERE recipient = ? OR recipientg = ?;";
	   $paramType = array(SQLITE3_TEXT, SQLITE3_TEXT);
           $paramArray = array($displayName, $role);
	   $memberResult = $this->ds->execute($query, $paramType, $paramArray);
	   
    	   $query = "SELECT * FROM note WHERE recipient = ? OR recipientg = ?";
           $paramType = array(SQLITE3_TEXT, SQLITE3_TEXT);
           $paramArray = array($displayName, $role);
	   $memberResult = $this->ds->select($query, $paramType, $paramArray);
	   
	   return $memberResult;
    }

    public function removeNote($id) {
	   $query = "DELETE FROM note WHERE id = ?;";
	   $paramType = array(SQLITE3_INTEGER);
	   $paramArray = array($id);
	   $insertResult = $this->ds->execute($query, $paramType, $paramArray);
	   
	   return true;
    }
}
