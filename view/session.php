<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

if (!isset($_SESSION)) {
   session_start();
}

if(empty($_SESSION["userId"])) {
  header ("Location: /index.php");
} else { 
  $datetime2 = strtotime($_SESSION['time']);
  $datetime1 = strtotime(date('Y-m-d H:i:s'));
  $minutes = ($datetime1 - $datetime2)/60;
  if ($minutes < 30) {
     $_SESSION['time'] = date('Y-m-d H:i:s');
     $displayName = $_SESSION["name"];
     $role = $_SESSION["role"];
  } else {
     unset($_SESSION['userId']);
     header ("Location: /index.php");
  }
}
