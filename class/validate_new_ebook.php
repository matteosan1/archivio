<?php

require_once "../view/config.php";
require_once "../class/solr_utilities.php";
require_once "../view/solr_client.php";
require_once "../class/resize_image.php";
require_once "../class/Member.php";
    
function process_ebook($i) {
    global $client;
    
    if ($_POST['tipologia'] == "----") {
        $prefix = "UNK";
    } else {
        $prefix = substr($_POST['tipologia'], 0, 3);
    }
      
    $orig_ext = explode(".", $_FILES['edoc']['name'][$i]);
    $orig_ext = strtolower(end($orig_ext));

    $domove = 0;
    if (isset($_POST['do_merge'])) {
        $tmp_filename = $GLOBALS['UPLOAD_DIR']."ocr.pdf";
   	$ext = "pdf";
    } else if (isset($_POST['do_ocr']) and ($orig_ext == "jpeg" or $orig_ext == "jpg" or
      	      			       	    $orig_ext == "tiff" or $orig_ext == "tif")) {
     	$tmp_filename = $GLOBALS['UPLOAD_DIR']."ocr".$i.".pdf";
      	$ext = "pdf";
    } else {
      	$tmp_filename = $_FILES['edoc']['tmp_name'][$i];
      	$tmp = explode(".", $_FILES['edoc']['name'][$i]);
        $ext = $orig_ext;
	$domove = 1;
    }
       
    $command = "/usr/bin/java -jar ".$GLOBALS['TIKA_APP']." -j -t -J ".$tmp_filename;
    $output = shell_exec($command);
    $data = json_decode($output, true)[0];
    
    $index = getLastByIndex($prefix) + 1;
    $ca = $prefix.".".str_pad($index, 5, "0", STR_PAD_LEFT);

    $update = $client->createUpdate();
    $doc = $update->createDocument();

    $doc->codice_archivio = $ca;
    $doc->tipologia = $_POST['tipologia'];
    $doc->note = $_POST['note'];
    $doc->privato = 0;
    $doc->resourceName = basename($_FILES['edoc']['name'][$i]);

    if (isset($data['X-TIKA:content'])) {
        $text = trim(preg_replace('/(\t){1,}/', '', $data['X-TIKA:content']));
        $text = trim(preg_replace('/(\n){2,}/', "\n", $text));
        $data['text'] = $text; 
        unset($data['X-TIKA:content']);
    }

    if (isset($data['X-Parsed-By'])) {
        unset($data['X-Parsed-By']);
    }

    foreach ($data as $key => $value) {
       $doc->$key = $value;
    }

    $error = "";
    try {
       $update->addDocuments(array($doc));
       $update->addCommit();
       $result = $client->update($update);
    } catch (Solarium\Exception\HttpException $e) {
        $error = $e->getMessage();
    }

    $ret = json_decode(upload_csv2(array2csv($data)), true);

    //if (isset($_POST['do_ocr'])) {
    //	  unlink($tmp_filename);
    //}

    if ($error != 0) {
        array_push($arr_result['error'], $error);
        return;
    }
}

//function convertScan($ca) {
//    if gettype($_FILES['scan']['name']));
//    $countfiles = count($_FILES['scan']['name']);
//    print_r ($countfiles);
//    
//    if ($countfiles > 1) {
//        $command = $GLOBALS['MERGE_PDF_BIN'].$GLOBALS['UPLOAD_DIR']."ocr.pdf ";
//        for ($i=0; $i<$countfiles; $i++) {
//            $command = $command.$GLOBALS['UPLOAD_DIR']."ocr".$i.".pdf ";
//        }
//        exec($command, $output, $result);
//
//        $target_directory = $GLOBALS['EDOC_DIR'].$ca."pdf";
//
//        if (!move_uploaded_file($tmp_filename, $target_directory)) {
//	        return array($arr_result['error'], "Errore nella fase di copia di ".$_FILES['scan']['name']);
//            return;
//        }
//    } else if ($_FILES['scan']['error'][0] == 0) {
//        $orig_ext = explode(".", $_FILES['scan']['name'][0]);
//        $orig_ext = strtolower(end($orig_ext));
//
//        if ($orig_ext == "jpg" or $orig_ext == "jpeg") {
//            $resize = new ResizeImage($_FILES['scan']['tmp_name'][0]);
//            $resize->resizeTo(200, 200, 'maxHeight');
//            $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$ca.".".strtoupper($orig_ext));
//        } else if ($orig_ext == "pdf") {
//            $command = $GLOBALS['PDF2IMAGE_BIN']." -f 1 -l 1 -dev jpeg ".$_FILES['scan']['tmp_name'][0];
//            $output = shell_exec($command);
// 	        $resize = new ResizeImage($_FILES['scan']['tmp_name'][0]."_1.jpg"); // _1.jpg because it is added by pdf2image
//      	    $resize->resizeTo(200, 200, 'maxHeight');
//      	    $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$ca.".".strtoupper($orig_ext));
//        } else if ($orig_ext == "tif" or $orig_ext == "tiff" or $orig_ext == "png") {      	
//            $command = $GLOBALS['CONVERT_BIN']." ".$_FILES['scan']['tmp_name']."[0] -resize x200 ".$GLOBALS['THUMBNAILS_DIR'].$ca.".JPG";
//            $output = shell_exec($command);
//        }
//        //if (!move_uploaded_file($tmp_filename, $target_directory)) {
//	    //    return array($arr_result['error'], "Errore nella fase di copia di ".$ca.$orig_ext);
//        //}
//    } 
//}

if (isset($_POST)) {
    $m = new Member();
    $prefix = $m->getPrefisso($_POST["tipologia"]);
    $id = getLastByIndex($prefix.".".$_POST['anno']) + 1;
    $codice_archivio = $prefix.".".$_POST['anno'].".".str_pad($id, 2, "0", STR_PAD_LEFT);

    $update = $client->createUpdate();
    $doc = $update->createDocument();
    
    $doc->codice_archivio = $codice_archivio;
    $doc->tipologia = $_POST['tipologia'];

//    if ($_POST['tipologia'] == 'SONETTO') {
//        $doc_testo = "";
//        $arr_check = array();
//        $arr_result = array("result"=>array(), "error"=>array());
//        $countfiles = count($_FILES['scan']['name']);
//        for ($i=0; $i<$countfiles; $i++) {
//            $tmp_filename = $_FILES['scan']['tmp_name'][$i];
//            //$resourceName = basename($_FILES['scan']['name'][$i]);
//            $tmp = explode(".", $_FILES['scan']['name'][$i]);
//
//            //$duplicate = lookForDuplicates($resourceName);
//
//            $ext = strtolower(end($tmp));
//            if ($ext == "pdf") {                
//                $command = "/usr/bin/java -jar ".$GLOBALS['TIKA_APP']." -j -t -J ".$tmp_filename;
//                $output = shell_exec($command);
//                $data = json_decode($output, true)[0];
//
//                if (isset($data['X-TIKA:content'])) {
//                    $text = trim(preg_replace('/(\t){1,}/', '', $data['X-TIKA:content']));
//                    $text = trim(preg_replace('/(\n){2,}/', "\n", $text));
//                    $data['text'] = $text; 
//                    //unset($data['X-TIKA:content']);
//                }
//
//                //if (isset($data['X-Parsed-By'])) {
//                //    unset($data['X-Parsed-By']);
//                //}
//
//                //foreach ($data as $key => $value) {
//                //   $doc->$key = $value;
//                //}
//                $doc_testo .= $data."\n";
//            } else {
//                $doc_testo .= $_POST['testo_ocr']."\n";
//                //$tmp_filename = $_FILES['scan']['tmp_name'][0];
//                $command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr".$i." -l ita PDF";
//                exec($command, $output, $status);
//                $results = rename($GLOBALS['UPLOAD_DIR']."/ocr".$i.".pdf", $GLOBALS['EDOC_DIR'].$ca.".PDF");
//
//                //move_uploaded_file($tmp_filename, $GLOBALS['THUMBNAILS_DIR'].'1.jpg'))
//                //print_r ("BEFORE");
//                //convertScan($codice_archivio);
//            }
//        }
//
////        if ($countfiles > 1) {
////            $command = $GLOBALS['MERGE_PDF_BIN'].$GLOBALS['UPLOAD_DIR']."ocr.pdf ";
////            for ($i=0; $i<$countfiles; $i++) {
////                $command = $command.$GLOBALS['UPLOAD_DIR']."ocr".$i.".pdf ";
////            }
////            exec($command, $output, $result);
////        }
////    
//                    //$doc->resourceName = basename($_FILES['edoc']['name'][$i]);
//        //} //else {
//        //    $doc->resourceName = basename($_FILES['scan']['name'][0]);    
//        //}

    if ($_POST['tipologia'] == 'SONETTO') {
        $doc_testo = $_POST['testo_ocr']."\n";
        $tmp_filename = $_FILES['scan']['tmp_name'];
        $tmp = explode(".", $_FILES['scan']['name']);
        $ext = strtolower(end($tmp));

        if ($ext == "tiff" or $ext == "tif") {
            $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename."[0] ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            exec($command, $output, $status);
        } else if ($ext == "png") {
            $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename." ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            exec($command, $output, $status);
        } 

        $command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr".$i." -l ita PDF";
        exec($command, $output, $status);
        $results = rename($GLOBALS['UPLOAD_DIR']."/ocr".$i.".pdf", $GLOBALS['EDOC_DIR'].$codice_archivio.".PDF");
    
        $doc->testo = $doc_testo;
        $doc->committente = $_POST['committente']; 
        $doc->ricorrenza = $_POST['ricorrenza']; 	
        $doc->autore = $_POST['autore'];
        $doc->dedica = $_POST['dedica']; 
        $doc->anno = $_POST['anno']; //substr($_POST['data'], 0, 4);
        $doc->data = $_POST['data'];
        $doc->stampato_da = $_POST['stampato_da'];         
        $doc->dimensioni = $_POST['dimensioni']; 
        $doc->note = $_POST['note'];
        $doc->privato = 0;

        if ($ext == "tiff" or $ext == "tif") {
            $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename."[0] -resize x200 ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            exec($command, $output, $status);
        } else if ($ext == "png") {
            $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename." -resize x200 ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            exec($command, $output, $status);
        } else if ($ext == "jpg" or $ext == "jpeg") {
            $resize = new ResizeImage($_FILES['scan']['tmp_name']);
            $resize->resizeTo(200, 200, 'maxHeight');
            $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG");
        }

    } else if ($_POST['tipologia'] == "PERGAMENA") {
        $tmp_filename = $_FILES['scan']['tmp_name'];
        $tmp = explode(".", $_FILES['scan']['name']);
        print ($tmp_filename);
        //$duplicate = lookForDuplicates($resourceName);

        $ext = strtolower(end($tmp));

        $resourceName = $codice_archivio.".".strtoupper($ext);
        $doc->resourceName = $resourceName;
        $doc->descrizione = $_POST['descrizione']; 
        $doc->tecnica = $_POST['tecnica']; 	
        $doc->autore = $_POST['autore']; 
        $doc->anno = $_POST['anno']; 
        $doc->dimensioni = $_POST['dimensioni']; 
        $doc->note = $_POST['note'];
        $doc->privato = 0;

        if ($ext == "tiff" or $ext == "tif") {
            $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename."[0] -resize x200 ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            exec($command, $output, $status);
        } else if ($ext == "png") {
            $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename." -resize x200 ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            exec($command, $output, $status);
        } else if ($ext == "jpg" or $ext == "jpeg") {
            $resize = new ResizeImage($tmp_filename);
            $resize->resizeTo(200, 200, 'maxHeight');
            $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG");
        }

        $status = move_uploaded_file($tmp_filename, $GLOBALS['EDOC_DIR'].$resourceName);
    } else if ($_POST['tipologia'] == "BOZZETTO") {
        $tmp_filename = $_FILES['scan']['tmp_name'];
        $tmp = explode(".", $_FILES['scan']['name']);

        //$duplicate = lookForDuplicates($resourceName);

        $ext = strtolower(end($tmp));

        $resourceName = $codice_archivio.".".strtoupper($ext);
        $doc->resourceName = $resourceName;
        $doc->categoria = $_POST['categoria']; 
        $doc->descrizione = $_POST['descrizione']; 
        $doc->tecnica = $_POST['tecnica']; 	
        $doc->autore = $_POST['autore']; 
        $doc->anno = $_POST['anno']; 
        $doc->dimensioni = $_POST['dimensioni']; 
        $doc->note = $_POST['note'];
        $doc->privato = 0;

        if ($ext == "tiff" or $ext == "tif") {
            $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename."[0] -resize x200 ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            exec($command, $output, $status);
        } else if ($ext == "png") {
            $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename." -resize x200 ".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            exec($command, $output, $status);
        } else if ($ext == "jpg" or $ext == "jpeg") {
            $resize = new ResizeImage($tmp_filename);
            $resize->resizeTo(200, 200, 'maxHeight');
            $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG");
        }

        move_uploaded_file($tmp_filename, $GLOBALS['EDOC_DIR'].$resourceName);
    }

    $error = "";
    try {
        $update->addDocuments(array($doc));
        $update->addCommit();
        $result = $client->update($update);
    } catch (Solarium\Exception\HttpException $e) {
        $error = $e->getMessage();
    }

    if ($error != "") {
        echo json_encode(array('error' => $error));
        exit;
    } else {
        //$error = convertScan($codice_archivio);
        //if ($error != "") {
        //    echo json_encode(array('error' => $error));
        //    exit;
        //}

        echo json_encode(array('result' => "Volume ".$codice_archivio." inserito correttamente."));
        return;
    }

    return;
}
?>
