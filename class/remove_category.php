<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!


if (isset($_GET)) {
    if ($_GET['table'] == 'ebook') {
        $table = 'ebook_categories';
    } elseif ($_GET['table'] == 'book') {
        $table = 'book_categories';
    }
    $id = $_GET['delete_id'];
    
    require_once ("Member.php");
    $m = new Member();
    $res = $m->removeCategory($table, $id);
}
?>
