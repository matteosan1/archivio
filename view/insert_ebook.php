<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/Member.php";

$m = new Member();
$categories = $m->getAlleBookCategories();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <meta charset="UTF-8" />
        <title>Inserimento eDoc</title>
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
		//console.log(response);
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
 <form class="new_ebook" name="new_ebook" id="new_ebook" action method="POST">
 <table style="width:80%">
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
    	 <label for="fname" class="fname">Documento elettronico (PDF, JPG, PNG, TIFF, DOC, DOCX, EML):</label>
	 </td><td>
	 <input name="edoc" id="edoc" type="file" value=""><br><br>
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
    <td>
    <input type="checkbox" id="do_ocr" name="do_ocr" value="Salva documento con testo OCR">
    </td>
    </tr>
    <tr>
    <td>
    <button type="submit" id="submit" name="import" class="btn-info btn-ocr">Prova OCR</button>
    </td>
    </tr>
    </table>
    <button type="submit" id="submit" name="import" class="btn-info insert_ebook">Inserisci</button>
    </form>
    <div class="col-1">
	<td>
	<label for="fname" class="fname">Testo OCR:</label>
	</td>
	<td>
	<textarea name="note" rows="10" cols="80" placeholder="OCR"></textarea>
	</td>
      </div>
   </body>
</html>
