<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";

if (isset($_POST)) {
   $tmp_filename = $_FILES['edoc']['tmp_name'];
   $command = "/usr/local/bin/tesseract ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr -l ita";
   exec($command);
   $ocr = file_get_contents($GLOBALS['UPLOAD_DIR']."/ocr.txt");
   $ocr = preg_replace('/^[ \t]*[\r\n]+/m', '', $ocr);
   echo $ocr;
}
?>
