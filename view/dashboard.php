<?php
require_once "session.php";
require "config.php";
require_once "solr_client.php";

$displayName = $_SESSION["name"];
$role = $_SESSION["role"];
numberRows();
?>

<html>
    <head>
	<title>Lavagna</title>
	<link href="/view/css/style.css" rel="stylesheet" type="text/css" />
        <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">
        </script>
        <script>
         $(function(){
             $("#footer").load("/view/footer.html"); 
         });
	</script>
	<style>
	 @import url("/view/css/main.css");
	</style>
    </head>
    <body>
	<div id="header"><?php
			 include "header.php";
			 ping();
			 ?></div>
	<br>
	<div id="content" align="center">
            <table width=95%>
                <tr>
                    <td align="left" rowspan=5 width=15%>
                        <h3>Archivio:</h3>
			<?php summary(); ?>
                    </td>
                </tr>
                <tr width=80%>
                    <td colspan=3>
			<?php
                        echo '<div align="center">';
			printf ('<button onclick="location.href = %s;" id="myButton1" class="float-left submit-button" style="height:80px;width:150px"><img src="/view/icons/search.png">Ricerca</button>', "'/view/search.php'");
			echo '</div>';
			?>
		    </td>
		</tr>
		<tr>
		    <td>  
			<?php
			if ($role != "photo" and $role != "economato") {
			    echo '<div align="center">';
			    printf('<button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/insert_book2.png">Libri</button>', "'/view/insert_book.php'");       
			    echo '</div>';
			}
			?>
		    </td>
			<td>
			<?php		    
			if ($role != "photo" and $role != "economato") {
			    echo '<div align="center">';
			    printf('<button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/faldoni.png">Faldoni</button>', "'/view/insert_faldoni.php'");
			    echo '</div>';
			}
			?>
		    </td>
		    <td>
			<?php
			if ($role != "photo" and $role != "economato") {
			    echo '<div align="center">';
			    printf('<button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/edoc.png">eDoc</button>', "'/view/insert_ebook.php'");
			    echo '</div>';
			}
			?>
		    </td>
		</tr>
		<tr>
		    <td>  
			<?php
			if ($role != "economato") {
			    echo '<div align="center">';
			    printf('<button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/insert_pic.png">Fotografie</button>', "'/view/insert_image.php'");
			    echo '</div>';
			}
			?>
		    </td>
		    <td>
		    </td>
		    <td>
			<?php
			if ($role != "economato") {
			    echo '<div align="center">';
			    printf('<button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/upload_video.png">Video</button>', "'/view/insert_video.php'");
			    echo '</div>';
			}
			?>
		    </td>
		</tr>
		<tr>
		    <td>  
			<?php
			if ($role != "photo") {
			    echo '<div align="center">';
			    printf('<button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/montura.png">Vestizioni</button>', "'/view/insert_vestizione.php'");
			    echo '</div>';
			}
			?>
		    </td>
		    <td>
		    </td>
		    <td>
			<?php
			    echo '<div align="center">';
			    printf('<button onclick="location.href = %s;" id="myButton1" class="btn-default" style="height:80px;width:150px"><img src="/view/icons/delibera.png">Delibere,&nbspVerbali</button>', "'/view/insert_verbali.php'");
			    echo '</div>';
			?>
		    </td>
		</tr>
	    </table>
	</div>
	<br><br>
	<div id="footer" align="center"></div>
    </body>
</html>
