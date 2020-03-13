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
			    $("#header").load("/view/header.html"); 
	    		    //$("#footer").load("/view/footer.html"); 
			});
    		   </script>
        </head>
<script type="text/javascript">
var request;

$(document).ready(function() {
    $("#registerBtn").click(function(){
        var formData = new FormData(document.getElementById("registration"));
	
	if (request) {
	    request.abort();
	}

	request = $.ajax({
	        url: "../class/validate_new_user.php",
		type: "post",
		data: formData,
		contentType: false,
		cache: false,
		processData:false			
	});

        request.done(function (response){
		if(response.hasOwnProperty('error')){
		    $('#registered').html(response['error']);
	       	} else {
		    $('#registered').html("USER INSERTED");
		    window.location.href = "management.php";
		}
	});

        request.fail(function (response){
	        console.log(
	            "The following error occurred: " + response
	        );
	});

	request.always(function () {
	        //$inputs.prop("disabled", false);
	});
   });
});
</script>
	<body>
	<div id="header" align="center"></div>
	<br>
	<div id="registered"></div>
	<div align=center>
		<form class="register" name="new user registration" id="registration" action method="POST">
		<table>
		<tr><td>Nome</td><td><input type="text" name="name" value="" /></td></tr>
		<tr><td>Username </td><td><input type="text" name="username" value="" /></td></tr>
		<tr><td>Password </td><td><input type="password" name="new-password1" value="" /></td></tr>
		<tr><td>Password (ripetere) </td><td><input type="password" name="new-password2" value="" /></td></tr>
		<tr><td>Ruolo</td><td><select name="role"><option>photo</option><option>archive</option><option>admin</option></select></td></tr>
		<tr><td>e-mail</td><td><input type="text" name="email" value="" placeholder="opzionale"/></td></tr>
		</table>
		<button type="button" id="registerBtn" class="btn btn-success"> Registra </button>
		</form>
	</div>
	</body>
</html>