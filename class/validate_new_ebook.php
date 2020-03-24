<?php

// FIXME TOGLIERE OCR PER CERTI TIPI DI FILE

require_once "../view/config.php";
require_once "../class/solr_curl.php";
require_once "../class/resize_image.php";

function array2csv($data, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
{
    $f = fopen('php://memory', 'r+');
    
    fputcsv($f, array_keys($data), $delimiter, $enclosure, $escape_char);
    fputcsv($f, $data, $delimiter, $enclosure, $escape_char);
    rewind($f);

    return stream_get_contents($f);
}

if (isset($_POST)) {

   $countfiles = count($_FILES['edoc']['name']);
   for ($i=0; $i<$countfiles; $i++) {
      $resourceName = basename($_FILES['edoc']['name'][$i]);
      // FIXME CONTROLLARE DUPLICATI NEL CASO DI multivalued...
      //$pippo = lookForEDocDuplicates($resourceName);
      //print_r ($pippo);
      //exit;
      if (lookForEDocDuplicates($resourceName)) {
      	 echo "Il documento e` gia` stato indicizzato";
      	 exit();
      }
   
      $tmp_filename = $_FILES['edoc']['tmp_name'][$i];
      $orig_ext = explode(".", $_FILES['edoc']['name'][$i]);

      if (isset($_POST['do_ocr'])) {
      	 $command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr".$i." -l ita pdf";
      	 exec($command);
      	 $tmp_filename = $GLOBALS['UPLOAD_DIR']."ocr".$i.".pdf";
      	 $ext = "pdf";
      } else {
      	 $tmp = explode(".", $_FILES['edoc']['name'][$i]);
         $ext = strtolower(end($tmp));
      }
   }
   
   if (isset($_POST['do_merge'])) {
      // FIXME FAI IL MERGE su ocr0
   }
   
   if ($_POST['tipologia'] == "----") {
      $prefix = "UNK";
   } else {
      $prefix = substr($_POST['tipologia'], 0, 3);
   }

   $command = "/usr/bin/java -jar ".$GLOBALS['TIKA_APP']." -j -t -J ".$tmp_filename;
   $output = shell_exec($command);//, $output, $result);
   $data = json_decode($output, true)[0];

//   $OldDate = $data['created']; //"2011-09-30";
//   $oldDateUnix = strtotime($OldDate);
//   $year = date("Y", $oldDateUnix);
   $index = getLastByIndex($prefix) + 1;
   $ca = $prefix.".".str_pad($index, 5, "0", STR_PAD_LEFT);

   $data["codice_archivio"] = $ca;
   $data["tipologia"] = $_POST['tipologia'];
   $data["note"] = $_POST['note'];
   $data["resourceName"] = $resourceName;
   if (isset($data['X-TIKA:content'])) {
      $data['text'] = $data['X-TIKA:content'];
      unset($data['X-TIKA:content']);
   }

   if (isset($data['X-Parsed-By'])) {
      unset($data['X-Parsed-By']);
   }

   $orig_ext = strtolower(end($orig_ext));
   if ($orig_ext == "jpg" or $orig_ext == "jpeg") {
      $resize = new ResizeImage($_FILES['edoc']['tmp_name']);
      $resize->resizeTo(200, 200, 'maxWidth');
      $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$ca.".".strtoupper($orig_ext));
   }

   $target_directory = $GLOBALS['EDOC_DIR'].$ca.".".$ext;
   $moved = rename($tmp_filename, $target_directory);
   if ($moved != 1) {
       echo json_encode(array('error' => "Errore nella fase di copia dell'edoc."));
       exit;
   }

   $ret = upload_csv2(array2csv($data));
   if (isset($_POST['do_ocr'])) {
       unlink($tmp_filename);
   }

   if ($ret['responseHeader']['status'] != 0) {
      echo json_encode(array('error' => $ret['error']['msg']));  
   } else {
      echo json_encode(array('result' => "eDoc ".$data['codice_archivio']." inserito correttamente."));
  }
}
?>
