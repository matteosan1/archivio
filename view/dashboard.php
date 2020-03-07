<?php
require_once "session.php";
?>

<html>
<head>
<title>Entry Page</title>
<link href="./view/css/style.css" rel="stylesheet" type="text/css" />
<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
    crossorigin="anonymous">
</script>	
<script>
$(function(){
  $("#header").load("/view/header.html"); 
  //$("#footer").load("/view/footer.html"); 
});
</script>
</head>
<body>
<div id="header" align="center"></div>
<br>
Benvenuto <?php echo $displayName.".";?> 
<br>
<table align="center" width=75%>
<column width=33% height=100px>
<column width=33% height=100px>
<column width=33% height=100px>
<?php
if ($role == "admin") {
	echo "<tr>";
	printf ('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px">Amministrazione Sito</button>', "'/view/management.php'");
	echo "</tr>";
}?>
<?php
    if ($role != "photo") {
        echo '<tr>';
	printf('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px">Inserisci Libro/Catalogo</button></td>', "'/view/insert_book.php'");
	printf('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px">Rimuovi Libro</button></td>', "'/view/remove_book.php'");
	echo '</tr>';
	echo '<tr>';
	printf('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px">Aggiorna Libro</button></td>', "'/view/update_book.php'");
	printf ('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px">Backup Biblioteca</button></td>', "'/view/backup.php'");
	echo '</tr>';
}?>
	<tr>
	    <td align="center"><button onclick="location.href = '/view/upload_image.php';" id="myButton1" class="float-left submit-button" style="height:80px;width:150px">Carica Fotografie</button>
    	    <td align="center"><button onclick="location.href = '/view/upload_video.php';" id="myButton2" class="float-left submit-button" style="height:80px;width:150px">Carica Video</button>
	</tr>
	</table>
    </div>
</body>
</html>
