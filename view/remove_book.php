<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../view/config.php";

function listRecords() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'query?fl=codice_archivio&q=*&sort=codice_archivio+asc&wt=json');
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
    		     			       'Accept: application/json'));	 

    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $result;
}

function removeRecord($codici) {
	 //curl -X POST -H 'Content-Type: application/json' 'http://localhost:8983/solr/bibliof/update?commit=true' --data-binary '{"delete":{"id":"59de0169-90a7-40bb-bc73-011257228750"}}'
    $result = "";
    foreach ($codici as $cod) {      
    	    $data = array("codice_archivio" => $cod); 
    	    $ch = curl_init();
    	    curl_setopt($ch, CURLOPT_URL, $GLOBALS['SOLR_URL'].'update?commit=true');
    	    curl_setopt($ch, CURLOPT_HTTPPOST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
    	    		     			       'Accept: application/json'));
		

    	    $result = $result.json_decode(curl_exec($ch), true);
    	    curl_close($ch);
    }

    return $result;
}

$exit_status = "";
$error = "";
$selects = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (isset($_POST['volumi'])) {
       $exit_status = removeRecord($_POST['volumi']);
//	$result = json_decode(upload_csv($fileName), true);
//	if ($result['responseHeader']['status'] == 0) {
//	    $exit_status = "File ".$fileName." creato correttamente.";
//	} else {
//	    $error = $result['responseHeader']['error'];
//	}
//      }
    }
}

$json = listRecords();
foreach ($json['response']['docs'] as $select) {
	$selects = $selects.'<option value="'.$select['codice_archivio'].'">'.$select['codice_archivio'].'</option>';
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
        <meta charset="UTF-8" />
        <title>Rimuovi Libro</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
    crossorigin="anonymous">
</script>	
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
    <h1 align="center">Rimuovi libro dal catalogo</h1>
    <div align=center><?php echo $exit_status;?></div>
    <span class="error" style="color:red"><?php echo $error;?></span>
    <br>
     <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
    	  <label for="cars">Scegli un libro:</label>
  	  <select width=100px id="volumi" name="volumi[]" size="15" multiple>
	  <?php echo $selects; ?>
  	  </select><br><br>
  	  <input type="submit">
    </form>
    </body>
</html>
