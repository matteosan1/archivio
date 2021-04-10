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
        <script type="text/javascript" src="js/autologout.js"></script>
        <script type="text/javascript" src="jquery.dform-1.1.0.js"></script>
        <script type="text/javascript" src="js/insert_book.js"></script>
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

            <div id="Insert" class="tabcontent">
                <div align=center id=result1 style="color:green"></div>
                <div align=center id=error1 style="color:red"></div>
                <br>
                <form id="insert_form"></form>
                <div id="footer1" align="center"></div>
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
                    <form class="form_sel_volume" id="form_sel_volume" name="form_sel_volume" action method="post">
                        <label for="volume">Scegli volume:</label>
                        <select id="volume" name="volume"> 
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
                    <form class="delete_book" name="delete_book" id="delete_book" action method="POST">
                        <div id="search_results_for_delete"></div>                   
                        <button type="submit" id="submit_delete"><img src="/view/icons/trash.png">&nbsp;Rimuovi Volumi Selezionati</button>
                    </form>
                </div>
                <div id="footer3" align="center"></div>
            </div>

            <div id="Backup" class="tabcontent">
                <div align=center id=result4 style="color:green"></div>
                <div align=center id=error4 style="color:red"></div>
                <br>

                <div align="center">
                <form class="fm_backup" id="fm_backup" name="fm_backup" action method="POST">
                    <input type="hidden" name="func" value="backup">
                    <table>
                        <tr>
                            <td><label for="backup_data">Data di backup:</label></td>
                            <td><input type="date" id="last_upload" name="last_upload"></td>
                        </tr>
                        <tr>
                            <td colspan=2 align=center><br>
                            <button type="submit" id="submit" name="import" class="btn-info btn-backup">Backup</button>
                            <br></td>
                        </tr>
                </form>
                        <tr>
                            <td><br><label for="backup_res">File di backup:</label></td>
                            <td><div id="link"></div></td>
                        </tr>
                    </table>
                </div>
                <br>
                <div id="overlay"><div><img src="icons/loading.gif" width="64px" height="64px"/></div></div>
                <div id="footer4" align="center"></div>
            </div>

            <div id="Restore" class="tabcontent">
                <div align=center id=result5 style="color:green"></div>
                <div align=center id=error5 style="color:red"></div>
                <br>

                <div align=center>
                <form class="new_catalogue" name="new_catalogue" id="new_catalogue" action method="POST">
                    <table>
                        <tr>
                            <td><label class="col-md-4 control-label">File di Catalogo (.CSV)</label></td>
                            <td><input type="file" name="filecsv" id="filecsv" accept=".csv"></td>
                        </tr>
                        <tr>
                            <td><label class="col-md-4 control-label">File delle copertine (.ZIP)</label></td>
                            <td><input type="file" name="filezip" id="filezip" accept=".zip"></td>
                        </tr>
                        <input type="hidden" name="func" value="restore">
                    </table>
                    <br><br>
                    <button type="submit" id="restore" name="restore" class="btn-info btn-restore"><img src="/view/icons/plus.png">&nbsp;Inserisci Catalogo</button>
                </form>
                </div>
                <br>
                <div id="overlay"><div><img src="icons/loading.gif" width="64px" height="64px"/></div></div>
                <div id="footer5" align="center"></div>
            </div>

            <div id="Cover" class="tabcontent">
                <div align=center id=result6 style="color:green"></div>
                <div align=center id=error6 style="color:red"></div>
                <br>
                <h3><?php echo $output[0];?></h3>
<?php
    $table = array_chunk(json_decode($output[1], true), 10);
    echo "<table border=1px>";
    for ($i=0; $i<count($table); $i++) {
    	echo "<tr>";
        for ($j=0; $j<count($table[$i]); $j++) {    
    	    echo "<td>&nbsp;".$table[$i][$j]."&nbsp;</td>";
	}
	echo "</tr>";
    }
    echo "</table>";
?>
<br>
<h3><?php echo $output[2];?></h3>
<?php
    $table = array_chunk(json_decode($output[3], true), 10);
    echo "<table border=1px>";
    for ($i=0; $i<count($table); $i++) {
    	echo "<tr>";
        for ($j=0; $j<8; $j++) {    
    	    echo "<td>&nbsp;".$table[$i][$j]."&nbsp;</td>";
	}
	echo "</tr>";
    }
    echo "</table>";
?>
                <div id="footer5" align="center"></div>
            </div>
        </div>
    </body>         
</html>
