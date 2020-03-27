<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

//include ("db.php");
require_once "../view/config.php";
require_once "../class/exif.php";
require_once "../class/solr_curl.php";

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
     	    processImage($i, $ext); //$_FILES['userfile']['tmp_name'][$i], $_FILES['userfile']['name'][$i]);
	}
    } else {		
	echo "File di tipo ".end($ext)." non possono essere caricati !";
	exit;
    }	
}	

function basicCheckOnFile($i) {
    global $target_directory;

    if ($_FILES['userfile']['size'][$i] > $GLOBALS['MAX_UPLOAD_BYTE']) {
        echo "Il file ".$_FILE['userfile']['name'][$i]." &egrave; troppo grande (>".$GLOBALS['MAX_UPLOAD_BYTE']." Bytes) !";
      	return FALSE;
    }
	
    $resourceName = basename($_FILES['userfile']['name'][$i]);
    if (lookForEDocDuplicates($resourceName)) {
       echo "Il file ".$_FILE['userfile']['name'][$i]." esiste gi&agrave;.";
       return FALSE
    }
    
    return TRUE;
}

function addEXIF($filename) {
    global $list_of_tags, $tagl1, $tagl2;

    $objIPTC = new IPTC($filename);
    $additional_tags = implode(" ", $list_of_tags);
    $objIPTC->setValue(IPTC_KEYWORDS, $additional_tags." ".$tagl1." ".$tagl2);
    if (isset($_POST['author'])) {
       $objIPTC->setValue(IPTC_BYLINE, $_POST['author']);
    }
}

function processZip($zipfilename) {
    global $img_ext, $target_directory;

    $zip = new ZipArchive;
    $res = $zip->open($zipfilename);
	
    if ($res === TRUE) {
       	$zip->extractTo(dirname($zipfilename));
        echo 'Unzipped !';
    } else {
        echo "L'unzip di ".$filename." (".$zipfilename.") &egrave; fallito !";
        exit;
    }

    for($i = 0; $i < $zip->numFiles; $i++) {   
	$fz = $zip->getNameIndex($i);
	$ext = explode(".", $fz);
	if (in_array(end($ext), $img_ext)) {
	   processImage($GLOBALS['UPLOAD_DIR'].$fz, $fz, end($ext), false);
	}
    }
    $zip->close();
}

function processImage($tmp_name, $name, $ext, $move=true) {
    global $target_directory;

    if (! file_exists($target_directory)) {
	mkdir($target_directory, 0777, TRUE);
    }
    
    $index = getLastByIndex("FOTO") + 1;
    $ca = "FOTO.".str_pad($index, 6, "0", STR_PAD_LEFT);
    
    //$tmp_name = $_FILES['userfile']['tmp_name'][$i];
    $name = $target_directory.$ca.".".strtoupper($ext);

    $resize = new ResizeImage($tmp_name);
    $resize->resizeTo(200, 200, 'maxHeight');
    $resize->saveImage($GLOBALS['THUMBNAILS_DIR'].$ca.".".strtoupper($ext));

    if ($move) {
        $ret = move_uploaded_file($tmp_name, $name);
    } else {
      	$ret = rename($tmp_name, $name);
    }
     
    {
       addEXIF($name);

       $command = "/usr/bin/java -jar ".$GLOBALS['TIKA_APP']." -j -t -J ".$name;
       $output = shell_exec($command);
       $data = json_decode($output, true)[0];

       $data["codice_archivio"] = $ca;
       $data["tipologia"] = "FOTOGRAFIA";
       $data["resourceName"] = basename($name); //_FILES["userfile"]["name"][$i];
       //if (isset($data['X-TIKA:content'])) {
       //   $data['text'] = $data['X-TIKA:content'];
       //   unset($data['X-TIKA:content']);
       //}
       //
       //if (isset($data['X-Parsed-By'])) {
       //   unset($data['X-Parsed-By']);
       //}

       $ret = upload_csv2(array2csv($data));
       if ($ret['responseHeader']['status'] != 0) {
           echo json_encode(array('error' => $ret['error']['msg']));  
       } else {
           echo json_encode(array('result' => "fotografia ".$data['codice_archivio']." inserita correttamente."));
       }
    } else {
    	echo 'Il caricamento di '.$userfile_name.' &egrave; fallito !';
    }
}
?>
