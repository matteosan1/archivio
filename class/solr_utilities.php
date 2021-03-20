<?php
require_once "../view/config.php";
require_once "../view/solr_client.php";

function lookForDuplicates($resourceName) {
    global $client;

    $query = $client->createSelect();

    $query->setQuery('resourceName:'.$resourceName);
    $query->setFields('codice_archivio');

    $error = "";
    try {
       $resultset = $client->select($query);
       $docs = $resultset->getDocuments();
    } catch (Solarium\Exception\HttpException $e) {
       $error = $e->getMessage();
       return -2;
    } catch (Exception $e) {
       $error = $e->getMessage();
       return -2;
    }

    if ($resultset->getNumFound() == 0) {
       return 1;
    } else {
       return -1;
    }
}


function getLastByIndex($search) {
    global $client;
    
    $query = $client->createSelect();

    $query->setQuery('codice_archivio:'.$search.'*');
    $query->setFields('codice_archivio');
    $query->addSort('codice_archivio', $query::SORT_ASC);    

    $resultset = $client->select($query);
    $docs = $resultset->getDocuments();
    $document = end($docs);

    if ($resultset->getNumFound() == 0) {
       $indice_codice_archivio = 0;
    } else {
       $codice_archivio_esploso = explode(".", $document['codice_archivio']);
       $indice_codice_archivio = end($codice_archivio_esploso);
    }
    
    return $indice_codice_archivio;
}


function findBook($cod) {
    global $client;
    
    $query = $client->createSelect();
    $query->setQuery('codice_archivio:'.$cod);
    $resultset = $client->select($query);
    if ($resultset->getNumFound() == 1) {
        $doc = $resultset->getDocuments();
        $res = array();
        foreach ($doc[0] as $field => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
    
            $res[$field]  = $value;
        }
        
        return $res;
    } else {
        echo "ERRORE CI SONO TROPPI DOCUMENTI";
    }
}


if (isset($_POST['func'])) {
  if ($_POST['func'] == 'find') {
     echo json_encode(findBook($_POST['sel']));
  } 
}
?>

