<?php
require_once "session.php";
if (isset($_GET['type'])) {
   $type = $_GET['type'];
}

// FIXME ERRORE !!!
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
		if (response != 1) {
		    $('#registered').html(response);
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
  $("#footer").load("/view/footer.html"); 
});
</script>
        </head>
	<body>
		<?php include "../view/header.php" ?>
		<br>
                <div id=registered style="color:red"></div>
		<div align="center">
		<form class="register" name="new category" id="category" action method="POST">
		<?php
		if ($type == 'ebook') {
		   echo '<input type="hidden" id="ebook" name="ebook" value="">';
		} elseif ($type == 'book')  {
		   echo '<input type="hidden" id="book" name="book" value="">';
		} elseif ($type == 'tagl1') {
		   echo '<input type="hidden" id="tagl1" name="tag" value="">';
		} 
		?>
		<table>
		<tr><td>
		<?php
		if ($type == 'ebook') {
		   echo "Categoria eDoc:  ";
		} elseif ($type == 'book')  {
		   echo "Categoria Libri:  ";
		} elseif ($type == 'tagl1') {
		   echo "TAG L1:";
		}  elseif ($type == 'tagl2') {
		   echo "TAG L2:";
		}
		?></td><td><input type="text" name="name" value="" /></td></tr>
		</table>
		<br>
		<button type="button" id="registerBtn" class="btn btn-success"><img src="/view/icons/plus.png"> Aggiungi </button>
		</form>
		</div>
		<br>
	<div id="footer" align="center"></div>
	</body>
</html>
