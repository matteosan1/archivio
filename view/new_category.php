<?php
require_once "session.php";
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
var request;

$(document).ready(function() {
    $("#registerBtn").click(function(){
        var formData = new FormData(document.getElementById("category"));
	
	if (request) {
	    request.abort();
	}

	request = $.ajax({
	        url: "../class/validate_new_category.php",
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
		    $('#registered').html("CATEGORY INSERTED");
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

<!DOCTYPE html>
<html>
	<head>
		<title>Definizione nuova categoria</title>
		<meta charset="utf-8"/>
		<link rel="stylesheet" type="text/css" href="style.css"></link>
	        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
		<script>
$(function(){
  $("#header").load("/view/header.html"); 
  //$("#footer").load("/view/footer.html"); 
});
</script>
        </head>
	<body>
		<div id="header" align="center"></div>
		<br>
                <div id=registered></div>
		<form class="register" name="new category" id="category" action method="POST">
		<table>
		<tr><td>Categoria</td><td><input type="text" name="name" value="" /></td></tr>
		</table>
		<button type="button" id="registerBtn" class="btn btn-success"> Registra </button>
		</form>
	</body>
</html>
