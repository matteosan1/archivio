<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

echo "CIAO";
if (isset($_GET)) {
//if (isset($_POST)) {
//      $id = $_POST['id'];
      $id = $_GET['delete_id'];
      
      require_once ("Member.php");
      $m = new Member();
      $res = $m->removeUser($id);
      exit();
}
?>
