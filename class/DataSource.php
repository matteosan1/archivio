<?php
//namespace Phppot;

class DataSource
{
    const DATABASENAME = "sql/prova.db"; //'phpsamples';

    private $conn;
    
    function __construct()
    {
        $this->conn = new SQLite3(self::DATABASENAME);
        
        $create_query = "CREATE TABLE IF NOT EXISTS `registered_users` (`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,`user_name` VARCHAR(255) NOT NULL,`display_name` VARCHAR(255) NOT NULL,`password` VARCHAR(255) NOT NULL,`email` VARCHAR(255) NOT NULL);";

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
    public function insert($query, $paramType, $paramArray)
    {
        print $query;
        $stmt = $this->conn->prepare($query);
        $this->bindQueryParams($stmt, $paramType, $paramArray);
        $stmt->execute();
        $insertId = $stmt->insert_id;
        return $insertId;
    }
    
    /**
     * To execute query
     * @param string $query
     * @param string $paramType
     * @param array $paramArray
     */
    public function execute($query, $paramType="", $paramArray=array())
    {
        $stmt = $this->conn->prepare($query);
        
        if(!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType="", $paramArray=array());
        }
        $stmt->execute();
    }
}
