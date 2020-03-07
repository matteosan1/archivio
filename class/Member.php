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

    function getAllCategories()
    {
    	$query = "SELECT * FROM book_categories;";
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
    
    public function addCategory($name, $username, $password, $role, $email) {
	   $query = "INSERT INTO book_categories ('category') VALUES (?);";
	   $paramType = array(SQLITE3_TEXT);
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

    public function removeCategory($id) {
	   $query = "DELETE FROM book_categories WHERE id = ?;";
	   $paramType = array(SQLITE3_INTEGER);
	   $paramArray = array($id);
	   $insertResult = $this->ds->execute($query, $paramType, $paramArray);
	   return true;
    }
}
