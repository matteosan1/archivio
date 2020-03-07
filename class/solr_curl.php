<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";

function upload_csv($filename) {
    $csv_file = file_get_contents($filename);
    $line = fgets(fopen($_FILES['filecsv']['tmp_name'], 'r'));
    $sep = explode("codice_archivio", $line)[1][0];	
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'update?commit=true&separator='.$sep.'');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $csv_file);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/csv'));

    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $result;
}

function listCodiceArchivio() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'query?fl=codice_archivio&q=*&sort=codice_archivio+asc&wt=json&rows='.$GLOBALS['MAX_ROWS']);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
    		     			       'Accept: application/json'));	 

    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $result;
}

function removeBook($cod) {
   $result = "";
   $data = array("delete" => array("query" => "codice_archivio:".$cod)); 
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'update?commit=true');
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		
   $result = json_decode(curl_exec($ch), true);
   curl_close($ch);

   return $result;
}

?>