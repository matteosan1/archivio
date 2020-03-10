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
    <title>Backup Catalogo</title>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
                response = JSON.parse(response);
                if(response.hasOwnProperty('error')){
                    alert (response['error']);
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
<div id="header" align="center"></div>
<br>

<h2 align="center">Backup Catalogo</h2>

<form class="fm_backup" id="fm_backup" name="fm_backup" action method="POST">
  <label for="backup_data">Data di backup:</label>
  <input type="date" id="last_upload" name="last_upload">
  <input type="hidden" name="func" value="backup">
  <button type="submit" id="submit" name="import" class="btn-info btn-backup">Backup</button>
</form>
<div id="link"></div>
</body>
</html>