<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once "../class/solr_utilities.php";
require_once "../view/solr_client.php";
require_once "../class/Member.php";

function errorMessage($error, $result=NULL) {
    if (is_null($result)) {
	echo json_encode(array('error' => $error));
    } else {
	echo json_encode(array('error' => $error, 'result' => implode('\n', $result)));
    }
    exit;
}

function convertDate($date) {
    return $date."T00:00:00Z";
}

function computeCodiceArchivio($doc, $anno=NULL, $table_name="book_categories") {
    $m = new Member();

    if (is_null($anno)) {
	$anno = $_POST['anno'];
    }

    if (isset($_POST['prefissi'])) {
	$prefix = $_POST['prefissi'];
	if ($_POST['prefissi'] == "") {
	    $id = getLastByIndex($_POST['anno']) + 1;
	} else {
	    $id = getLastByIndex($_POST['prefissi'].".".$_POST['anno']) + 1;
	}
    } else {
	$prefix = $m->getPrefisso($_POST["tipologia"]);
	$id = getLastByIndex($prefix.".".$anno) + 1;
    }

    $categories = $m->getAllCategories($table_name, TRUE);
    if ($_POST['tipologia'] == "VIDEO") {
    	$codice_archivio = $prefix.".".$anno.".".str_pad($id, 4, "0", STR_PAD_LEFT);
    } elseif (in_array($_POST['tipologia'], $categories)) {
	if ($prefix == "") {
	    $codice_archivio = $anno.".".str_pad($id, 2, "0", STR_PAD_LEFT);
	} else {
	    $codice_archivio = $prefix.".".$anno.".".str_pad($id, 2, "0", STR_PAD_LEFT);
	}	
    } else {
    	$codice_archivio = $prefix.".".$anno.".".str_pad($id, 4, "0", STR_PAD_LEFT);
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
    //$tmp_filename = $_FILES['scan']['tmp_name'][0]; // UHM MOLTO STRANO AVERCI DOVUTO METTERE [0]
    if ($ext == "pdf") {
	$command = "gs -dSAFER -dNOPLATFONTS -dNOPAUSE -dBATCH -sOutputFile='".$GLOBALS['UPLOAD_DIR']."outputFileName.jpg' -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dUseTrimBox -dFirstPage=1 -dLastPage=1 ".$tmp_filename." 2>&1";
	exec($command, $output, $status);
	$command = $GLOBALS['CONVERT_BIN']." ".$GLOBALS['UPLOAD_DIR']."outputFileName.jpg -resize x200 ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG 2>&1";
	exec($command, $output, $status);
    } else if ($ext == "doc" || $ext == "docx") {
	return;
    } else {
	$command = $GLOBALS['CONVERT_BIN']." ".$tmp_filename." -resize x200 ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
	if ($ext == "tiff" or $ext == "tif") {
	    $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename."[0] -resize x200 -colorspace sRGB -quality 80 ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
	}
	exec($command, $output, $status);
    }
}

function addFaldone() {
    list($doc, $update) = createDocument();

    $tmp_filename = $_FILES['scan']['tmp_name'];
    $ext = getExt($_FILES['scan']['name']);

    $doc->argomento_breve = $_POST['argomento_breve'];
    $doc->titolo = $_POST['titolo'];
    $doc->note = $_POST['note'];
    // FIXME MIGLIORARE LA SCELTA ANCHE SE NON SAPREI BENE COME
    if ($_POST['tipologia'] == "LIBRI_VERBALI_E_DELIBERAZIONI") {
        $doc->privato = 1;
    } else {
      	$doc->privato = 0;
    }
    
    computeCodiceArchivio($doc);
    //createThumbnailAndStore($tmp_filename, $doc->codice_archivio, $ext);
    $doc->resourceName = $doc->codice_archivio.".".strtoupper($ext);
    move_uploaded_file($tmp_filename, $GLOBALS['EDOC_DIR'].$doc->resourceName);

    saveDocument($doc, $update, "Documento faldone ".$doc->codice_archivio." inserito correttamente.");
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

    saveDocument($doc, $update, "Pergamena ".$doc->codice_archivio." inserita correttamente.");
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

    saveDocument($doc, $update, "Bozzetto ".$doc->codice_archivio." inserito correttamente.");
}

//if (!function_exists('is_countable')) {
//    function is_countable($var) {
//        return (is_array($var) || $var instanceof Countable);
//    }
//}

function multiFileMerge() {
    if (is_countable($_FILES['scan']['name'])) {
        $countfiles = count($_FILES['scan']['name']);
    	$ext = getExt($_FILES['scan']['name'][0]);
    } else {
        $countfiles = 1;
    	$ext = getExt($_FILES['scan']['name']);
    }

    if ($ext != "docx" && $ext != "doc") {
	$files = array();
	for ($i=0; $i<$countfiles; $i++) {

            if (is_countable($_FILES['scan']['tmp_name'])) {	
	        $tmp_filename = $_FILES['scan']['tmp_name'][$i];
	    } else {
	        $tmp_filename = $_FILES['scan']['tmp_name'];
	    }

	    if ($ext == "jpg" || $ext == "jpeg" || $ext == "tif" || $ext == "tiff") {
		$command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr".$i." -l ita pdf";
		exec($command, $output, $status);

	    } else if ($ext == "pdf") {
		rename($tmp_filename, $GLOBALS['UPLOAD_DIR']."/ocr".$i.".pdf");
	    }
	    $files[$i] = $GLOBALS['UPLOAD_DIR']."/ocr".$i.".pdf";
	}

	if ($countfiles > 1) {
	    $command = $GLOBALS['MERGE_PDF_BIN'].$GLOBALS['UPLOAD_DIR']."/ocr.pdf ".implode(" ", $files);
	    exec($command, $output, $status);
	} else {
	    $res = rename($GLOBALS['UPLOAD_DIR']."/ocr0.pdf", $GLOBALS['UPLOAD_DIR']."/ocr.pdf");
	}
	$tmp_filename = $GLOBALS['UPLOAD_DIR']."/ocr.pdf";
	$resourceExt = "pdf";
    } else {
        if (is_countable($_FILES['scan']['tmp_name'])) {	
      	   $tmp_filename = $_FILES['scan']['tmp_name'][$i];
        } else {
           $tmp_filename = $_FILES['scan']['tmp_name'];
        }
        $resourceExt = $ext;
    }

    $command = "python3 tika.py ".$tmp_filename." 2>&1";
    $res = exec($command, $output, $status);
    if ($status != 0) {
	$error = file_get_contents('../upload/tika.error');
	errorMessage("TIKA: ".$error);
    }

    $json = file_get_contents('../upload/tika.output');
    $json_data = json_decode($json, true);

    return array($tmp_filename, $resourceExt, $json_data);
}

function addDocumento() {
    list($doc, $update) = createDocument();

    list($tmp_filename, $ext, $json_data) = multiFileMerge();

    if (isset($data['cdate'])) {
    	$anno = substr($data['cdate'], 0, 4);
    	$doc->data = convertDate(substr($data['cdate'], 0, 4));
    } else {
    	$anno = $_POST['anno'];
    	$doc->data = convertDate("1000-01-01");
    }
    
    $doc->anno = $anno;
    $doc->note = $_POST['note'];
    $doc->titolo = $_POST['titolo'];
    $doc->autore = $_POST['autore'];
    $doc->privato = 0;
    
    $keys = NULL;
    if ($ext == "pdf") {
    	$keys = array('size', 'type', 'mdate', 'testo', 'pagine');
    } elseif ($ext == "doc" || $ext == "docx") {
    	$keys = array('size', 'type', 'mdate', 'pagine', 'parole', 'testo');
    }
    //    $keys = array(''pagine');
    //} elseif ($ext == "doc" || $ext == "docx") {
    //    keys = array('pagine', 'parole');
    
    if (!is_null($keys)) {
    	foreach ($keys as $key) {
    	    if ($key == 'pagine' || $key == 'parole' || $key == 'size') {
    		if ($json_data[$key] != "") {
    		    $doc->$key = (int)$json_data[$key];
    		} else {
    		    $doc->$key = 0;
    		}
    	    } elseif ($key == 'mdate') {
    		if ($json_data[$key] != "") {
    		    $doc->$key = convertDate($json_data[$key]);
    		} else {
    		    $doc->$key = convertDate("1000-01-01");
    		}
    	    } else {
    		$doc->$key = $json_data[$key];
    	    }
    	}
    }
    
    computeCodiceArchivio($doc, $anno);
    createThumbnailAndStore($tmp_filename, $doc->codice_archivio, $ext);
    $doc->resourceName = $doc->codice_archivio.".".strtoupper($ext);
    $results = rename($tmp_filename, $GLOBALS['EDOC_DIR'].$doc->resourceName);
    
    saveDocument($doc, $update, "Documento ".$doc->codice_archivio." inserito correttamente.");
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
    $doc->data = convertDate($_POST['data']);
    $doc->stampato_da = $_POST['stampato_da'];
    $doc->dimensioni = $_POST['dimensioni'];
    $doc->note = $_POST['note'];
    $doc->privato = 0;

    computeCodiceArchivio($doc, $anno);
    createThumbnailAndStore($tmp_filename, $doc->codice_archivio, $ext);
    $doc->resourceName = $doc->codice_archivio.".".strtoupper($ext);
    $results = rename($tmp_filename, $GLOBALS['EDOC_DIR'].$doc->resourceName);
    chmod($GLOBALS['EDOC_DIR'].$doc->resourceName, 0644);
    
    saveDocument($doc, $update, "Sonetto ".$doc->codice_archivio." inserito correttamente.");
}

function addDelibera() {
    list($doc, $update) = createDocument();

    $anno = substr($_POST['data'], 0, 4);

    $doc->argomento_breve = $_POST['argomento_breve'];
    $doc->tipo_delibera = $_POST['tipo_delibera'];
    $doc->data = convertDate($_POST['data']);
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
    saveDocument($doc, $update, "Delibera ".$doc->codice_archivio." inserita correttamente.");
}

function addVerbale() {
    list($doc, $update) = createDocument();

    list($tmp_filename, $ext, $json_data) = multiFileMerge();

    $anno = substr($_POST['data'], 0, 4);
    $doc->data = convertDate($_POST['data']);

    $keys = NULL;
    if ($ext == "pdf") {
    	$keys = array('size', 'type', 'mdate', 'testo', 'pagine');
    } elseif ($ext == "doc" || $ext == "docx") {
    	$keys = array('size', 'type', 'mdate', 'pagine', 'parole', 'testo');
    }
    //    $keys = array(''pagine');
    //} elseif ($ext == "doc" || $ext == "docx") {
    //    keys = array('pagine', 'parole');

    if (!is_null($keys)) {
    	foreach ($keys as $key) {
    	    if ($key == 'pagine' || $key == 'parole' || $key == 'size') {
    		if ($json_data[$key] != "") {
    		    $doc->$key = (int)$json_data[$key];
    		} else {
    		    $doc->$key = 0;
    		}
    	    } elseif ($key == 'mdate') {
    		if ($json_data[$key] != "") {
    		    $doc->$key = convertDate($json_data[$key]);
    		} else {
    		    $doc->$key = convertDate("1000-01-01");
    		}
    	    } else {
    		$doc->$key = $json_data[$key];
    	    }
    	}
    }

    $doc->tipo_verbale = $_POST['tipo_verbale'];
    $doc->num_contestuale = $_POST['num_contestuale'];
    
    if ($_POST['tipo_verbale'] == "Seggio") {
	$doc->privato = 1;
    } else {
	$doc->privato = 0;
    }

    computeCodiceArchivio($doc, $anno);
    $doc->resourceName = $doc->codice_archivio.".".strtoupper($ext);
    saveDocument($doc, $update, "Delibera ".$doc->codice_archivio." inserito correttamente.");
}

function addMonturato() {
    $m = new Member();
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
	    $msgs = array();
	    for ($i=1; $i<count($monturati); $i++) {
		list($doc, $update) = createDocument();
		computeCodiceArchivio($doc, $anno);
		$doc->nome_cognome = rtrim($monturati[$i][0]);
		if (! $m->checkRuolo(rtrim($monturati[$i][1]))) {
		    errorMessage("Ruolo ".ucwords(rtrim($monturati[$i][1]))." non riconosciuto.", $msgs);
		}
		$doc->ruolo = ucwords(rtrim($monturati[$i][1]));
		$doc->evento = $_POST['evento'];
		$doc->data = convertDate($_POST['data']);
		$doc->privato = 0;

		$msgs = saveDocument($doc, $update, "Monturato ".$doc->codice_archivio." inserito correttamente.", $msgs);
	    }
	    echo json_encode(array('result' => implode('<br>', $msgs)));

	} else {
	    errorMessage('Il CSV deve avere due colonne: nome, comparsa');
	}
    } else {
	list($doc, $update) = createDocument();
	computeCodiceArchivio($doc, $anno);
	$doc->nome_cognome = $_POST['nome_cognome'];
	$doc->ruolo = $_POST['ruolo'];
	$doc->evento = $_POST['evento'];
	$doc->data = convertDate($_POST['data']);
	$doc->privato = 0;
	saveDocument($doc, $update, "Monturato ".$doc->codice_archivio." inserito correttamente.");
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

    saveDocument($doc, $update, "Fotografia ".$doc->codice_archivio." inserita correttamente.");
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
	$command = $GLOBALS['CONVERT_BIN']." ". $cover_tmp." -resize x200 ..".$GLOBALS['COVER_DIR'].$doc->codice_archivio.".JPG";
	exec($command, $output, $status);
    }

    saveDocument($doc, $update, "Libro ".$doc->codice_archivio." inserito correttamente.");
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
	saveDocument($doc, $update, "Video ".$doc->codice_archivio." inserito correttamente.");
    }
}

function createDocument() {
    global $client;
    $update = $client->createUpdate();
    $doc = $update->createDocument();

    return array($doc, $update);
}

function saveDocument($doc, $update, $msg=NULL, $multi=NULL) {
    global $client;
    try {
	$update->addDocuments(array($doc));
	$update->addCommit();
	$result = $client->update($update);
    } catch (Solarium\Exception\HttpException $e) {
	errorMessage($e->getMessage(), $multi);
    }
    
    if (is_null($msg)) {
	$msg = "Documento ".$doc->codice_archivio." inserito correttamente.";
    }

    if (!is_null($multi)) {
	array_push($multi, $msg);
	return $multi;
    }

    echo json_encode(array('result' => $msg));
}

// FIXME
$faldone_cats = array('STATUTI_E_REGOLAMENTI','LIBRI_VERBALI_E_DELIBERAZIONI','ELEZIONI',
	              'ORATORIO_E_AFFARI_DI_CULTO','AFFARI_INTERNI','PROTETTORATO','BENI_IMMOBILI',
		      'PATRIMONIO_ARTISTICO_MUSEO_ARCHIVIO','COSTUMI','ECONOMATO','PROTOCOLLI_CORRISPONDENZA',
		      'CARRIERE_E_PUBBLICI_SPETTACOLI','CELEBRAZIONI_RICORRENZE_ATTIVITA_CULTURALI',
		      'TERRITORIO','CONTABILITA_GENERALE','MISCELLANEA','PRESIDENTI_SOCIETA',
		      'SOC_PUBBLICHE_RAPPRESENTANZE','SOC_IL_RISORGIMENTO','SOC_UNIONE',
		      'SOC_AVANGUARDISTA','SOC_IL_LEONE','CIRCOLO_IL_LEONE');

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
    } else if ($_POST['tipologia'] == 'VERBALE') {
        addVerbale();
    } else if ($_POST['tipologia'] == 'MONTURATO') {
	addMonturato();
    } else if (in_array($_POST['tipologia'], $faldone_cats)) {
        addFaldone();
    } else if (($_POST['tipologia'] == 'LIBRO') or
      ($_POST['tipologia'] == 'PUBBLICAZIONE_DI_CONTRADA') or
      ($_POST['tipologia'] == 'LIBRI_DELLA_LITURGIA') or
      ($_POST['tipologia'] == 'MANOSCRITTO') or
      ($_POST['tipologia'] == 'OPUSCOLO') or
      ($_POST['tipologia'] == 'RIVISTA') or
      ($_POST['tipologia'] == 'NUMERO_UNICO') or
      ($_POST['tipologia'] == 'TESI') or
      ($_POST['tipologia'] == 'PERIODICO')) {
	addLibro();
    } else {
	errorMessage("Tipologia sconosciuta.");
    }
}
?>
