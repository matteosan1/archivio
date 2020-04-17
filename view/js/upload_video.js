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
		   $('#result1').html("");
		   $('#error1').html(response['error']);
		   return false;
                } else {
		$('#error1').html("");
		$('#result1').html(response['result']);
		setTimeout(function(){
	           location.reload();}, 2000);
                }
           });
        }
      return false;
   });

   $('.delete_videos').click(function() {
    	var formData = new FormData(document.getElementById("delete_video"));
        
        if (request) {
            request.abort();
        }

	request = $.ajax({
                url: "../class/remove.php",
                type: "post",
                data: formData,
                contentType: false,
                cache: false,
                processData:false                       
        });

        request.done(function (response) {
	        response = JSON.parse(response);
                if(response.hasOwnProperty('error')){
		    $('#result3').html("");
    		    $('#error3').html(response['error']);
		    return false;
                } else {
		    $('#error3').html("");
		    $('#result3').html(response['result']);
		    setTimeout(function(){
           	       location.reload();
      		       }, 2000); 
                }
        });

        request.fail(function (response){			    
                console.log(
                    "The following error occurred: " + response
                );
        });
	return false;
    });

    $(".sel_video").change(function() {
	var sel = document.getElementById("video").value;
	request = $.ajax({
                url: "../class/solr_curl.php",
                type: "POST",
                data: {'sel':sel, 'func':'find'},
        });

	request.done(function (response){
			      console.log(response);
	    var dict = JSON.parse(response);
	    for (var key in dict) {
	        if (key == '_version_' || key == 'timestamp') {
		   continue;
		}
          	document.getElementById(key).value = dict[key];
	    }
	    document.getElementById("codice_archivio2").value = dict["codice_archivio"];
        });
	return true;

    });
    
    $('.btn-update-video').click(function() {
	var formData = new FormData(document.getElementById("upd_video"));
        
        if (request) {
            request.abort();
        }

        request = $.ajax({
                url: "../class/validate_new_item.php",
                type: "post",
                data: formData,
                contentType: false,
                cache: false,
                processData:false                       
        });

        request.done(function (response){
      	    response = JSON.parse(response);
            if(response.hasOwnProperty('error')){
	        $('#result2').html("");
		$('#error2').html(response['error']);
		return false;
            } else {
	        $('#error2').html("");
	        $('#result2').html("Il video &egrave; stato aggiornato in " + response['responseHeader']['QTime'] + " ms");
		setTimeout(function(){
           	    location.reload();
      		    }, 2000); 		
            }
        });

        request.fail(function (response){                           
                console.log(
                    "The following error occurred: " + response
                );
        });
        return false;
    });
});
