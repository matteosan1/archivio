<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!


if (!isset($_SESSION)) {
   session_start();
}

if(!isset($_SESSION["userId"])) {
   header ("Location: /index.php");
} 
?>