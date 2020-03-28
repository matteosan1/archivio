<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/solr_curl.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Backup</title>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
	$(function(){
	  $("#footer").load("/view/footer.html"); 
	});
    </script>
</head>
<script type="text/javascript">
var request;
$(document).ready(function() {
    $('.btn-backup').click(function() {
        var formData = new FormData(document.getElementById("fm_backup"));

	if(document.getElementById("last_upload").value == '') {
	   alert ("Devi scegliere una data !");
	   return false;
	}
	
        if (request) {
            request.abort();
        }

        request = $.ajax({
                url: "../class/solr_curl.php",
                type: "post",
                data: formData,
                contentType: false,
                cache: false,
                processData:false                       
        });

        request.done(function (response){
        		      console.log(response);
                response = JSON.parse(response);
                if(response.hasOwnProperty('error')){
                    alert (response['error']);
		    return false;
                } else {
		  $('#link').html(response['result']);
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
<?php include "../view/header.php"; ?> 
<br>

<h2 align="center">Backup Archivio</h2>
<br>
<div align="center">
<form class="fm_backup" id="fm_backup" name="fm_backup" action method="POST">
<input type="hidden" name="func" value="backup">
<table>
  <tr>
  <td><label for="backup_data">Data di backup:</label></td>
  <td><input type="date" id="last_upload" name="last_upload"></td>
  </tr>
  <tr>
  <td><label for="do_ocr">Salva solo catalogo biblioteca </label></td>
  <td> <input type="checkbox" id="do_biblio" name="do_biblio" value="biblio"></td>
  </tr>
  <tr>
  <td colspan=2 align=center><br>
  <button type="submit" id="submit" name="import" class="btn-info btn-backup">Backup</button>
  <br></td>
  </tr>
</form>
<tr>
<td><label for="backup_res">File di backup:</label></td>
<td><div id="link"></div></td>
</tr>
</table>
</div>
<br>
<div id="footer" align="center"></div>
</body>
</html>