<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/Member.php";
require_once "../class/solr_curl.php";

$m = new Member();
$categories = $m->getAllCategories('book_categories');

$selects = "";
$json = listCodiceArchivio();
foreach ($json['response']['docs'] as $select) {
        $selects = $selects.'<option value="'.$select['codice_archivio'].'">'.$select['codice_archivio'].'</option>';
}

$size = count($json['response']['docs']);
if ($size > 14) {
   $size = 15;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <meta charset="UTF-8" />
        <title>Libri</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">
</script>	
<script>
$(function(){
  $("#footer1").load("/view/footer.html");
  $("#footer2").load("/view/footer.html");
  $("#footer3").load("/view/footer.html"); 
});
</script>
<style>
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
</style>
</head>
<script type="text/javascript">
var request;
$(document).ready(function() {
//    $('.insert_catalogue').click(function() {
//   	var formData = new FormData(document.getElementById("new_catalogue"));
//       
//       if (request) {
//           request.abort();
//       }
//
//	request = $.ajax({
//               url: "../class/validate_new_book.php",
//               type: "post",
//               data: formData,
//               contentType: false,
//               cache: false,
//               processData:false                       
//       });
//
//       request.done(function (response){
//	        response = JSON.parse(response);
//               if(response.hasOwnProperty('error')){
//		    alert (response['error']);
//               } else {
//		       
//                   //window.location.href = "../view/dashboard.php";
//		    return true;
//               }
//       });
//
//       request.fail(function (response){			    
//               console.log(
//                   "The following error occurred: " + response
//               );
//       });
//	return false;
//
//   });

    $('.btn-insert-book').click(function() {
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

	request.done(function (response) {
	        console.log(response);
	        response = JSON.parse(response);
                if(response.hasOwnProperty('error')){
    		    $('#error1').html(response['error']);
		    return false;
                } else {
		    $('#result1').html(response['result']);
		    //		   setTimeout(function(){
           	    // 			    location.reload();
      		    //			    }, 1000);
		    return false;
                }
            });

        request.fail(function (response){			    
                console.log(
                    "The following error occurred: " + response
                );
        });
	return false;
   });

    $('.btn-delete-book').click(function() {
    	var formData = new FormData(document.getElementById("delete_book"));
        
        if (request) {
            request.abort();
        }

	request = $.ajax({
                url: "../class/remove.php",
                type: "post",
                data: formData,
                contentType: false,
                cache: false,
                processData:false                       
        });

        request.done(function (response) {
	        response = JSON.parse(response);
                if(response.hasOwnProperty('error')){
    		    $('#error3').html(response['error']);
		    return false;
                } else {
		    $('#result3').html(response['result']);
		    		   setTimeout(function(){
           	   			    location.reload();
      					    }, 1000); 

                }
        });

        request.fail(function (response){			    
                console.log(
                    "The following error occurred: " + response
                );
        });
	return false;
    });

    $(".sel_volume").change(function() {
	var sel = document.getElementById("volume").value;
	request = $.ajax({
                url: "../class/solr_curl.php",
                type: "POST",
                data: {'sel':sel, 'func':'find'},
        });

	request.done(function (response){
			      console.log(response);
	    var dict = JSON.parse(response);
	    for (var key in dict) {
	        if (key == '_version_' || key == 'timestamp') {
		   continue;
		}
          	document.getElementById(key).value = dict[key];
	    }
	    document.getElementById("codice_archivio2").value = dict["codice_archivio"];
        });
	return true;

    });
    
    $('.btn-update-book').click(function() {
	var formData = new FormData(document.getElementById("upd_book"));
        
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
		$('#error2').html(response['error']);
		return false;
            } else {
	        $('#result2').html("L'immagine &egrave; stato aggiornato in " + response['responseHeader']['QTime'] + " ms");
		    		   setTimeout(function(){
           	   			    location.reload();
      					    }, 2000); 		
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
    <?php include "../view/header.php"; ?>
<div class="tab">
  <button class="tablinks" onclick="openCity(event, 'inserimento')">Inserimento</button>
  <button class="tablinks" onclick="openCity(event, 'aggiornamento')">Aggiornamento</button>
  <button class="tablinks" onclick="openCity(event, 'cancellazione')">Cancellazione</button>
  <div align="right" style="vertical-align=bottom;">
         <h2>Biblioteca</h2>
  </div>  
</div>

<div id="inserimento" class="tabcontent">
<div align=center id=result style="color:green"></div>
<div align=center id=error style="color:red"></div>
<br>
<div align="center">
<table style="width:90%">
  <!----<tr>
    <th>Inserimento singolo</th>
    <th>Carica Catalogo</th>
  </tr> -->
<tr>
    <td>
    <form enctype="multipart/form-data" class="new_book" name="new_book" id="new_book" action method="POST">
        <table>
	    <tr>
                <td>
                    <label for="fname" class="fname">Codice archivio:</label>
		</td>
		<td>
		    <input type="text" size="25" id="codice_archivio" name="codice_archivio" placeholder="XXXX.YY">
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
                    <label for="fname" class="fname">Titolo:</label>
		</td>
		<td>
		    <textarea name="titolo" id="titolo" rows="4" cols="60" placeholder=Titolo del libro"></textarea>
		</td>
	    </tr>
	    <tr>
  	      	<td>
                    <label for="fname" class="fname">Sottotitolo:</label>
		</td>
		<td>
		    <textarea name="sottotitolo" rows="4" cols="60" placeholder="Eventuale sottotitolo"></textarea>
		</td>
	    </tr>
	    <tr>
	       	<td>
                    <label for="fname" class="fname">Prima responsabilit&agrave;:</label>
		</td>
		<td>
                    <input type="text" size="50" id="prima_responsabilita" name="prima_responsabilita">
		</td>
	    </tr>
	    <tr>
	      	<td>
                    <label for="fname" class="fname">Altre responsabilit&agrave;:</label>
		</td>
 		<td>
                    <input type="text" size="60" id="altre_responsabilita" name="altre_responsabilita">
		</td>
            </tr>
	    <tr>
		<td>
                    <label for="fname" class="fname">Luogo:</label>
		</td>
		<td>
                    <input type="text" size="50" id="luogo" name="luogo">
		</td>
            </tr>
	    <tr>
		<td>
                    <label for="fname" class="fname">Edizione:</label>
		</td>
		<td>
                    <input type="text" size="50" id="edizione" name="edizione">
	        </td>
            </tr>
	    <tr>
		<td>
                    <label for="fname" class="fname">Ente:</label>
		</td>
		<td>
                    <input type="text" size="50" id="ente" name="ente">
		</td>
            </tr>
	    <tr>
	   	<td>
                    <label for="fname" class="fname">Serie:</label>
		</td>
		<td>
                    <input type="text" size="50" id="serie" name="serie">
		</td>
            </tr>
	    <tr>
	      	<td>
                    <label for="fname" class="fname">Anno:</label>
		</td>
		<td>
                    <input type="number" size="4" id="anno" name="anno" placeholder="XXXX">
		</td>
            </tr>
	    <tr>
		<td>
                    <label for="fname" class="fname">Descrizione:</label>
		</td>
		<td>
                    <input type="text" size="35" id="descrizione" name="descrizione" placeholder="XX p. : ill. ; YY cm">
		</td>
            </tr>
	    <tr>
		<td>
		    <label for="fname" class="fname">CDD:</label>
		</td>
		<td>
                    <input type="text" size="20" id="cdd" name="cdd" placeholder="123.456789">
		</td>
	    </tr>
	    <tr>
		<td>
                    <label for="fname" class="fname">Soggetto:</label>
		</td>
		<td>
                    <input type="text" size="60" id="soggetto" name="soggetto">	
	        </td>
            </tr>
	    <tr>
		<td>
		    <label for="fname" class="fname">Note:</label>
		</td>
		<td>
		    <textarea name="note" rows="10" cols="60" placeholder="note"></textarea>
		</td>
            </tr>
	    <tr>
		<td>
		    <label for="fname" class="fname">File copertina (JPG):</label>
		</td>
		<td>
		    <input name="copertina" id="copertina" type="file" value="" accept=".jpg,.jpeg"><br><br>
		</td>
	    </tr>
            <tr>
		<td colspan=2 align="center">
		    <button class="btn btn-sm btn-info btn-insert-book" id="inserisci"><img src="/view/icons/plus.png">&nbsp;Inserisci</button>		 
		</td>
	    </tr>
	</table>
    </form>
    </td>
<!----    <td>
    <form class="new_catalogue" name="new_catalogue" id="new_catalogue" action method="POST">
    <label class="col-md-4 control-label">Catalogo (.CSV)</label> <input type="file" name="filecsv" id="filecsv" accept=".csv">
    <br>
    <label class="col-md-4 control-label">Copertine (.ZIP)</label> <input type="file" name="filezip" id="filezip" accept=".zip">
    <br><br>
     <input type="hidden" name="catalogo">
     <div align="center">
    <button type="submit" id="submit" name="import" class="btn-info insert_catalogue">Inserisci Catalogo</button>
    </div>
    <div id="labelError"></div>
    </form>
    </td> --->
</tr>
</table> 
</div>
<br>
<div id="footer1" align="center"></div>
</div>

<div id="aggiornamento" class="tabcontent">
<div align=center id=result2 style="color:green"></div>
<div align=center id=error2 style="color:red"></div>
<br>
<div align="center">
<form class="sel_volume" name="sel_volume" id="sel_volume" action method="post">
    <label for="cars">Scegli volume:</label>
    <select id="volume" name="volume">
        <option>----</option>
	<?php echo $selects;  ?>
    </select>
</form>
<br>

<form enctype="multipart/form-data" id="upd_book" class="upd_book" name="upd_book" action method="post">
<input type="hidden" id="codice_archivio" name="codice_archivio">
<input type="hidden" id="tipologia" name="tipologia">

<table style="width:90%">

<!----    <input type=hidden name="update_or_insert" id="update_or_insert" value="0"> ---->
    <table>
    <tr>
     	<td>
            <label for="fname" class="fname">Codice archivio:</label>
	</td>
	<td>
	    <input type="text" size="25" id="codice_archivio2" name="codice_archivio2" disabled placeholder="XXXX.YY">
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Tipologia:</label>
	</td>
	<td>
	    <input type="text" id="tipologia2" name="tipologia2" disabled>
<!---	    <select name="tipologia" class="tipologia" id="tipologia">
		<option selected="selected">----</option>
			<?php
			    foreach ($categories as $category) {
			        echo '<option>'.$category['category'].'</option>';
			    }
			?>
	                </select> ---->
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Titolo:</label>
	</td>
	<td>
	    <textarea id="titolo" name="titolo" rows="3" cols="80" placeholder=Titolo del libro"></textarea>
	</td>
    </tr>
    <tr>
        <td>
            <label for="fname" class="fname">Sottotitolo:</label>
	</td>
	<td>
	    <textarea id="sottotitolo" name="sottotitolo" rows="3" cols="80" placeholder="Eventuale sottotitolo"></textarea>
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Prima responsabilit&agrave;:</label>
	</td>
	<td>
            <input type="text" size="50" id="prima_responsabilita" name="prima_responsabilita">
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Altre responsabilit&agrave;:</label>
	</td>
	<td>
            <input type="text" size="80" id="altre_responsabilita" name="altre_responsabilita">
	</td>
    </tr>
    <tr>
	<td>
            <label for="fname" class="fname">Luogo:</label>
	</td>
	<td>
            <input type="text" size="50" id="luogo" name="luogo">
	</td>
    </tr>
    <tr>
	<td>
            <label for="fname" class="fname">Edizione:</label>
	</td>
	<td>
            <input type="text" size="50" id="edizione" name="edizione">
	</td>
    </tr>
    <tr>
     	<td>
            <label for="fname" class="fname">Ente:</label>
	</td>
	<td>
            <input type="text" size="50" id="ente" name="ente">
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Serie:</label>
	</td>
	<td>
            <input type="text" size="50" id="serie" name="serie">
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Anno:</label>
	</td>
	<td>
            <input type="number" size="4" id="anno" name="anno" placeholder="XXXX">
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Descrizione:</label>
	</td>
	<td>
            <input type="text" size="35" id="descrizione" name="descrizione" placeholder="XX p. : ill. ; YY cm">
	</td>
    </tr>
    <tr>
   	<td>
	    <label for="fname" class="fname">CDD:</label>
	</td>
	<td>
            <input type="text" size="20" id="cdd" name="cdd" placeholder="123.456789">
	</td>
    </tr>
    <tr>
	<td>
            <label for="fname" class="fname">Soggetto:</label>
	</td>
	<td>
            <input type="text" size="80" id="soggetto" name="soggetto">
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
	    <label for="fname" class="fname">File copertina (JPG):</label>
	</td>
	<td>
	    <input name="copertina" id="copertina" type="file" value=""><br><br>
	</td>
    </tr>
    <tr>
	<td colspan=2>
	<div align="center">
	    <button class="btn btn-sm btn-info btn-update-book" id="inserisci"><img src="/view/icons/update_small.png">&nbsp;Aggiorna</button>
	</div>
	</td>
    </tr>
</table>
</form>
</div>
<br>
<div id="footer2" align="center"></div>
</div>

<div id="cancellazione" class="tabcontent">
<div align=center id=result3 style="color:green"></div>
<div align=center id=error3 style="color:red"></div>
<br>
<div align="center">
<form class="delete_book" name="delete_book" id="delete_book" action method="POST">
    <label for="cars">Scegli i documenti da rimuovere:</label>
    <select width=100px id="codici[]" name="codici[]" size="<?php echo $size; ?>" multiple>
	  <?php echo $selects; ?>
  	  </select><br><br>
  	  <button type="submit" id="submit" name="import" class="btn-danger btn-delete-book"><img src="/view/icons/trash.png">&nbsp;Rimuovi Volumi Selezionati</button>  
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
