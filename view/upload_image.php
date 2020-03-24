<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
//include 'create-csv.php';

require_once "session.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
        <meta charset="UTF-8" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <title>Inserimento Immagini</title>
    </head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
        $(function(){
        $("#footer").load("/view/footer.html"); 
        });
</script>

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

//$(document).ready(function() {

//});
</script>
    <body>
    <?php include "header.php"; ?>
    <br>
        </div>
	<h2 align=center>Upload Immagini</h2>
        <table>
  	<tr><td valign="top">
    		<form enctype="multipart/form-data" action method="POST" id="upload">
        	Foto (jpeg, tiff o zip):</td><td><input name="userfile[]" type="file" multiple></br></br>
 	</td></tr>
	<tr><td valign="top">
                Tag 1 :</td><td>
                <select name="tagl1" class="tagl1" id="tagl1">
                <option selected="selected">----</option>
<?php
require_once ('../class/Member.php');
$m = new Member();
$result = $m->getL1Tags();
foreach ($result as $row) {
    $id = $row['id'];
    $data = $row['name'];
    echo '<option value="'.$id.'">'.$data.'</option>';
}
?>
                </select><br/><br/>
        </td></tr>
        <tr><td valign="top">
                Tag 2:</br></td><td>
                <select name="tagl2" class="tagl2" id="tagl2">
         	       <option selected="selected" default>----</option>
                </select><br>
        </td></tr>
	<tr><td valign="top">        
		Fotografo:</td><td><input type="text" name="author" id="author" size=50 placeholder="Nome Cognome">
        </td></tr>
	<tr><td valign="top">        
		Tag addizionali (comma separated):</td><td><textarea name="list_of_tags" rows="5" cols="30"></textarea>
        </td></tr>
        <tr><td>
	<button type="button" id="uploadBtn" class="btn btn-success"> Upload </button>
	</form>
        </td></tr>
	</table>
	<div id=uploaded></div>
    </body>
</html>
