<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once "../class/Member.php";

$m = new Member();

//function customError($errno, $errstr) {
//  echo json_encode(array('error' => $errstr));
//  exit;
//}
//
//set_error_handler("customError");

function array2csv($data, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
{
    $f = fopen('php://memory', 'r+');
    
    foreach ($data as $key=>$value) {
       if (gettype($value) == "array") {
       	  unset($data[$key]);
       }
    }

    fputcsv($f, array_keys($data), $delimiter, $enclosure, $escape_char);
    fputcsv($f, $data, $delimiter, $enclosure, $escape_char);
    rewind($f);

    return stream_get_contents($f);
}

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

function curlOperationPOST($URL, $filename="") {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $filename);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/csv'));

    $data = curl_exec($ch); 							
    curl_close($ch);
    if ($data == false) {
        return json_decode(json_encode('{"solr_error":"Solr server non e` attivo. Contatta l\'amministratore."}'));
    } else {
        return $data;
    }			       
}

function upload_csv2($filename, $sep=",") {
    //$csv_file = file_get_contents($filename);
    //$line = fgets(fopen($filename, 'r'));
    //$sep = explode("codice_archivio", $line)[1][0];	

    return curlOperationPOST($GLOBALS['SOLR_URL'].'update?commit=true&separator='.$sep, $filename);
}

function upload_json_string($json_data) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'update?commit=true');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}


function curlOperationGET($URL) {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $URL);
   curl_setopt($ch, CURLOPT_HTTPGET, true);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                   			      'Accept: application/json'));

    $data = curl_exec($ch); 							
    curl_close($ch);
    if ($data == false) {
        return json_decode(json_encode('{"solr_error":"Solr server non e` attivo. Contatta l\'amministratore."}'));
    } else {
        return $data;
    }			       
}

function lookForDuplicates($resourceName) {
    $resourceName = urlencode($resourceName);
    $result = json_decode(curlOperationGET($GLOBALS['SOLR_URL'].'query?fl=codice_archivio&q=resourceName:"'.$resourceName.'"'), true);

    if (isset($result['solr_error'])) {
       return -2;
    } else if (isset($result['error']) or (!isset($result['error']) and $result['response']['numFound'] == 0)) {
       return 1;
    } else {
       return -1;
    }
}

function getLastByIndex_old($search) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL']."query?fl=codice_archivio&q=codice_archivio:".$search."*&rows=1");
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
    		     			       'Accept: application/json'));
					       
    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    return $result['response']['numFound'];
}

function getLastByIndex($search) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL']."query?fl=codice_archivio&q=codice_archivio:".$search."*&rows=10000000&wt=json&sort=codice_archivio+asc");
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
    		     			       'Accept: application/json'));
					       
    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    $codice_archivio_esploso = explode(".", end($result['response']['docs'])['codice_archivio']);
    $indice_codice_archivio = end($codice_archivio_esploso);
    
    return $indice_codice_archivio;
}

function listCodiceArchivio($isBiblio="book_categories", $selection="*") {
    global $m;

    $query = "q=(".$m->curlFlBiblio($isBiblio).")+AND+codice_archivio:".$selection;
    $result = curlOperationGET($GLOBALS['SOLR_URL'].'query?fl=codice_archivio&'.$query.'&sort=codice_archivio+asc&wt=json&rows='.$GLOBALS['MAX_ROWS']);

    return $result;
}

function removeItems($cod) {
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

function findBook2($cod) {
   global $m;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'query?q=codice_archivio:'.$cod.'&wt=json');
   curl_setopt($ch, CURLOPT_HTTPGET, true);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));     

   // FIXME MODIFICARE QUANDO CI SARA UN SOLO RISULTATO PER FORZA !!!!					  
   $result = json_decode(curl_exec($ch), true);
   curl_close($ch);

   $doc = $result['response']['docs'][0];
   
   $tip = $m->findTypeGroup($doc['tipologia']);

   $json_result = array("doc" => $doc, "type_group" => $tip);
   // FIXME GESTIONE ERRORI
   return json_encode($json_result);
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

function backup($upload_time, $all) {
   global $m;

   if ($all) {
       $ch = curl_init();
       // FIXME PROVARE AD USARE UNA SOLA DATA
       // FIXME SEPARATORE |
       $last_upload = date('Y-m-d\T\0\0\:\0\0\:\0\0\Z', strtotime($upload_time));
       $date_for_file = date('Y-m-d', strtotime($upload_time));
       curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'select?separator=%7C&fq=timestamp:['.$last_upload.'%20TO%20NOW]&q=*:*&wt=csv&rows='.$GLOBALS['MAX_ROWS']);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/csv; charset=utf-8'));
       $data = curl_exec($ch);
       curl_close($ch);
    
       if ($data == false) {
           return array("error" => 'Solr server non e` attivo. Contatta l\'amministratore.');
       }

       $csv_filename = $GLOBALS['BACKUP_DIR'].'backup_'.$date_for_file.'.csv';
       $csv_url = "http://localhost/backup/".'backup_'.$date_for_file.'.csv';
       file_put_contents($csv_filename, $data);

       return array("result" => '<a href="'.$csv_url.'">Catalogo  CSV</a>');
   } else {
       // ho tolto urlencode
       $q_string = ($m->curlFlBiblio());

       $ch = curl_init();
       // FIXME PROVARE AD USARE UNA SOLA DATA
       // FIXME SEPARATORE |
       $last_upload = date('Y-m-d\T\0\0\:\0\0\:\0\0\Z', strtotime($upload_time));
       $date_for_file = date('Y-m-d', strtotime($upload_time));

       curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'select?separator=%7C&fl=codice_archivio,titolo,sottotitolo,prima_responsabilita,anno,altre_responsabilita,luogo,tipologia,descrizione,ente,edizione,serie,soggetto,cdd,note,timestamp&sort=codice_archivio%20asc&fq=timestamp:['.$last_upload.'%20TO%20NOW]&q='.$q_string.'&wt=csv&rows='.$GLOBALS['MAX_ROWS']);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/csv; charset=utf-8'));
       $data = curl_exec($ch);
       curl_close($ch);
    
       if ($data == false) {
           return array("error" => 'Solr server non e` attivo. Contatta l\'amministratore.');
       }

       $csv_filename = $GLOBALS['BACKUP_DIR'].'backup_'.$date_for_file.'.csv';
       $csv_url = "http://localhost/backup/".'backup_'.$date_for_file.'.csv';
       //print_r(mb_detect_encoding($data, mb_detect_order(), true));
       file_put_contents($csv_filename, iconv(mb_detect_encoding($data, mb_detect_order(), true), "UTF-8", $data)); 
       //utf8_encode($data));
    
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'select?fl=codice_archivio&fq=timestamp:['.$last_upload.'%20TO%20NOW]&q=*:*&wt=json&rows='.$GLOBALS['MAX_ROWS']);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
       $data = json_decode(curl_exec($ch), true);
       curl_close($ch);
    
       $zip = new ZipArchive();
       $zip_filename =  $GLOBALS['BACKUP_DIR'].'cover_'.$date_for_file.'.zip';
       $zip_url = "http://localhost/backup/".'cover_'.$date_for_file.'.zip';
       if ($zip->open($zip_filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
          foreach($data['response']['docs'] as $entry) {
          	$zip->addFile($GLOBALS['COVER_DIR'].$entry['codice_archivio'].".JPG", $entry['codice_archivio'].".JPG");
          }
          $zip->close();
       } else {
          echo json_encode(array("error"=>"Non posso aprire ".$zip_filename));
          exit;
       }
    
       return array("result" => '<a href="'.$csv_url.'">Catalogo  CSV</a>&nbsp;'.' <a href="'.$zip_url.'">Copertine ZIP</a><br>');
   }
}

function restore($file, $isCsv) {
    if ($isCsv) {
        if ($file["size"] > 0) {
            $fileName = $file["tmp_name"];
            $csv_file = file_get_contents($fileName);
    	    
	    //return curlOperationPOST($GLOBALS['SOLR_URL'].'update?commit=true&separator=%7C', $csv_file);

            $ch = curl_init();
    	    curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'update?commit=true&separator=%7C');
    	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	    curl_setopt($ch, CURLOPT_POST,           true);
    	    curl_setopt($ch, CURLOPT_POSTFIELDS,     $csv_file); 
    	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:text/csv; charset=utf-8'));
    
    	    $data = curl_exec ($ch);
    	    curl_close($ch);
	    
	    if ($data == false) {
                return json_encode(array("error" => 'Solr server non e` attivo. Contatta l\'amministratore.'));
       	    }
    
    	    return $data;
      	}
    } else {
        if ($_FILES["size"] > 0) {
            $fileName = $file["tmp_name"];
      	    $zip = new ZipArchive;
    	    $res = $zip->open($fileName);
    	    if ($res == true) {
      	        $zip->extractTo($GLOBALS['COVER_DIR']);
    		$zip->close();
	      	return json_encode(array("result"=>"Copertine aggiornate !"));
    	    } else {
	      	return json_encode(array("error"=>"Problemi nell'estrazione delle copertine ".$zip_filename));
    	    }
      	}     
    }
}

if (isset($_POST['func'])) {
  if ($_POST['func'] == 'find') {
     echo json_encode(findBook($_POST['sel']));
  } elseif ($_POST['func'] == 'find2') {
     echo json_encode(findBook2($_POST['sel']));
  } elseif ($_POST['func'] == 'backup') {
    if (isset($_POST['do_biblio'])) {
       echo json_encode(backup($_POST['last_upload'], FALSE));
    } else {
       echo json_encode(backup($_POST['last_upload'], TRUE));
    }
  } elseif ($_POST['func'] == 'restore') {
      if (isset($_FILES['filecsv'])) {	 
          print_r (restore($_FILES['filecsv'], TRUE));
      } elseif (isset($_FILES['filezip'])) {
          print_r (restore($_FILES['filezip'], FALSE));
      }
  }
}


//print_r(listCodiceArchivio("video"));
?>