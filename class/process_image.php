<?php
//error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once "../class/exif.php";
require_once "../class/Member.php";
require_once "../view/solr_client.php";
require_once "../class/solr_utilities.php";
require_once "../class/resize_image.php";

$m = new Member();
$tagl1 = $m->getTagNameById($_POST['tagl1']);
$tagl2 = $m->getTagNameById($_POST['tagl2']);
$list_of_tags = explode(",", $_POST['list_of_tags']);
$img_ext = array('tif', 'tiff', 'jpeg', 'jpg');

$target_directory = $GLOBALS['PHOTO_DIR'].$tagl1.'/'.$tagl2.'/';
$maxsize = $GLOBALS['MAX_UPLOAD_BYTE'];

$countfiles = count($_FILES['userfile']['name']);
for ($i=0; $i<$countfiles; $i++) {
    $filename = $_FILES['userfile']['name'][$i];
    $ext = explode(".", $filename);
    if (end($ext) == "zip") {
	if (basicCheckOnFile($i)) {
            processZip($_FILES['userfile']['tmp_name'][$i]);
	}
    } elseif (in_array(end($ext), $img_ext)) {
	if (basicCheckOnFile($i)) {
     	    processImage($_FILES['userfile']['tmp_name'][$i], $_FILES['userfile']['name'][$i], end($ext));
	}
    } else {		
	echo json_encode(array("error"=>"File di tipo ".end($ext)." non possono essere caricati !"));
	exit;
    }	
}	

echo json_encode(array("result"=>"Le immagini sono state caricate."));
exit;

function basicCheckOnFile($i) {
    global $target_directory;

    if ($_FILES['userfile']['size'][$i] > $GLOBALS['MAX_UPLOAD_BYTE']) {
	echo json_encode(array("error"=>"Il file ".$_FILE['userfile']['name'][$i]." &egrave; troppo grande (>".$GLOBALS['MAX_UPLOAD_BYTE']." Bytes) !"));
      	exit;
    }

    checkForDuplicates(basename($_FILES['userfile']['name'][$i]));
    
    return TRUE;
}

function checkForDuplicates($resourceName) {

    $duplicate = lookForDuplicates($resourceName);    

    if ($duplicate == -1) {
       echo json_encode(array("error"=>"Il file ".$resourceName." esiste gi&agrave;."));
       exit;
    }
}

function addEXIF($filename) {
    global $list_of_tags, $tagl1, $tagl2;
    
    $objIPTC = new IPTC($filename);
    $additional_tags = implode(" ", $list_of_tags);
    //print_($$additional_tags);
    //print_r($tagl1." ".$tagl2." ".$additional_tags);
    $objIPTC->setValue(IPTC_KEYWORDS, $tagl1." ".$tagl2." ".$additional_tags);
    if (isset($_POST['author'])) {
       $objIPTC->setValue(IPTC_BYLINE, $_POST['author']);
    }
}

function processZip($zipfilename) {
    global $img_ext, $target_directory;

    $zip = new ZipArchive;
    $res = $zip->open($zipfilename);
	
    if ($res === TRUE) {
	for($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $fileinfo = pathinfo($filename);
	    $tmp_name = $GLOBALS['UPLOAD_DIR'].$fileinfo['basename'];
	    checkForDuplicates($fileinfo['basename']);
            copy("zip://".$zipfilename."#".$filename, $tmp_name);
	    processImage($tmp_name, $fileinfo['basename'], $fileinfo['extension'], false);
	}                  
    } else {
	echo json_encode(array("error"=>"L'unzip di ".$filename." (".$zipfilename.") &egrave; fallito !"));
        exit;
    }
    $zip->close();
}

function processImage($tmp_name, $real_name, $ext, $move=true) {
    global $target_directory, $client;
    
    //print ($target_directory);
    if (! file_exists($target_directory)) {
	    mkdir($target_directory, 0777, TRUE);
    }

    $index = getLastByIndex("FOTO") + 1;
    $ca = "FOTO.".str_pad($index, 6, "0", STR_PAD_LEFT);
    
    $name = $target_directory.$ca.".".strtoupper($ext);

    $resize = new ResizeImage($tmp_name);
    $resize->resizeTo(200, 200, 'maxHeight');
    $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$ca.".JPG");

    if ($move) {
        $ret = move_uploaded_file($tmp_name, $name);
    } else {
      	$ret = rename($tmp_name, $name);
    }

    // FIXME CONTROLLARE TRASFERIMENTO
    //} else {
    //	echo json_encode(array("error"=>'Il caricamento di '.$userfile_name.' &egrave; fallito !'));
//	exit;
//    }
    
    addEXIF($name);

    $update = $client->createUpdate();
    $doc = $update->createDocument();
    
    $command = "/usr/bin/java -jar ".$GLOBALS['TIKA_APP']." -j -t -J ".$name;
    $output = shell_exec($command);
    $data = json_decode($output, true)[0];

    $doc->codice_archivio = $ca;
    $doc->tipologia = "FOTOGRAFIA";
    $doc->resourceName = basename($real_name);
    $doc->privato = 0;

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

    if ($error != "") {
        echo json_encode($error);
	exit;
    }
       
    return $error;
}
?>
