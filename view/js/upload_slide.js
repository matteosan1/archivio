var request;
$(document).ready(function() {
    setInterval(checkForWarning, 1000 * 60)
    
    $(".btn-insert-slide").click(function() {
	var filename = document.getElementById('userfile').value;
        if (filename == "") {
            alert ("Devi specificare una stampa da caricare...");
            return false;
        }

        var tagl1 = document.getElementById('tagl1').value;
	var tagl2 = document.getElementById('tagl2').value;
	if (tagl1 == "----" || tagl2 == "----") {
	    alert ("Devi specificare i due tag !!!");
	    return false;
        }
	
        var formData = new FormData(document.getElementById("new_slide"));
        request = $.ajax({
	    url: "../class/process_slide.php",
	    type: 'POST',
	    data: formData, 
	    contentType: false,
	    cache: false,
	    processData:false
	});
	
	request.done(function (response) {
	    //console.debug(response);
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
    
    $('.btn-delete-slides').click(function() {
    	var formData = new FormData(document.getElementById("delete_slide"));
        
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
            //console.debug(response);
            //return false;
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
    
    $(".sel_slide").change(function() {
	    var sel = document.getElementById("slide").value;
	    request = $.ajax({
            url: "../class/solr_utilities.php",
            type: "POST",
            data: {'sel':sel, 'func':'find'},
        });
	    
	    request.done(function (response) {
            //console.debug(response);
            var dict = JSON.parse(response);
            //console.debug(dict);
	        for (var key in dict) {
                console.debug(key);
		        if (key == "Keywords") {
		            keywords = dict[key].trim().split(" ");
		            var tagl1 = keywords[0];
		            var tagl2 = keywords[1];
		        }
	        }
	        // FIXME
	        //document.getElementById("thumbnail").src = "<?php echo $GLOBALS['THUMBNAILS_DIR']?>" + dict['codice_archivio']+ ".JPG";
	        document.getElementById("thumbnail").src = "/thumbnails/" + dict['codice_archivio']+ ".JPG";
    	    document.getElementById("codice_archivio").value = dict["codice_archivio"];
            document.getElementById('By-line').value = dict['autore'];
            document.getElementById("upd_dimensione").value = dict["dimensione"];
            document.getElementById("upd_anno").value = dict["anno"];
            document.getElementById("upd_note").value = dict["note"];    
	        document.getElementById("upd_tagl1").value = tagl1;
	        document.getElementById("upd_tagl2").value = tagl2;
        });
	    return true;
	    
    });
    
    $('.btn-update-slide').click(function() {
	    var tagl1 = document.getElementById("upd_tagl1").value;
	    var tagl2 = document.getElementById("upd_tagl2").value;
	    tags = tags.trim().split(",");
        document.getElementById("Keywords").value = tagl1 + " " + tagl2 + " " + tags.join(" ");	
	    var formData = new FormData(document.getElementById("upd_slide"));
        
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
	
	    // FIXME sistemare riscrittura tutte keywords all update
        request.done(function (response){
  	        var dict = JSON.parse(response);
            if(dict.hasOwnProperty('error')) {
		        $('#error2').html(dict['error']['msg']);
		        return false;
            } else {
 	            $('#error2').html("");
	            $('#result2').html("La stampa &egrave; stato aggiornata in " + dict['responseHeader']['QTime'] + " ms");
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
