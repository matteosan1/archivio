<?php
require_once "../view/config.php";

if (isset($_POST)) {
   $ch = curl_init();

   curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'query?q=codice_archivio:'.$_POST['sel'].'&wt=json');
   curl_setopt($ch, CURLOPT_HTTPGET, true);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                                              'Accept: application/json'));     

// FIXME MODIFICARE QUANDO CI SARA UN SOLO RISULTATO PER FORZA !!!!					  
   $result = json_decode(curl_exec($ch), true);
   curl_close($ch);

   echo json_encode($result['response']['docs'][0]);
}
exit;
?>
