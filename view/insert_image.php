<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/Member.php";
require_once "../class/solr_utilities.php";

$m = new Member();
$categories = $m->getAllCategories("photo_categories");
$l1tags = $m->getL1Tags();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
        <meta charset="UTF-8" />
        <title>Foto, Stampe e Lastre</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">

         $(function() {
             $("#footer1").load("/view/footer.html");
             $("#footer2").load("/view/footer.html");
             $("#footer3").load("/view/footer.html");
         });
        </script>
        <style>
         @import url("/view/css/main.css");
        </style>
    </head>
    <script type="text/javascript" src="js/autologout.js"></script>
    <script type="text/javascript" src="jquery.dform-1.1.0.js"></script>
    <script type="text/javascript" src="js/insert_image.js"></script>

    <body>
	<div id="header"><?php include "../view/header.php"; ?></div>
	<div id="scroll"></div>

	<div id="content">
            <div class="tab">
  	        <div class="testo" align="center"><h2>Foto, Stampe e Lastre</h2></div>
		<button class="tablink" onclick="openPage('Insert', this)">Inserimento</button>
		<button class="tablink" onclick="openPage('Update', this)">Aggiornamento</button>
		<button class="tablink" onclick="openPage('Delete', this)">Cancellazione</button>
            </div>

            <div id="Insert" class="tabcontent">
		<div align=center id=result1 style="color:green"></div>
		<div align=center id=error1 style="color:red"></div>
		<br>
		<div align="center">
                    <form class="sel_tipologia" name="sel_tipologia" id="sel_tipologia" action method="POST">
			<label>Tipologia:</label>
			<select name="tipologia" class="tipologia" id="tipologia">
      	                    <option selected="selected">----</option>
	                    <?php
  	                    foreach ($categories as $category) {
   	    	                echo '<option>'.$category['category'].'</option>';
  	                    }
	                    ?>
       			</select>
                    </form>
                    <br>
                    <form id="insert_form"></form>
                    <div id="footer1" align="center"></div>
		</div>
            </div>

            <div id="Update" class="tabcontent">
		<div align=center id=result2 style="color:green"></div>
		<div align=center id=error2 style="color:red"></div>
		<br>
		<form id="lets_search_for_update" action="" style="width:400px;margin:0 auto;text-align:left;">
                    Filtro codice_archivio:&nbsp;<input type="text" name="str" id="str">
                    <input type="submit" value="Filtra" name="send" id="send">
		</form>
		<br>
		<div align="center">
                    <form class="form_sel_photo" id="form_sel_photo" name="form_sel_photo" action method="post">
			<label>Scegli Fotografia:</label>
			<select id="sel_photo" name="sel_photo">
			</select>
                    </form>
		</div>
		<br>
		<form id="update_form"></form>
		<div id="footer2" align="center"></div>
            </div>

            <div id="Delete" class="tabcontent">
		<div align=center id=result3 style="color:green"></div>
		<div align=center id=error3 style="color:red"></div>
		<br>

		<form id="lets_search_for_delete" action="" style="width:400px;margin:0 auto;text-align:left;">
                    Filtro codice_archivio:<input type="text" name="str_for_delete" id="str_for_delete">
                    <input type="submit" value="Filtra" name="send" id="send">
		</form>
		<br><br>
		<div align="center">
                    <form class="delete_photo" name="delete_photo" id="delete_photo" action method="POST">
			<div id="search_results_for_delete"></div>
			<button type="submit" id="submit_delete"><img src="/view/icons/trash.png">&nbsp;Rimuovi Foto Selezionate</button>
                    </form>
		</div>
		<div id="footer3" align="center"></div>
            </div>
	</div>
    </body>
</html>
