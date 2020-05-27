<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once '../class/solr_curl.php';
require_once '../view/config.php';

if (isset($_POST)) {

   if (isset($_POST['codici'])) {
      $codici = $_POST['codici'];
      foreach ($codici as $cod) { 
      	  $result = removeItems($cod);
	  if (array_key_exists('error', $result)) {
	     echo json_encode(array('error' => $result['error']['msg']));
             exit();
          }
	  
	  //if ($_POST['type'] == "image") {
	  //    
	  //} else if ($_POST['type'] == "video"}
	  //} else if ($_POST['type'] == "ebook"}
	  //} else if ($_POST['type'] == "book"}
	  //}	  
      }
      
      echo json_encode(array('result' => "Cancellazione avvenuta con successo."));
   }
}
?>
