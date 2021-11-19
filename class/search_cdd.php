<?php
require_once "../view/config.php";

$ch = curl_init();

$title = "I promessi sposi";//curl_escape($ch, $_POST['title']);
$author = "Alessandro Manzoni";//curl_escape($ch, $_POST['author']);

$URL = $GLOBALS['OCLC_URL']."?title=".$title."&author=".$author;
print_r ($URL);
exit;    
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', 'Accept: application/xml'));

$data = curl_exec($ch);
curl_close($ch);

if ($data == false) {
    echo json_encode(array("error"=>"OCLC server down. Passa alla ricerca manuale."));
} else {
    $xml = simplexml_load_string($data);
    print_r($xml);
    exit;
    if (isset($xml->workCount)) {
       echo $xml->workCount;
    } else {
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
}
?>