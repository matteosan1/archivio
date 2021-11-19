var request;

function openPage(pageName, elmnt) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].style.backgroundColor = "";
    }
    document.getElementById(pageName).style.display = "block";
}

$(document).ready(function() {
    setInterval(checkForWarning, 1000 * 60);
    $('html, body').animate({
        //scrollTop: $('#scroll').offset().top
        scrollTop: $('#scroll').scrollTop(0)
    }, 'slow');
    
    $("#tipologia").change(function() {
        var tipo = document.getElementById("tipologia").value;

        request = $.ajax({
            url: "../class/solr_utilities.php",
            type: "POST",
            data: {'type':tipo, 'callback':'newitem'},
        });
        
        request.done(function (response) {
            //console.debug(response);
            var data = JSON.parse(response);
            //console.debug(data);
            //var testData = !!document.getElementById("insert_form");
            //console.debug(testData);
            //var elements = document.getElementById("insert_form").elements;

            //for (var i = 0, element; element = elements[i++];) {
            //    element.remove();
            //}
            $('#insert_form').replaceWith('<form class="insert_form" id="insert_form"></form>');
            $("#insert_form").dform(data);
            return false;
        });
    });

    //$(document).on('change','.date', function(){
    //    $("#date").datepicker();
    //});
    
    $(document).on('click','.inserisci', function(){
	    var formData = new FormData(document.getElementById("insert_form"));
	    if (request) {
            request.abort();
        }

        var filenames = document.getElementById('scan[]').files;
        if (filenames.length == 0) {
	        alert ("Almeno un documento deve essere selezionato.");
	        return false;
        }

        var is_ocr = !!document.getElementById('testo_ocr');        
        if (is_ocr) {
            var ocr = document.getElementById('testo_ocr').value;
            if (ocr == "") {
                if (! confirm('Vuoi proseguire senza OCR ?')) {
                    return false;
                } 
            }
        }
        
        formData.append('tipologia', document.getElementById('tipologia').value);
        
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
	    
	    return false;
    });

    $(document).on('click','.OCR', function(){
	    var filenames = document.getElementById('scan[]').files;
        //console.debug(filenames);
        if (filenames.length == 0) {
	        alert ("Il documento da analizzare deve essere specificato.");
	        return false;
	    } else {
            for (var i = 0; i < filenames.length; i++) {                
	            var parts = filenames[i]['name'].split('.');
 	            var ext = parts[parts.length - 1].toLowerCase();
                
	            // FIXME AGGIUNGERE pdf2image per processare pdf
	            if (ext != 'jpg' && ext != 'jpeg' &&
		            ext != 'tiff' && ext != 'tif' &&
                    ext != 'png') {
		            alert ("Non è possibile effetturare OCR su file " + ext);
  	  	            return false;
                }
            }
	    }

        if (request) {
            request.abort();
        }

  	    var formData = new FormData(document.getElementById("insert_form"));
		request = $.ajax({
            url: "../class/check_ocr.php",
            type: "post",
            data: formData,
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){$("#overlay").show();}
		});
		
		request.done(function (response) {
            setInterval(function() {$("#overlay").hide(); }, 500);
	        $('#testo_ocr').html(response);
		    return false;
		});

	    return false;
    });
    
    $("#lets_search_for_delete").bind('submit',function() {
        var value = $('#str_for_delete').val();
        $.post('/class/codice_archivio_selection.php',{value:value, type:"delete", category:"ebook_categories"}, function(data) {
            $("#search_results_for_delete").html(data);
        });
        return false;
    });

    $('#submit_delete').click(function() {
        var r = confirm("Sicuro di voler rimuovere i volumi selezionati ?");
        if (r == true) {
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
        }
	    return false;
    });


    function addOption(selectbox, text, value) {
        var optn = document.createElement("OPTION");
        optn.text = text;
        optn.value = value;
        selectbox.options.add(optn);
    }
    
    $("#lets_search_for_update").bind('submit',function() {
        var value = $('#str').val();
        $.post('/class/codice_archivio_selection.php',{category:"ebook_categories", value:value, type:"update"}, function(data) {
            var data_decoded = JSON.parse(data);
            $('#sel_edoc').empty();
            $.each(data_decoded, function(key, codice_archivio) {
                addOption(document.form_sel_edoc.sel_edoc, codice_archivio, codice_archivio);
            });
        });
        return false;
    });
    
    $("#form_sel_edoc").change(function() {
        var sel = document.getElementById("sel_edoc").value;

	    request = $.ajax({
            url: "../class/solr_utilities.php",
            type: "POST",
            data: {'codice_archivio':sel, 'callback':'finditem'},
        });
	    
	    request.done(function (response) {
            //console.debug(response);
	        var data = JSON.parse(response);
            //if (dict.hasOwnProperty('error')) {
            //    $('#error2').html(dict['error']);
		    //    return false;
            //} else {
            $("#update_form").dform(data);
            //}
            return false;
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
	            $('#result2').html(dict['result']);
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
