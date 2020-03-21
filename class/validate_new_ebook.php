<?php
require_once "../view/config.php";
require_once "../class/solr_curl.php";

if (isset($_POST)) {
   $tmp_filename = $_FILES['edoc']['tmp_name'];

   if (isset($_POST['do_ocr'])) {
      $command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr -l ita pdf";
      exec($command);
      $tmp_filename = $GLOBALS['UPLOAD_DIR']."ocr.pdf";
      $ext = "pdf";
   } else {
      $tmp = explode(".", $_FILES['edoc']['name']);
      $ext = end($tmp);
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
   $index = getLastByIndex($prefix);
   $index = $index['response']['numFound'] + 1;
   $ca = $prefix.".".str_pad($index, 5, "0", STR_PAD_LEFT);

   $data["codice_archivio"] = $ca;
   $data["tipologia"] = $_POST['tipologia'];
   $data["note"] = $_POST['note'];
   $data["resourceName"] = basename($_FILES['edoc']['name']);
   if (isset($data['X-TIKA:content'])) {
      $data['text'] = $data['X-TIKA:content'];
      unset($data['X-TIKA:content']);
   }
   
   print_r ($data);

//   $ret = upload_json_string($data);
//   $target_directory = $GLOBALS['EDOC_DIR'].$ca.".".end($ext);
//   if (!move_uploaded_file($tmp_filename, $target_directory)) {
//       echo json_encode(array('error' => "Errore nella fase di copia dell'edoc."));
//       exit;
//   }
}
// FIXME LOOK FOR DUPLICATES
// FIXME ADD THUMBNAILS !!!
?>
