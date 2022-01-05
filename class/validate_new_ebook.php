<?php
require_once "../view/config.php";
require_once "../class/solr_utilities.php";
require_once "../view/solr_client.php";
require_once "../class/Member.php";

function errorMessage($error) {
    echo json_encode(array('error' => $error));
    exit;
}

function computeCodiceArchivio($doc, $anno=NULL) {
    if (is_null($anno)) {
        $anno = $_POST['anno'];
    }
    
    if (isset($_POST['prefissi'])) {
        if ($_POST['prefissi'] == "") {
            $id = getLastByIndex($_POST['anno']) + 1;
        } else {
            $id = getLastByIndex($_POST['prefissi'].".".$_POST['anno']) + 1;
        }
    } else {
        $m = new Member();
        $prefix = $m->getPrefisso($_POST["tipologia"]);
        $id = getLastByIndex($prefix.".".$anno) + 1;
    }
    
    if ($_POST['tipologia'] == "DELIBERA") {
        $codice_archivio = $prefix.".".$anno.".".str_pad($id, 3, "0", STR_PAD_LEFT);
    } else if ($_POST['tipologia'] == "MONTURATO") {
        $codice_archivio = $prefix.".".$anno.".".str_pad($id, 4, "0", STR_PAD_LEFT);
    } else if ($_POST['tipologia'] == "VIDEO") {
        $codice_archivio = $prefix.".".$anno.".".str_pad($id, 5, "0", STR_PAD_LEFT);
    } else {
        $codice_archivio = $prefix.".".$anno.".".str_pad($id, 2, "0", STR_PAD_LEFT);
    }

    $doc->codice_archivio = $codice_archivio;
    $doc->tipologia = $_POST['tipologia'];
    $doc->anno = $anno;
}

function getExt($filename) {
    $tmp = explode(".", $filename);
    
    return strtolower(end($tmp));
    //$duplicate = lookForDuplicates($resourceName);    
}

function createThumbnailAndStore($tmp_filename, $codice_archivio, $ext) {
    //$tmp_filename = $_FILES['scan']['tmp_name'];
    if ($ext == "pdf") {
        $command = "gs -dSAFER -dNOPLATFONTS -dNOPAUSE -dBATCH -sOutputFile='".$GLOBALS['UPLOAD_DIR']."outputFileName.jpg' -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dUseTrimBox -dFirstPage=1 -dLastPage=1 ".$tmp_filename;
        exec($command, $output, $status);
        $command = $GLOBALS['CONVERT_BIN']." ".$GLOBALS['UPLOAD_DIR']."outputFileName.jpg -resize x200 ..".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
        exec($command, $output, $status);
    } else if ($ext == "doc" || $ext == "docx") {
        return;
    } else {
	print ("PUPPAMELO ".$tmp_filename);
        $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename." -resize x200 ..".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
        if ($ext == "tiff" or $ext == "tif") {
            $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename."[0] -resize x200 -colorspace sRGB -quality 80 ..".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
        }
        exec($command, $output, $status);
    }
}

function addPergamena() {
    list($doc, $update) = createDocument();
    
    $tmp_filename = $_FILES['scan']['tmp_name'];
    $ext = getExt($_FILES['scan']['name']);
    
    $doc->descrizione = $_POST['descrizione']; 
    $doc->tecnica = $_POST['tecnica']; 	
    $doc->autore = $_POST['autore']; 
    $doc->dimensioni = $_POST['dimensioni']; 
    $doc->note = $_POST['note'];
    $doc->privato = 0;
    
    computeCodiceArchivio($doc);
    createThumbnailAndStore($tmp_filename, $doc->codice_archivio, $ext);
    $doc->resourceName = $doc->codice_archivio.".".strtoupper($ext);
    move_uploaded_file($tmp_filename, $GLOBALS['EDOC_DIR'].$doc->resourceName);

    saveDocument($doc, $update);
}

function addBozzetto() {
    list($doc, $update) = createDocument();
    
    $tmp_filename = $_FILES['scan']['tmp_name'];
    $ext = getExt($_FILES['scan']['name']);

    $doc->categoria = $_POST['categoria']; 
    $doc->descrizione = $_POST['descrizione']; 
    $doc->tecnica = $_POST['tecnica']; 	
    $doc->autore = $_POST['autore']; 
    $doc->dimensioni = $_POST['dimensioni']; 
    $doc->note = $_POST['note'];
    $doc->privato = 0;

    computeCodiceArchivio($doc);
    createThumbnailAndStore($tmp_filename, $doc->codice_archivio, $ext);
    $doc->resourceName = $doc->codice_archivio.".".strtoupper($ext);
    move_uploaded_file($tmp_filename, $GLOBALS['EDOC_DIR'].$doc->resourceName);

    saveDocument($doc, $update);
}

function multiFileMerge() {
    $countfiles = count($_FILES['scan']['name']);
    $ext = getExt($_FILES['scan']['name'][0]);

    if ($ext != "docx" && $ext != "doc") {
        $files = array();
        for ($i=0; $i<$countfiles; $i++) {

            $tmp_filename = $_FILES['scan']['tmp_name'][$i];
            if ($ext == "jpg" || $ext == "jpeg" || $ext == "tif" || $ext == "tiff") {       
                $command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr".$i." -l ita pdf";
                exec($command, $output, $status);

            } else if ($ext == "pdf") {
                rename($tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr".$i.".pdf");
            }
            $files[$i] = $GLOBALS['UPLOAD_DIR']."/ocr".$i.".pdf";
        }
        
        if ($countfiles > 1) {
            $command = $GLOBALS['MERGE_PDF_BIN'].$GLOBALS['UPLOAD_DIR']."/ocr.pdf' ".implode(" ", $files);
            exec($command, $output, $status);
        } else {
            $res = rename($GLOBALS['UPLOAD_DIR']."/ocr0.pdf", $GLOBALS['UPLOAD_DIR']."/ocr.pdf");
        }
        $tmp_filename = $GLOBALS['UPLOAD_DIR']."/ocr.pdf";
        $resourceExt = "pdf";
    } else {
        $resourceExt = strtoupper($ext);
    }

    $command = "python3 tika.py ".$tmp_filename." 2>&1"; 
    $res = exec($command, $output, $status);
    if ($status != 0) {
	$error = file_get_contents('../upload/tika.error');
	errorMessage("TIKA: ".$error);	
    }

    $json = file_get_contents('../upload/tika.output');
    $json_data = json_decode($json,true);

    return array($tmp_filename, $resourceExt, $json_data);
}

function addDocumento() {        
    list($doc, $update) = createDocument();

    list($tmp_filename, $ext, $json_data) = multiFileMerge();
    
    $doc->testo = $json_data['testo'];
    $doc->anno = substr($data['cdate'], 0, 4);
    $doc->note = $_POST['note'];
    $doc->titolo = $_POST['titolo'];
    $doc->autore = $_POST['autore'];    
    $doc->privato = 0;
    computeCodiceArchivio($doc, $anno);
    createThumbnailAndStore($tmp_filename, $doc->codice_archivio, $ext);
    $doc->resourceName = $doc->codice_archivio.".".strtoupper($ext);    
    $results = rename($GLOBALS['UPLOAD_DIR']."/".$tmp_files, $GLOBALS['EDOC_DIR'].$doc->resourceName);
    
    saveDocument($doc, $update);                        
}

function addSonetto() {
    list($doc, $update) = createDocument();
    
    list($tmp_filename, $ext, $json_data) = multiFileMerge();

    $doc->testo = $json_data['testo'];
    $doc->committente = $_POST['committente']; 
    $doc->ricorrenza = $_POST['ricorrenza']; 	
    $doc->autore = $_POST['autore'];
    $doc->dedica = $_POST['dedica']; 
    $anno = substr($_POST['data'], 0, 4);
    $doc->data = $_POST['data']."T00:00:00Z";
    $doc->stampato_da = $_POST['stampato_da'];         
    $doc->dimensioni = $_POST['dimensioni']; 
    $doc->note = $_POST['note'];
    $doc->privato = 0;

    computeCodiceArchivio($doc, $anno);
    createThumbnailAndStore($tmp_filename, $doc->codice_archivio, $ext);
    $doc->resourceName = $doc->codice_archivio.".".strtoupper($ext);
    $results = rename($tmp_filename, $GLOBALS['EDOC_DIR'].$doc->resourceName);
    chmod($GLOBALS['EDOC_DIR'].$doc->resourceName, 0644);
    
    saveDocument($doc, $update);                            
}

function addDelibera() {
    list($doc, $update) = createDocument();
    
    $anno = substr($_POST['data'], 0, 4);

    $doc->argomento_breve = $_POST['argomento_breve'];
    $doc->tipo_delibera = $_POST['tipo_delibera'];
    $doc->data = $_POST['data']."T00:00:00Z";
    $doc->unanimita = $_POST['unanimita'];
    $doc->favorevoli = $_POST['favorevoli'];
    $doc->contrari = $_POST['contrari'];
    $doc->astenuti = $_POST['astenuti'];
    $doc->straordinaria = $_POST['straordinaria'];
    $doc->capitolo = $_POST['capitolo'];
    $doc->pagina = $_POST['pagina'];
    $doc->num_contestuale = $_POST['num_contestuale'];
    $doc->testo = $_POST['testo'];
    if ($_POST['tipo_delibera'] == "Seggio") {
        $doc->privato = 1;
    } else {
        $doc->privato = 0;
    }

    computeCodiceArchivio($doc, $anno);
    saveDocument($doc, $update);                        
}

function addMonturato() {
    $anno = substr($_POST['data'], 0, 4);        

    if ($_FILES['comparsa']['error'] == 0) {
        $filename = $_FILES['comparsa']['tmp_name'];
        $handler = fopen($filename, 'r');
        
        while($data = fgetcsv($handler)) {
            $monturati[] = $data;
        }
        
        fclose($handler);
        $columns = array_map('strtolower', $monturati[0]);

        if ("nome" == $columns[0] && "comparsa" == $columns[1]) {
            for ($i=1; $i<count($monturati); $i++) {
                list($doc, $update) = createDocument();
                computeCodiceArchivio($doc, $anno);                
                $doc->nome_cognome = $monturati[$i][0];
                $doc->ruolo = $monturati[$i][1];                   
                $doc->evento = $_POST['evento'];
                $doc->data = $_POST['data']."T00:00:00Z";
                $doc->privato = 0;
                saveDocument($doc, $update);
            }
        } else {
            errorMessage('Il CSV deve avere due colonne: nome, comparsa');
        }
    } else {
        list($doc, $update) = createDocument();
        computeCodiceArchivio($doc, $anno);
        $doc->nome_cognome = $_POST['nome_cognome'];
        $doc->ruolo = $_POST['ruolo'];
        $doc->evento = $_POST['evento'];
        $doc->data = $_POST['data']."T00:00:00Z";
        $doc->privato = 0;
        saveDocument($doc, $update);
    }
}

function addStampa() {
    list($doc, $update) = createDocument();
    
    $m = new Member();
    $tagl1 = $m->getTagNameById($_POST['tagl1']);
    $tagl2 = $m->getTagNameById($_POST['tagl2']);   
    $list_of_tags = $tagl1." ".$tagl2;

    $tmp_filename = $_FILES['userfile']['tmp_name'];
    $ext = getExt($_FILES['userfile']['name']);

    if (isset($_POST['is_lastra'])) {
        $_POST['tipologia'] = "LASTRA"; 
    } else {
        $_POST['tipologia'] = "STAMPA";
    }

    $doc->note = $_POST['note'];
    $doc->dimensione = $_POST['dimensione'];
    $doc->anno = $_POST['anno'];
    $doc->Keywords = $list_of_tags;
    $doc->autore = $_POST['author'];
    $doc->privato = 0;

    computeCodiceArchivio($doc);
    createThumbnailAndStore($tmp_filename, $doc->codice_archivio, $ext);
    move_uploaded_file($tmp_filename, $GLOBALS['PHOTO_DIR'].$doc->codice_archivio.".".strtoupper($ext));
    $doc->resourceName = $codice_archivio.".".strtoupper($ext);

    saveDocument($doc, $update);
}

function addLibro() {
    list($doc, $update) = createDocument();

    $doc->titolo = $_POST['titolo'];
    $doc->sottotitolo = $_POST['sottotitolo'];
    $doc->prima_responsabilita = $_POST['prima_responsabilita'];
    $doc->altre_responsabilita = $_POST['altre_responsabilita'];
    $doc->luogo = $_POST['luogo'];
    $doc->edizione = $_POST['edizione'];
    $doc->ente = $_POST['ente'];
    $doc->serie = $_POST['serie'];
    $doc->descrizione = $_POST['descrizione'];
    $doc->cdd = $_POST['cdd'];
    $doc->soggetto = $_POST['soggetto'];
    $doc->note = $_POST['note'];
    $doc->privato = 0;
    computeCodiceArchivio($doc);

    if ($_FILES['copertina']['name'] != "") {
        $cover_tmp = $_FILES['copertina']['tmp_name'];
        $command = $GLOBALS['CONVERT_BIN']." ". $cover_tmp." -resize x200 ..".$GLOBALS['COVER_DIR'].$codice_archivio.".JPG";
        exec($command, $output, $status);
    }

    saveDocument($doc, $update);           
}

function videoFileCheck($i) {
    if ($_FILES['videos']['size'][$i] > $GLOBALS['MAX_UPLOAD_BYTE']) {
        errorMessage("Il file ".$_FILE['videos']['name'][$i]." &egrave; troppo grande (>".$GLOBALS['MAX_UPLOAD_BYTE']." Bytes) !");
    }
    
    //$resourceName = basename($_FILES['videos']['name'][$i]);
    //if (lookForDuplicates($resourceName)) {
    //    return FALSE;
    //}
}

function addVideo() {
    $target_directory = $GLOBALS['VIDEO_DIR'];
    $maxsize = $GLOBALS['MAX_UPLOAD_BYTE'];
    
    $countfiles = count($_FILES['videos']['name']);
    for ($i=0; $i<$countfiles; $i++) {
        list($doc, $update) = createDocument();        
        $ext = getExt($_FILES['videos']['name'][$i]);        
        videoFileCheck($i);

        $doc->note = $_POST['note'];
        $doc->privato = 0;        
        computeCodiceArchivio($doc);
        $doc->resourceName = $doc->codice_archivio.".".strtoupper($ext);
        
        if (! file_exists($GLOBALS['VIDEO_DIR'])) {
            mkdir($GLOBALS['VIDEO_DIR'], 0777, TRUE);
        }
        
        // FIXME PENSARE AD EVENTUALE THUMBNAIL PER VIDEO
        move_uploaded_file($_FILES['videos']['tmp_name'][$i], $GLOBALS['VIDEO_DIR'].$doc->resourceName);
        saveDocument($doc, $update);
    }
}

function createDocument() {
    global $client;
    $update = $client->createUpdate();
    $doc = $update->createDocument();
    
    return array($doc, $update);
}

function saveDocument($doc, $update, $msg=NULL) {
    global $client;
    try {
        $update->addDocuments(array($doc));
        $update->addCommit();
        $result = $client->update($update);
    } catch (Solarium\Exception\HttpException $e) {
        errorMessage($e->getMessage());
    }
    
    if (is_null($msg)) {
        $msg = "Documento ".$doc->codice_archivio." inserito correttamente.";
    }
    echo json_encode(array('result' => $msg));
}

if (isset($_POST)) {    
    if ($_POST['tipologia'] == "PERGAMENA") {
        addPergamena();
    } else if ($_POST['tipologia'] == "BOZZETTO") {
        addBozzetto();        
    } else if ($_POST['tipologia'] == 'DOCUMENTO') {
        addDocumento();
    } else if ($_POST['tipologia'] == 'SONETTO') {
        addSonetto();
    } else if ($_POST['tipologia'] == 'DELIBERA') {
        addDelibera();	
    } else {
        errorMessage("Tipologia sconosciuta.");
    }
}
?>
