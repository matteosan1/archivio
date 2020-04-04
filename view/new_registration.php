<?php
require_once "../view/session.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Registrazione nuovo utente</title>
		<meta charset="utf-8"/>
		<link rel="stylesheet" type="text/css" href="style.css"></link>
	        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		    <script>
			$(function(){
	    		    $("#footer").load("/view/footer.html"); 
			});
    		   </script>
        </head>
<script type="text/javascript">
var request;

$(document).ready(function() {
    $(".btn-new-user").click(function(){
        var formData = new FormData(document.getElementById("registration"));
	
	if (request) {
	    request.abort();
	}

	if (document.getElementById("name").value == "") {
	   alert ("L'utente deve avere un nome.");
	   return false;
	}

	if (document.getElementById("username").value == "") {
	   alert ("L'utente deve avere un username.");
	   return false;
	}

	var pwd1 = document.getElementById("password1").value;
	var pwd2 = document.getElementById("password2").value;
	if (pwd1 == "") {
	   alert ("La password ?????.");
	   return false;
	}

	if (pwd2 == "") {
	   alert ("Devi ripetere la password per motivi di sicurezza.");
	   return false;
	}

	if (pwd1 != pwd2) {
	   alert ("Le due password non coincidono.");
	   return false;
	}

	var strongRegex = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");
	if (!strongRegex.test(pwd1)) {
	   alert('La password deve avere almeno 8 caratteri di cui almeno una lettera maiuscola, un numero ed un carattere speciale.');
	   return false;
	}
	
	request = $.ajax({
	        url: "../class/validate_new_user.php",
		type: "post",
		data: formData,
		contentType: false,
		cache: false,
		processData:false			
	});

        request.done(function (response) {
			      console.log(response);
            var dict = JSON.parse(response);
	    if(dict.hasOwnProperty('error')){
		$('#result').html("");
                $('#error').html(dict['error']);
                    return false;
            } else {
                $('#error').html("");
                $('#result').html(dict['result']);
                setTimeout(function(){
                    //location.reload();
		    window.location.href = "../view/management.php";
                    }, 2000);
            }
	});

        request.fail(function (response){
	        console.log(
	            "The following error occurred: " + response
	        );
	});
   });
});
</script>
	<body>
	<?php include "../view/header.php"; ?>
	<div align=center>
	<div id="result" style="color:green"></div>
	<div id="error" style="color:red"></div>
	<br>
		<form class="register" name="new user registration" id="registration" action method="POST">
		<table>
		<tr><td>Nome</td><td><input type="text" id="name" name="name" value="" /></td></tr>
		<tr><td>Username </td><td><input type="text" id="username" name="username" value="" /></td></tr>
		<tr><td>Password </td><td><input type="password" id="password1" name="new-password1" value="" /></td></tr>
		<tr><td>Password (ripetere) </td><td><input type="password" id="password2" name="new-password2" value="" /></td></tr>
		<tr><td>Ruolo</td><td>
		<select name="role">
		<option>photo</option>
		<option>archive</option>
		<option>admin</option>
		</select></td></tr>
		<tr><td>e-mail</td><td><input type="text" name="email" value="" placeholder="opzionale"/></td></tr>
		</table>
		<br>
		<button type="button" id="registerBtn" class="btn btn-success btn-new-user"> Registra </button>
		</form>
	</div>
	<br>
	<div id="footer" align="center"></div>	
	</body>
</html>
