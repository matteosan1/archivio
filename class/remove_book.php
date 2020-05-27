<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once '../class/solr_curl.php';

if (isset($_POST)) {
   if (isset($_POST['volumi'])) {
      $codici = $_POST['volumi'];
      foreach ($codici as $cod) { 
      	  $result = removeBook($cod);
	  if (array_key_exists('error', $result)) {
	     echo json_encode(array('error' => $result['error']['msg']));
             exit();
          }
      }
      
      echo json_encode(array('result' => "Volumi rimossi con successo"));
   }
}
?>
