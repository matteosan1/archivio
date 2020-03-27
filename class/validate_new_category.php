<?php
require_once ("Member.php");

if (isset($_POST)) {
   $cat = $_POST['name'];
   
   $m = new Member();
   
   if (isset($_POST['ebook'])) {
      $res = $m->addCategory("ebook_categories", $cat);
   } elseif (isset($_POST['book'])) {
      $res = $m->addCategory("book_categories", $cat);
   } elseif (isset($_POST['tag'])) {
      $res = $m->addCategory("tags", $cat);
   }
}
?>
