
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1); 

require "class/Member.php";
use Member as Member;

$m = new Member();
print_r ($m->processLogin("ikate_91", "kate@03"))

//session_start();
//if(!empty($_SESSION["userId"])) {
//    require_once './view/dashboard.php';
//} else {
//    require_once './view/login-form.php';
//}
?>
