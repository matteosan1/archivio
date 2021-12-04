<?php
    require_once '../view/formats.php';
    
    function deliberaOutput($document, $highlighting) {
        $out = '<table class="result_table" width=90%>';
        $field_conversion = array("codice_archivio"=>"Codice Archivio", "tipologia"=>"Tipologia",
                                  "tipo_delibera"=>"Organo deliberante", "data"=>"Data", "argomento_breve"=>"Argomento",
                                  "testo"=>"Testo", "straordinaria"=>"Straordinaria", "unanimita"=>"Unanimit&agrave");

        foreach ($field_conversion as $key => $value) {
            if (isset($document[$key])) {
                $val = $document[$key];
            } else {
                continue;
            }

            if (is_array($val)) {
                $val = explode(",", $val);
            }

            if ($key == "data") {
                $val = substr($val, 0, 10);  
            }

            if ($key == "unanimita") {
                if ($val == "1") {
                    $out .= '<tr><th align="right" valign="middle">'.$value.': </th><td valign="middle" align="left">SI</td></tr>';    
                } 
            } else if ($key == "straordinaria") {
                if ($val == 1) {
                    $out .= '<tr><th align="right" valign="middle">'.$value.': </th><td valign="middle" align="left">SI</td></tr>';    
                }
            } else if ($key == 'argomento_breve') {
                $highlightedDoc = $highlighting->getResult($document->codice_archivio);
                $out .= testoFormat($key, $value, $val, $highlightedDoc);
                //$text = combineHighlight($highlightedDoc, $val);
                //$out .= '<tr style="border-top: 1px solid #d3d3d3;"><th align="right" valign="middle">'.$value.': </th><td valign="middle" align="left">'.$text."</td></tr>";
            } else if ($key == 'testo') {
                $highlightedDoc = $highlighting->getResult($document->codice_archivio);
                $out .= testoFormat($key, $value, $val, $highlightedDoc, 0);
                //$text = combineHighlight($highlightedDoc, $val);
                //$out .= '<tr style="border-bottom: 1px solid #d3d3d3;"><th align="right" valign="middle">'.$value.': </th><td valign="middle" align="left">'.$text."</td></tr>";    
            } else {
                $out .= '<tr><th align="right" valign="middle">'.$value.': </th><td valign="middle" align="left">'.$val."</td></tr>";    
            }
        }
        $votazione = $document['favorevoli']."-".$document['contrari']."-".$document['astenuti'];
        $out .= '<tr><th align="right" valign="middle">Votazione: </th><td valign="middle" align="left">'.$votazione."</td></tr>";    
        $riferimento = $document['num_contestuale']." cap. ".$document['capitolo']." (pg. ".$document['pagina'].")";
        $out .= '<tr><th align="right" valign="middle">Riferimento: </th><td valign="middle" align="left">'.$riferimento."</td></tr>";    
        $out .= "</table>";
        return $out;
    }
?>
