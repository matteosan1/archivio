<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/Member.php";
//require_once "../class/solr_curl.php";

$m = new Member();
$categories = $m->getAllCategories();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <meta charset="UTF-8" />
        <title>Inserimento Libri</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
    crossorigin="anonymous">
</script>	
<script>
$(function(){
  $("#header").load("/view/header.html"); 
  $("#footer").load("/view/footer.html"); 
});
</script>
    </head>
<script type="text/javascript">
var request;
$(document).ready(function() {
    $('.insert_catalogue').click(function() {
    	var formData = new FormData(document.getElementById("new_catalogue"));
        
        if (request) {
            request.abort();
        }

	request = $.ajax({
                url: "../class/validate_new_book.php",
                type: "post",
                data: formData,
                contentType: false,
                cache: false,
                processData:false                       
        });

        request.done(function (response){
	        response = JSON.parse(response);
                if(response.hasOwnProperty('error')){
		    alert (response['error']);
                } else {
                    window.location.href = "../view/dashboard.php";
		    return true;
                }
        });

        request.fail(function (response){			    
                console.log(
                    "The following error occurred: " + response
                );
        });
	return false;

    });

    $('.insert_book').click(function() {
	var formData = new FormData(document.getElementById("new_book"));
        
        if (request) {
            request.abort();
        }

        if (document.getElementById('codice_archivio').value == "") {
	   alert ("Il codice_archivio deve essere specificato.");
	   return false;
	}

        if (document.getElementById('tipologia').value == "----") {
	   alert ("La tipologia deve essere specificata.");
	   return false;
	}

        if (document.getElementById('titolo').value == "") {
	   alert ("Volume senza titolo ? uhm...");
	   return false;
	}
	
        request = $.ajax({
                url: "../class/validate_new_book.php",
                type: "post",
                data: formData,
                contentType: false,
                cache: false,
                processData:false                       
        });

        request.done(function (response){
	        //$('#exit_status').html(response);
	        response = JSON.parse(response);
                if(response.hasOwnProperty('error')){
		    alert (response['error']);
                } else {
                    window.location.href = "../view/dashboard.php";
		    return true;
                }
        });

        request.fail(function (response){			    
                console.log(
                    "The following error occurred: " + response
                );
        });
	return false;
   });
});
</script>
    <body>
    <div id="header" align="center"></div>
    <br>	 
    <!-- <div align=center id=exit_status style="color:red"><?php echo $exit_status;?></div>
    <span class="error" style="color:red"><?php echo $error;?></span>
     <div align=center id=exit_status style="color:red"></div>-->
 <br>
 <table style="width:100%" border=1px>
  <tr>
    <th>Inserimento singolo</th>
    <th>Carica Catalogo</th>
  </tr>
  <tr>
    <td><div class="formme">
	    <form class="new_book" name="new_book" id="new_book" action method="POST">
		<table>
                <div class="form-1">
		    <tr>
                    <div class="col-1">
		    	<td>
                        <label for="fname" class="fname">Codice archivio:</label>
			</td><td>
			<input type="text" size="25" id="codice_archivio" name="codice_archivio" placeholder="XXXX.YY">
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
			<?php
			    foreach ($categories as $category) {
			        echo '<option>'.$category['category'].'</option>';
			    }
			?>
	                </select>
			</td>
		    </div>
		    </tr>
		    <tr>
    		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Titolo:</label>
			 </td><td>
			 <textarea name="titolo" id="titolo" rows="3" cols="80" placeholder=Titolo del libro"></textarea>
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Sottotitolo:</label>
			 </td><td>
			 <textarea name="sottotitolo" rows="3" cols="80" placeholder="Eventuale sottotitolo"></textarea>
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
                         <input type="text" size="35" id="descrizione" name="descrizione" placeholder="XX p. : ill. ; YY cm">
			 </td>
                    </div>
		    </tr>
		    <tr>
    		    <div class="col-1">
		    	 <td>
		    	 <label for="fname" class="fname">CDD:</label>
			 </td><td>
                         <input type="text" size="20" id="cdd" name="cdd" placeholder="123.456789">
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
		    <tr>
		     <div class="col-1">
		    	 <td>
		    	 <label for="fname" class="fname">File copertina (JPG):</label>
			 </td><td>
			 <input name="copertina" id="copertina" type="file" value="" accept=".jpg,.jpeg"><br><br>
			 </td>
                    </div>
		    </tr>
                </div>
		</table>
		<br>
                <div class="btn" align="center">
		    <button class="btn btn-sm btn-info insert_book" id="inserisci">Inserisci</button>
                </div>
            </form>
        </div>
    </td>
    <td>
    <form class="new_catalogue" name="new_catalogue" id="new_catalogue" action method="POST">
    <label class="col-md-4 control-label">File di Catalogo (.CSV)</label> <input type="file" name="filecsv" id="filecsv" accept=".csv">
    <br>
    <label class="col-md-4 control-label">File delle copertine (.ZIP)</label> <input type="file" name="filezip" id="filezip" accept=".zip">
    <br>
     <input type="hidden" name="catalogo">
    <button type="submit" id="submit" name="import" class="btn-info insert_catalogue">Inserisci Catalogo</button>
    <div id="labelError"></div>
    </form>
    </td>
  </tr>
</table> 
<br>
<div id="footer" align="center"></div>
    </body>
</html>
