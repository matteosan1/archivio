<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

session_start();

if(empty($_SESSION["userId"])) {
  header ("Location: /site/Usered/index.php");
} else { 
  $datetime2 = strtotime($_SESSION['time']);
  $datetime1 = strtotime(date('Y-m-d H:i:s'));
  $minutes = ($datetime1 - $datetime2)/60;
  if ($minutes < 10) {
     $_SESSION['time'] = date('Y-m-d H:i:s');
     $displayName = $_SESSION["name"];
     $role = $_SESSION["role"];
  } else {
     unset($_SESSION['userId']);
     header ("Location: /site/Usered/index.php");
  }
}

function csv_to_array($filename='', $delimiter='|')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE) {
       while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
       	     if(!$header)
	         $header = $row;
	     else
	         $data[] = array_combine($header, $row);
       }
       fclose($handle);
    }
    return $data;
}


//curl_setopt($ch, CURLOPT_URL, 'http://localhost:8983/solr/admin/cores?action=status&core=mpscs_docs&indexInfo=true');
//curl_setopt($ch, CURLOPT_URL, 'http://lx000000010550.sum.local:8983/solr/commodity/select?fl=last_modified&q=*:*&rows=1&sort=last_modified%20desc');
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

//$data = curl_exec($ch);
//$dict = json_decode($data, true);

$exit_status = "";
$error = "";
$link = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

   $ch = curl_init();
   $last_upload = date( 'Y-m-d\T\0\0\:\0\0\:\0\0\Z', strtotime($_POST['last_upload']));
   curl_setopt($ch, CURLOPT_URL, 'http://localhost:8983/solr/bibliof/select?fq=timestamp:['.$last_upload.'%20TO%20NOW]&q=*:*&rows=10000&wt=csv');
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
   $data = curl_exec($ch);
   curl_close($ch);


   $csv_filename = "/Users/sani/myupload/backup_".$last_upload.".csv";
   file_put_contents($csv_filename, $data);
   $entries = csv_to_array($filename='', $delimiter=',');
   //$zip = new ZipArchive();
   //$zip_filename = "/Users/sani/myupload/copertine_".$last_upload.".zip";
   //if ($zip->open($zip_filename, ZipArchive::CREATE)!= true) {
   //   $error + "Non posso aprire ".$zip_filename;
   //   exit;
   //}
   
   //foreach($entry as $entries) {
   //   $zip->addFile($entry);
   //}
   //$zip->close();
   $link = '<a href="'.$csv_filename.'">Catalogo  CSV</a><br>';	
   //$link = $link.' <a href="'.$zip_filename.'">Copertine ZIP</a><br>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Backup Catalogo</title>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
	$(function(){
	  $("#header").load("/site/Usered/view/header.html"); 
	    //$("#footer").load("/site/Usered/view/footer.html"); 
	});
    </script>
</head>

<body>
<div id="header" align="center"></div>
<br>

<h2 align="center">Backup Catalogo</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
  <label for="backup_data">Data di backup:</label>
  <input type="date" id="last_upload" name="last_upload">
  <input type="submit" value="Backup">
</form>
<div id="link"><?php echo $link; ?></div>
</body>
</html>