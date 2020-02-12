<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

//include 'create-csv.php';
//include 'upload.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
        <meta charset="UTF-8" />

        <title>Upload immagini</title>
    </head>
    <body>

 <table style="width:100%">
  <tr>
    <th>Upload Foto</th>
  </tr>
  <tr>
    <td>
    <form enctype="multipart/form-data" action="upload.php" method="POST">
        Foto (singola o zip): <input name="userfile" type="file"></br>
	Tag Predefiniti: <input list="browsers" name="browser">
	<datalist id="browsers">
    	   <option value="Internet Explorer">
    	   <option value="Firefox">
    	   <option value="Chrome">
    	   <option value="Opera">
    	   <option value="Safari">
  	</datalist><br>
	Tag addizionali: <textarea name="list_of_tags" rows="5" cols="30">
	</textarea>
    <input type="submit" value="Upload">
    </form>
    </td>
  </tr>
</table> 
    </body>
</html>
