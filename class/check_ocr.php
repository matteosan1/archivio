<?php
require_once "../view/config.php";

if (isset($_POST)) {
   $tmp_filename = $_FILES['edoc']['tmp_name'][0];
   $command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr -l ita";
   exec($command);
   $ocr = file_get_contents($GLOBALS['UPLOAD_DIR']."/ocr.txt");
   $ocr = preg_replace('/^[ \t]*[\r\n]+/m', '', $ocr);
   echo $ocr;
}
?>
