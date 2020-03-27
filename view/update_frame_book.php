    <form id="upd_book" class="upd_book" name="upd_book" action method="post">
    <input type=hidden name="update_or_insert" id="update_or_insert" value="0">
      <table>
      <div class="form-1">
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
                         <label for="fname" class="fname">Titolo:</label>
			 </td><td>
			 <textarea id="titolo" name="titolo" rows="3" cols="80" placeholder=Titolo del libro"></textarea>
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Sottotitolo:</label>
			 </td><td>
			 <textarea id="sottotitolo" name="sottotitolo" rows="3" cols="80" placeholder="Eventuale sottotitolo"></textarea>
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Prima responsabilit&agrave;:</label>
			 </td><td>
                         <input type="text" size="50" id="prima_responsabilita" name="prima_responsabilita">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Altre responsabilit&agrave;:</label>
			 </td><td>
                         <input type="text" size="80" id="altre_responsabilita" name="altre_responsabilita">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Luogo:</label>
			 </td><td>
                         <input type="text" size="50" id="luogo" name="luogo">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Edizione:</label>
			 </td><td>
                         <input type="text" size="50" id="edizione" name="edizione">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Ente:</label>
			 </td><td>
                         <input type="text" size="50" id="ente" name="ente">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Serie:</label>
			 </td><td>
                         <input type="text" size="50" id="serie" name="serie">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Anno:</label>
			 </td><td>
                         <input type="number" size="4" id="anno" name="anno" placeholder="XXXX">
			 </td>
                    </div>
		    </tr>
		    <tr>
		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Descrizione:</label>
			 </td><td>
                         <input type="text" size="35" id="descrizione" name="descrizione" placeholder="XX p. : ill. ; YY cm">
			 </td>
                    </div>
		    </tr>
		    <tr>
    		    <div class="col-1">
		    	 <td>
		    	 <label for="fname" class="fname">CDD:</label>
			 </td><td>
                         <input type="text" size="20" id="cdd" name="cdd" placeholder="123.456789">
			 </td>
                    </div>
		    </tr>
		    <tr>
    		    <div class="col-1">
		    	 <td>
                         <label for="fname" class="fname">Soggetto:</label>
			 </td><td>
                         <input type="text" size="80" id="soggetto" name="soggetto">
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
		     <div class="col-1">
		    	 <td>
		    	 <label for="fname" class="fname">File copertina (JPG):</label>
			 </td><td>
			 <input name="copertina" id="copertina" type="file" value="" accept=".jpeg,.jpg"><br><br>
			 </td>
                    </div>
		    </tr>
                </div>
		</table>
		<div class="btn" align="center">
		    <button class="btn btn-sm btn-info update_book" id="inserisci">Aggiorna</button>
                </div>
            </form>
