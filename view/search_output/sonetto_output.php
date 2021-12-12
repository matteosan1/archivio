<?php
    require_once '../view/formats.php';
    
    function sonettoOutput($document, $highlighting) {
        $out = '<table class="result_table" width=90%>';

        $field_conversion = array("codice_archivio"=>"Codice Archivio", "tipologia"=>"Tipologia",
                                  "autore"=>"Autore", "dedica"=>"Dedica", "committente"=>"Committente",
                                  "ricorrenza"=>"Ricorrenza", "stampato_da"=>"Stampato da",
                                  "anno"=>"Anno", "dimensioni"=>"Dimensioni", "note"=>"Note");
    
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
                $out .= codiceArchivioFormatSon($value, $val, 'THUMBNAILS_DIR');
            } else if  ($key == 'note') {  
                $highlightedDoc = $highlighting->getResult($document->codice_archivio);
                $out .= noteFormat($val, $highlightedDoc);  
            } else {
                $out .= '<tr><th align="right" valign="middle">'.$value.': </th><td valign="middle" align="left">'.$val."</td></tr>";    
            }

        }
        $out .= "</table>";
        return $out;
    }
?>
