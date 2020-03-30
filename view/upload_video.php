<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/solr_curl.php";

$selects = "";
$json = listCodiceArchivio("video");

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
        <title>Video</title>
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
   $('.btn-insert-video').click(function() {
	var formData = new FormData(document.getElementById("new_video"));

	if (request) {
            request.abort();
        }

	var filename = document.getElementById('videos').value;
        if (filename == "") {
	   alert ("Il file da indicizzare devono essere specificati.");
	   return false;
	} else {
 	   request = $.ajax({
               	 url: "../class/process_video.php",
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
		   $('#error1').html(response['error']);
		   return false;
                } else {
		   $('#result1').html(response['result']);
                   return true;
                }
           });
        }
      return false;
   });

   $('.delete_videos').click(function() {
    	var formData = new FormData(document.getElementById("delete_video"));
        
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

    $(".sel_video").change(function() {
	var sel = document.getElementById("video").value;
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
    
    $('.btn-update-video').click(function() {
	var formData = new FormData(document.getElementById("upd_video"));
        
        if (request) {
            request.abort();
        }

        request = $.ajax({
                url: "../class/validate_new_item.php",
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
	        $('#result2').html("Il video &egrave; stato aggiornato in " + response['responseHeader']['QTime'] + " ms");
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
       <h2>Video</h2>
  </div>
</div>

<div id="inserimento" class="tabcontent">
<h2 align=center>Upload</h2>
<div align=center id=result1 style="color:green"></div>
<div align=center id=error1 style="color:red"></div>

<form class="new_video" name="new_video" id="new_video" action method="POST">
<br>
<div align="center">
<table style="width:80%">
<tr>
     <td>
    	 <label for="fname" class="fname">Video da caricare:</label>
     </td>
     <td>
	 <input name="videos[]" id="videos" type="file" value="" multiple accept=".mpg,.mpeg,.ogg,.mp4,.avi,.wmv,.mov,.flv,.mtk">
	 <br><br>
     </td>
</tr>
<tr>
     <td>
	<label for="fname" class="fname">Note:</label>
     </td>
     <td>
	<textarea name="note" rows="12" cols="60" placeholder="note"></textarea>
     </td>
</tr>
<tr>
    <td align="center" colspan=2>
	<button id="import" class="btn btn-sm btn-info btn-insert-video"><img src="/view/icons/plus.png">&nbsp;Inserisci</button>
    </td>
</tr>
</table>
</div>
</form>
<br>
<div id="footer1" align="center"></div>
</div>


<div id="aggiornamento" class="tabcontent">
<h2 align="center">Aggiornamento</h2>
<div align=center id=result2 style="color:green"></div>
<div align=center id=error2 style="color:red"></div>
<br>
<div align="center">
<form class="sel_video" name="sel_video" id="sel_video" action method="post">
      <label for="cars">Scegli volume:</label>
      <select id="video" name="video">
       	     <option>----</option>
	     <?php echo $selects; ?>
      </select>
</form>
<br>

<form class="upd_video" name="upd_video" id="upd_video" action method="POST">
<input type="hidden" id="codice_archivio" name="codice_archivio">
<input type="hidden" id="tipologia" name="tipologia">
<table style="width:80%">
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
	    <label for="fname" class="fname">Note:</label>
	 </td>
	 <td>
	    <textarea id="note" name="note" rows="12" cols="60" placeholder="note"></textarea>
	 </td>
    </tr>
    <tr>
	<td align="center" colspan=2>
	    <button id="import" class="btn-info btn-update-video"><img src="/view/icons/update_small.png">&nbsp;Aggiorna</button>
	</td>
    </tr>
</table>
</form>
</div>
<br>
<div id="footer2" align="center"></div>
</div>

<div id="cancellazione" class="tabcontent">
<h2 align="center">Rimuovi</h2>
<div align=center id=result3 style="color:green"></div>
<div align=center id=error3 style="color:red"></div>

<br>
<div align="center">
     <form class="delete_video" name="delete_video" id="delete_video" action method="POST">
  	   <select width=100px id="codici[]" name="codici[]" size="<?php echo $size; ?>" multiple>
	   <?php echo $selects; ?>
  	   </select><br><br>
  	   <button type="submit" id="submit" name="import" class="btn-danger delete_videos">Rimuovi Video Selezionati</button>  
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
