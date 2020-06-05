<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once "../class/solr_curl.php";
require_once "../class/resize_image.php";

//function customError($errno, $errstr) {
//  echo json_encode(array('error' => $errstr));
//  exit;
//}

//set_error_handler("customError");

function endsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}

if (isset($_POST)) {

   $data = array();
   foreach ($_POST as $key => $value) {
       if (endsWith($key, "_upd")) {
       	  $real_key = rtrim($key, "_upd");
       } else {
          $real_key = $key;
       }
       
       if ($real_key == "codice_archivio") {
       	  $data[$real_key] = $value;
       } else {
          $data[$real_key] = array("set"=>$value);
       }
   }
   
   $ret = upload_json_string(json_encode(array($data)));

   if (isset($_FILES['copertina'])) {
     if ($_FILES['copertina']['name'] != "") {
     	$cover_tmp = $_FILES['copertina']['tmp_name'];
     	$cover_name = $_POST['codice_archivio'].".JPG";
     	$ext = explode(".", $_FILES['copertina']['name']);
     	if (strtolower(end($ext)) != "jpg" and strtolower(end($ext)) != "jpeg") {
     	   echo json_encode(array('error' => "La copertina deve essere salvata in jpg.".strtolower(end($ext))));
           exit;
     	}

	$resize = new ResizeImage($cover_tmp);
      	$resize->resizeTo(200, 200, 'maxHeight');
      	$resize->saveImage($GLOBALS['UPLOAD_DIR'].$cover_name);

	$res = rename($GLOBALS['UPLOAD_DIR'].$cover_name, $GLOBALS['COVER_DIR'].strtoupper($cover_name));
	if ($res != 1) {
	   echo json_encode(array('error' => "Errore nella fase di copia della copertina."));
	   exit;
	}
      }
    }

    print_r ($ret);
    exit;
}
?>
