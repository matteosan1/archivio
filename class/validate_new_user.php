<?php
require_once ("Member.php");

if (isset($_POST)) {
//   if ($_POST['new-password1'] == $_POST['new-password2']) {
//      $password = $_POST['new-password1'];
//      
//      $uppercase = preg_match('@[A-Z]@', $password);
//      $lowercase = preg_match('@[a-z]@', $password);
//      $number    = preg_match('@[0-9]@', $password);
//      $specialChars = preg_match('@[^\w]@', $password);
//
//      if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
//          echo json_encode(array("error" => 'La password deve avere almeno 8 caratteri di cui almeno una lettera maiuscola, un numero ed un carattere speciale.'), true);
//	  exit;
//      } 

      $m = new Member();
      if ($m->getMemberByName($_POST['name']) or $m->getMemberByName($_POST['username'], TRUE)) {
          echo json_encode(array("error" => 'Non ci possono essere pi&ugrave; utenti con lo stesso nome o lo stesso username.'), true);
	  exit;
      }
      
      $res = $m->addUser($_POST["name"], $_POST["username"], $_POST["new-password1"], $_POST["role"], $_POST["email"]);
      echo json_encode(array('result' => "Registrazione avvenuta con successo."));
      exit();
    } else {
      header('Content-Type: application/json');
      echo json_encode(array('error' => "Le due password non coincidono..."));
      exit;
    }
}
?>
