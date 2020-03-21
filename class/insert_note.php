<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once "Member.php";

if (isset($_POST)) {
   $timestamp = date('Y-m-d H:i:s');
   
   $m = new Member();
   $res = $m->addNote($_POST["sender"], $_POST["recipient"], $_POST["recipientg"], $_POST["note"], $timestamp);

   echo "Messaggio inviato correttamente.";
}
?>
