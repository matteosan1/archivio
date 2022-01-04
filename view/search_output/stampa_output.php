<?php
require_once '../view/formats.php';

function stampaOutput($document, $highlighting) {
    $out = '<table class="result_table" width=90%>';
    // FIXME mancano campi di sicuro
    
    $field_conversion = array("codice_archivio"=>"Codice Archivio", "tipologia"=>"Tipologia",
                              "By-line"=>"Fotografo",
                              "dimensione"=>"Dimensione",
                              "anno"=>"Anno", "Keywords"=>"Tags",
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
            $out .= codiceArchivioFormat($value, $val, 'THUMBNAILS_DIR');
        } else if ($key == 'Keywords') {
            $highlightedDoc = $highlighting->getResult($document->codice_archivio);
            $out .= keywordsFormat($value, $val, $highlightedDoc);
        } else if  ($key == 'note') {  
            $highlightedDoc = $highlighting->getResult($document->codice_archivio);
            //print (gettype($highlightedDoc));
            $out .= noteFormat($val, $highlightedDoc);  
        } else {
            $out .= '<tr><th align="right" valign="top">'.$value.': </th><td align="left">'.$val."</td></tr>";    
        }
    }
    $out .= "</table>";
    return $out;
}
?>
