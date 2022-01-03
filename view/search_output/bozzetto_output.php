<?php
    require_once '../view/formats.php';
    
    function bozzettoOutput($document, $highlighting) {
        $out = '<table class="result_table" width=90%>';
        // FIXME mancano campi di sicuro

        $field_conversion = array("codice_archivio"=>"Codice Archivio", "tipologia"=>"Tipologia",
                                  "autore"=>"Autore",
                                  "categoria"=>"Categoria", "descrizione"=>"Descrizione", "tecnica"=>"Tecnica",
                                  "anno"=>"Anno", "dimensioni"=>"Dimensioni", 
                                  "note"=>"Note");
    
        foreach ($field_conversion as $key => $value) {
            if (!empty($document[$key])) {
                $val = $document[$key];
            } else {
                continue;
            }
           
            if (is_array($val)) {
                $val = explode(",", $val);
            }

            if ($key == 'codice_archivio') {
                $out .= codiceArchivioFormatBoz($value, $val, 'THUMBNAILS_DIR', $document->resourceName);
            } else if  ($key == 'note') {  
                $highlightedDoc = $highlighting->getResult($document->codice_archivio);
                //print (gettype($highlightedDoc));
                $out .= noteFormat($val, $highlightedDoc);  
            } else {
                $out .= '<tr><th align="right" valign="middle">'.$value.': </th><td valign="middle" align="left">'.$val."</td></tr>";    
            }

        }
        $out .= "</table>";
        return $out;
    }
?>
