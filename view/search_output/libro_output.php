<?php
    require_once '../view/formats.php';
        
    function libroOutput($document, $highlighting) {
        $out = '<table class="result_table" width=90%>';
        // FIXME mancano campi di sicuro
        $field_conversion = array("codice_archivio"=>"Codice Archivio", "titolo"=>"Titolo",
                                  "sottotitolo"=>"Sottititolo", "prima_responsabilita"=>"Autore",
                                  "altre_responsabilita"=>"Coautori", "tipologia"=>"Tipologia",
                                  "anno"=>"Anno", "luogo"=>"Luogo",
                                  "edizione"=>"Edizione", "soggetto"=>"Soggetto", "descrizione"=>"Descrizione",
                                  "cdd"=>"CDD", "note"=>"Note");
        foreach ($field_conversion as $key => $value) {
            $val = $document[$key];
            if ($val == "") {
                continue;
            }
    
            if ($key == "data") {
                $val = substr($val[0], 0, 10);                
            }
            if (is_array($val)) {
                $val = explode(",", $val);
            }
         
            if ($key == 'codice_archivio') {
                $out .= codiceArchivioFormat($value, $val, 'COVER_DIR');
            } else if  ($key == 'note') {  
                $highlightedDoc = $highlighting->getResult($document->codice_archivio);
                //print (gettype($highlightedDoc));
                $out .= noteFormat($val, $highlightedDoc);  
            } else {
                $out .= '<tr><th align="right" valign="middle">'.$value.':</th><td valign="middle" align="left">'.$val."</td><td></td></tr>";    
            }

        }
        $out .= "</table>";
        return $out;
    }
?>

