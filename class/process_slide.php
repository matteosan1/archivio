<?php
require_once "../class/Member.php";
require_once "../view/config.php";
require_once "../class/solr_utilities.php";
require_once "../class/resize_image.php";
    
if (isset($_POST)) {
    $m = new Member();
    $tagl1 = $m->getTagNameById($_POST['tagl1']);
    $tagl2 = $m->getTagNameById($_POST['tagl2']);   
    $list_of_tags = $tagl1." ".$tagl2;

    $arr_result = array("result"=>array(), "error"=>array());
    $tmp_filename = $_FILES['userfile']['tmp_name'];
    $resourceName = basename($_FILES['userfile']['name']);
    
    // FIXME CONTROLLARE DUPLICATI NEL CASO DI multivalued...
    $duplicate = lookForDuplicates($resourceName);
    if ($duplicate == -2) {
        echo json_encode(array("error" => "Il server SOLR non &egrave; attivo. Contattare l'amministratore."));
        return;
    } else if ($duplicate == -1) {
        echo json_encode(array("error" => "Il file ".$resourceName." &egrave; gi&agrave; presente in archivio."));
        return;
    }

    $orig_ext = explode(".", $_FILES['userfile']['name']);
    $orig_ext = strtolower(end($orig_ext));
    
    if (isset($_POST['is_lastra'])) {
        $prefix = 'LAST';
    } else {
        $prefix = 'STMP';
    }
    
    $index = getLastByIndex($prefix) + 1;
    $ca = $prefix.".".str_pad($index, 5, "0", STR_PAD_LEFT);
    
    $update = $client->createUpdate();
    $doc = $update->createDocument();
    
    $doc->codice_archivio = $ca;
    if (isset($_POST['is_lastra'])) {
        $doc->tipologia = "LASTRA"; 
    } else {
        $doc->tipologia = "STAMPA";
    }
    $doc->note = $_POST['note'];
    $doc->resourceName = basename($_FILES['userfile']['name']);
    $doc->dimensione = $_POST['dimensione'];
    $doc->anno = $_POST['anno'];
    $doc->Keywords = $list_of_tags;
    $doc->autore = $_POST['author'];
    $doc->privato = 0;
    
    //    if ($orig_ext == "jpg" or $orig_ext == "jpeg") {
    //        $resize = new ResizeImage($_FILES['userfile']['tmp_name'][$i]);
    //        $resize->resizeTo(200, 200, 'maxHeight');
    //        $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$ca.".".strtoupper($orig_ext));
    //    } else if ($orig_ext == "tif" or $orig_ext == "tiff") {
    //        $command = $GLOBALS['CONVERT_BIN']." ".$_FILES['userfile']['tmp_name'][$i]."[0] -resize x200 ".$GLOBALS['THUMBNAILS_DIR'].$ca.".JPG";
    //        $output = shell_exec($command);
    //    }
    // 
    //    $target_directory = $GLOBALS['SLIDE_DIR'].$ca.".".$ext;
    //    if (!move_uploaded_file($tmp_filename, $target_directory)) {
    //	    array_push($arr_result['error'], "Errore nella fase di copia di ".$_FILES['userfile']['name'][$i]);
    //        return;
    //	}
    //    
    $error = "";
    try {
        $update->addDocuments(array($doc));
        $update->addCommit();
        $result = $client->update($update);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    
    if ($error != 0) {
        array_push($arr_result['error'], $error);
    }
    
    if (count($arr_result['error']) != 0) {
        echo json_encode(array("error" => implode("<br>", $arr_result['error'])));
    } else {
        echo json_encode(array("result"=> "Tutti i documenti sono stati inseriti correttamente.")); 
    }
}
?>
