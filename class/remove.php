<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once '../view/config.php';
require_once '../view/solr_client.php';

if (isset($_POST)) {
    //print_r ($_POST);
    //echo json_encode(array('result' => "Volumi rimossi con successo in ms."));
    //return;
    if (isset($_POST['codici'])) {
        $update = $client->createUpdate();
        $codici = $_POST['codici'];
        foreach ($codici as $cod) { 
            $update->addDeleteById($cod);
        }
        
        $update->addCommit();
        $error = "";
        try {
            $result = $client->update($update);
        } catch (Solarium\Exception\HttpException $e) {
            $error = $e->getMessage();
        } 
        
        if ($error == "") {
            //if ($_POST['type'] == "image") {
            //    
            //} else if ($_POST['type'] == "video"}
            //} else if ($_POST['type'] == "ebook"}
            //} else if ($_POST['type'] == "book"}
            //}	  
            echo json_encode(array('result' => "Record rimossi con successo in ".$result->getQueryTime(). "ms."));
        } else {
            echo json_encode(array('error' => $error));
        }
    }
}
?>
