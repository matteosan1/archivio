var request;
$(document).ready(function() {
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
		ext != 'doc' && ext != 'msg' &&
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
		
       		request.done(function (response) {
	            console.log(response);
	            var dict = JSON.parse(response);
                    if(dict.hasOwnProperty('error')){
    			$('#error1').html(dict['error']);
			return false;
                    } else {
			$('#error1').html("");
			$('#result1').html(dict['result']);
			setTimeout(function(){
           		    location.reload();
      			}, 2000);
                    }
		});
      	    }
	}
	
	return false;
    });
    
    $('.btn_ocr').click(function() {
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
	    // FIXME AGGIUNGERE pdf2image per processare pdf
	    if (ext != 'jpg' && ext != 'jpeg' &&
		ext != 'tiff' && ext != 'tif') {
		alert ("Non è possibile fare analisi OCR con file " + ext);
  	  	return false;
	    } else {
		request = $.ajax({
               	    url: "../class/check_ocr.php",
               	    type: "post",
               	    data: formData,
               	    contentType: false,
                    cache: false,
                    processData:false                       
		});
		
		request.done(function (response) {				
	            $('#testo_ocr').html(response);
		    return false;
		});
      	    }
	}
	return false;
    });
    
    $('.btn-delete-ebook').click(function() {
    	var formData = new FormData(document.getElementById("delete_ebook"));
        
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
	return false;
    });
    
    $(".sel_ebook").change(function() {
	var sel = document.getElementById("volume").value;
	request = $.ajax({
            url: "../class/solr_curl.php",
            type: "POST",
            data: {'sel':sel, 'func':'find'},
        });
	
	request.done(function (response){
	    var dict = JSON.parse(response);
	    document.getElementById("codice_archivio_upd").value = dict["codice_archivio"];
	    document.getElementById("tipologia_upd").value = dict["tipologia"];
	    if (dict.hasOwnProperty("text")) {
	        document.getElementById("text_upd").value = dict['text'];
	    }
	    
	    if (dict.hasOwnProperty('note')) {
	        document.getElementById("note_upd").value = dict["note"];
	    }
	    
	    var ext = dict['resourceName'].split('.').pop().toLowerCase();
	    if (ext == "jpeg" || ext == "jpg" || ext == "tiff" || ext == "tif" || ext == "pdf") {
		document.getElementById("thumbnail").src = "/upload/" + dict['codice_archivio'] + ".JPG";
	    }
        });
	return true;
    });
    
    $('.btn-update-ebook').click(function() {
	var formData = new FormData(document.getElementById("upd_ebook"));
        
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
	
        request.done(function (response) {
	    console.log(response);
      	    var dict = JSON.parse(response);
            if(dict.hasOwnProperty('error')){
		$('#error2').html(dict['error']);
		return false;
            } else {
	        $('#error2').html("");
	        $('#result2').html("L'immagine &egrave; stato aggiornato in " + dict['responseHeader']['QTime'] + " ms");
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
