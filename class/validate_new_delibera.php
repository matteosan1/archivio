<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
require_once "../view/config.php";
require_once '../view/solr_client.php';
require_once "../class/solr_utilities.php";

if (isset($_POST)) {
    //print_r ($_POST);
    $anno = substr($_POST['data'], 0, 4);
    $id = getLastByIndex("DEL.".$anno) + 1;
    $codice_archivio = "DEL.".$anno.".".str_pad($id, 2, "0", STR_PAD_LEFT);
    
    $update = $client->createUpdate();
    
    $doc = $update->createDocument();
    $doc->codice_archivio = $codice_archivio;
    $doc->tipologia = "DELIBERA";
    $doc->argomento_breve = $_POST['argomento_breve'];
    $doc->tipo_delibera = $_POST['tipo_delibera'];
    $doc->data = $_POST['data'];
    $doc->anno = $anno;
    $doc->unanimita = $_POST['unanimita'];
    $doc->favorevoli = $_POST['favorevoli'];
    $doc->contrari = $_POST['contrari'];
    $doc->astenuti = $_POST['astenuti'];
    $doc->straordinaria = $_POST['straordinaria'];
    $doc->capitolo = $_POST['capitolo'];
    $doc->pagina = $_POST['pagina'];
    $doc->num_contestuale = $_POST['num_contestuale'];
    $doc->testo = $_POST['testo'];
    if ($_POST['tipo_delibera'] == "Seggio") {
        $doc->privato = 1;
    } else {
        $doc->privato = 0;
    }
    //$doc->_version_ = $version;
    
    $error = "";
    try {
        $update->addDocuments(array($doc));
        $update->addCommit();
        $result = $client->update($update);
    } catch (Solarium\Exception\HttpException $e) {
        $error = $e->getMessage();
    } 
    
    if ($error != "") {
        echo json_encode(array('error' => $error));  
    } else {
        echo json_encode(array('result' => "Volume ".$codice_archivio." inserito correttamente."));
    }
}
?>
