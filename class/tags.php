<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once "../class/Member.php";

$m = new Member();

if ($_POST['id']) {
    $id = $_POST['id'];
    $result = $m->getL2Tags($id);
    foreach ($result as $row) {
        $id = $row['id'];
        $data = $row['name'];
        echo $id."   ".$data;
        echo '<option value="'.$id.'">'.$data.'</option>';
    }
}      
?>
