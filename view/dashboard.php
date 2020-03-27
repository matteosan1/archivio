<?php
require_once "session.php";
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
//if ($role == "admin") {
//	echo "<tr>";
//
////<button class="btn btn-default">
////  <img src="http://i.stack.imgur.com/e2S63.png" width="20" /> Sign In with Facebook
////</button>
//
//	printf ('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/site_admin.png">Amministrazione</button>', "'/view/management.php'");
//	echo "</tr>";
//}?>
<?php
	echo '<tr>';
    	if ($role != "photo") {
	printf('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/insert_book2.png">Libri</button></td>', "'/view/insert_book.php'");
	printf('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px"><img src="/view/icons/edoc.png">eDoc</button></td>', "'/view/insert_ebook.php'");
	}
	<td align="center"><button onclick="location.href = '/view/upload_image.php';" id="myButton1" class="float-left submit-button" style="height:80px;width:150px"><img src="/view/icons/insert_pic.png">Fotografie</button>
        <td align="center"><button onclick="location.href = '/view/upload_video.php';" id="myButton2" class="float-left submit-button" style="height:80px;width:150px"><img src="/view/icons/upload_video.png">Video</button>
	echo '</tr>';
	echo '<tr>';
	printf ('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px"><img src="/view/icons/backup.png">Backup</button></td>', "'/view/backup.php'");
	printf ('<td align="center"><button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px"><img src="/view/icons/backup.png">Backup</button></td>', "'/view/restore.php'");
	echo '<tr>';
}?>
	</table>
    </div>
    <br><br>
<div id="footer" align="center"></div>
</body>
</html>
