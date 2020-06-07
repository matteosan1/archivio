var request;
$(document).ready(function() {
    $('.btn-insert-book').click(function() {
	var formData = new FormData(document.getElementById("new_book"));
        if (request) {
            request.abort();
        }
	
        if (document.getElementById('titolo').value == "") {
	    alert ("Volume senza titolo ? uhm...");
	    return false;
	}

        if (document.getElementById('anno').value == "") {
	    alert ("Devi specificare l'anno di pubblicazione per definire il codice_archivio.");
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
		$('#error1').html("");
		$('#result1').html(dict['result']);
		setTimeout(function(){
           	    location.reload();
      		}, 2500);
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
		    $('#error3').html("");
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
	    var dict = JSON.parse(response);
	    for (var key in dict) {
	        if (key == '_version_' || key == 'timestamp' || key == '_id') {
		    continue;
		}
          	document.getElementById(key + "_upd").value = dict[key];
	    }
	    document.getElementById("thumbnail").src = "/copertine/" + dict['codice_archivio'] + ".JPG";
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
		$('#error2').html("");
	        $('#result2').html("Il volume &egrave; stato aggiornato in " + response['responseHeader']['QTime'] + " ms");
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

    $("#lets_search_for_update").bind('submit',function() {
        var value = $('#str').val();
        $.post('/class/codice_archivio_selection.php',{value:value, type:"update"}, function(data){
	    var data_decoded = JSON.parse(data);
	    $('#volume').empty();
	    $.each(data_decoded, function(key, codice_archivio) {
		var option = new Option(codice_archivio, codice_archivio);
                $(option).html(codice_archivio);
                $("#volume").append(option);
            });
        });
        return false;
    });

    $("#lets_search_for_delete").bind('submit',function() {
        var value = $('#str_for_delete').val();
        $.post('/class/codice_archivio_selection.php',{value:value, type:"delete"}, function(data) {
            $("#search_results_for_delete").html(data);
        });
        return false;
    });
});
