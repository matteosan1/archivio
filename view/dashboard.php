<?php
require_once "session.php";
require_once "config.php";
require_once "solr_client.php";

$displayName = $_SESSION["name"];
$role = $_SESSION["role"];
?>

<html>
<head>
	<title>Lavagna</title>
	<link href="./view/css/style.css" rel="stylesheet" type="text/css" />
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
	<script>
	$(function(){
  	    $("#footer").load("/view/footer.html"); 
	});
	</script>
</head>
<body>
<?php include "header.php"; ?>
<br>
<table align="center" width=75%>
<column width=25% height=100px>
<column width=25% height=100px>
<column width=25% height=100px>
<column width=25% heigth=100px>
<?php
	echo '<tr>';
    	if ($role != "photo") {
	   printf('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/insert_book2.png">Libri</button></td>', "'/view/insert_book.php'");
	   printf('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/edoc.png">eDoc</button></td>', "'/view/insert_ebook.php'");
	}
	printf('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/insert_pic.png">Fotografie</button></td>', "'/view/upload_image.php'");
   	printf('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/download.png">Stampe/Lastre</button></td>', "'/view/upload_slide.php'");
	printf('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/upload_video.png">Video</button></td>', "'/view/upload_video.php'");
	echo '</tr>';
	echo '<tr>';
	if ($role != "photo") {
	   printf ('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px"><img src="/view/icons/backup.png">Backup</button></td>', "'/view/backup.php'");
	   printf ('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px"><img src="/view/icons/restore.png">Ripristino&nbsp;&nbsp;&nbsp;&nbsp;Catalogo</button></td>', "'/view/restore.php'");
	   printf ('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px"><img src="/view/icons/copertina.png">Controlla&nbsp;&nbsp;&nbsp;&nbsp;Copertine</button></td>', "'/view/check_cover.php'");
   	   printf ('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px"><img src="/view/icons/search.png">Ricerca</button></td>', "'/view/main.php'");
	}
	echo '</tr>';
?>
	</table>
    </div>
    <br><br>
<div id="footer" align="center"></div>
</body>
</html>
