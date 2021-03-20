<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once '../view/solr_client.php';
require_once "../class/solr_utilities.php";
require_once "../class/resize_image.php";

if (isset($_POST)) {
     $version = -1;

     if ($_POST['prefissi'] == "") {
       $id = getLastByIndex($_POST['anno']) + 1;
       $codice_archivio = $_POST['anno'].".".str_pad($id, 2, "0", STR_PAD_LEFT);
     } else {
       $id = getLastByIndex($_POST['prefissi'].".".$_POST['anno']) + 1;
       $codice_archivio = $_POST['prefissi'].".".$_POST['anno'].".".str_pad($id, 2, "0", STR_PAD_LEFT);
     }

     $update = $client->createUpdate();

     $doc = $update->createDocument();
     $doc->codice_archivio = $codice_archivio;
     $doc->tipologia = $_POST['tipologia'];
     $doc->titolo = $_POST['titolo'];
     $doc->sottotitolo = $_POST['sottotitolo'];
     $doc->prima_responsabilita = $_POST['prima_responsabilita'];
     $doc->altre_responsabilita = $_POST['altre_responsabilita'];
     $doc->luogo = $_POST['luogo'];
     $doc->edizione = $_POST['edizione'];
     $doc->ente = $_POST['ente'];
     $doc->serie = $_POST['serie'];
     $doc->anno = $_POST['anno'];
     $doc->descrizione = $_POST['descrizione'];
     $doc->cdd = $_POST['cdd'];
     $doc->soggetto = $_POST['soggetto'];
     $doc->note = $_POST['note'];
     $doc->_version_ = $version;

     $error = "";
     try {
     	 $update->addDocuments(array($doc));
     	 $update->addCommit();
     	 $result = $client->update($update);
     } catch (Solarium\Exception\HttpException $e) {
         $error = $e->getMessage();
     } 
     
     if ($_FILES['copertina']['name'] != "") {
     	$cover_tmp = $_FILES['copertina']['tmp_name'];
     	$cover_name = $codice_archivio.".JPG";
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

     if ($error != "") {
        echo json_encode(array('error' => $error));  
     } else {
        echo json_encode(array('result' => "Volume ".$codice_archivio." inserito correttamente."));
     }
}
?>
