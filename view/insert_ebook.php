<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/Member.php";
require_once "../class/solr_curl.php";

$m = new Member();
$categories = $m->getAllCategories("ebook_categories");

$selects = "";
$size = 0;

function fillSelection() {
    global $selects, $size;

    $selects = "";
    $json_dec = json_decode(listCodiceArchivio("ebook_categories"), true);
    if (isset($json_dec['solr_error'])) {
       echo "<div style='color:red'>Il server Solr non &egrave; attivo. Contattare l'amministratore del sistema.</div>";
    } else {
        foreach ($json_dec['response']['docs'] as $select) {
            $selects = $selects.'<option value="'.$select['codice_archivio'].'">'.$select['codice_archivio'].'</option>';
        }

        $size = count($json_dec['response']['docs']);
        if ($size > 14) {
           $size = 15;
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <meta charset="UTF-8" />
        <title>eDoc</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">
</script>
<script>
$(function() {
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
<script type="text/javascript" src="js/insert_ebook.js"></script>

<body>
    <?php include "../view/header.php"; ?>
    <div class="tab">
    <button class="tablinks" onclick="openCity(event, 'inserimento')">Inserimento</button>
    <button class="tablinks" onclick="openCity(event, 'aggiornamento')">Aggiornamento</button>
    <button class="tablinks" onclick="openCity(event, 'rimozione')">Cancellazione</button>
  <div align="right" style="vertical-align=bottom;">
         <h2>eDocs</h2>
  </div>
    </div>
    
<div id="inserimento" class="tabcontent">
<div align=center id=result1 style="color:green"></div>
<div align=center id=error1 style="color:red"></div>
<br>
<?php fillSelection(); ?>
<div align="center">
<form class="new_ebook" name="new_ebook" id="new_ebook" action method="POST">
<table style="width:80%">
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
    <td rowspan=3>
        <button id="import" class="btn-info btn-insert-ebook"><img src="/view/icons/plus.png">&nbsp;Inserisci</button>
    </td>
</tr>
<tr>
    	   	<td>
		    <label for="fname" class="fname">Documento elettronico<br>(PDF, JPG, TIFF, DOC, DOCX, EML, MSG):</label>
	 	</td>
		<td>
		    <input name="edoc[]" id="edoc" type="file" value="" multiple><br><br>
	 	</td>
    	   </tr>
    	   <tr>
	        <td>
		    <label for="do_ocr">Unisci per creare un singolo documento </label>
		</td>
    		<td>
		    <input type="checkbox" id="do_merge" name="do_merge" value="merge">
    		</td>
    		</td>
    	   </tr>
    	   <tr>
	        <td>
		    <label for="do_ocr">Salva documento con testo OCR </label>
    	        </td>
	    	<td>
		    <input type="checkbox" id="do_ocr" name="do_ocr" value="OCR">
    	   	</td>
    	   </tr>
    	   <tr>
    	        <td>
		    <button id="test_ocr" class="btn-info btn_ocr">Prova OCR</button>
    		</td>
    	   </tr>
    	   <tr>
     	   	<td>
		    <label for="fname" class="fname">Testo OCR:</label>
		</td>
		<td>
		    <textarea readonly id="testo_ocr" name="testo_ocr" rows="10" cols="60" placeholder="OCR"></textarea>
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
           </form>
       </table>
</div>
<br>
<div id="footer1" align="center"></div>
</div>

<div id="aggiornamento" class="tabcontent">
<div align=center id=result2 style="color:green"></div>
<div align=center id=error2 style="color:red"></div>
<br>
<?php fillSelection(); ?>
<div align="center">
     <form class="sel_ebook" name="sel_ebook" id="sel_ebook" action method="post">
     	   <label for="cars">Scegli eDoc:</label>
           <select id="volume" name="volume">
           	  <option>----</option>
           	  <?php echo $selects;  ?>
           </select>
    </form>
    <br>

    <form enctype="multipart/form-data" class="upd_ebook" name="upd_ebook" id="upd_ebook" action method="POST">
    <table style="width:80%">
    <tr>
    	 <td>
	    <label for="fname" class="fname">Codice archivio:</label>
    	 </td>
	 <td>
	    <input type="text" size="25" id="codice_archivio_upd" name="codice_archivio" readonly="readonly" placeholder="XXXX.YY">
    	 </td>
	 <td rowspan=2>
	     <button id="import" class="btn-info btn-update-ebook"><img src="/view/icons/update_small.png">&nbsp;Aggiorna</button>
	 </td>
    </tr>
    <tr>
   	 <td>
	    <label for="fname" class="fname">Tipologia:</label>
	 </td>
	 <td>
	 <input type="text" id="tipologia_upd" name="tipologia" readonly="readonly">
<!----	    <select name="tipologia" class="tipologia" id="tipologia">
            	    <option selected="selected">----</option>
		    <?php
	  	    foreach ($categories as $category) {
	    	    echo '<option>'.$category['category'].'</option>';
	  	    }
		    ?>
            </select>  ---->
	 </td>
    </tr>
    <tr>
    	 <td>
	    <label for="fname" class="fname">Note:</label>
	 </td>
	 <td>
	    <textarea id="note_upd" name="note_upd" rows="10" cols="60" placeholder="note"></textarea>
	 </td>
	 <td rowspan=2>
	    <img id="thumbnail" src="">
	 </td>
    </tr>
    <tr>
    	 <td>
	    <label for="fname" class="fname">Testo OCR:</label>
	 </td>
	 <td>
	    <textarea id="text_upd" name="text_upd" rows="10" cols="60" placeholder="OCR"></textarea>
	 </td>
    </tr>
    </table>
    </form>
</div>
<br>
<div id="footer2" align="center"></div>
</div>

<div id="rimozione" class="tabcontent">
<div align=center id=result3 style="color:green"></div>
<div align=center id=error3 style="color:red"></div>
<br>
<?php fillSelection(); ?>
<div align="center">
    <form class="delete_ebook" name="delete_ebook" id="delete_ebook" action method="POST">
  	  <select width=100px id="codici[]" name="codici[]" size="<?php echo $size; ?>" multiple>
	  <?php echo $selects; ?>
  	  </select><br><br>
  	  <button type="submit" id="submit" name="import" class="btn-danger btn-delete-ebook"><img src="/view/icons/trash.png">&nbsp;Rimuovi eDoc Selezionati</button>  
    </form>
</div>
<br>
<div id="footer3" align="center"></div>
</div>

<script type="text/javascript" src="js/tab_selection.js"></script>
</body>
</html>
