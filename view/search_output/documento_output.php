<?php
require_once '../view/formats.php';

function documentoOutput($document, $highlighting) {
    $out = '<table class="result_table" width=100%>';
    
    $field_conversion = array("codice_archivio"=>"Codice Archivio", "tipologia"=>"Tipologia", "titolo"=>"Titolo",
                              "autore"=>"Autore", "anno"=>"Anno", "size"=>"Dimensione",
                              "cdate"=>"Data", "mdate"=>"Modifica", "pagine"=>"Pagine", "parole"=>"Parole",
                              "note"=>"Note");        
    
    foreach ($field_conversion as $key => $value) {
        if (!empty($document[$key])) {
            $val = $document[$key];
        } else {
            continue;
        }
        
        // FIXME SINGLE VALUE
        if (is_array($val)) {
            $val = $val[0];//explode(",", $val);
        }
        
        if ($key == 'size') {
            $val = $val." bytes";
        }
        
        if ($key == 'cdate' or $key == 'mdate') {
            $val = substr($val, 0, 10);
        }
        
        if ($key == 'codice_archivio') {
            $out .= codiceArchivioFormatEdoc($value, $val, $document->resourceName, 'THUMBNAILS_DIR');
        } else if ($key == 'tipologia') {
            // FIXME SINGLE VALUE
            $out .= tipologiaEdoc($value, $val, $document->type);
        } else if ($key == 'note') {  
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
