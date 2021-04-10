<?php
    require_once "../view/config.php";
    require_once "../class/Member.php";
    require_once "../class/solr_utilities.php";
 
    $selection = "*".$_POST['value']."*";
    $category = $_POST['category'];
    $cas = listCodiceArchivio($category, $selection);

    $size = count($cas);
    if ($size > 14) {
        $size = 15;
    }

    if ($_POST['type'] == "update") {
        $tmp = array();
        $tmp[] = "     ";
        foreach($cas as $select) {
            $tmp[] = $select;
        }

        print_r (json_encode($tmp));
    } else {
        echo '<select width=100px id="codici[]" name="codici[]" size="'.$size.'" multiple>';
        foreach ($cas as $select) {
            echo '<option value="'.$select.'">'.$select.'</option>';
        }  
        echo '</select><br><br>';
    }
?>