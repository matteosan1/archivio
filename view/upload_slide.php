<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/solr_curl.php";
//require_once "../view/solr_client.php";  
//require_once "../class/solr_utilities.php";  
require_once "../class/Member.php";

$m = new Member();
$l1tags = $m->getL1Tags();

$selects = "";
$size = 0;

function fillSelection() {
    global $selects, $size, $client;

    $selects = "";
    $json_dec = json_decode(listCodiceArchivio("slide"), true);

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
        <meta charset="UTF-8" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <title>Immagini</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"> </script>
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
<script type="text/javascript" src="js/upload_slide.js"></script>
<body>
<?php include "header.php"; ?>
<div class="tab">
  <button class="tablinks" onclick="openCity(event, 'inserimento')">Inserimento</button>
  <button class="tablinks" onclick="openCity(event, 'aggiornamento')">Aggiornamento</button>
  <button class="tablinks" onclick="openCity(event, 'cancellazione')">Cancellazione</button>
  <div align="right" style="vertical-align=bottom;">
         <h2>Stampe e Lastre</h2>
  </div>
</div>

<div id="inserimento" class="tabcontent">
<div align=center id=result1 style="color:green"></div>
<div align=center id=error1 style="color:red"></div>
<br>
<?php fillSelection(); ?>
<div align="center">
<form enctype="multipart/form-data" action method="POST" id="new_slide" name="new_slide" class="new_slide">
<table>
    <tr>
	<td valign="top">
            Scansione Stampa/Lastra (jpeg, tiff):
	</td>
	<td>
	    <input id="userfile" name="userfile" type="file" accept=".jpeg,.jpg,.tiff,.tif">
	    <br><br>
    	</td>
    </tr>
    <tr>
    <td>
        <label for="is_slide">Lastra</label>
    </td>
    <td>
        <input type="checkbox" id="is_lastra" name="is_lastra" value="lastra">
    </td>
    </tr>
    <tr>
    <td>
        <label for="fname" class="fname">Dimensione:</label>
    </td>
    <td>
        <input type="text" size="20" id="dimensione" name="dimensione" placeholder="YYYxZZZ cm">
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
	<td valign="top">
            Tag 1 :
	</td>
	<td>
            <select name="tagl1" class="tagl1" id="tagl1">
            <option selected="selected">----</option>
	    <?php
	    foreach ($l1tags as $row) {
    	    	    $id = $row['id'];
    		    $data = $row['name'];
    		    echo '<option value="'.$id.'">'.$data.'</option>';
	    }
	    ?>
            </select>
	    <br><br>
        </td>
    </tr>
    <tr>
	<td valign="top">
            Tag 2:<br>
	</td>
	<td>
	    <select name="tagl2" class="tagl2" id="tagl2">
            <option selected="selected" default>----</option>
            </select><br>
        </td>
    </tr>
    <tr>
        <td valign="top">
	    <br>
	    <label>Fotografo o Archivio: </label>
	</td>
	<td>
	    <input list="author" value="<?php echo $displayName; ?>" name="author">
            <datalist id="author">
            <?php echo "<option value='".$displayName."'>"; ?>     
	    </datalist>
        </td>
    </tr>
    <tr>
     <td>
        <label for="fname" class="fname">Note:</label>
     </td>
     <td>
        <textarea name="note" rows="12" cols="60" placeholder="note"></textarea>
     </td>
    </tr>
    <tr>
	<td align="center" colspan=2>
	    <br>
	    <button id="import" class="btn btn-sm btn-info btn-insert-slide"><img src="/view/icons/plus.png">&nbsp;Inserisci</button>
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
<?php fillSelection(); ?>
<div align="center">
<form class="sel_slide" name="sel_slide" id="sel_slide" action method="post">
      <label for="cars">Scegli Stampa/Lastra:</label>
      <select id="slide" name="slide">
       	     <option>----</option>
	     <?php echo $selects; ?>
      </select>
</form>
<br>
<table width=60%>
<col width=40%>
<col width=60%>
<tr>
    <td>
    <form enctype="multipart/form-data" action method="POST" id="upd_slide" name="upd_slide" class="upd_slide">
    <!----<input type="hidden" id="codice_archivio" name="codice_archivio">---->
    <table>
    <tr>
    	 <td>
	    <label for="fname" class="fname">Codice archivio:</label>
    	 </td>
	 <td>
	    <input type="text" size="25" id="codice_archivio" name="codice_archivio" readonly="readonly" placeholder="STMP.XXXXXX">
    	 </td>
    </tr>
    <tr>
    <td>
        <label for="fname" class="fname">Dimensione:</label>
    </td>
    <td>
        <input type="text" size="20" id="upd_dimensione" name="dimensione" placeholder="YYYxZZZ cm">
    </td>
    </tr>
    <tr>
    <td>
        <label for="fname" class="fname">Anno:</label>
    </td>
    <td>
        <input type="number" size="4" id="upd_anno" name="anno" placeholder="XXXX">
    </td>
    </tr>
    <tr>
	<td valign="top"><br>
   	    <label>Fotografo: </label>
	</td>
	<td>
	    <input type="text" id="By-line" name="By-line">
   	</td>
    </tr>
    <tr>
	<td>
	    <label>Tag L1:</label>
	</td>
	<td>
	    <input type="text" id="upd_tagl1" readonly="readonly" name="upd_tagl1">
	</td>
    </tr>
        <tr>
	<td>
	    <label>Tag L2:</label>
	</td>
	<td>
	    <input type="text" id="upd_tagl2" readonly="readonly" name="upd_tagl2">
	</td>
    </tr>
    <tr>
     <td>
        <label for="fname" class="fname">Note:</label>
     </td>
     <td>
        <textarea id="upd_note" name="note" rows="12" cols="60" placeholder="note"></textarea>
     </td>
    </tr>
    <tr>
	<td colspan=2>
	<div align="center">
	    <button id="import" class="btn-info btn-update-slide"><img src="/view/icons/update_small.png">&nbsp;Aggiorna</button>
	</div>
	</td>
    </tr>
    </table>
    </form>
    </td>
    <td rowspan=6>
    	<div align=center>
    	     <img id="thumbnail" src="">
	</div>     
    </td>
</tr>
</table>
</div>
<br>
<div id="footer2" align="center"></div>
</div>


<div id="cancellazione" class="tabcontent">
<div align=center id=result3 style="color:green"></div>
<div align=center id=error3 style="color:red"></div>
<br>
<?php fillSelection(); ?>
<div align="center">
     <form class="delete_slide" name="delete_slide" id="delete_slide" action method="POST">
       <input type="hidden" id="type" name="type" value="slide">
       <select width=100px id="volumi[]" name="volumi[]" size="<?php echo $size; ?>" multiple>
       <?php echo $selects; ?>
       </select><br><br>
       <button type="submit" id="submit" name="import" class="btn-danger btn-delete-slides"><img src="/view/icons/trash.png">&nbsp;Rimuovi Stampe Selezionate</button>  
     </form>
</div>
<br>
<div id="footer3" align="center"></div>
</div>

<script type="text/javascript" src="js/tab_selection.js"></script>
</body>
</html>
