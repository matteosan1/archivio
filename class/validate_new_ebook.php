<?php

// FIXME TOGLIERE OCR PER CERTI TIPI DI FILE

require_once "../view/config.php";
require_once "../class/solr_curl.php";
require_once "../class/resize_image.php";

if (isset($_POST)) {

   $countfiles = count($_FILES['edoc']['name']);
   for ($i=0; $i<$countfiles; $i++) {
      $resourceName = basename($_FILES['edoc']['name'][$i]);
      // FIXME CONTROLLARE DUPLICATI NEL CASO DI multivalued...
      //$pippo = lookForEDoDuplicates($resourceName);
      //print_r ($pippo);
      //exit;
      //if (lookForDuplicates($resourceName)) {
      //	 echo "Il documento e` gia` stato indicizzato";
      //	 exit();
      //}
   
      if (isset($_POST['do_ocr'])) {
      	 $command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr".$i." -l ita pdf";
      	 exec($command);
      	 $tmp_filename = $GLOBALS['UPLOAD_DIR']."ocr".$i.".pdf";
      	 $ext = "pdf";
      } else {
         $tmp_filename = $_FILES['edoc']['tmp_name'][$i];
      	 $tmp = explode(".", $_FILES['edoc']['name'][$i]);
         $ext = strtolower(end($tmp));
      }
   }
   
   if (isset($_POST['do_merge'])) {
      $command = $GLOBALS['MERGE_PDF_BIN'].$GLOBALS['UPLOAD_DIR']."ocr0.pdf ";
      for ($i=0; $i<$countfiles; $i++) {
          $command = $command."ocr".$i.".pdf ";
      }
      exec($command);
      $countfiles = 1;
   }
   
   for ($i=0; $i<$countfiles; $i++) {
       if ($_POST['tipologia'] == "----") {
       	  $prefix = "UNK";
       } else {
          $prefix = substr($_POST['tipologia'], 0, 3);
       }
      
       $orig_ext = explode(".", $_FILES['edoc']['name'][$i]);

       if (isset($_POST['do_ocr'])) {
       	  $tmp_filename = $GLOBALS['UPLOAD_DIR']."ocr".$i.".pdf";
      	  $ext = "pdf";
       } else {
       	  $tmp_filename = $_FILES['edoc']['tmp_name'][$i];
      	  $tmp = explode(".", $_FILES['edoc']['name'][$i]);
          $ext = strtolower(end($tmp));
       }

       $command = "/usr/bin/java -jar ".$GLOBALS['TIKA_APP']." -j -t -J ".$tmp_filename;
       $output = shell_exec($command);//, $output, $result);
       $data = json_decode($output, true)[0];

       $index = getLastByIndex($prefix) + 1;
       $ca = $prefix.".".str_pad($index, 5, "0", STR_PAD_LEFT);

       $data["codice_archivio"] = $ca;
       $data["tipologia"] = $_POST['tipologia'];
       $data["note"] = $_POST['note'];
       $data["resourceName"] = $resourceName;
       if (isset($data['X-TIKA:content'])) {
          $text = trim(preg_replace('/(\t){1,}/', '', $data['X-TIKA:content']));
          $text = trim(preg_replace('/(\n){2,}/', "\n", $text));
       	  $data['text'] = $text; 
      	  unset($data['X-TIKA:content']);
       }

       if (isset($data['X-Parsed-By'])) {
       	  unset($data['X-Parsed-By']);
       }

       $orig_ext = strtolower(end($orig_ext));
       if ($orig_ext == "jpg" or $orig_ext == "jpeg") {
       	  $resize = new ResizeImage($_FILES['edoc']['tmp_name'][$i]);
      	  $resize->resizeTo(200, 200, 'maxHeight');
      	  $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$ca.".".strtoupper($orig_ext));
       } else if ($orig_ext == "pdf") {
          $command = $GLOBALS['PDF2IMAGE_BIN']." -f 1 -l 1 -dev jpeg ".$_FILES['edoc']['tmp_name'][$i];
          $output = shell_exec($command);
	  $resize = new ResizeImage($_FILES['edoc']['tmp_name'][$i]."_1.jpg");
      	  $resize->resizeTo(200, 200, 'maxHeight');
      	  $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$ca.".".strtoupper($orig_ext));
       } else if ($orig_ext == "tif" or $orig_ext == "tiff") {
       	  $command = $GLOBALS['CONVERT_BIN']." ".$_FILES['edoc']['tmp_name'][$i]." x200 ".$GLOBALS['THUMBNAILS_DIR'].$ca.".JPG";
          $output = shell_exec($command);
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
}
?>
