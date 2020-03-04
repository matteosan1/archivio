<?php
//require_once "../view/session.php";
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

$exit_status = "";
$error = "";
$codice_archivio = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (isset($_POST['volume'])) {
       $codice_archivio = $_POST['volume'];
   }
     if (empty($_POST["codice_archivio"])) {
     	$error = "Il codice_archivio &egrave; necessario.<br>";
     } 

     if ($_POST["tipologia"] == "----") {
     	$error = "La tipologia &egrave; necessaria.";
     }

     if (empty($_POST["titolo"])) {
      	$error = "Il titolo ?";
     }

     if ($error == "") {
     	$header = "codice_archivio,tipologia,titolo,sottotitolo,prima_responsabilita,altre_responsabilita,luogo,edizione,ente,serie,anno,descrizione,cdd,soggetto,note\n";
      	$data = $_POST['codice_archivio']."|".$_POST['tipologia']."|".$_POST['titolo']."|".$_POST['sottotitolo']."|".$_POST['prima_responsabilita']."|".$_POST['altre_responsabilita']."|".$_POST['luogo']."|".$_POST['edizione']."|".$_POST['ente']."|".$_POST['serie']."|".$_POST['anno']."|".$_POST['descrizione']."|".$_POST['cdd']."|".$_POST['soggetto']."|".$_POST['note']."\n";
      
	$fileName = "/Users/sani/myupload/newbook_" . date("d-m-y-H-i-s") . ".csv";
      	if (file_exists($fileName)) {
      	   file_put_contents($fileName, $data, FILE_APPEND);
      	} else {
           file_put_contents($fileName, $header . $data);
      	}
	
	$result = json_decode(upload_csv($fileName), true);
	if ($result['responseHeader']['status'] == 0) {
	    $exit_status = "File ".$fileName." creato correttamente.";
	} else {
	    $error = $result['responseHeader']['error'];
	}
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
        <title>Inserimento Libri</title>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
var request;
$(document).ready(function() {
    $("#volume").change(function() {
    	//alert( "Handler for .change() called." );

	var sel = $(this).val();
	request = $.ajax({
                url: "../class/find_record.php",
                type: "POST",
                data: {'sel':sel},
        });

	request.done(function (response){
	    var dict = JSON.parse(response);
	    // FIXME SEMPLIFICARE CON UN BEL FOR
	    document.getElementById("codice_archivio").value = dict['codice_archivio'];
	    document.getElementById("tipologia").value = dict['tipologia'];
    	    document.getElementById("titolo").value = dict['titolo'];
    	    document.getElementById("sottotitolo").value = dict['sottotitolo'];
    	    document.getElementById("prima_responsabilita").value = dict['prima_respo'];
    	    document.getElementById("altre_responsabilita").value = dict['altre_respo'];
    	    document.getElementById("luogo").value = dict['luogo'];
    	    document.getElementById("edizione").value = dict['edizione'];
    	    document.getElementById("ente").value = dict['ente'];
   	    document.getElementById("serie").value = dict['serie'];
    	    document.getElementById("anno").value = dict['anno'];
   	    document.getElementById("descrizione").value = dict['descrizione'];
       	    document.getElementById("cdd").value = dict['cdd'];
       	    document.getElementById("soggetto").value = dict['soggetto'];
	    document.getElementById("note").value = dict['note'];
        });

    });
});
</script>
<body>
    <div id="header" align="center"></div>
    <br>	 
    <div align="center"><?php echo $exit_status;?></div>
    <br>
    <span class="error" style="color:red"><?php echo $error;?></span>
    <br>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    	  <label for="cars">Scegli volume:</label>
    	  <select id="volume" name="volume">
	       <?php echo $selects;  ?>
  	  </select>
    </form>
    <br>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
      <table>
      <div class="form-1">
      <tr>
           <div class="col-1">
		    	<td>
                        <label for="fname" class="fname">Codice archivio:</label>
			</td><td>
			<input type="text" size="25" id="codice_archivio" name="codice_archivio" placeholder="XXXX.YY" value=<?php echo '"'.$codice_archivio.'"'?>>
			</td>
		    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	<td>
                        <label for="fname" class="fname">Tipologia:</label>
			</td><td>
	                <select name="tipologia" class="tipologia" id="tipologia">
	                <option selected="selected">----</option>
			<option>LIBRO</option>
			<option>PUBBLICAZIONE_DI_CONTRADA</option>
			<option>PERIODICO</option>
			<option>NUMERO_UNICO</option>
			<option>RIVISTA</option>
			<option>LIBRI_DELLA_LITURGIA</option>
			<option>MANOSCRITTO</option>
			<option>OPUSCOLO</option>
			<option>TESI</option>
	                </select>
			</td>
		    </div>
		    </tr>
		    <tr>
    		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Titolo:</label>
			 </td><td>
			 <textarea id="titolo" name="titolo" rows="3" cols="80" placeholder=Titolo del libro"></textarea>
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Sottotitolo:</label>
			 </td><td>
			 <textarea id="sottotitolo" name="sottotitolo" rows="3" cols="80" placeholder="Eventuale sottotitolo"></textarea>
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Prima responsabilit&agrave;:</label>
			 </td><td>
                         <input type="text" size="50" id="prima_responsabilita" name="prima_responsabilita">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Altre responsabilit&agrave;:</label>
			 </td><td>
                         <input type="text" size="80" id="altre_responsabilita" name="altre_responsabilita">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Luogo:</label>
			 </td><td>
                         <input type="text" size="50" id="luogo" name="luogo">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Edizione:</label>
			 </td><td>
                         <input type="text" size="50" id="edizione" name="edizione">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Ente:</label>
			 </td><td>
                         <input type="text" size="50" id="ente" name="ente">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Serie:</label>
			 </td><td>
                         <input type="text" size="50" id="serie" name="serie">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Anno:</label>
			 </td><td>
                         <input type="number" size="4" id="anno" name="anno" placeholder="XXXX">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Descrizione:</label>
			 </td><td>
                         <input type="text" size="25" id="descrizione" name="descrizione" placeholder="XX p. : ill. ; YY cm">
			 </td>
                    </div>
		    </tr>
		    <tr>
    		    <div class="col-1">
		    	 <td>
		    	 <label for="fname" class="fname">CDD:</label>
			 </td><td>
                         <input type="text" size="12" id="cdd" name="cdd" placeholder="123.456789">
			 </td>
                    </div>
		    </tr>
		    <tr>
    		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Soggetto:</label>
			 </td><td>
                         <input type="text" size="80" id="soggetto" name="soggetto">
			 </td>
                    </div>
		    </tr>
		    <tr>
    		    <div class="col-1">
		    	 <td>
		    	 <label for="fname" class="fname">Note:</label>
			 </td><td>
			 <textarea name="note" rows="10" cols="80" placeholder="note"></textarea>
			 </td>
                    </div>
		    </tr>
                </div>
		</table>
		<button type="button" id="registerBtn" class="btn btn-success"> Aggiorna </button>
            </form>
    </body>
</html>
