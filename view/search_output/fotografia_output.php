<?php
    require_once '../view/formats.php';
    
    function fotoOutput($document, $highlighting) {
        $out = '<table class="result_table" width=90%>';
        // FIXME mancano campi di sicuro

        $field_conversion = array("codice_archivio"=>"Codice Archivio", "tipologia"=>"Tipologia",
                                  "By-line"=>"Fotografo", "Content-Length"=>"Dimensione",
                                  //"Content-Type"=>"image/jpeg",
                                  "date"=>"Data", "modified"=>"Ultima modifica",
                                  "Image_Height"=>"Altezza",
                                  "Image_Width"=>"Larghezza", "exif_ExposureTime"=>"Exp.", "exif_FNumber"=>"fNumber",
                                  "exif_FocalLength"=>"Focale","exif_IsoSpeedRatings"=>"ISO", "geo_lat"=>"Lat.",
                                  "geo_long"=>"Long.", "Keywords"=>"Tags");

        foreach ($field_conversion as $key => $value) {
            if (!empty($document[$key])) {
                $val = $document[$key];
            } else {
                continue;
            }
       
            if ($key == "date" or $key == "modified") {
                $val = substr($val[0], 0, 10);                
            } else if ($key == 'Content-Length') {
                $val = (int)($val[0]/1024)." kB";
            } else if ($key == 'geo_long' or $key == 'geo_lat' or $key == 'exif_ExposureTime' or $key == "exif_FNumber" or $key == "exif_Flash" or $key == "exif_FocalLength" or $key == "exif_IsoSpeedRatings") {
                $val = $val[0];
            }
    
            if (is_array($val)) {
                $val = explode(",", $val);
            }

            if ($key == 'codice_archivio') {
                $out .= codiceArchivioFormat($value, $val, 'THUMBNAILS_DIR');
            } else if ($key == 'Keywords') {
                $highlightedDoc = $highlighting->getResult($document->codice_archivio);
                $out .= keywordsFormat($value, $val, $highlightedDoc);
            } else {
                $out .= '<tr><th align="right" valign="top">'.$value.': </th><td align="left">'.$val."</td></tr>";    
            }

        }
        $out .= "</table>";
        return $out;
    }
?>
