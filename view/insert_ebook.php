<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/Member.php";
require_once "../class/solr_curl.php";

$m = new Member();
$categories = $m->getAllCategories("ebook_categories");

$selects = "";
$json = listCodiceArchivio("ebook_categories");

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
        <title>eDoc</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">
</script>
<script>
$(function() {
  $("#footer1").load("/view/footer.html");
  $("#footer2").load("/view/footer.html");
  $("#footer3").load("/view/footer.html");
});
    </script>

<!----                <style>
body {font-family: Arial;}

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
</style>---->
</head>

<script type="text/javascript">
var request;
$(document).ready(function() {
   $('.btn-insert-ebook').click(function() {
	var formData = new FormData(document.getElementById("new_ebook"));

	if (request) {
            request.abort();
        }

	var filename = document.getElementById('edoc').value;
        if (filename == "") {
	   alert ("Il documento da analizzare deve essere specificato.");
	   return false;
	} else {
	  var parts = filename.split('.');
 	  var ext = parts[parts.length - 1].toLowerCase();

	  if (ext != 'jpg' && ext != 'jpeg' &&
	      ext != 'tiff' && ext != 'tif' &&
	      ext != 'doc' &&
      	      ext != 'docx' && ext != 'eml' &&
      	      ext != 'pdf') {
		 alert ("Non è possibile inserire documento in formato " + ext);
  	  	 return false;
	  } else {
		 request = $.ajax({
                 	 url: "../class/validate_new_ebook.php",
                	 type: "post",
                	 data: formData,
                	 contentType: false,
               		 cache: false,
                	 processData:false                       
        	});

        	request.done(function(response) {
		console.log(response);
                    response = JSON.parse(response);
                    if(response.hasOwnProperty('error')) {
			alert (response['error']);
			return false
                    } else {
                      //window.location.href = "../view/dashboard.php";
                      return true;
                    }
        	});
      	  }
      }
      
      return false;
   });

   $('.btn_ocr').click(function() {
	var formData = new FormData(document.getElementById("new_ebook"));
        if (request) {
            request.abort();
        }

	var filename = document.getElementById('edoc').value;
        if (filename == "") {
	   alert ("Il documento da analizzare deve essere specificato.");
	   return false;
	} else {
	  var parts = filename.split('.');
 	  var ext = parts[parts.length - 1].toLowerCase();

	  if (ext != 'jpg' && ext != 'jpeg' &&
	      ext != 'tiff' && ext != 'tif') {
		 alert ("Non è possibile fare analisi OCR con file " + ext);
  	  	 return false;
	  } else {
	     request = $.ajax({
               	 url: "../class/check_ocr.php",
               	 type: "post",
               	 data: formData,
               	 contentType: false,
                 cache: false,
                 processData:false                       
              });
		    
              request.done(function (response) {				
	          $('#testo_ocr').html(response);
	          return false;
              });
      	  }
       }
       return false;
   });
});
</script>
    <body>
    <?php include "../view/header.php"; ?>
    <div class="tab">
    <button class="tablinks" onclick="openCity(event, 'inserimento')">Inserimento</button>
    <button class="tablinks" onclick="openCity(event, 'aggiornamento')">Aggiornamento</button>
    <button class="tablinks" onclick="openCity(event, 'cancellazione')">Cancellazione</button>
    </div>
    
<div id="inserimento" class="tabcontent">
<h2 align="center">Inserimento eDoc</h2>
<!-----<br>
<div align=center id=result style="color:green"></div>
<div align=center id=error style="color:red"></div>
<br>
<div align="center">
     <form class="new_ebook" name="new_ebook" id="new_ebook" action method="POST">
     	   <table style="width:80%">
   	   <tr>
   	   	<td>
		    <label for="fname" class="fname">Tipologia:</label>
		</td>
		<td>
		    <select name="tipologia" class="tipologia" id="tipologia">
        	    <option selected="selected">----</option>
		    <?php
	  	    foreach ($categories as $category) {
	    	    	    echo '<option>'.$category['category'].'</option>';
	  	    }
		    ?>
        	    </select>
		</td>
    	   </tr>
    	   <tr>
    	   	<td>
		    <label for="fname" class="fname">Documento elettronico<br>(PDF, JPG, TIFF, DOC, DOCX, EML):</label>
	 	</td>
		<td>
		    <input name="edoc[]" id="edoc" type="file" value="" multiple><br><br>
	 	</td>
    	   </tr>
    	   <tr>
	        <td>
		    <label for="do_ocr">Unisci per creare un singolo documento </label>
		</td>
    		<td>
		    <input type="checkbox" id="do_merge" name="do_merge" value="merge">
    		</td>
    		</td>
    	   </tr>
    	   <tr>
	   	<td>
		    <label for="fname" class="fname">Note:</label>
		</td>
		<td>
		    <textarea name="note" rows="10" cols="80" placeholder="note"></textarea>
		</td>
    	   </tr>
    	   <tr>
	        <td>
		    <label for="do_ocr">Salva documento con testo OCR </label>
    	        </td>
	    	<td>
		    <input type="checkbox" id="do_ocr" name="do_ocr" value="OCR">
    	   	</td>
    	   </tr>
    	   <tr>
    	        <td>
		    <button id="test_ocr" class="btn-info btn_ocr">Prova OCR</button>
    		</td>
    	   </tr>
    	   <tr>
     	   	<td>
		    <label for="fname" class="fname">Testo OCR:</label>
		</td>
		<td>
		    <textarea readonly id="testo_ocr" name="testo_ocr" rows="10" cols="80" placeholder="OCR"></textarea>
		</td>
    	   </tr>
	   <tr>
	        <td>
		    <button id="import" class="btn-info btn-insert-ebook">Inserisci</button>
		</td>
	   </tr>
       	   </table>
    </form>
</div>
<br>
<div id="footer1" align="center"></div>---->
</div>

<div id="aggiornamento" class="tabcontent">
<h2 align="center">Aggiornamento eDoc</h2>
<!-----<div align="center">
     <form class="upd_edoc" name="upd_edoc" id="upd_edoc" action method="post">
     	   <label for="cars">Scegli eDoc:</label>
           <select id="volume" name="volume">
           	  <option>----</option>
           	  <?php echo $selects;  ?>
           </select>
    </form>
    <br>

    <form class="new_ebook" name="new_ebook" id="new_ebook" action method="POST">
    <table style="width:80%">
    <tr>
    	 <td>
	    <label for="fname" class="fname">Codice archivio:</label>
    	 </td>
	 <td>
	    <input type="text" size="25" id="codice_archivio" name="codice_archivio" disabled placeholder="XXXX.YY">
    	 </td>
    </tr>
    <tr>
   	 <td>
	    <label for="fname" class="fname">Tipologia:</label>
	 </td>
	 <td>
	    <select name="tipologia" class="tipologia" id="tipologia">
            	    <option selected="selected">----</option>
		    <?php
	  	    foreach ($categories as $category) {
	    	    echo '<option>'.$category['category'].'</option>';
	  	    }
		    ?>
            </select>
	 </td>
    </tr>
    <tr>
    	 <td>
	    <label for="fname" class="fname">Note:</label>
	 </td>
	 <td>
	    <textarea name="note" rows="10" cols="80" placeholder="note"></textarea>
	 </td>
    </tr>
    <tr>
    	 <td>
	    <label for="fname" class="fname">Testo OCR:</label>
	 </td>
	 <td>
	    <textarea id="testo_ocr" name="testo_ocr" rows="10" cols="80" placeholder="OCR"></textarea>
	 </td>
    </tr>
    <tr>
	<td>
	    <button id="import" class="btn-info btn-insert-ebook">Aggiorna</button> 
	</td>
    </tr>	
    </table>
    </form>    
</div>
<br>
<div id="footer2" align="center"></div>---->
</div>

<div id="rimozione" class="tabcontent">
<h2 align="center">Rimuovi eDoc</h2>
    <br>
    <div align="center">
    <form class="delete_book" name="delete_book" id="delete_book" action method="POST">
    	  <label for="cars">Scegli i documenti da rimuovere:</label>
  	  <select width=100px id="volumi[]" name="volumi[]" size="15" multiple>
	  <?php echo $selects; ?>
  	  </select><br><br>
  	  <button type="submit" id="submit" name="import" class="btn-danger delete_volumes">Rimuovi eDoc Selezionati</button>  
    </form>
    </div>
<br>
<div id="footer3" align="center"></div>
</div>

<script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>

</body>
</html>
