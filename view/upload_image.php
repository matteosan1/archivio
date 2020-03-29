<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/Member.php";

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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
$(document).ready(function() {
	$("#uploadBtn").click(function() {
	   var filename = document.getElementById('userfile').value;
           if (filename == "") {
               alert ("Devi specificare una o più foto da caricare...");
               return false;
           }
	   
           var tagl1 = document.getElementById('tagl1').value;
	   var tagl2 = document.getElementById('tagl2').value;
	   if (tagl1 == "----" || tagl2 == "----") {
	      echo "Devi specificare i due tag !!!";
	      return false;
           }
	   
           var formData = new FormData(document.getElementById("upload"));
            $.ajax({
		url: "../class/process_image.php",
		type: 'POST',
		data: formData, 
		contentType: false,
		cache: false,
		processData:false,
		success: function(response) {
			$('#uploaded').html(response);
		}
	    });
	    var oForm = document.getElementById('upload');
	    oForm.elements["tagl2"].value = 0;
	    document.getElementById('upload').reset();
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
</div>

<div align=center id=result style="color:green"></div>
<div align=center id=error style="color:red"></div>

<div id="inserimento" class="tabcontent">
<h2 align=center>Upload Immagini</h2>
<br>
<div align="center">
<form enctype="multipart/form-data" action method="POST" id="upload">
<table>
    <tr>
	<td valign="top">
            Foto (jpeg, tiff o zip):
	</td>
	<td>
	    <input name="userfile[]" type="file" multiple accept=".jpeg,.jpg,.tiff,.tif,.zip">
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
	    <button type="button" id="uploadBtn" class="btn btn-success"> Upload </button>
        </td>
    </tr>
</table>
</form>
</div>
<br>
<div id="footer1" align="center"></div>
</div>


<div id="aggiornamento" class="tabcontent">
<h2 align="center">Aggiornamento Immagine</h2>
<br>
<div align="center">
    <form enctype="multipart/form-data" action method="POST" id="upload">
    <table>
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
	    <button type="button" id="uploadBtn" class="btn btn-success">Upload</button>
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
<h2 align="center">Rimuovi Immagini</h2>
<br>
<div align="center">
     <form class="delete_book" name="delete_book" id="delete_book" action method="POST">
  	   <select width=100px id="volumi[]" name="volumi[]" size="15" multiple>
	   <?php echo $selects; ?>
  	   </select><br><br>
  	   <button type="submit" id="submit" name="import" class="btn-danger delete_volumes">Rimuovi Immagini Selezionate</button>  
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
