<?php
require('config.php');
require __DIR__.'/../vendor/autoload.php';

function summary() {
    global $client;

    //curl 'http://localhost:8985/solr/archivio2/select?q=tipologia:*&rows=0&facet=on&facet.field=tipologia'

    $member = new Member();
  
    $searchValue = "tipologia:*";
    $query = $client->createSelect();
    $query->setOmitHeader(false);

    $facetSet = $query->getFacetSet();
    $facetSet->createFacetField('tipologia')->setField('tipologia');
   
    $query->setRows(0);
   
    $query->setQuery($searchValue);
    $resultset = $client->select($query);
    $facet = $resultset->getFacetSet()->getFacet('tipologia');
    $facet_text = "";
    echo "<table>";
    foreach($facet as $value => $count) {
        $value = str_replace("_", " ", $value);
//        if (strlen($value) > 20) {
//            $l = strlen($value) - 17;
//            $offset = (strlen($value) - $l)/2;
//            $value = substr($value, 0, 10)."...".substr($value, -10, 10); //substr_replace($value, '...', $offset, $l);
//       }
        echo "<tr>";
        echo "<td>".$value."</td>"."<td>".$count."</td>";
        echo "</tr>";
            //        $facet_text .= $value . ' [' . $count . ']<br/>';
    }
    echo "</table>"; 
    //print ($facet_text);  
}

function numberRows() {
    global $client;

    $coreAdminQuery = $client->createCoreAdmin();
    $statusAction = $coreAdminQuery->createStatus();
    $statusAction->setCore($GLOBALS['SOLR_CORE']);
    $coreAdminQuery->setAction($statusAction);

    $response = $client->coreAdmin($coreAdminQuery);
    $statusResult = $response->getStatusResult();
    $_SESSION['rows'] = $statusResult->getNumberOfDocuments();
}
    
function ping() {
    global $client;
    $ping = $client->createPing();

    try {
        $result = $client->ping($ping);
    } catch (Exception $e) {
        echo "<div style='color:red' align='center'>Il server Solr non &egrave; attivo. Contattare l'amministratore del sistema.</div>";
    }
}    

$adapter = new Solarium\Core\Client\Adapter\Curl();
$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
?>