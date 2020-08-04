<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";

if (isset($_POST)) {
   $old_ca = $_POST['old_codice_archivio'];
   $new_ca = $_POST['new_codice_archivio']:
   
   exec("/usr/bin/python change_id.py ".$old_ca." ".$new_ca, $output, $status);
   if ($status != 0) {
      print_r (array("error"=>$output));
      exit;
   } else {
      rename($GLOBALS['COVER_DIR'].old_ca.".JPG", $GLOBALS['COVER_DIR'].new_ca.".JPG" 
      print_r(array("result"=>$output));
   }
}
?>
