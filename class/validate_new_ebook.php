<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once "../class/solr_utilities.php";
require_once "../view/solr_client.php";
require_once "../class/Member.php";

function createThumbnail($codice_archivio, $ext, $tag='scan') {
    $tmp_filename = $_FILES[$tag]['tmp_name'];

    if ($ext == "pdf") {
        //$command = $GLOBALS['PDF2IMAGE_BIN']." gs -dSAFER -dNOPLATFONTS -dNOPAUSE -dBATCH -sOutputFile='".$GLOBALS['UPLOAD_DIR']."outputFileName.jpg' -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dUseTrimBox -dFirstPage=1 -dLastPage=1 ".$tmp_filename;
        $command = "gs -dSAFER -dNOPLATFONTS -dNOPAUSE -dBATCH -sOutputFile='".$GLOBALS['UPLOAD_DIR']."outputFileName.jpg' -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dUseTrimBox -dFirstPage=1 -dLastPage=1 ".$tmp_filename;
        exec($command, $output, $status);
        $tmp_filename = $GLOBALS['UPLOAD_DIR']."outputFileName.jpg";
    } 

    $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename." -resize x200 ..".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
    if ($ext == "tiff" or $ext == "tif") {
        $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename."[0] -resize x200 -colorspace sRGB -quality 80 ..".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
    }
    exec($command, $output, $status);	    	    
    move_uploaded_file($tmp_filename, $GLOBALS['EDOC_DIR'].$resourceName);
}

function fileExtension($tag='scan']) {
    $tmp = explode(".", $_FILES[$tag]['name']);
    //$duplicate = lookForDuplicates($resourceName);
    $ext = strtolower(end($tmp));

    return $ext;
}

function addPergamena($doc) {
    $ext = fileExtension();
    $codice_archivio = $doc->codice_archivio;
    
    $doc->resourceName = $codice_archivio.".".strtoupper($ext);
    $doc->descrizione = $_POST['descrizione']; 
    $doc->tecnica = $_POST['tecnica']; 	
    $doc->autore = $_POST['autore']; 
    //$doc->anno = $_POST['anno']; 
    $doc->dimensioni = $_POST['dimensioni']; 
    $doc->note = $_POST['note'];
    $doc->privato = 0;

    createThumbnail($codice_archivio, $ext);
    return $doc;
}

function addBozzetto($doc) {
    $ext = fileExtension();
    $codice_archivio = $doc->codice_archivio;
    
    $doc->resourceName = $codice_archivio.".".strtoupper($ext);
    $doc->categoria = $_POST['categoria']; 
    $doc->descrizione = $_POST['descrizione']; 
    $doc->tecnica = $_POST['tecnica']; 	
    $doc->autore = $_POST['autore']; 
    //$doc->anno = $_POST['anno']; 
    $doc->dimensioni = $_POST['dimensioni']; 
    $doc->note = $_POST['note'];
    $doc->privato = 0;
    
    createThumbnail($codice_archivio, $ext);
    return $doc;
}

function singlePageOCR() {
    $tmp_filename = $_FILES['scan']['tmp_name'];
    $command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr" -l ita PDF";
    exec($command, $output, $status);
    $results = rename($GLOBALS['UPLOAD_DIR']."/ocr".pdf", $GLOBALS['EDOC_DIR'].$codice_archivio.".PDF");
}

function addSonetto($doc) {
    $ext = fileExtension();
    $codice_archivio = $doc->codice_archivio;
    
 //   if ($ext == "tiff" or $ext == "tif") {
 //       $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename."[0] ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
 //       exec($command, $output, $status);
 //   } else if ($ext == "png") {
 //       $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename." ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
 //       exec($command, $output, $status);
 //   } 
    singlePageOCR();
    
    $doc->testo = $_POST['testo_ocr']."\n";
    $doc->committente = $_POST['committente']; 
    $doc->ricorrenza = $_POST['ricorrenza']; 	
    $doc->autore = $_POST['autore'];
    $doc->dedica = $_POST['dedica']; 
    //$doc->anno = $_POST['anno']; //substr($_POST['data'], 0, 4);
    $doc->data = $_POST['data'];
    $doc->stampato_da = $_POST['stampato_da'];         
    $doc->dimensioni = $_POST['dimensioni']; 
    $doc->note = $_POST['note'];
    $doc->privato = 0;

    createThumbnail($codice_archivio, $ext);
    return $doc;
}

function addDocumento($doc) {        
    $countfiles = count($_FILES['scan']['name']);
    $doc_text = '';
    for ($i=0; $i<$countfiles; $i++) {
        $tmp_filename = $_FILES['scan']['tmp_name'][$i];
        //$resourceName = basename($_FILES['scan']['name'][$i]);
        $tmp = explode(".", $_FILES['scan']['name'][$i]);
        $ext = strtolower(end($tmp));
        //$status = move_uploaded_file($tmp_filename, $GLOBALS['UPLOAD_DIR'].$resourceName);       
        //$duplicate = lookForDuplicates($resourceName);

        $command = "../class/tika.py ".$tmp_filename; //$GLOBALS['UPLOAD_DIR'].$resourceName; //$tmp_filename;   
        exec($command, $output, $status);
        if ($status != 0) {
            echo json_encode(array('error' => json_encode($output)));
            return;
        }

        $data = json_decode($output[0], true);
        //$data['resourceName'] = $resourceName;
        $doc_text .= $data['testo'];

        if ($i == 0) {
            createThumbnail($doc->codice_archivio, $ext);
        }
        
        if ($countfiles == 1) {
            $results = rename($tmp_filename, $GLOBALS['EDOC_DIR'].$doc->codice_archivio.".".strtoupper($ext));      
            //chmod ($GLOBALS['EDOC_DIR'].$codice_archivio.".".strtoupper($ext), 0644);
        } else {
            $command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr".$i." -l ita PDF";
            exec($command, $output, $status);
        }
    }

    if ($countfiles > 1) {
        $command = $GLOBALS['MERGE_PDF_BIN'].$GLOBALS['EDOC_DIR'].$ca."PDF ";
        for ($i=0; $i<$countfiles; $i++) {
            $command = $command.$GLOBALS['UPLOAD_DIR']."ocr".$i.".pdf ";
        }
        exec($command, $output, $result);
        //$target_directory = $GLOBALS['EDOC_DIR'].$ca."PDF";
        //$results = rename($tmp_filename, $GLOBALS['EDOC_DIR'].$codice_archivio.".".strtoupper($ext));
    }           
    
    $doc->note = $_POST['note'];
    $doc->titolo = $_POST['titolo'];
    $doc->autore = $_POST['autore'];
    
//    foreach ($data as $key => $value) {
//        if ($key == "autore" or $key == "testo") {
//            continue;
//        }
//        $doc->$key = $value;
//    }
    $doc->testo = $doc_text."\n";
    $doc->privato = 0;
    
    return $doc;
}

function addLibro($doc) {

    if ($_FILES['copertina']['name'] != "") {
        $ext = fileExtension('copertina');
        createThumbnail($doc->codice_archivio, $ext, 'copertina');
    }
    
    $doc->titolo = $_POST['titolo'];
    $doc->sottotitolo = $_POST['sottotitolo'];
    $doc->prima_responsabilita = $_POST['prima_responsabilita'];
    $doc->altre_responsabilita = $_POST['altre_responsabilita'];
    $doc->luogo = $_POST['luogo'];
    $doc->edizione = $_POST['edizione'];
    $doc->ente = $_POST['ente'];
    $doc->serie = $_POST['serie'];
    //$doc->anno = $_POST['anno'];
    $doc->descrizione = $_POST['descrizione'];
    $doc->cdd = $_POST['cdd'];
    $doc->soggetto = $_POST['soggetto'];
    $doc->note = $_POST['note'];
    $doc->privato = 0;

    return $doc;
}

function returnError($error) {
    echo json_encode(array('error' => $error));
    exit;
}


function endsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}

function startsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, strlen($needle)) === 0;
}

if (isset($_POST)) {
    $aggiornamento = false;
    foreach ($_POST as $key => $value) {
        $change = false
        if (endsWith($key, "_upd")) {
            $real_key = substr($key, 0, strlen($key)-4);
            $change = true;
            $aggiornamento = true;
        } else if (startsWith($key, "upd_")) {
            $real_key = substr($key, 4);
            $change = true;
            $aggiornamento = true;
        } 

        if ($change) {
            $_POST[$real_key] = $_POST[$key];
        }
    }

    $error = "";
    $m = new Member();
    $prefix = "";
    if (isset($_POST['prefissi'])) {
        $prefix = $_POST['prefissi'];
    } else {   
        $prefix = $m->getPrefisso($_POST["tipologia"]);
    }
    
    $anno = "";
    if (isset($_POST['anno'])) {
        $anno = $_POST['anno']
    } else {
        $anno = substr($data['cdate'], 0, 4);
    }
    $id = getLastByIndex($prefix.".".$anno) + 1;
    if ($_POST['tipologia'] == "MONTURATO") {
        $codice_archivio = $prefix.".".$anno.".".str_pad($id, 4, "0", STR_PAD_LEFT);
    } else {
        $codice_archivio = $prefix.".".$anno.".".str_pad($id, 2, "0", STR_PAD_LEFT);  
    }
    
    $update = $client->createUpdate();
    $doc = $update->createDocument();

    if (!array_key_exists("privato", $_POST)) {
        $doc->privato = 0;
    }
    
    $doc->codice_archivio = $codice_archivio;
    $doc->tipologia = $_POST['tipologia'];
    $doc->anno = $anno;
    
    $update = $client->createUpdate();
    
    $libri = $m->getAllCategories("book_categories", true);
    if ($_POST['tipologia'] == 'DOCUMENTO') {
        addDocument($doc);
    } else if ($_POST['tipologia'] == "PERGAMENA") {    
        $doc = addPergamena($doc);
    } else if ($_POST['tipologia'] == "BOZZETTO") {
        $doc = addBozzetto($doc);
    } else if ($_POST['tipologia'] == 'SONETTO') {
        $doc = addSonetto($doc);            
    } else if (in_array($_POST['tipologia'], $libri)) {
        $doc = addLibro($doc);
    }
    
    try {
        $update->addDocuments(array($doc));
        $update->addCommit();
        $result = $client->update($update);
        //print_r ($result);
    } catch (Solarium\Exception\HttpException $e) {
        returnError($e->getMessage());
    }

    echo json_encode(array('result' => "Documento ".$codice_archivio." inserito correttamente."));
}
?>
