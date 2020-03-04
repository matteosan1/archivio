<?php
require_once __DIR__ . "/../view/config.php";

class DataSource
{
    //DATABASENAME = __DIR__ . "/../sql/" . $GLOBALS['DATABASENAME']; //prova.db"; //'phpsamples';

    private $conn;
    
    function __construct()
    {
        $this->conn = new SQLite3(__DIR__ . "/../sql/" . $GLOBALS['DATABASENAME']);
        $create_query = "CREATE TABLE IF NOT EXISTS `registered_users` (`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,`user_name` VARCHAR(255) NOT NULL,`display_name` VARCHAR(255) NOT NULL, `role` VARCHAR(20) NOT NULL, `password` VARCHAR(255) NOT NULL,`email` VARCHAR(255) NOT NULL);";

	$this->conn->exec($create_query);
    }

    public function select($query, $paramType=array(), $paramArray=array())
    {
        $stmt = $this->conn->prepare($query);
        
	if(!empty($paramType) && !empty($paramArray)) {
		$i = 0;
		foreach ($paramArray as $value) {
			$stmt->bindValue($i+1, $value, $paramType[$i]);
			$i = $i + 1;
		}
        }
        
	$res = $stmt->execute();
	while ($row = $res->fetchArray()) {
        	$resultset[] = $row;
        }
        
        if (! empty($resultset)) {
            return $resultset;
        }
    }
    
    /**
     * To insert
     * @param string $query
     * @param string $paramType
     * @param array $paramArray
     * @return int
     */
    public function execute($query, $paramType, $paramArray)
    {
        $stmt = $this->conn->prepare($query);
	if(!empty($paramType) && !empty($paramArray)) {
	    $i = 0;
	    foreach ($paramArray as $value) {
	        $stmt->bindValue($i+1, $value, $paramType[$i]);
	        $i = $i + 1;
	    }
	}
        
        $res = $stmt->execute();
        return $res;
    }
}
