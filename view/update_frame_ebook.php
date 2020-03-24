 <form class="new_ebook" name="new_ebook" id="new_ebook" action method="POST">
 <table style="width:80%">
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
    <div class="col-1">
   	<td>
        <label for="fname" class="fname">Tipologia:</label>
	</td><td>
        <select name="tipologia" class="tipologia" id="tipologia">
        <option selected="selected">----</option>
	<?php
	  foreach ($categories as $category) {
	    echo '<option>'.$category['category'].'</option>';
	  }
	?>
        </select>
	</td>
     </div>
    </tr>
    <tr>
      <div class="col-1">
	<td>
	<label for="fname" class="fname">Note:</label>
	</td><td>
	<textarea name="note" rows="10" cols="80" placeholder="note"></textarea>
	</td>
      </div>
    </tr>
    <tr>
    <td>
    <button id="test_ocr" class="btn-info btn_ocr">Prova OCR</button>
    </td>
    </tr>
    <tr>
     <div class="col-1">
     <td>
      <label for="fname" class="fname">Testo OCR:</label>
	</td>
	<td>
	<textarea id="testo_ocr" name="testo_ocr" rows="10" cols="80" placeholder="OCR"></textarea>
	</td>
      </div>
    </tr>
    </table>
    <button id="import" class="btn-info btn-insert-ebook">Aggiorna</button>
    </div>
    </form>
