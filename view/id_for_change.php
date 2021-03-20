<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
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
<script type="text/javascript" src="js/change_id.js"></script>

<body>
<?php include "../view/header.php"; ?> 
<br>

<div align=center id=result1 style="color:green"></div>
<div align=center id=error1 style="color:red"></div>
<br>

<form enctype="multipart/form-data" class="change_id" name="change_id" id="change_id" action method="POST">
<table>
  <tr>
    <td>
      <label for="fname" class="fname">Vecchio Codice Archivio:</label>
    </td>
    <td>
      <input type="text" size="50" id="old_codice_archivio" name="old_codice_archivio">
    </td>
  </tr>
  <tr>
    <td>
      <label for="fname" class="fname">Nuovo Codice Archivio:</label>
    </td>
    <td>
      <input type="text" size="50" id="new_codice_archivio" name="new_codice_archivio">
    </td>
  </tr>
</table>
<button class="btn btn-sm btn-info btn-change-id" id="change_id"><img src="/view/icons/update_small.png">&nbsp;Modifica</button>	
</form>

<br>
<div id="footer" align="center"></div>
</body>
</html>