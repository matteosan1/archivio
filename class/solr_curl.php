<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";

function customError($errno, $errstr) {
  echo json_encode(array('error' => $errstr));
  exit;
}

set_error_handler("customError");

function upload_csv($filename) {
    $csv_file = file_get_contents($filename);
    $line = fgets(fopen($filename, 'r'));
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

function findBook($cod) {

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'query?q=codice_archivio:'.$cod.'&wt=json');
   curl_setopt($ch, CURLOPT_HTTPGET, true);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));     

   // FIXME MODIFICARE QUANDO CI SARA UN SOLO RISULTATO PER FORZA !!!!					  
   $result = json_decode(curl_exec($ch), true);
   curl_close($ch);

   return $result['response']['docs'][0];
}

function backup($upload_time) {
   $ch = curl_init();
   // FIXME PROVARE AD USARE UNA SOLA DATA
   // FIXME SEPARATORE |
   // FIXME _version_ = 0 ?!?!?!?
   $last_upload = date('Y-m-d\T\0\0\:\0\0\:\0\0\Z', strtotime($upload_time));
   $date_for_file = date('Y-m-d', strtotime($upload_time));
   curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'select?fl=codice_archivio,titolo,sottotitolo,prima_responsabilita,anno,altre_responsabilita,luogo,tipologia,descrizione,ente,edizione,serie,soggetto,cdd,note&fq=timestamp:['.$last_upload.'%20TO%20NOW]&q=*:*&wt=csv&rows='.$GLOBALS['MAX_ROWS']);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
   $data = curl_exec($ch);
   curl_close($ch);

   $csv_filename = $GLOBALS['UPLOAD_DIR'].'backup_'.$date_for_file.'.csv';
   $csv_url = "http://localhost/upload/".'backup_'.$date_for_file.'.csv';
   file_put_contents($csv_filename, $data);

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'select?fl=codice_archivio&fq=timestamp:['.$last_upload.'%20TO%20NOW]&q=*:*&wt=json&rows='.$GLOBALS['MAX_ROWS']);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
   $data = json_decode(curl_exec($ch), true);
   curl_close($ch);

   $zip = new ZipArchive();
   $zip_filename =  $GLOBALS['UPLOAD_DIR'].'cover_'.$date_for_file.'.zip';
   $zip_url = "http://localhost/upload/".'cover_'.$date_for_file.'.zip';
   if ($zip->open($zip_filename, ZipArchive::CREATE) === TRUE) {
      foreach($data['response']['docs'] as $entry) {
      	$zip->addFile($GLOBALS['COVER_DIR'].$entry['codice_archivio'].".JPG");
      }
      $zip->close();
   } else {
      echo json_encode(array("error"=>"Non posso aprire ".$zip_filename));
      exit;
   }

   return array("result" => '<a href="'.$csv_url.'">Catalogo  CSV</a><br>'.' <a href="'.$zip_url.'">Copertine ZIP</a><br>');
}

if (isset($_POST['func'])) {
  if ($_POST['func'] == 'find') {
     echo json_encode(findBook($_POST['sel']));
  } elseif ($_POST['func'] == 'backup') {
     echo json_encode(backup($_POST['last_upload']));
  }
}

?>