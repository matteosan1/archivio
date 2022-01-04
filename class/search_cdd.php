<?php
exec("../class/search_cdd.py '".$_POST['author']."' '".$_POST['title']."'", $output, $status);

if ($status == 0) {
    print_r(json_encode(array('result'=>implode('<br>', $output))));
} else {
    print_r(json_encode(array('error'=>implode('<br>', $output))));
}
?>
