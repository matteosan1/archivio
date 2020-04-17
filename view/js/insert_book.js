var request;
$(document).ready(function() {
    $('.btn-insert-book').click(function() {
	var formData = new FormData(document.getElementById("new_book"));
        if (request) {
            request.abort();
        }
	
        if (document.getElementById('codice_archivio').value == "") {
	    alert ("Il codice_archivio deve essere specificato.");
	    return false;
	}
	
        if (document.getElementById('tipologia').value == "----") {
	    alert ("La tipologia deve essere specificata.");
	    return false;
	}
	
        if (document.getElementById('titolo').value == "") {
	    alert ("Volume senza titolo ? uhm...");
	    return false;
	}
	
        request = $.ajax({
            url: "../class/validate_new_book.php",
            type: "post",
            data: formData,
            contentType: false,
            cache: false,
            processData:false                       
        });
	
	request.done(function (response) {
	    var dict = JSON.parse(response);
            if(dict.hasOwnProperty('error')){
    		$('#error1').html(dict['error']);
		return false;
            } else {
		$('#result1').html(dict['result']);
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
    
    $('.btn-delete-book').click(function() {
    	var r = confirm("Sicuro di voler rimuovere i volumi selezionati ?");
        if (r == true) {
    	    var formData = new FormData(document.getElementById("delete_book"));
            
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
    		    $('#error3').html(response['error']);
		    return false;
                } else {
		    $('#result3').html(response['result']);
		    setTimeout(function(){
           	   	location.reload();
      		    }, 1000); 
		    
                }
            });
	    
            request.fail(function (response){			    
                console.log(
                    "The following error occurred: " + response
                );
            });
	}
	return false;
    });
    
    $(".sel_volume").change(function() {
	var sel = document.getElementById("volume").value;
	request = $.ajax({
            url: "../class/solr_curl.php",
            type: "POST",
            data: {'sel':sel, 'func':'find'},
        });
	
	request.done(function (response){
	    console.log(response);
	    var dict = JSON.parse(response);
	    for (var key in dict) {
	    	console.log(key);
	        if (key == '_version_' || key == 'timestamp') {
		    continue;
		}
          	document.getElementById(key + "_upd").value = dict[key];
	    }
	    document.getElementById("thumbnail").src = "/upload/" + dict['codice_archivio'] + ".JPG";
	    console.debug(document.getElementById("thumbnail").src);
        });
	return true;
	
    });
    
    $('.btn-update-book').click(function() {
	var formData = new FormData(document.getElementById("upd_book"));
        
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
		$('#error2').html(response['error']);
		return false;
            } else {
	        $('#result2').html("Il volumen &egrave; stato aggiornato in " + response['responseHeader']['QTime'] + " ms");
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
