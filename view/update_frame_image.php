<form enctype="multipart/form-data" action method="POST" id="upload">
<table>
<tr>
<div class="col-1">
    <td>
    <label for="fname" class="fname">Codice archivio:</label>
    </td><td>
    <input type="text" size="25" id="codice_archivio" name="codice_archivio" disabled placeholder="XXXX.YY">
    </td>
    </div>
   </tr>
   <tr>
   <img src="">
   </tr>
   <tr><td valign="top"><br>
   <label>Fotografo: </label></td><td>
   <input id="author" value="" name="author">
   </td></tr>
   <tr><td valign="top">        
   Tag addizionali (comma separated):</td><td><textarea name="list_of_tags" rows="5" cols="30"></textarea>
    </td></tr>
    <tr><td>
    <button type="button" id="uploadBtn" class="btn btn-success"> Upload </button>
      </form>
      </td></tr>
	</table>
	<div id=uploaded></div>
