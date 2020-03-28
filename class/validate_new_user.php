<?php
if (isset($_POST)) {
   if ($_POST['new-password1'] == $_POST['new-password2']) {
      $password = $_POST['new-password1'];
      
      $uppercase = preg_match('@[A-Z]@', $password);
      $lowercase = preg_match('@[a-z]@', $password);
      $number    = preg_match('@[0-9]@', $password);
      $specialChars = preg_match('@[^\w]@', $password);

      if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
          echo json_encode(array("error" => 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.'), true);
	  exit;
      } 

      require_once ("Member.php");
      $m = new Member();
      $res = $m->addUser($_POST["name"], $_POST["username"], $_POST["new-password1"], $_POST["role"], $_POST["email"]);
      //print ("validate".$res);
      //header("Location: ../view/management.php");
      exit();
    } else {
      header('Content-Type: application/json');
      echo json_encode(array('error' => "Le due password non coincidono..."));
      exit;
    }
}
?>
