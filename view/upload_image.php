<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/solr_curl.php";
require_once "../class/Member.php";

$selects = "";
$json = listCodiceArchivio("image");

foreach ($json['response']['docs'] as $select) {
    $selects = $selects.'<option value="'.$select['codice_archivio'].'">'.$select['codice_archivio'].'</option>';
}

$size = count($json['response']['docs']);
if ($size > 14) {
   $size = 15;
}

$m = new Member();
$l1tags = $m->getL1Tags();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
        <meta charset="UTF-8" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <title>Immagini</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"> </script>
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
	$(".btn-insert-image").click(function() {
	   var filename = document.getElementById('userfile').value;
           if (filename == "") {
               alert ("Devi specificare una o più foto da caricare...");
               return false;
           }
	   
           var tagl1 = document.getElementById('tagl1').value;
	   var tagl2 = document.getElementById('tagl2').value;
	   if (tagl1 == "----" || tagl2 == "----") {
	      alert ("Devi specificare i due tag !!!");
	      return false;
           }

           var formData = new FormData(document.getElementById("new_image"));
           request = $.ajax({
		url: "../class/process_image.php",
		type: 'POST',
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

            //var oForm = document.getElementById('new_image');
	    //oForm.elements["tagl2"].value = 0;
	    //document.getElementById('new_image').reset();
	    return false;
	});

    $('.delete_images').click(function() {
    	var formData = new FormData(document.getElementById("delete_image"));
        
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

    $(".sel_image").change(function() {
	var sel = document.getElementById("image").value;
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
    
    $('.btn-update-image').click(function() {
	var formData = new FormData(document.getElementById("upd_image"));
        
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

    $(".tagl1").change(function() {
        	var id = $(this).val();
                var dataString = 'id=' + id;
                $.ajax({
                    type: 'post',
                    url: "../class/tags.php",
                    data: dataString,
                    cache: false,
                    success: function(html) {
                        $(".tagl2").html(html);
                    }
                });
         });
});
</script>
    <body>
    <?php include "header.php"; ?>
<div class="tab">
  <button class="tablinks" onclick="openCity(event, 'inserimento')">Inserimento</button>
  <button class="tablinks" onclick="openCity(event, 'aggiornamento')">Aggiornamento</button>
  <button class="tablinks" onclick="openCity(event, 'cancellazione')">Cancellazione</button>
  <div align="right" style="vertical-align=bottom;">
         <h2>Fotografie</h2>
  </div>
</div>

<div id="inserimento" class="tabcontent">
<div align=center id=result1 style="color:green"></div>
<div align=center id=error1 style="color:red"></div>
<br>
<div align="center">
<form enctype="multipart/form-data" action method="POST" id="new_image" name="new_image" class="new_image">
<table>
    <tr>
	<td valign="top">
            Foto (jpeg, tiff o zip):
	</td>
	<td>
	    <input id="userfile" name="userfile[]" type="file" multiple accept=".jpeg,.jpg,.tiff,.tif,.zip">
	    <br><br>
    	</td>
    </tr>
    <tr>
	<td valign="top">
            Tag 1 :
	</td>
	<td>
            <select name="tagl1" class="tagl1" id="tagl1">
            <option selected="selected">----</option>
	    <?php
	    foreach ($l1tags as $row) {
    	    	    $id = $row['id'];
    		    $data = $row['name'];
    		    echo '<option value="'.$id.'">'.$data.'</option>';
	    }
	    ?>
            </select>
	    <br><br>
        </td>
    </tr>
    <tr>
	<td valign="top">
            Tag 2:<br>
	</td>
	<td>
	    <select name="tagl2" class="tagl2" id="tagl2">
            <option selected="selected" default>----</option>
            </select><br>
        </td>
    </tr>
    <tr>
        <td valign="top">
	    <br>
	    <label>Fotografo: </label>
	</td>
	<td>
	    <input list="author" value="<?php echo $displayName; ?>" name="author">
            <datalist id="author">
            <?php echo "<option value='".$displayName."'>"; ?>     
	    </datalist>
        </td>
    </tr>
    <tr>
	<td valign="top">        
	    Tag addizionali (comma separated):</td><td><textarea name="list_of_tags" rows="5" cols="30"></textarea>
        </td>
    </tr>
    <tr>
	<td align="center" colspan=2>
	    <br>
	    <button id="import" class="btn btn-sm btn-info btn-insert-image"><img src="/view/icons/plus.png">&nbsp;Inserisci</button>
        </td>
    </tr>
</table>
</form>
</div>
<br>
<div id="footer1" align="center"></div>
</div>


<div id="aggiornamento" class="tabcontent">
<div align=center id=result2 style="color:green"></div>
<div align=center id=error2 style="color:red"></div>
<br>
<div align="center">
<form class="sel_image" name="sel_image" id="sel_image" action method="post">
      <label for="cars">Scegli Immagine:</label>
      <select id="immagine" name="immagine">
       	     <option>----</option>
	     <?php echo $selects; ?>
      </select>
</form>
<br>
<form enctype="multipart/form-data" action method="POST" id="upd_image" name="upd_image" class="upd_image">
<input type="hidden" id="codice_archivio" name="codice_archivio">
<input type="hidden" id="tipologia" name="tipologia">
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
	    <img src="">
	</td>
    </tr>
    <tr>
	<td valign="top"><br>
   	    <label>Fotografo: </label>
	</td>
	<td>
	    <input id="author" value="" name="author">
   	</td>
    </tr>
    <tr>
	<td valign="top">        
   	    Tag addizionali (comma separated):</td><td><textarea name="list_of_tags" rows="5" cols="30"></textarea>
    	</td>
    </tr>
    <tr>
	<td colspan=2>
	<div align="center">
	    <button id="import" class="btn-info btn-update-image"><img src="/view/icons/update_small.png">&nbsp;Aggiorna</button>
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
     <form class="delete_image" name="delete_image" id="delete_image" action method="POST">
  	   <select width=100px id="codici[]" name="codici[]" size="<?php echo $size; ?>" multiple>
	   <?php echo $selects; ?>
  	   </select><br><br>
  	   <button type="submit" id="submit" name="import" class="btn-danger btn-delete-images"><img src="/view/icons/trash.png">&nbsp;Rimuovi Immagini Selezionate</button>  
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
