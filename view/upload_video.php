<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <meta charset="UTF-8" />
        <title>Inserimento Video</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">
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
   $('.btn-insert-video').click(function() {
	var formData = new FormData(document.getElementById("new_video"));

	if (request) {
            request.abort();
        }

	var filename = document.getElementById('videos').value;
        if (filename == "") {
	   alert ("Il file da indicizzare devono essere specificati.");
	   return false;
	} else {
 	   request = $.ajax({
               	 url: "../class/process_video.php",
               	 type: "post",
               	 data: formData,
               	 contentType: false,
           	 cache: false,
               	 processData:false                       
        	});

           request.done(function(response) {
	   	console.log(response);
                response = JSON.parse(response);
                if(response.hasOwnProperty('error')) {
		    alert (response['error']);
		    return false
                } else {
                    window.location.href = "../view/dashboard.php";
                   return true;
                }
           });
      	  }
      }
      
      return false;
   });
});
</script>
    <body>
    <?php include "../view/header.php"; ?>
    <div id="header" align="center"></div>
    <br>	 
    <!-- <div align=center id=exit_status style="color:red"><?php echo $exit_status;?></div>
    <span class="error" style="color:red"><?php echo $error;?></span>
     <div align=center id=exit_status style="color:red"></div>-->
 <br>
 <div align="center">
 <form class="new_ebook" name="new_video" id="new_video" action method="POST">
 <table style="width:80%">
    <tr>
       <div class="col-1">
    	 <td>
    	 <label for="fname" class="fname">File Video da caricare<br>:</label>
	 </td><td>
	 <input name="videos[]" id="videos" type="file" value="" multiple accept=".mpg,.mpeg,.ogg,.mp4,.avi,.wmv,.mov,.flv,.mtk"><br><br>
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
    <button id="import" class="btn-info btn-insert-video">Inserisci</button>
    </div>
    </form>

   </body>
   <br>
   <div id="footer" align="center"></div>
</html>
