<?php

require_once "../view/config.php";
require_once "../class/solr_utilities.php";
require_once "../view/solr_client.php";
require_once "../class/resize_image.php";
require_once "../class/Member.php";
    
if (isset($_POST)) {
    if ($_POST['tipologia'] == 'DOCUMENTO') {   
        $m = new Member();
        $prefix = $m->getPrefisso($_POST["tipologia"]);

        $countfiles = count($_FILES['scan']['name']);
        $doc_text = '';
        for ($i=0; $i<$countfiles; $i++) {
            $tmp_filename = $_FILES['scan']['tmp_name'][$i];
            $resourceName = basename($_FILES['scan']['name'][$i]);
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
            $data['resourceName'] = $resourceName;
            $doc_text .= $data['testo'];
        }

        $_POST['anno'] = substr($data['cdate'], 0, 4);
        $id = getLastByIndex($prefix.".".$_POST['anno']) + 1;
        $codice_archivio = $prefix.".".$_POST['anno'].".".str_pad($id, 2, "0", STR_PAD_LEFT);

        $update = $client->createUpdate();
        $doc = $update->createDocument();
    
        $doc->codice_archivio = $codice_archivio;
        $doc->tipologia = $_POST['tipologia'];

        $doc->anno = $_POST['anno'];
        $doc->note = $_POST['note'];
        $doc->titolo = $_POST['titolo'];
        $doc->autore = $_POST['autore'];
    
        foreach ($data as $key => $value) {
            if ($key == "autore" or $key == "testo") {
                continue;
            }
            $doc->$key = $value;
        }
        $doc->testo = $doc_text."\n";
        $doc->privato = 0;

        if ($countfiles == 1) {
            if ($ext == "pdf") {
                //$command = $GLOBALS['PDF2IMAGE_BIN']." gs -dSAFER -dNOPLATFONTS -dNOPAUSE -dBATCH -sOutputFile='".$GLOBALS['UPLOAD_DIR']."outputFileName.jpg' -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dUseTrimBox -dFirstPage=1 -dLastPage=1 ".$tmp_filename;
                $command = "gs -dSAFER -dNOPLATFONTS -dNOPAUSE -dBATCH -sOutputFile='".$GLOBALS['UPLOAD_DIR']."outputFileName.jpg' -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dUseTrimBox -dFirstPage=1 -dLastPage=1 ".$tmp_filename;
                exec($command, $output, $status);
     	        $resize = new ResizeImage($GLOBALS['UPLOAD_DIR']."outputFileName.jpg");
          	    $resize->resizeTo(200, 200, 'maxHeight');
          	    $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$codice_archivio."."."JPG");
            } else if ($ext == "jpg" or $ext == "jpeg") {
                $resize = new ResizeImage($tmp_filename);
                $resize->resizeTo(200, 200, 'maxHeight');
                $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG");
            }

            $results = rename($tmp_filename, $GLOBALS['EDOC_DIR'].$codice_archivio.".".strtoupper($ext));
            chmod ($GLOBALS['EDOC_DIR'].$codice_archivio.".".strtoupper($ext), 0644);
        } else if ($countfiles > 1) {
            for ($i=0; $i<$countfiles; $i++) {
                $tmp_filename = $_FILES['scan']['tmp_name'][$i];
                if ($i == 0) {
                    $resize = new ResizeImage($tmp_filename);
                    $resize->resizeTo(200, 200, 'maxHeight');
                    $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG");
                }
        
                $command = $GLOBALS['OCR_BIN']." ".$tmp_filename." ".$GLOBALS['UPLOAD_DIR']."/ocr".$i." -l ita PDF";
                exec($command, $output, $status);
            }
  
            $command = $GLOBALS['MERGE_PDF_BIN'].$GLOBALS['EDOC_DIR'].$ca."PDF";
            for ($i=0; $i<$countfiles; $i++) {
                $command = $command.$GLOBALS['UPLOAD_DIR']."ocr".$i.".pdf ";
            }
            exec($command, $output, $result);
            //$target_directory = $GLOBALS['EDOC_DIR'].$ca."PDF";
            //$results = rename($tmp_filename, $GLOBALS['EDOC_DIR'].$codice_archivio.".".strtoupper($ext));
        }
    } else {
        $m = new Member();
        $prefix = $m->getPrefisso($_POST["tipologia"]);
        $id = getLastByIndex($prefix.".".$_POST['anno']) + 1;
        $codice_archivio = $prefix.".".$_POST['anno'].".".str_pad($id, 2, "0", STR_PAD_LEFT);

        $update = $client->createUpdate();
        $doc = $update->createDocument();
    
        $doc->codice_archivio = $codice_archivio;
        $doc->tipologia = $_POST['tipologia'];

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

	    $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename." -resize x200 ..".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            if ($ext == "tiff" or $ext == "tif") {
	    	 $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename."[0] -resize x200 -colorspace sRGB -quality 80 ..".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            }
            exec($command, $output, $status);	    	    
            move_uploaded_file($tmp_filename, $GLOBALS['EDOC_DIR'].$resourceName);
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

	    $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename." -resize x200 ..".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            if ($ext == "tiff" or $ext == "tif") {
	    	 $command = $GLOBALS['CONVERT_BIN']." ". $tmp_filename."[0] -resize x200 -colorspace sRGB -quality 80 ..".$GLOBALS['THUMBNAILS_DIR'].$codice_archivio.".JPG";
            }
            exec($command, $output, $status);	    	    
            move_uploaded_file($tmp_filename, $GLOBALS['EDOC_DIR'].$resourceName);
        }
    }

    $error = "";
    try {
        $update->addDocuments(array($doc));
        $update->addCommit();
        $result = $client->update($update);
        //print_r ($result);
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
