<?php
if (isset($_POST)) {
      $cat = $_POST['name'];
      if(strpos($mystring, " ") !== false) {
          $cat = str_replace(" ","_", $cat);
      			
      } 

      require_once ("Member.php");
      $m = new Member();
      $res = $m->addCategory($cat);
      exit();
    } else {
      header('Content-Type: application/json');
      echo json_encode(array('error' => "Categoria non valida..."));
      exit;
    }
}
?>
