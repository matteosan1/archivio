<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

include ("db.php");
require_once("class/exif.php");

$tagl1 = "palio"; //$sqlite->querySingle("SELECT name FROM tags WHERE id=".$_POST['tagl1'].";", FALSE);
$tagl2 = "aceto"; //$sqlite->querySingle("SELECT name FROM tags WHERE id=".$_POST['tagl2'].";", FALSE);
$list_of_tags = array("pippo22", "pluto"); //explode(",", $_POST['list_of_tags']);
$img_ext = array('tif', 'tiff', 'jpeg', 'jpg');

$target_directory = '/var/www/myupload/'.$tagl1.'/'.$tagl2.'/';
$maxsize = 200000000;

//$ext_ok = array('csv', 'pdf', 'tif', 'tiff', 'jpeg', 'jpg', 'png', 'doc', 'docx');
if (isset($_POST['submit'])) {
	
	if (!isset($_FILES['userfile'])) { // || !is_uploaded_file($_FILES['userfile']['tmp_name'])) {
                echo 'No file uploaded...\n';
                exit;
        }

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
			echo "File type ".end($ext)." is not allowed";
			exit;
		}
    	}	
}	

function basicCheckOnFile($i) {
	global $maxsize, $target_directory;

	if ($_FILES['userfile']['size'][$i] > $maxsize) {
                echo "File ".$_FILE['userfile']['name'][$i]." is too big !";
               	return FALSE;
        }
	
	$target_file = $target_directory.$_FILES['userfile']['name'][$i];
	if (file_exists($target_file)) {
                echo 'File '.$_FILES['userfile']['name'][$i].'already exists.';
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
                echo 'Unziped!';
        } else {
                echo "Unzip of ".$filename." (".$zipfilename.") failed!";
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
    		echo 'File '.$userfile_name.' successfully uploaded.';
	    	//exec(dirname(__FILE__) . '/myscript.sh');
		//shell_exec
	} else {
    		echo 'Upload of '.$userfile_name.' failed';
	}

	addEXIF($target_directory . $userfile_name);
}
?>
