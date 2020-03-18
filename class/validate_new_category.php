<?php
if (isset($_POST)) {
   $cat = $_POST['name'];

   require_once ("Member.php");
   $m = new Member();
   if (isset($_POST['ebook'])) {
      $res = $m->addCategory("ebook_categories", $cat);
   } else {
      $res = $m->addCategory("book_categories", $cat);
   }     
}
?>
