<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1);
        include('db.php');
	if ($_POST['id']) {
                $id = $_POST['id'];
                $result = $sqlite->query("SELECT * FROM tags WHERE parent_id=".$id.";");
                while ($row = $result->fetchArray()) {
                        $id = $row['id'];
                        $data = $row['name'];
			echo $id."   ".$data;
                        echo '<option value="'.$id.'">'.$data.'</option>';
                }
        }      
?>
