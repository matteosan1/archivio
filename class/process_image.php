<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

include ("db.php");
require_once("exif.php");

if (!isset($_FILES['userfile'])) {
   echo 'Non hai scelto nessun file...';
   exit;
}

if ($_POST['tagl1'] == "---" || $_POST['tagl2'] == "---") {
	echo "Devi specificare i due tag !!!";
	exit;
}

$tagl1 = $sqlite->querySingle("SELECT name FROM tags WHERE id=".$_POST['tagl1'].";", FALSE);
$tagl2 = $sqlite->querySingle("SELECT name FROM tags WHERE id=".$_POST['tagl2'].";", FALSE);
$list_of_tags = explode(",", $_POST['list_of_tags']);
$img_ext = array('tif', 'tiff', 'jpeg', 'jpg');

$target_directory = '/Users/sani/myupload/'.$tagl1.'/'.$tagl2.'/';
$maxsize = 200000000;

//$ext_ok = array('csv', 'pdf', 'tif', 'tiff', 'jpeg', 'jpg', 'png', 'doc', 'docx');
#if (isset($_POST['submit'])) 
{
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
				processImage($_FILES['userfile']['tmp_name'][$i], $_FILES['userfile']['name'][$i]);
			}
	        } else {		
			echo "File di tipo ".end($ext)." non possono essere caricati !";
			exit;
		}
    	}	
}	

function basicCheckOnFile($i) {
	global $maxsize, $target_directory;

	if ($_FILES['userfile']['size'][$i] > $maxsize) {
                echo "Il file ".$_FILE['userfile']['name'][$i]." &egrave; troppo grande (>".$maxsize."B) !";
               	return FALSE;
        }
	
	$target_file = $target_directory.$_FILES['userfile']['name'][$i];
	if (file_exists($target_file)) {
                echo 'Il file '.$_FILES['userfile']['name'][$i].' esiste gi&agrave;.';
                return FALSE;
	}

	return TRUE;
}

function addEXIF($filename) {
	global $list_of_tags, $tagl1, $tagl2;

	$objIPTC = new IPTC($filename);
	$additional_tags = implode(" ", $list_of_tags);
	$objIPTC->setValue(IPTC_KEYWORDS, $additional_tags." ".$tagl1." ".$tagl2);
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
			rename('/var/www/myupload/'.$fz, $target_directory . $fz);
			addEXIF($target_directory . $fz);
		}
	}
	$zip->close();
}

function processImage($userfile_tmp, $userfile_name) {

	global $target_directory;

	if (! file_exists($target_directory)) {
		mkdir($target_directory, 0777, TRUE);
	}

    	if (move_uploaded_file($userfile_tmp, $target_directory . $userfile_name)) {
    		echo 'Il file '.$userfile_name.' &egrave; stato caricato.';
	    	//exec(dirname(__FILE__) . '/myscript.sh');
		//shell_exec
	} else {
    		echo 'Il caricamento di '.$userfile_name.' &egrave; fallito !';
	}

	addEXIF($target_directory . $userfile_name);
}
?>
