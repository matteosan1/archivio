<?php

require_once "../view/config.php";
require_once "../class/solr_utilities.php";
require_once "../view/solr_client.php";
require_once "../class/resize_image.php";
require_once "../class/Member.php";
    
function process_ebook($i) {
    global $client;
    
    if ($_POST['tipologia'] == "----") {
        $prefix = "UNK";
    } else {
        $prefix = substr($_POST['tipologia'], 0, 3);
    }
      
    $orig_ext = explode(".", $_FILES['edoc']['name'][$i]);
    $orig_ext = strtolower(end($orig_ext));

    $domove = 0;
    if (isset($_POST['do_merge'])) {
        $tmp_filename = $GLOBALS['UPLOAD_DIR']."ocr.pdf";
   	$ext = "pdf";
    } else if (isset($_POST['do_ocr']) and ($orig_ext == "jpeg" or $orig_ext == "jpg" or
      	      			       	    $orig_ext == "tiff" or $orig_ext == "tif")) {
     	$tmp_filename = $GLOBALS['UPLOAD_DIR']."ocr".$i.".pdf";
      	$ext = "pdf";
    } else {
      	$tmp_filename = $_FILES['edoc']['tmp_name'][$i];
      	$tmp = explode(".", $_FILES['edoc']['name'][$i]);
        $ext = $orig_ext;
	$domove = 1;
    }
       
    $command = "/usr/bin/java -jar ".$GLOBALS['TIKA_APP']." -j -t -J ".$tmp_filename;
    $output = shell_exec($command);
    $data = json_decode($output, true)[0];
    
    $index = getLastByIndex($prefix) + 1;
    $ca = $prefix.".".str_pad($index, 5, "0", STR_PAD_LEFT);

    $update = $client->createUpdate();
    $doc = $update->createDocument();

    $doc->codice_archivio = $ca;
    $doc->tipologia = $_POST['tipologia'];
    $doc->note = $_POST['note'];
    $doc->resourceName = basename($_FILES['edoc']['name'][$i]);

    if (isset($data['X-TIKA:content'])) {
        $text = trim(preg_replace('/(\t){1,}/', '', $data['X-TIKA:content']));
        $text = trim(preg_replace('/(\n){2,}/', "\n", $text));
        $data['text'] = $text; 
        unset($data['X-TIKA:content']);
    }

    if (isset($data['X-Parsed-By'])) {
        unset($data['X-Parsed-By']);
    }

    foreach ($data as $key => $value) {
       $doc->$key = $value;
    }

    $error = "";
    try {
       $update->addDocuments(array($doc));
       $update->addCommit();
       $result = $client->update($update);
    } catch (Solarium\Exception\HttpException $e) {
        $error = $e->getMessage();
    }

    $ret = json_decode(upload_csv2(array2csv($data)), true);

    //if (isset($_POST['do_ocr'])) {
    //	  unlink($tmp_filename);
    //}

    if ($error != 0) {
        array_push($arr_result['error'], $error);
        return;
    }
}

function convertScan($ca) {
    if ($_FILES['scan']['error'] == 0) {
    
        $orig_ext = explode(".", $_FILES['scan']['name'][$i]);
        $orig_ext = strtolower(end($orig_ext));

        if ($orig_ext == "jpg" or $orig_ext == "jpeg") {
            $resize = new ResizeImage($_FILES['scan']['tmp_name']);
            $resize->resizeTo(200, 200, 'maxHeight');
            $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$ca.".".strtoupper($orig_ext));
        } else if ($orig_ext == "pdf") {
            $command = $GLOBALS['PDF2IMAGE_BIN']." -f 1 -l 1 -dev jpeg ".$_FILES['scan']['tmp_name'];
            $output = shell_exec($command);
 	        $resize = new ResizeImage($_FILES['scan']['tmp_name'][$i]."_1.jpg"); // _1.jpg because it is added by pdf2image
      	    $resize->resizeTo(200, 200, 'maxHeight');
      	    $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$ca.".".strtoupper($orig_ext));
        } else if ($orig_ext == "tif" or $orig_ext == "tiff") {      	
            $command = $GLOBALS['CONVERT_BIN']." ".$_FILES['scan']['tmp_name']."[0] -resize x200 ".$GLOBALS['THUMBNAILS_DIR'].$ca.".JPG";
            $output = shell_exec($command);
        }
 
        $target_directory = $GLOBALS['EDOC_DIR'].$_FILES['scan']['name'];

        if (!move_uploaded_file($tmp_filename, $target_directory)) {
	        return array($arr_result['error'], "Errore nella fase di copia di ".$_FILES['scan']['name']);
            return;
        }
    }
}

if (isset($_POST)) {
    $m = new Member();
    $prefix = $m->getPrefisso($_POST["tipologia"]);
    $id = getLastByIndex($prefix.".".$_POST['anno']) + 1;
    $codice_archivio = $prefix.".".$_POST['anno'].".".str_pad($id, 2, "0", STR_PAD_LEFT);

    $update = $client->createUpdate();
    $doc = $update->createDocument();
    
    $doc->codice_archivio = $codice_archivio;
    $doc->tipologia = $_POST['tipologia'];
    $doc->resourceName = basename($_FILES['scan']['name']);
    
    if ($_POST['tipologia'] == "PERGAMENA") {
        $doc->descrizione = $_POST['descrizione']; 
        $doc->tecnica = $_POST['descrizione']; 	
        $doc->autore = $_POST['descrizione']; 
        $doc->anno = $_POST['descrizione']; 
        $doc->dimensioni = $_POST['descrizione']; 
        $doc->note = $_POST['descrizione'];
    } else if ($_POST['tipologia'] == "BOZZETTO") {
        $doc->categoria = $_POST['categoria']; 
        $doc->descrizione = $_POST['descrizione']; 
        $doc->tecnica = $_POST['descrizione']; 	
        $doc->autore = $_POST['descrizione']; 
        $doc->anno = $_POST['descrizione']; 
        $doc->dimensioni = $_POST['descrizione']; 
        $doc->note = $_POST['descrizione']; 
    }
    
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
        exit;
    } else {
        $error = convertScan($codice_archivio);
        if ($error != "") {
            echo json_encode(array('error' => $error));
            exit;
        }

        echo json_encode(array('result' => "Volume ".$codice_archivio." inserito correttamente."));
        return;
    }

    return;
    
   $arr_check = array();
   $arr_result = array("result"=>array(), "error"=>array());
   $countfiles = count($_FILES['edoc']['name']);
   for ($i=0; $i<$countfiles; $i++) {

      $tmp_filename = $_FILES['edoc']['tmp_name'][$i];
      $resourceName = basename($_FILES['edoc']['name'][$i]);
      $tmp = explode(".", $_FILES['edoc']['name'][$i]);

      // FIXME CONTROLLARE DUPLICATI NEL CASO DI multivalued...
      $duplicate = lookForDuplicates($resourceName);
      if ($duplicate == -2) {
      	 echo json_encode(array("error" => "Il server SOLR non &egrave; attivo. Contattare l'amministratore."));
	 exit;
      }

      if (!isset($_POST['do_merge']) and $duplicate == -1) {
      	 array_push($arr_result['error'], "Il documento ".$resourceName." &egrave; gi&agrave; stato indicizzato");
	 array_push($arr_check, -1);
	 continue;
      } else {
      	 array_push($arr_check, 1);
      }

      $ext = strtolower(end($tmp));
      if (isset($_POST['do_ocr']) and ($ext == "jpeg" or $ext == "jpg" or $ext == "tiff" or $ext == "tif")) {
      	 $command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr".$i." -l ita pdf";
      	 exec($command);
      }
   }

   if (isset($_POST['do_merge'])) {
      $command = $GLOBALS['MERGE_PDF_BIN'].$GLOBALS['UPLOAD_DIR']."ocr.pdf ";
      for ($i=0; $i<$countfiles; $i++) {
          $command = $command.$GLOBALS['UPLOAD_DIR']."ocr".$i.".pdf ";
      }
      exec($command, $output, $result);

      process_ebook(0);
    } else {   
       for ($i=0; $i<$countfiles; $i++) {

       	   if (!isset($_POST['do_merge']) and $arr_check[$i] == -1) {
       	      continue;
       	   }

	   process_ebook($i);
       }
    }

    if (count($arr_result['error']) != 0) {
       echo json_encode(array("error" => implode("<br>", $arr_result['error'])));
    } else {
       echo json_encode(array("result"=> "Tutti i documenti sono stati inseriti correttamente.")); 
    }
}
?>
