<?php
//error_reporting(E_ALL);
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

    function getL1Tags() {
    	$query = "SELECT id, name FROM tags WHERE parent_id=-1;";
 	    $result = $this->ds->select($query, array(), array());

	    return $result;
    }

    function getTagNameById($id) {
        $query = "SELECT name FROM tags WHERE id=?;";
	    $paramType = array(SQLITE3_INTEGER);
    	$paramArray = array($id);
	    $result = $this->ds->select($query, $paramType, $paramArray);

	    return $result[0]['name'];
    }

    function getL2Tags($tagl1) {
    	$query = "SELECT * FROM tags WHERE parent_id=?;";
	    $paramType = array(SQLITE3_INTEGER);
    	$paramArray = array($tagl1);
	    $result = $this->ds->select($query, $paramType, $paramArray);

     	return $result;
    }

    function getAllCategories($table_name="book_categories")
    {
	    $group = 1;
	    if ($table_name == 'ebook_categories') {
	         $group = 2;
	    }
	
	    if ($table_name == 'all') {
	         $query = "SELECT * FROM categories";
	    } else {
    	     $query = "SELECT * FROM categories WHERE cgroup=?;";
	    };
	    $paramType = array(SQLITE3_INTEGER);
    	$paramArray = array($group);
	    $result = $this->ds->select($query, $paramType, $paramArray);
    
    	return $result;
    }

    function getAllPrefissi()
    {
	    $query = "SELECT prefix FROM codice_archivio ORDER BY prefix";
	    $paramType = array();
    	$paramArray = array();
	    $result = $this->ds->select($query, $paramType, $paramArray);
    
    	return $result;
    }

    function curlFlBiblio($category="book_categories") {

    	if ($category == "video") {
	       $cats = array(array("VIDEO"));
	    } else if ($category == "image") {
	       $cats = array(array("FOTOGRAFIA"));
        } else if ($category == "slide") {
           $cats = array(array("STAMPA"), array("LASTRA"));
	    } else {
           $cats = $this->getAllCategories($category);
	    }

	    $res = array();
	    for ($i=0; $i<count($cats); $i++) {
	        $res[$i] = "tipologia:".$cats[$i][0];
	    }

        return implode("+OR+", $res);
    }
    
    function findTypeGroup($tipologia) {
    	$query = "SELECT cgroup FROM category WHERE category=\"".$tipologia."\";";
	    $result = $this->ds->select($query, array(), array());
    
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

    function getMemberByName($name, $isUser=FALSE)
    {
	if ($isUser) {
            $query = "SELECT * FROM registered_users WHERE user_name = ?";
	} else {
	    $query = "SELECT * FROM registered_users WHERE display_name = ?";
	}    
        $paramType = array(SQLITE3_TEXT);
        $paramArray = array($name);
        $memberResult = $this->ds->select($query, $paramType, $paramArray);

	if (isset($memberResult) and count($memberResult) > 0)  {
           return TRUE;
    	} else {
	   return FALSE;
	}
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
    
    public function addCategory($table_name, $name, $id=-1) {
	   // FIXME merge due if per tagl1 e tagl2
	   if ($table_name == "tagl1") {
              $query = "INSERT INTO tags ('name', 'parent_id') VALUES (?, ?);";
	      $paramType = array(SQLITE3_TEXT, SQLITE3_INTEGER);
	      $paramArray = array($name, -1);
	   } elseif ($table_name == "tagl2") {
              $query = "INSERT INTO tags ('name', 'parent_id') VALUES (?, ?);";
	      $paramType = array(SQLITE3_TEXT, SQLITE3_INTEGER);
	      $paramArray = array($name, $id);
	   } else {
              $name = strtoupper(str_replace(" ", "_", $name));
	      $query = "INSERT INTO categories ('category', 'cgroup') VALUES (?, ?);";
	      $paramType = array(SQLITE3_TEXT, SQLITE3_INTEGER);
	      if ($table_name == "ebook") {
      	      	  $paramArray = array($name, 2);
	      } else {
	      	  $paramArray = array($name, 1);
	      }
	   }
	   $insertResult = $this->ds->execute($query, $paramType, $paramArray);
	   
	   return $insertResult;
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

//$m = new Member();
//$m->addCategory("pluto", "pippo");