<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once "../view/solr_client.php";
require_once "../class/resize_image.php";

function endsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}

function startsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, strlen($needle)) === 0;
}

if (isset($_POST)) {
    $update = $client->createUpdate();
    $doc = $update->createDocument();

    foreach ($_POST as $key => $value) {
        if (endsWith($key, "_upd")) {
	    $real_key = substr($key, 0, strlen($key)-4);
        } else if (startsWith($key, "upd_")) {
            $real_key = substr($key, 4);
        } else {
            $real_key = $key;
        }
        
        $doc->$real_key = $value;
        if ($real_key == 'data') {
	    if (substr($value, 0, 4) != "1000") { 
		$doc->anno = substr($value, 0, 4);
	    }
	    $doc->data .= "T00:00:00Z"; 
        }
    }

    if (!array_key_exists("privato", $_POST)) {
	$doc->privato = 0;
    }   
    $error = "";
    
    try {
	$update->addDocument($doc);
	$update->addCommit();
	$result = $client->update($update);
    } catch (Solarium\Exception\HttpException $e) {
	$error = $e->getMessage();
    }
    
    if (isset($_FILES['copertina'])) {
	if ($_FILES['copertina']['name'] != "") {
     	    $cover_tmp = $_FILES['copertina']['tmp_name'];
     	    $cover_name = $_POST['codice_archivio'].".JPG";
     	    $ext = explode(".", $_FILES['copertina']['name']);
     	    if (strtolower(end($ext)) != "jpg" and strtolower(end($ext)) != "jpeg") {
     		$error = "La copertina deve essere salvata in jpg.".strtolower(end($ext));
     	    } else {
		$resize = new ResizeImage($cover_tmp);
      		$resize->resizeTo(200, 200, 'maxHeight');
      		$resize->saveImage($GLOBALS['UPLOAD_DIR'].$cover_name);
		
		$res = rename($GLOBALS['UPLOAD_DIR'].$cover_name, $GLOBALS['COVER_DIR'].strtoupper($cover_name));
		if ($res != 1) {
		    $error = "Errore nella fase di copia della copertina.";
		}
	    }
	}
    }
    
    if ($error == "") {
	echo json_encode(array('result' => "Doc ".$doc->codice_archivio." aggiornato in ".$result->getQueryTime()." ms"));
    } else {
	echo json_encode(array("error" => $error));
    }
}
?>
