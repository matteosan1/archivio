<?php

require_once "../view/config.php";
require_once "../class/Member.php";
require_once "../class/solr_curl.php";

//define("DB_MSG_ERROR", 'Could not connect!<br />Please contact the site\'s administrator.');

$selection = $_POST['value']."*";

$json = json_decode(listCodiceArchivio("book_categories", $selection), true);

$size = count($json['response']['docs']);
if ($size > 14) {
   $size = 15;
}

if (isset($json['solr_error'])) {
   echo "<div style='color:red'>Il server Solr non &egrave; attivo. Contattare l'amministratore del sistema.</div>";
} else {
  if ($_POST['type'] == "update") {
     $tmp = array();
     $tmp[] = "----";
     foreach($json['response']['docs'] as $select) {
          $tmp[] = $select['codice_archivio'];
     }
     echo json_encode($tmp);
  } else {
    echo '<select width=100px id="codici[]" name="codici[]" size="'.$size.'" multiple>';
    foreach ($json['response']['docs'] as $select) {
      echo '<option value="'.$select['codice_archivio'].'">'.$select['codice_archivio'].'</option>';
    }  
    echo '</select><br><br>';
  }
}

?>