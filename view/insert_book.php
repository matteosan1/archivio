<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../view/config.php";
require_once "../class/Member.php";
require_once "../class/solr_curl.php";

$m = new Member();
$categories = $m->getAllCategories('book_categories');
$prefissi = $m->getAllPrefissi();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <meta charset="UTF-8" />
        <title>Libri</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">
	</script>	
	<script>
	    $(function(){
		$("#footer1").load("/view/footer.html");
		$("#footer2").load("/view/footer.html");
  		$("#footer3").load("/view/footer.html"); 
	    });
	</script>
<style>
@import url("/view/css/band_style.css");
</style>
    </head>
    <script type="text/javascript" src="js/autologout.js"></script>
    <script type="text/javascript" src="js/insert_book.js"></script>

<body>
    <?php include "../view/header.php"; ?>
    <div class="tab">
    	 <button class="tablinks" onclick="openCity(event, 'inserimento')">Inserimento</button>
  	 <button class="tablinks" onclick="openCity(event, 'aggiornamento')">Aggiornamento</button>
  	 <button class="tablinks" onclick="openCity(event, 'cancellazione')">Cancellazione</button>
  	 <div align="right" style="vertical-align=bottom;">
              <h2>Biblioteca</h2>
  	 </div>
    </div>

<div id="inserimento" class="tabcontent">
     <div align=center id=result1 style="color:green"></div>
     <div align=center id=error1 style="color:red"></div>
     <br>
     <div align="center">
<form enctype="multipart/form-data" class="new_book" name="new_book" id="new_book" action method="POST">
    <table>
    <tr>
                <td>
                    <label for="fname" class="fname">Prefisso codice archivio:</label>
		</td>
		<td>
	            <select name="prefissi" class="prefissi" id="prefissi">
	                <option selected="selected"></option>
			<?php
			    foreach ($prefissi as $category) {
			        echo '<option>'.$category['prefix'].'</option>';
			    }
			?>
	            </select>
		</td>
		<td rowspan=2>
		    <button class="btn btn-sm btn-info btn-insert-book" id="inserisci"><img src="/view/icons/plus.png">&nbsp;Inserisci</button>		 
		</td>
	    </tr>
	    <tr>
	    	<td>
                    <label for="fname" class="fname">Tipologia:</label>
		</td>
		<td>
	            <select name="tipologia" class="tipologia" id="tipologia">
	                <option selected="selected">----</option>
			<?php
			    foreach ($categories as $category) {
			        echo '<option>'.$category['category'].'</option>';
			    }
			?>
	            </select>
		</td>
	    </tr>
	    <tr>
	    	<td>
                    <label for="fname" class="fname">Titolo:</label>
		</td>
		<td>
		    <textarea name="titolo" id="titolo" rows="4" cols="60" placeholder="Titolo del libro"></textarea>
		</td>
	    </tr>
	    <tr>
  	      	<td>
                    <label for="fname" class="fname">Sottotitolo:</label>
		</td>
		<td>
		    <textarea name="sottotitolo" rows="4" cols="60" placeholder="Eventuale sottotitolo"></textarea>
		</td>
	    </tr>
	    <tr>
	       	<td>
                    <label for="fname" class="fname">Prima responsabilit&agrave;:</label>
		</td>
		<td>
                    <input type="text" size="50" id="prima_responsabilita" name="prima_responsabilita">
		</td>
	    </tr>
	    <tr>
	      	<td>
                    <label for="fname" class="fname">Altre responsabilit&agrave;:</label>
		</td>
 		<td>
                    <input type="text" size="60" id="altre_responsabilita" name="altre_responsabilita">
		</td>
            </tr>
	    <tr>
		<td>
                    <label for="fname" class="fname">Luogo:</label>
		</td>
		<td>
                    <input type="text" size="50" id="luogo" name="luogo">
		</td>
            </tr>
	    <tr>
		<td>
                    <label for="fname" class="fname">Edizione:</label>
		</td>
		<td>
                    <input type="text" size="50" id="edizione" name="edizione">
	        </td>
            </tr>
	    <tr>
		<td>
                    <label for="fname" class="fname">Ente:</label>
		</td>
		<td>
                    <input type="text" size="50" id="ente" name="ente">
		</td>
            </tr>
	    <tr>
	   	<td>
                    <label for="fname" class="fname">Serie:</label>
		</td>
		<td>
                    <input type="text" size="50" id="serie" name="serie">
		</td>
            </tr>
	    <tr>
	      	<td>
                    <label for="fname" class="fname">Anno:</label>
		</td>
		<td>
                    <input type="number" size="4" id="anno" name="anno" placeholder="XXXX">
		</td>
            </tr>
	    <tr>
		<td>
                    <label for="fname" class="fname">Descrizione:</label>
		</td>
		<td>
                    <input type="text" size="35" id="descrizione" name="descrizione" placeholder="XX p. : ill. ; YY cm">
		</td>
            </tr>
	    <tr>
		<td>
		    <label for="fname" class="fname">CDD:</label>
		</td>
		<td>
                    <input type="text" size="20" id="cdd" name="cdd" placeholder="123.456789">
		    <div id="search_cdd_error" style="color:red">Mancano autore e/o titolo per cercare il CDD</div>
		</td>
	    </tr>
	    <tr>
		<td>
                    <label for="fname" class="fname">Soggetto:</label>
		</td>
		<td>
                    <input type="text" size="60" id="soggetto" name="soggetto">	
	        </td>
            </tr>
	    <tr>
		<td>
		    <label for="fname" class="fname">Note:</label>
		</td>
		<td>
		    <textarea name="note" rows="10" cols="60" placeholder="note"></textarea>
		</td>
            </tr>
	    <tr>
		<td>
		    <label for="fname" class="fname">File copertina (JPG):</label>
		</td>
		<td>
		    <input name="copertina" id="copertina" type="file" value="" accept=".jpg,.jpeg"><br><br>
		</td>
	    </tr>
	</table>
</form>
    </div>
    <br>
    <div id="footer1" align="center"></div>
</div>

<div id="aggiornamento" class="tabcontent">
     <div align=center id=result2 style="color:green"></div>
     <div align=center id=error2 style="color:red"></div>
     <br>

     <form id="lets_search_for_update" action="" style="width:400px;margin:0 auto;text-align:left;">
        Filtro codice_archivio:<input type="text" name="str" id="str">
        <input type="submit" value="Filtra" name="send" id="send">
     </form>
     <br>
     <div align="center">
     	  <form class="sel_volume" name="sel_volume" id="sel_volume" action method="post">
  	  	<label for="cars">Scegli volume:</label>
		<select id="volume" name="volume"> 
      		       <div id="search_results_for_update"></div>
    		</select>	     
	  </form> 
     </div>
     <br>


<form enctype="multipart/form-data" id="upd_book" class="upd_book" name="upd_book" action method="post">
    <table>
    <tr>
     	<td>
            <label for="fname" class="fname">Codice archivio:</label>
	</td>
	<td>
	    <input type="text" size="25" id="codice_archivio_upd" name="codice_archivio" readonly="readonly" placeholder="XXXX.YY">
	</td>
	<td rowspan=2>
	    <button class="btn btn-sm btn-info btn-update-book" id="inserisci"><img src="/view/icons/update_small.png">&nbsp;Aggiorna</button>
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Tipologia:</label>
	</td>
	<td>
	    <input type="text" id="tipologia_upd" name="tipologia" cols="60" readonly="readonly">
	</td>
	<td rowspan=4>
    	    <img id="thumbnail" width="200px" src="">
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Titolo:</label>
	</td>
	<td>
	    <textarea id="titolo_upd" name="titolo" rows="3" cols="60" placeholder=Titolo del libro"></textarea>
	</td>
    </tr>
    <tr>
        <td>
            <label for="fname" class="fname">Sottotitolo:</label>
	</td>
	<td>
	    <textarea id="sottotitolo_upd" name="sottotitolo" rows="3" cols="60" placeholder="Eventuale sottotitolo"></textarea>
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Prima responsabilit&agrave;:</label>
	</td>
	<td>
            <input type="text" size="50" id="prima_responsabilita_upd" name="prima_responsabilita">
	</td>
	<td rowspan=5>
	    <img id="thumbnail" src="">
	 </td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Altre responsabilit&agrave;:</label>
	</td>
	<td>
            <input type="text" size="60" id="altre_responsabilita_upd" name="altre_responsabilita">
	</td>
    </tr>
    <tr>
	<td>
            <label for="fname" class="fname">Luogo:</label>
	</td>
	<td>
            <input type="text" size="50" id="luogo_upd" name="luogo">
	</td>
    </tr>
    <tr>
	<td>
            <label for="fname" class="fname">Edizione:</label>
	</td>
	<td>
            <input type="text" size="50" id="edizione_upd" name="edizione">
	</td>
    </tr>
    <tr>
     	<td>
            <label for="fname" class="fname">Ente:</label>
	</td>
	<td>
            <input type="text" size="50" id="ente_upd" name="ente">
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Serie:</label>
	</td>
	<td>
            <input type="text" size="50" id="serie_upd" name="serie">
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Anno:</label>
	</td>
	<td>
            <input type="number" size="4" id="anno_upd" name="anno" readonly="readonly" placeholder="XXXX">
	</td>
    </tr>
    <tr>
   	<td>
            <label for="fname" class="fname">Descrizione:</label>
	</td>
	<td>
            <input type="text" size="35" id="descrizione_upd" placeholder="XX p. : ill. ; YY cm">
	</td>
    </tr>
    <tr>
   	<td>
	    <label for="fname" class="fname">CDD:</label>
	</td>
	<td>
            <input type="text" size="20" id="cdd_upd" name="cdd" placeholder="123.456789">
	</td>
    </tr>
    <tr>
	<td>
            <label for="fname" class="fname">Soggetto:</label>
	</td>
	<td>
            <input type="text" size="60" id="soggetto_upd" name="soggetto">
	</td>
    </tr>
    <tr>
	<td>
	    <label for="fname" class="fname">Note:</label>
	</td>
	<td>
	    <textarea id="note_upd" name="note_upd" rows="10" cols="60" placeholder="note"></textarea>
	</td>
    </tr>
    <tr>
	<td>
	    <label for="fname" class="fname">File copertina (JPG):</label>
	</td>
	<td>
	    <input name="copertina" id="copertina_upd" type="file" value="" accept=".jpg,.jpeg"><br><br>
	</td>
    </tr>
    </table>
</form>
<br>
<div id="footer2" align="center"></div>
</div>

<div id="cancellazione" class="tabcontent">
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
      	   	<button type="submit" id="submit" name="import" class="btn-danger btn-delete-book"><img src="/view/icons/trash.png">&nbsp;Rimuovi Volumi Selezionati</button>
     	  </form>
     </div>
     <br>
     <div id="footer3" align="center"></div>
</div>

<script type="text/javascript" src="js/tab_selection.js"></script>
</body>
</html>
