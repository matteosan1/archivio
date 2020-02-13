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
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript">
$('#upload').submit(function() {
	$.ajax({
		data: $(this).serialize(),
		type: $(this).attr('method'),
		url: $(this).attr('action'),
		success: function(response) {
			$('#uploaded').html(response);
		}
	});
	return false;
});
</script>
    <body>

 <table style="width:100%">
  <tr>
    <th>Upload Foto</th>
  </tr>
  <tr>
    <td>
    <form enctype="multipart/form-data" action="process_image.php" method="POST" id=upload>
        Foto (jpeg, tiff o zip): <input name="userfile[]" type="file" multiple></br>
	Tag addizionali: <textarea name="list_of_tags" rows="5" cols="30"></textarea>
    <input type="submit" value="Upload">
    </form>
    </td>
  </tr>

</table> 
<div id=uploaded></div>
    </body>
</html>
