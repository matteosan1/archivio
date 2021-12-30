<?php
    require_once '../view/config.php';

    function combineHighlight($highlight, $note) {
        $dom = new domDocument('1.0', 'utf-8');
        
        foreach ($highlight as $snippet) {
            $dom->loadHTML($snippet); 
            $dom->preserveWhiteSpace = false; 
            $hTwo= $dom->getElementsByTagName('u');
            $match = $hTwo->item(0)->nodeValue;

            $note = str_replace($match,$GLOBALS['HIGHLIGHT_BEGIN'].$match.$GLOBALS['HIGHLIGHT_END'], $note);
        }

        //print ($note);
        return $note;
    }

    function testoFormat($key, $value, $val, $highlightedDoc, $up=1) {
        $out = '';
        $edge = 'border-top';
        if ($up == 0) {
            $edge = 'border-bottom';
        }

        if (array_key_exists($key, $highlightedDoc)) {
            $text = combineHighlight($highlightedDoc[$key], $val);
            $out .= '<tr style="'.$edge.': 1px solid #d3d3d3;"><th align="right" valign="middle">'.$value.': </th><td valign="middle" align="left">'.$text."</td></tr>";
        } else {
            $out .= '<tr style="'.$edge.': 1px solid #d3d3d3;"><th align="right" valign="middle">'.$value.': </th><td valign="middle" align="left">'.$val."</td></tr>";
        }
        return $out;
    }
    
    function noteFormat($val, $highlightedDoc) {
        $out = '';
        if (count($highlightedDoc) == 0) {
            $out .= '<tr style="border: solid 0; border-top-width:1px;"><td style="padding:1ex" colspan=3 align="justify"><b>NOTE: </b>'.$val."</td></tr>";    
        } else {
            foreach ($highlightedDoc as $field => $highlight) {
               // FIXME PER METTERE I ... ANCHE QUANDO HO 1 RISULTATO SOLO
               $text = "<b><i>NOTE: </i></b>".combineHighlight($highlight, $val); //implode(' (...) ', $highlight);
               $out .= '<tr style="border: solid 0; border-top-width:1px;"><td style="padding:1ex" colspan=3 align="justify">'.$text."</td></tr>";    
            }
        }
        return $out;
    }

    function keywordsFormat($value, $val, $highlightedDoc) {
        $out = "";
        if (count($highlightedDoc) == 0) {
             $out .= '<tr><th align="right" valign="middle">'.$value.':</th><td valign="middle" align="left">'.$val."</td><td></td></tr>";    
        } else {
             foreach ($highlightedDoc as $field => $highlight) {
                 // FIXME PER METTERE I ... ANCHE QUANDO HO 1 RISULTATO SOLO
                 $text = "<b>NOTE: </b>".combineHighlight($highlight, $val); //implode(' (...) ', $highlight);
                 $out .= '<tr><th align="right" valign="middle">'.$value.':</th><td colspan="2" valign="middle" align="left">'.$text."</td></tr>";    
             }
        }
        return $out;
    }
        
    function codiceArchivioFormat($value, $val, $label) {
        $path = $GLOBALS[$label].$val.".JPG";
        $path_no_image = 'this.src="../img/no_image.png"';
        $out = '<tr><th align="right" valign="middle">'.$value.':</th><td valign="middle" align="left">'.$val."</td>";   
        $out .= '<td width=200px rowspan="15" valign="top" align="center"><img style="padding: 15px 5px 10px 20px;" heigth=150px src="'.$path."\" onerror='".$path_no_image."'></td></tr>";
        return $out;
    }

    function codiceArchivioFormatEdoc($value, $val, $resourceName, $label) {
        $tmp = explode(".", $resourceName);
        $ext = strtolower(end($tmp));
        $path = "/thumb/".$val.".JPG"; //$GLOBALS[$label].$val.".JPG";
        $path_no_image = 'this.src="../img/no_image.png"';
        $out = '<tr><th align="right" valign="middle">'.$value.':</th><td valign="middle" align="left">'.$val."</td>";   
        $out .= '<td width=200px rowspan="15" valign="top" align="center"><a href="'.'/edoc/'.$val.'.'.strtoupper($ext).'"><img style="padding: 15px 5px 10px 20px;" heigth=150px src="'.$path."\" onerror='".$path_no_image."'></a></td></tr>";
        return $out;
    }

    function codiceArchivioFormatSon($value, $val, $label) {
        $path = "/thumb/".$val.".JPG"; //$GLOBALS[$label].$val.".JPG";
        $path_no_image = 'this.src="../img/no_image.png"';
        $out = '<tr><th align="right" valign="middle">'.$value.':</th><td valign="middle" align="left">'.$val."</td>";   
        $out .= '<td width=200px rowspan="15" valign="top" align="center"><a href="'.'/edoc/'.$val.'.PDF "><img style="padding: 15px 5px 10px 20px;" heigth=150px src="'.$path."\" onerror='".$path_no_image."'></a></td></tr>";
        return $out;
    }

    function codiceArchivioFormatBoz($value, $val, $label, $resourceName) {
        $path = "/thumb/".$val.".JPG"; //$GLOBALS[$label].$val.".JPG";
        $path_no_image = 'this.src="../img/no_image.png"';
        $out = '<tr><th align="right" valign="middle">'.$value.':</th><td valign="middle" align="left">'.$val."</td>";   
        $out .= '<td width=200px rowspan="15" valign="top" align="center"><a href="'.'/edoc/'.$resourceName.'"><img style="padding: 15px 5px 10px 20px;" heigth=150px src="'.$path."\" onerror='".$path_no_image."'></a></td></tr>";
        return $out;
    }

    function tipologiaEdoc($value, $val, $type) {
        if (strpos($type, "word", 0)) {
            $icon = "icons/doc.png";
        } else {
            $icon = "icons/pdf.png";
        }
        $out = '<tr><th align="right" valign="middle">'.$value.':</th><td valign="middle" align="left">'.$val.'<img src="'.$icon.'" height=30></td>';   
        return $out;
    }
?>
 
