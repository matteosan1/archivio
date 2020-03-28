<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once "../class/solr_curl.php";
require_once "../class/resize_image.php";

function customError($errno, $errstr) {
  echo json_encode(array('error' => $errstr));
  exit;
}

set_error_handler("customError");

if (isset($_POST)) {
   //if (isset($_POST["catalogo"])) {
   //   if (isset($_FILES['filecsv'])) {
   //      if ($_FILES["filecsv"]["size"] > 0) {
   //         $result = upload_csv($_FILES['filecsv']['tmp_name']);
   //
   //	    if (array_key_exists("error", $result)) {
   //             echo json_encode(array('error' => $result['error']['msg']));
   //	        exit();
   //	    }
   //      }
   //   }	  
   //
   //   if (isset($_FILES['filezip'])) {
   //      if ($_FILES["filezip"]["size"] > 0) {
   //          $zip = new ZipArchive;
   //          $res = $zip->open($_FILES["filezip"]['tmp_name']);
   //          if ($res == true) {
   //             $zip->extractTo($GLOBALS['COVER_DIR']); 
   //             $zip->close();
   //          }
   //
   //	     if ($zip->open($_FILES["filezip"]['tmp_name']) == TRUE) {
   //	     	for ($i=0; $i<$zip->numFiles; $i++) {
   //	            $filename = $zip->getNameIndex($i);
   //		    rename($GLOBALS['COVER_DIR'].$filename, $GLOBALS['COVER_DIR'].strtoupper($filename));
   //	     	}
   //		$zip->close();
   //	     }
   //      }     
   //   }
   //
   //   echo json_encode(array("result" => "Catalogo inserito correttamente."));
   //   exit;
   //} else {
     if (isset($_POST['update_or_insert'])) {
         $version = 1;
     } else {
         $version = -1;
     }

     $header = "codice_archivio|tipologia|titolo|sottotitolo|prima_responsabilita|altre_responsabilita|luogo|edizione|ente|serie|anno|descrizione|cdd|soggetto|note|_version_\n";
     $data = $_POST['codice_archivio']."|".$_POST['tipologia']."|".$_POST['titolo']."|".$_POST['sottotitolo']."|".$_POST['prima_responsabilita']."|".$_POST['altre_responsabilita']."|".$_POST['luogo']."|".$_POST['edizione']."|".$_POST['ente']."|".$_POST['serie']."|".$_POST['anno']."|".$_POST['descrizione']."|".$_POST['cdd']."|".$_POST['soggetto']."|".$_POST['note']."|".$version."\n";
     
     if ($version == -1 or ($version == 0 and $_FILES['copertina']['name'] != "")) {
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
     	//$target_directory = $GLOBALS['COVER_DIR'].$cover_name;
	//if (!move_uploaded_file($cover_tmp, $target_directory)) {
	if ($res != 1) {
	   echo json_encode(array('error' => "Errore nella fase di copia della copertina."));
	   exit;
	}
     }

     $fileName = $GLOBALS['UPLOAD_DIR'].'newbook_'.date("d-m-y-H-i-s").'.csv';
     file_put_contents($fileName, $header.$data, LOCK_EX);
     	
     $result = upload_csv($fileName);
     unlink($fileName);

     if ($result['responseHeader']['status'] != 0) {
        echo json_encode(array('error' => $result['error']['msg']));  
     } else {
        echo json_encode(array('result' => "Volume ".$_POST['codice_archivio']." inserito correttamente."));
     }
  //}
}
?>
