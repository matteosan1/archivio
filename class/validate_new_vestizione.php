<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once '../view/solr_client.php';
require_once "../class/solr_utilities.php";

function insertMonturato() {
    global $client;
    
    $update = $client->createUpdate();
    $doc = $update->createDocument();
    $doc->codice_archivio = $_POST['codice_archivio'];
    $doc->tipologia = $_POST['tipologia'];
    $doc->anno = $_POST['anno'];
    $doc->nome_cognome = $_POST['nome_cognome'];
    $doc->ruolo = $_POST['ruolo'];
    $doc->evento = $_POST['evento'];
    $doc->data = $_POST['data'];
    $doc->privato = 0;
    
    $error = "";
    try {
        $update->addDocuments(array($doc));
        $update->addCommit();
        $result = $client->update($update);
    } catch (Solarium\Exception\HttpException $e) {
        $error = $e->getMessage();
    }
    
    return $error;
}

if (isset($_POST)) {
    $error = "";
    $id = getLastByIndex("MONT.".$_POST['anno']) + 1;
    $_POST['codice_archivio'] = "MONT.".$_POST['anno'].".".str_pad($id, 4, "0", STR_PAD_LEFT);
    
    if ($_FILES['comparsa']['error'] == 0) {
        $filename = $_FILES['comparsa']['tmp_name'];
        $handler = fopen($filename, 'r');
        
        while($data = fgetcsv($handler)) {
            $users[] = $data;
        }
        
        fclose($handler);
        
        $columns = array_map('strtolower', $users[0]);
        
        if ("nome" == $columns[0] && "comparsa" == $columns[1]) {
            for ($i=1; $i<count($users); $i++) {
                $_POST['codice_archivio'] = "MONT.".$_POST['anno'].".".str_pad($id, 4, "0", STR_PAD_LEFT);
                $_POST['nome_cognome'] = $users[$i][0];
                $_POST['ruolo'] = $users[$i][1];                   
                $error .= insertMonturato();
                $id = $id + 1;
            }
        } else {
            echo json_encode(array('error' => 'Il CSV deve avere due colonne: nome, comparsa'));
        }
    } else {
        $error .= insertMonturato();
    }
    
    if ($error != "") {
        echo json_encode(array('error' => $error));  
    } else {
        echo json_encode(array('result' => "Monturati inseriti correttamente."));
    } 
}
?>
