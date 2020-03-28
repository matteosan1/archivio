<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../class/Member.php";

$m = new Member();
$users = $m->getAllMembers();
$roles = $m->getAllRoles();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <meta charset="UTF-8" />
        <title>Lascia una nota</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
    crossorigin="anonymous">
</script>	
<script>
$(function(){
  $("#footer").load("/view/footer.html"); 
});
</script>
    </head>
<script type="text/javascript">
var request;
$(document).ready(function() {
    $('.insert_note').click(function() {
    	var formData = new FormData(document.getElementById("new_note"));
        
        if (request) {
            request.abort();
        }

	var rec1 = document.getElementById("recipient").value;
	var rec2 = document.getElementById("recipientg").value;

	if (rec1 == "----" && rec2 == "----") {
	   alert ("Devi selezionare un destinatario...");
	   return false;
	}

	if (rec1 != "----" && rec2 != "----") {
	   alert ("Devi scegliere o singolo destinatario e gruppo...");
	   return false;
	}

	request = $.ajax({
                url: "../class/insert_note.php",
                type: "post",
                data: formData,
                contentType: false,
                cache: false,
                processData:false                       
        });

        request.done(function (response){
	    $('#exit_status').html(response);
	    setTimeout(function () {
	    	window.location.href = "../view/dashboard.php";
                return true;
	    }, 1000);
        });

        request.fail(function (response){			    
            console.log(
                "The following error occurred: " + response
            );
        });
	return false;

    });
});
</script>
    <body>
    <?php include "header.php"; ?>
    
    <!-- <div align=center id=exit_status style="color:red"><?php echo $exit_status;?></div>
    <span class="error" style="color:red"><?php echo $error;?></span>-->
     <div align=center id=exit_status style="color:red"></div
 <br>
 <div align="center">
 <form class="new_note" name="new_note" id="new_note" action method="POST">
 <input type="hidden" name="sender" id="sender" value="<?php echo $displayName;?>">
  <table style="width80%" border=1px>
  <tr>
      <div class="col-1">
   	<td>
        <label for="fname" class="fname">Destinatario Singolo:</label>
	</td><td>
	<select name="recipient" class="recipient" id="recipient">
	<option selected="selected">----</option>
	<?php
	    foreach ($users as $user) {
	        echo '<option>'.$user['display_name'].'</option>';
	    }
	?>
	</select>
	</td>
      </div>
   </tr>
     <tr>
      <div class="col-1">
   	<td>
        <label for="fname" class="fname">Destinatario Gruppo:</label>
	</td><td>
	<select name="recipientg" class="recipientg" id="recipientg">
	<option selected="selected">----</option>
	<?php
	    foreach ($roles as $role) {
	        echo '<option>'.$role['name'].'</option>';
	    }
	?>
	</select>
	</td>
      </div>
   </tr>
   <tr>
    <div class="col-1">
      <td>
    	 <label for="fname" class="fname">Note:</label>
	 </td><td>
	 <textarea name="note" rows="10" cols="80" placeholder="note"></textarea>
      </td>
    </div>
   </tr>
</table>
<br>
<div class="btn" align="center">
   <button class="btn btn-sm btn-info insert_note" id="inserisci"><img src="/view/icons/send_note.png"></button>
</div>
</form>
</div>
</div>
<br>
<div id="footer" align="center"></div>
    </body>
</html>
