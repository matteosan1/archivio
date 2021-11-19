<?php
require_once "../view/session.php";
require_once "../class/Member.php";

$member = new Member();
$users = $member->getAllMembers();
$categories = $member->getAllCategories();
$categories_ebook = $member->getAllCategories("ebook_categories");
$tagl1 = $member->getL1Tags();
?> 

    <html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
        <meta charset="UTF-8" />
        <title>Libri</title>
	    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">

	     $(function(){
		     $("#footer1").load("/view/footer.html");
		     $("#footer2").load("/view/footer.html");
  		     $("#footer3").load("/view/footer.html");
   		     $("#footer4").load("/view/footer.html");
   		     $("#footer5").load("/view/footer.html"); 
	     });
	    </script>
        <style>
         @import url("/view/css/main.css");
        </style>
        
<!DOCTYPE html>
<html>
	<head>
		<title>Amministrazione sito</title>
		<meta charset="utf-8"/>
	        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script>
		$(function(){
		  $("#footer1").load("/view/footer.html");
		  $("#footer2").load("/view/footer.html");
		  $("#footer3").load("/view/footer.html");
		  $("#footer4").load("/view/footer.html");
  		  $("#footer5").load("/view/footer.html");	
		});
		</script>
<script type="text/javascript" src="js/autologout.js"></script>
<script type="text/javascript" src="js/management.js"></script>
<script type="text/javascript" src="js/change_id.js"></script>
</style>
</head>

<body>
<div id="header"><?php include "../view/header.php"; ?></div>
<div id="scroll"></div>

<div id="content">
<div class="tab">
  <div class="testo" align="center"><h2>Biblioteca</h2></div>
                <button class="tablink_book" onclick="openPage('Insert', this)">Inserimento</button>
                <button class="tablink_book" onclick="openPage('Update', this)">Aggiornamento</button>
                <button class="tablink_book" onclick="openPage('Delete', this)">Cancellazione</button>
                <button class="tablink_book" onclick="openPage('Backup', this)">Backup</button>   
                <button class="tablink_book" onclick="openPage('Restore', this)">Ripristino</button>
                <button class="tablink_book" onclick="openPage('Cover', this)">Copertine</button>    
            </div>

<div class="tab">
  <button id=pippo class="tablinks" onclick="openCity(event, 'gestione_utenti')">Utenti</button>
  <button class="tablinks" onclick="openCity(event, 'book_cat')">Tipologia Libri</button>
  <button class="tablinks" onclick="openCity(event, 'ebook_cat')">Tipologia eDoc</button>
  <button class="tablinks" onclick="openCity(event, 'tags')">Tag Fotografie</button>
  <button class="tablinks" onclick="openCity(event, 'ca')">Modifica Cod. Archivio</button>
</div>

<div id="registered" style="color:red"></div>

<div id="gestione_utenti" class="tabcontent">
<br>
	    <div align=center>
	    <div id="result"></div>
	    <table>
		<col width="130">
  		<col width="250">
		<col width="130">
  		<col width="200">
		<tr>
		<th> USERNAME </th>
		<th> NOME </th>
		<th> EMAIL </th>
		<th> RUOLO </th>
		<th> COMANDO </th>
		</tr>
		<?php
		    foreach ($users as $user) {
		    	    echo "<tr>";
			    echo "<td>".$user['user_name']."</td>";
   			    echo "<td class='display_name'>".$user['display_name']."</td>";
    			    echo "<td>".$user['email']."</td>";
    			    echo "<td>".$user['role']."</td>";
			    echo "<td><button class=\"btn btn-sm btn-danger delete_user\" id=\"".$user['id']."\" ><img src=\"/view/icons/trash.png\"></button></td>";
			    echo "</tr>";			    
		    }
		?>     
    	    </table>
	    <br>
	    <form enctype="multipart/form-data" action method="POST" id="new_user">
	    <input type="button" class="btn btn-info" value="Nuovo utente" onclick="relocate_home()">
	    </form>
	    </div>
	    <br>
<div id="footer1" align="center"></div>
</div>
<div id="book_cat" class="tabcontent">
	    <div align=center>
	    <div id="result_libri"></div>
	    <br>
	    <table>
		<col width="130">
		<col align="center">
		<tr>
		<th> CATEGORIA </th>
		</tr>
		<?php
		    foreach ($categories as $category) {
		    	    echo "<tr>";
			    echo "<td class='category'>".$category['category']."</td>";
			    //echo "<td align=center><button class=\"btn btn-sm btn-danger delete_category\" id=\"".$category['id']."\" >RIMUOVI</button></td>";
			    echo "</tr>";			    
		    }
		?>     
    	    </table>
	    <br>
	    <form enctype="multipart/form-data" action method="POST" id="new_book">
	    <input type="button" class="btn btn-info" value="Nuova categoria libri" onclick="relocate_home2('book')">
	    <input type="hidden" id="book" name="book" value="">
	    </form>
	    </div>
	    <br>
<div id="footer2" align="center"></div>
</div>
<div id="ebook_cat" class="tabcontent">
     <div align=center>
     <div id="result_edoc"></div>
     	  <br>
	    <table>
		<col width="130">
		<tr>
		<th> CATEGORIA </th>
		</tr>
		<?php
		    foreach ($categories_ebook as $category) {
		    	    echo "<tr>";
			    echo "<td class='ebook_category'>".$category['category']."</td>";
			    echo "</tr>";			    
		    }
		?>     
    	    </table>
	    <br>
    	    <form enctype="multipart/form-data" action method="POST" id="new_ebook">
    	    <input type="button" class="btn btn-info" value="Nuova categoria eDoc" onclick="relocate_home2('ebook')">
       	    <input type="hidden" id="ebook" name="ebook" value="">
	    </form>
	    </div>
	    <br>
<div id="footer3" align="center"></div>
</div>

<div id="tags" class="tabcontent">
     <div align=center>
     <div id="result_tags"></div>
     	  <br>
	    <table>
		<col width="130">
		<tr>
		<th>TAG L1</th>
		</tr>
		<?php
		    foreach ($tagl1 as $tag) {
			    if ($tag['name'] == "----") {
			       continue;
			    }
		    	    echo "<tr>";
			    echo "<td class='tagsl1'>".$tag['name']."</td>";
			    echo "</tr>";			    
		    }
		?>     
    	    </table>
	    <br>
    	    <form enctype="multipart/form-data" action method="POST" id="new_tagl1">
    	    <input type="button" class="btn btn-info" value="Nuovo TAG L1" onclick="relocate_home2('tagl1')">
       	    <input type="hidden" id="tagl1" name="tagl1" value="">
	    </form>
	    </div>
	    <hr>
	    <form enctype="multipart/form-data" action method="POST" id="new_tagl2">
	    <input type="hidden" name="tagl2">
	    <?php
	    foreach ($tagl1 as $tl1) {
    		 $tagl2 = $member->getL2Tags($tl1['id']);
		 if (isset($tagl2)) {
	            echo "<label><b>TAG L1: ".$tl1['name']."</b></label><br>";
		    echo '<input id="tagl2" name="'.$tl1['id'].'">&nbsp;&nbsp;';
		    echo "<input type=\"button\" id=\"mybutton-".$tl1['name']."\" class=\"btn btn-info\" value=\"Nuovo TAG L2\">";
		    echo "<table>";
	            foreach ($tagl2 as $tl2) {
		        echo "<tr><td>".$tl2['name']."</td></tr>";
		    }
  		    echo "</table><br>";
		    echo '<hr width="50%">';
		}    
	    }
	    ?>     
	    </form>
  <br>
<div id="footer4" align="center"></div>
</div>

<div id="ca" class="tabcontent">
   <div align=center>
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
<div id="footer5" align="center"></div>
</div>

<script>
document.getElementById("pippo").click();

function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>

</body>
</html>
