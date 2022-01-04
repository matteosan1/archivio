<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../view/config.php";

exec($GLOBALS['PYTHON_BIN'].' ../class/check_copertine.py', $output, $status);
?>

<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
        <meta charset="UTF-8" />
        <title>Vestizioni</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">

	 $(function(){
	     $("#footer1").load("/view/footer.html");
	     $("#footer2").load("/view/footer.html");
  	     $("#footer3").load("/view/footer.html");
	 });
	</script>
        <style>
         @import url("/view/css/main.css");
        </style>
        <script type="text/javascript" src="js/autologout.js"></script>
        <script type="text/javascript" src="js/jquery.dform-1.1.0.js"></script>
        <script type="text/javascript" src="js/insert_vestizione.js"></script>
    </head>
    <body>
        <div id="header"><?php include "../view/header.php"; ?></div>
        <div id="scroll"></div>

        <div id="content">
            <div class="tab">
  	        <div class="testo" align="center"><h2>Monturati</h2></div>
                <button class="tablink" onclick="openPage('Insert', this)">Inserimento</button>
                <button class="tablink" onclick="openPage('Update', this)">Aggiornamento</button>
                <button class="tablink" onclick="openPage('Delete', this)">Cancellazione</button>
            </div>

            <div id="Insert" class="tabcontent">
		<div align=center id=result1 style="color:green"></div>
		<div align=center id=error1 style="color:red"></div>
                <br>
                <form id="insert_form"></form>
                <div id="overlay"><div><img src="icons/loading.gif" width="64px" height="64px"/></div></div>
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
                <form class="form_sel_vestizione" id="form_sel_vestizione" name="form_sel_vestizione" action method="post">
                    <label for="vestizione">Scegli vestizione:</label>
                    <select id="vestizione" name="vestizione">
                    </select>
                </form>
            </div>
            <br>
            <form id="update_vestizione"></form>
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
                <form class="delete_vestizione" name="delete_vestizione" id="delete_vestizione" action method="POST">
                    <div id="search_results_for_delete"></div>
                    <button type="submit" id="submit_delete"><img src="/view/icons/trash.png">&nbsp;Rimuovi monturati selezionati</button>
                </form>
            </div>
            <div id="footer3" align="center"></div>
        </div>
        </div>
    </body>
</html>
