<?php
    require_once "../view/session.php";
    require_once "../view/config.php";

    $exit_status = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    	if (isset($_FILES['filecsv'])) {
    	    $fileName = $_FILES["filecsv"]["tmp_name"];
    
    	    if ($_FILES["filecsv"]["size"] > 0) {
               $csv_file = file_get_contents($fileName);
    
    	       $ch = curl_init();
    	       curl_setopt($ch, CURLOPT_URL, $SOLR_URL.'/update?commit=true');
    	       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	       curl_setopt($ch, CURLOPT_POST,           true);
    	       curl_setopt($ch, CURLOPT_POSTFIELDS,     $csv_file); 
    	       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/csv'));
    
    	       $result = curl_exec ($ch);
    	       curl_close($ch);
    
    	       $exit_status = $result;
      	    }
    	}
    
    	if (isset($_FILES['filezip'])) {
      	   if ($_FILES["filezip"]["size"] > 0) {
      	      $zip = new ZipArchive;
    	      $res = $zip->open($fileName);
    	      if ($res == true) {
      	      	 $zip->extractTo('/myzips/extract_path/');
    		 $zip->close();
    	    	 $exit_status = "Copertine unzippate correttamente.";
    	      } else {
    	      	 $exit_status = "errore";
    	      }
      	   }     
        }
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

<h2 align="center">Ripristina Catalogo</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data">
    <label class="col-md-4 control-label">File di Catalogo (.CSV)</label> <input type="file" name="file" id="filecsv" accept=".csv">
    <button type="submit" id="submit" name="import" class="btn-submit">Import</button>
    <br>
    <label class="col-md-4 control-label">File delle copertine (.ZIP)</label> <input type="file" name="file" id="filezip" accept=".zip">
    <button type="submit" id="submit" name="import" class="btn-submit">Import</button>
    <div id="labelError"></div>
</form>

<div id="status"><?php echo $exit_status; ?></div>
</body>
</html>