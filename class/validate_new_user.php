<?php
require_once ("Member.php");

if (isset($_POST)) {
      $m = new Member();
      if ($m->getMemberByName($_POST['name']) or $m->getMemberByName($_POST['username'], TRUE)) {
          echo json_encode(array("error" => 'Non ci possono essere pi&ugrave; utenti con lo stesso nome o lo stesso username.'), true);
	  exit;
      }
      
      $res = $m->addUser($_POST["name"], $_POST["username"], $_POST["new-password1"], $_POST["role"], $_POST["email"]);
      echo json_encode(array('result' => "Registrazione avvenuta con successo."));
      exit;
}
?>
