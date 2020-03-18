<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/Member.php";

$m = new Member();
$categories = $m->getAllCategories("ebook_categories");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <meta charset="UTF-8" />
        <title>Inserimento eDoc</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">
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
   $('.btn_ocr').click(function() {
	var formData = new FormData(document.getElementById("new_ebook"));
        if (request) {
            request.abort();
        }

        if (document.getElementById('edoc').value == "") {
	   alert ("Il documento da analizzare deve essere specificato.");
	   return false;
	}

	// FIXME CHECK FILE TYPE HERE !!!!

        request = $.ajax({
                url: "../class/check_ocr.php",
                type: "post",
                data: formData,
                contentType: false,
                cache: false,
                processData:false                       
        });

        request.done(function (response){
	        $('#testo_ocr').html(response);
	        return false;
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
 <div align="center">
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
    	 <label for="fname" class="fname">Documento elettronico<br>(PDF, JPG, PNG, TIFF, DOC, DOCX, EML):</label>
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
    <label for="do_ocr">Salva documento con testo OCR </label>
    <input type="checkbox" id="do_ocr" name="do_ocr" value="OCR">
    </td>
    </tr>
    <tr>
    <td>
    <button id="test_ocr" class="btn-info btn_ocr">Prova OCR</button>
    </td>
    </tr>
    <tr>
     <div class="col-1">
     <td>
      <label for="fname" class="fname">Testo OCR:</label>
	</td>
	<td>
	<textarea id="testo_ocr" name="testo_ocr" rows="10" cols="80" placeholder="OCR"></textarea>
	</td>
      </div>
    </tr>
    </table>
    <!---- <button name="import" class="btn-info insert_ebook">Inserisci</button> --->
    </div>
    </form>

   </body>
   <br>
   <div id="footer" align="center"></div>
</html>
