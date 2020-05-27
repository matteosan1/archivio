var request;
$(document).ready(function() {
    $(".btn-insert-imge").click(function() {
	var filename = document.getElementById('userfile').value;
        if (filename == "") {
            alert ("Devi specificare una o piu` foto da caricare...");
            return false;
        }

        var tagl1 = document.getElementById('tagl1').value;
	var tagl2 = document.getElementById('tagl2').value;
	if (tagl1 == "----" || tagl2 == "----") {
	    alert ("Devi specificare i due tag !!!");
	    return false;
        }
	
        var formData = new FormData(document.getElementById("new_image"));
        request = $.ajax({
	    url: "../class/process_image.php",
	    type: 'POST',
	    data: formData, 
	    contentType: false,
	    cache: false,
	    processData:false
	});
	
	request.done(function (response) {
	    console.debug(response);
	    response = JSON.parse(response);
            if(response.hasOwnProperty('error')){
    		$('#error1').html(response['error']);
		return false;
            } else {
		$('#error1').html("");
		$('#result1').html(response['result']);
		setTimeout(function(){
           	    location.reload();
      		}, 2000);
            }
        });
	
        //var oForm = document.getElementById('new_image');
	//oForm.elements["tagl2"].value = 0;
	//document.getElementById('new_image').reset();
	return false;
    });
    
    $(".tagl1").change(function() {
        var id = $(this).val();
        var dataString = 'id=' + id;
        $.ajax({
            type: 'post',
            url: "../class/tags.php",
            data: dataString,
            cache: false,
            success: function(html) {
                $(".tagl2").html(html);
            }
        });
    });
    
    $('.btn-delete-images').click(function() {
    	var formData = new FormData(document.getElementById("delete_image"));
        
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
    
    $(".sel_image").change(function() {
	var sel = document.getElementById("immagine").value;
	request = $.ajax({
            url: "../class/solr_curl.php",
            type: "POST",
            data: {'sel':sel, 'func':'find'},
        });
	
	request.done(function (response){
	    //console.debug(response);
	    var dict = JSON.parse(response);
	    for (var key in dict) {
	        if (key == 'By-line') {
		    document.getElementById(key).value = dict[key];
		} else if (key == "Keywords") {
		    keywords = dict[key].trim().split(" ");
		    var tagl1 = keywords[0];
		    var tagl2 = keywords[1];
   		    document.getElementById("Keywords").value = keywords.slice(2).join(",");	
		}
	    }
	    // FIXME
	    //document.getElementById("thumbnail").src = "<?php echo $GLOBALS['THUMBNAILS_DIR']?>" + dict['codice_archivio']+ ".JPG";
	    document.getElementById("thumbnail").src = "/thumbnails/" + dict['codice_archivio']+ ".JPG";
	    console.debug(dict["codice_archivio"]);
    	    document.getElementById("codice_archivio").value = dict["codice_archivio"];    
	    document.getElementById("upd_tagl1").value = tagl1;
	    document.getElementById("upd_tagl2").value = tagl2;
        });
	return true;
	
    });
    
    $('.btn-update-image').click(function() {
        var tags = document.getElementById("Keywords").value;
	var tagl1 = document.getElementById("upd_tagl1").value;
	var tagl2 = document.getElementById("upd_tagl2").value;
	tags = tags.trim().split(",");
        document.getElementById("Keywords").value = tagl1 + " " + tagl2 + " " + tags.join(" ");	
	var formData = new FormData(document.getElementById("upd_image"));
        
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
	
	// FIXME sistemare riscrittura tutte keywords allupdate
        request.done(function (response){
  	    var dict = JSON.parse(response);
            if(dict.hasOwnProperty('error')){
		$('#error2').html(dict['error']['msg']);
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
    
    $(".tagl1").change(function() {
        var id = $(this).val();
        var dataString = 'id=' + id;
        $.ajax({
            type: 'post',
            url: "../class/tags.php",
            data: dataString,
            cache: false,
            success: function(html) {
                $(".tagl2").html(html);
            }
        });
    });
});
