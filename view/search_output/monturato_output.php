<?php
    function monturatoOutput($document) {
        $out = '<table class="result_table" width=90%>';
        //$out .= "<column width=50px><column width=200px>";
        $field_conversion = array("data"=>"Data", "nome_cognome"=>"Nome", "ruolo"=>"Ruolo", "evento"=>"Evento");
        foreach ($field_conversion as $key => $value) {
            $val = $document[$key];
            if ($val == "") {
                continue;
            }

            if ($key == "data") {
                $val = substr($val, 0, 10);                
            }
            if (is_array($val)) {
                $val = explode(",", $val);
            }
        
            $out .= '<tr><th align="right" valign="top">'.$value.': </th><td align="left">'.$val."</td><td></td>";
        }
        $out .= "</table>";
        return $out;
    }          
?>
