<?php
require_once ("Member.php");

$m = new Member();
$res = "";

if (isset($_POST)) {
    if (isset($_POST['tagl2'])) {
      	foreach ($_POST as $key => $val) {
            if ($val != "") {
                $res = $m->addCategory("tagl2", $val, $key);
                if ($res != 1) {
                    print_r ($res);
                    exit;
                }
            }
        }
    } else {
        $cat = $_POST['name'];
        
        if (isset($_POST['ebook'])) {
            $res = $m->addCategory("ebook", $cat);
        } elseif (isset($_POST['book'])) {
            $res = $m->addCategory("book", $cat);
        } elseif (isset($_POST['tag'])) {
            $res = $m->addCategory("tagl1", $cat);
        }
    }
    
    print_r ($res);
}
?>
