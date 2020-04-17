<?php
require_once "../view/session.php";
require_once "../view/config.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ripristina Catalogo</title>
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

<script type="text/javascript" src="js/restore.js"></script>
<body>
<?php include "../view/header.php"; ?>
<br>

<h2 align="center">Ripristina Catalogo</h2>
<div id="result" style="color:green"></div>
<div id="error" style="color:red"></div>
<br>
<div align=center>
    <br>
    <form class="new_catalogue" name="new_catalogue" id="new_catalogue" action method="POST">
    <label class="col-md-4 control-label">File di Catalogo (.CSV)</label> <input type="file" name="filecsv" id="filecsv" accept=".csv">
    <br>
    <label class="col-md-4 control-label">File delle copertine (.ZIP)</label> <input type="file" name="filezip" id="filezip" accept=".zip">
    <br>
     <input type="hidden" name="func" value="restore">
     <br><br>
    <button type="submit" id="submit" name="import" class="btn-info btn-restore"><img src="/view/icons/plus.png">&nbsp;Inserisci Catalogo</button>
    </form>
</div>
<br>
<div id="footer" align="center"></div>
</body>
</html>