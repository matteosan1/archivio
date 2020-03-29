<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/config.php";
require_once "../class/solr_curl.php";

$target_directory = $GLOBALS['VIDEO_DIR'];
$maxsize = $GLOBALS['MAX_UPLOAD_BYTE'];

$countfiles = count($_FILES['videos']['name']);
for ($i=0; $i<$countfiles; $i++) {
    $filename = $_FILES['videos']['name'][$i];
    $ext = explode(".", $filename);

    $ret = basicCheckOnFile($i);

    if ($ret == 1) {
       echo '{"error":"Il file '.$_FILES['videos']['name'][$i].' esiste gia`."}';
       exit;
    }
    
    if (!isset($ret['error'])) {
        $ret = processVideo($i, end($ext));

	if (isset($ret['error'])) {
	    echo '{"error":"'.$ret['error'].'"';
	    exit;
	}
    } else {
      	print_r (array("error"=>$ret['error']));
	exit;
    }
}	

print_r (array("result"=>"I file sono stati caricati correttamente."));
exit;

function basicCheckOnFile($i) {
    global $target_directory;

    if ($_FILES['videos']['size'][$i] > $GLOBALS['MAX_UPLOAD_BYTE']) {
        echo "Il file ".$_FILE['videos']['name'][$i]." &egrave; troppo grande (>".$GLOBALS['MAX_UPLOAD_BYTE']." Bytes) !";
      	return FALSE;
    }

    $resourceName = basename($_FILES['videos']['name'][$i]);
    if (lookForDuplicates($resourceName)) {
       return FALSE;
    }
    
    return TRUE;
}

function processVideo($i, $ext) {
    global $target_directory;

    if (! file_exists($target_directory)) {
	mkdir($target_directory, 0777, TRUE);
    }
    
    $index = getLastByIndex("VID") + 1;
    $ca = "VID.".str_pad($index, 5, "0", STR_PAD_LEFT);

    // FIXME PENSARE AD EVENTUALE THUMBNAIL PER VIDEO
    if (move_uploaded_file($_FILES['videos']['tmp_name'][$i], $target_directory.$ca.".".$ext)) {
        
        $csv_data = array();
	$csv_data["codice_archivio"] = $ca;
	$csv_data["tipologia"] = "VIDEO";
	$csv_data["note"] = $_POST['note'];

	$csv_file = array2csv($csv_data);
	$ret = upload_csv2($csv_file);

	return $ret;
    } else {
      return array("error"=>"Problema nello spostamento di ".$_FILES['videos']['name'][$i]);
    }
}
?>
