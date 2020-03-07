<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/solr_curl.php";

$selects = "";
$json = listCodiceArchivio();
foreach ($json['response']['docs'] as $select) {
	$selects = $selects.'<option value="'.$select['codice_archivio'].'">'.$select['codice_archivio'].'</option>';
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <meta charset="UTF-8" />
        <title>Rimuovi Libro</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
    crossorigin="anonymous">
</script>	
<script>
$(function(){
  $("#header").load("/view/header.html"); 
  //$("#footer").load("/view/footer.html"); 
});
</script>
</head>
<script type="text/javascript">
var request;
$(document).ready(function() {
    $('.delete_volumes').click(function() {
    	var formData = new FormData(document.getElementById("delete_book"));
        
        if (request) {
            request.abort();
        }

	request = $.ajax({
                url: "../class/remove_book.php",
                type: "post",
                data: formData,
                contentType: false,
                cache: false,
                processData:false                       
        });

        request.done(function (response) {
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
    <h2 align="center">Rimuovi libri dal catalogo</h2>
    <br>
    <form class="delete_book" name="delete_book" id="delete_book" action method="POST">
    	  <label for="cars">Scegli i volumi da rimuovere:</label>
  	  <select width=100px id="volumi[]" name="volumi[]" size="15" multiple>
	  <?php echo $selects; ?>
  	  </select><br><br>
  	  <button type="submit" id="submit" name="import" class="btn-info delete_volumes">Rimuovi volumi selezionati</button>  
    </form>
    </body>
</html>
