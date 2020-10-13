<?php
require_once "../view/config.php";

$ch = curl_init();
$title = curl_escape($ch,$_POST['title']);
$author = curl_escape($ch, $_POST['author']);

$URL = $GLOBALS['OCLC_URL']."?title=".$title."&author=".$author."&summary=false";

curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', 'Accept: application/xml'));

$data = curl_exec($ch);
curl_close($ch);

if ($data == false) {
    echo json_encode(array("error"=>"OCLC server down. Contatta l\'amministratore."));
} else {
    $xml = simplexml_load_string($data);
    if (isset($xml->recommendations->ddc->mostRecent)) {
       echo json_encode(array("cdd"=>$xml->recommendations->ddc->mostRecent['sfa']));
    } else if (isset($xml->recommendations->dcc->mostPopular)) {
       echo json_encode(array("cdd"=>$xml->recommendations->ddc->mostPopular['sfa']));
    } else if (isset($xml->recommendations->dcc->latestEdition)) {
       echo json_encode(array("cdd"=>$xml->recommendations->ddc->latestEdition['sfa']));
    } else {
       echo json_encode(array("error"=>"Volume non trovato."));
    }
}

?>