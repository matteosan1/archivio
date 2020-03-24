4<?php
require_once "../view/session.php";
require_once "../class/solr_curl.php";
require_once "../class/Member.php";

$m = new Member();
$categories = $m->getAllCategories();

$selects = "";
$json = listCodiceArchivio();
foreach ($json['response']['docs'] as $select) {
        $selects = $selects.'<option value="'.$select['codice_archivio'].'">'.$select['codice_archivio'].'</option>';
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <meta charset="UTF-8" />
        <title>Aggiornamento Documenti</title>
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
    $(".upd_volume").change(function() {
	var sel = document.getElementById("volume").value;
	request = $.ajax({
                url: "../class/solr_curl.php",
                type: "POST",
                data: {'sel':sel, 'func':'find2'},
        });

	request.done(function (response){
	    var dict = JSON.parse(response);
	    if (dict['type_group'] == 1) {
	        $("#frame").load("/view/update_frame_book.php");
	    	for (var key in dict['doc']) {
	            if (key == '_version_' || key == 'timestamp') {
		    continue;
		    }
          	    document.getElementById(key).value = dict[key];
	    	}
	    } else if (dict['type_group'] == 2) {
	        $("#frame").load("/view/update_frame_ebook.php");
	    	for (var key in dict['doc']) {
	            if (key == '_version_' || key == 'timestamp') {
		    continue;
		    }
          	    document.getElementById(key).value = dict[key];
	    	}
	    }
        });
	return true;
    });

    $('.update_book').click(function() {
	var formData = new FormData(document.getElementById("upd_book"));
        
        if (request) {
            request.abort();
        }

        request = $.ajax({
                url: "../class/validate_new_book.php",
                type: "post",
                data: formData,
                contentType: false,
                cache: false,
                processData:false                       
        });

        request.done(function (response){
                //if (response.hasOwnProperty('error')){
		//    alert (response['error']);
                //} else {
                //    window.location.href = "../view/dashboard.php";
		//    return true;
                /}
        });

        request.fail(function (response){			    
                console.log(
                    "The following error occurred: " + response
                );
        });
	return false;
    });

   $('.btn-insert-ebook').click(function() {
	var formData = new FormData(document.getElementById("new_ebook"));
        if (request) {
            request.abort();
        }

	var filename = document.getElementById('edoc').value;
        if (filename == "") {
	   alert ("Il documento da analizzare deve essere specificato.");
	   return false;
	} else {
	  var parts = filename.split('.');
 	  var ext = parts[parts.length - 1].toLowerCase();

	  if (ext != 'jpg' && ext != 'jpeg' &&
	      ext != 'tiff' && ext != 'tif' &&
	      ext != 'png' && ext != 'doc' &&
      	      ext != 'docx' && ext != 'eml' &&
      	      ext != 'pdf') {
		 alert ("Non è possibile inserire documento in formato " + ext);
  	  	 return false;
	  } else {
		 request = $.ajax({
                 	 url: "../class/validate_new_ebook.php",
                	 type: "post",
                	 data: formData,
                	 contentType: false,
               		 cache: false,
                	 processData:false                       
        	});

        	request.done(function(response) {
		    console.log(response);
                    //$('#exit_status').html(response);
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
    <br>	 
    <form class="upd_volume" name="upd_volume" id="upd_volume" action method="post">
    	  <label for="cars">Scegli documento da modificare:</label>
    	  <select id="volume" name="volume">
	       <option>----</option>
	       <?php echo $selects;  ?>
  	  </select>
    </form>
    <br>
    <div align="center" id="frame"></div>
    <br>
    <div id="footer" align="center"></div>
    </body>
</html>
