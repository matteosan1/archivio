<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once "../class/solr_curl.php";

function customError($errno, $errstr) {
  echo json_encode(array('error' => $errstr));
  exit;
}

set_error_handler("customError");

if (isset($_POST)) {
   print_r ($_POST);
   exit;
   $tmp_filename = $_FILES['edoc']['tmp_name'];

   if ($_POST['do_ocr'] == "OCR") {
      $command = "/usr/local/bin/tesseract ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr -l ita pdf";
      exec($command);
      $tmp_filename = $GLOBALS['UPLOAD_DIR']."ocr.pdf";
   }
   
   $ext = explode(".", $tmp_filename);
   $prefix = substr($_POST['tipologia'], 0, 3);
   
   $command = "java -jar tika... -J ".$tmp_filename;
   exec($command, $output);

   $data = json_decode($output, true);
   $OldDate = $data['created']; //"2011-09-30";
   $oldDateUnix = strtotime($OldDate);
   $year = date("Y", $oldDateUnix);
   $index = getLastByYear($prefix.".".$year);
   $ca = $prefix.".".$year.".".str_pad($index, 3, "0", STR_PAD_LEFT).".".end($ext);
   array_push($data, "codice_archivio" => $ca);

   $target_directory = $GLOBALS['EDOC_DIR'].$ca.".".$ext;
   if (!move_uploaded_file($tmp_filename, $target_directory)) {
       echo json_encode(array('error' => "Errore nella fase di copia dell'edoc."));
       exit;
   }
}
?>
