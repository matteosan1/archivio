<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

include ("db.php");

$tagl1 = $sqlite->querySingle("SELECT name FROM tags WHERE id=".$_POST['tagl1'].";", FALSE);
$tagl2 = $sqlite->querySingle("SELECT name FROM tags WHERE id=".$_POST['tagl2'].";", FALSE);
$list_of_tags = explode(",", $_POST['list_of_tags']);
$img_ext = array('tif', 'tiff', 'jpeg', 'jpg', 'png');

//$ext_ok = array('csv', 'pdf', 'tif', 'tiff', 'jpeg', 'jpg', 'png', 'doc', 'docx');
//if (isset($_POST['submit'])) 
{
    	print_r ($_FILES);
	if (!isset($_FILES['userfile']) || !is_uploaded_file($_FILES['userfile']['tmp_name'])) {
                echo 'No file uploaded...';
                exit;
        }

   	$ext = end(explode(".", $_FILES['userfile']['name']));
	if (end($ext) == ".zip") {
		// Get Project path
		define('_PATH', dirname(__FILE__));

		$filename = 'zipfile.zip';
		$zip = new ZipArchive;
		$res = $zip->open($filename);
		if ($res === TRUE) {
 			$path = _PATH."/files/";
 			$zip->extractTo($path);
 			$zip->close();
 			echo 'Unziped!';
		} else {
 			echo 'failed!';
			exit;	
		}
    	} else if (!in_array($ext, $ext_img)) {
        } else {	
		echo 'File type is not allowed';
        	exit;
    	}

// LOOP SU I FILE UPLOADATI O UNZIPPATI
// ATTACCA EXIF 
// SPOSTARE NELLA DIRECTORY APPROPRIATA
    
    	if ($_FILES['userfile']['size'] > 20000000) {
        	echo 'File is too big !';
        	exit;
    	}
    
    	$target_file = '/var/www/myupload/' . $_FILES['userfile']['name'];
    	if (file_exists($target_file)) {
        	echo 'File already exists.';
        	exit;
    	}

    	if (in_array($ext, $img_ext)) {
        	$is_img = getimagesize($_FILES['userfile']['tmp_name']);
        	if (!$is_img) {
            	echo 'Puoi inviare solo immagini';
            	exit;    
        }
    }
    
    $uploaddir = '/var/www/myupload/';
    $userfile_tmp = $_FILES['userfile']['tmp_name'];
    $userfile_name = $_FILES['userfile']['name'];
    
    //if (move_uploaded_file($userfile_tmp, $uploaddir . $userfile_name)) {
    //    echo 'File successfully uploaded.';
    //    exec(dirname(__FILE__) . '/myscript.sh');
    //    // shell_exec
    //} else {
    //    echo 'Upload failed';
    //}
}
?>

