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
    if (pageName == 'Insert') {
	document.getElementById("tipologia").selectedIndex = 0;
	$('#insert_form').html("");
    }
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
	    console.debug(response);
            var data = JSON.parse(response);
            $('#insert_form').html("");
            $("#insert_form").dform(data);
            return false;
        });
        return true;
    });
    
    //$(document).on('change','.date', function(){
    //    $("#date").datepicker();
    //});
    
    $("#insert_form").submit(function() {
	var formData = new FormData(document.getElementById("insert_form"));
        var tipologia = document.getElementById('tipologia').value;
        formData.set('tipologia', tipologia);
	
	if (request) {
            request.abort();
        }
	
        if (tipologia == "BOZZETTO" || tipologia == "PERGAMENA") {
            var filenames = document.getElementById('scan').files;
            if (filenames.length == 0) {
	        alert ("Almeno un documento deve essere selezionato.");
	        return false;
            }
	}

	if (tipologia == "DOCUMENTO" || tipologia == "SONETTO") {
            var filenames = document.getElementById('scan[]').files;
            if (filenames.length == 0) {
	        alert ("Almeno un documento deve essere selezionato.");
	        return false;
            }

	    let ext = filenames.item(0).name.split('.').pop();
	    for (let i = 0; i < filenames.length; i++) {
		let file_ext = filenames.item(i).name.split(".").pop();
		if (file_ext != ext) {
		    alert ("Nel caricamente multiplo i file devono essere tutti dello stesso tipo.");
	            return false;
		}		
	    }
        }

//if (tipologia == "SONETTO") {		    
//    var is_ocr = !!document.getElementById('testo_ocr');        
//    if (is_ocr) {
//        var ocr = document.getElementById('testo_ocr').value;
//        if (ocr == "") {
//            if (! confirm('Vuoi proseguire senza OCR ?')) {
//                return false;
//            } 
//        }
//    }
//}
	
        if (tipologia == "SONETTO") {
            if (document.getElementById('data').value == "") {
                alert ("La data e` necessaria.");
                return false;
            }
        } else if (tipologia == "BOZZETTO" || tipologia == "PERGAMENA" || tipologia == 'DOCUMENTO') {
            if (document.getElementById('anno').value == "") {
                alert ("L'anno e` necessario.");
                return false;
            }
        }
	
        if (tipologia == "SONETTO") {
            var e = document.getElementById("ricorrenza");
            var selection = e.options[e.selectedIndex].text;
            formData.set("ricorrenza", selection);
            
            var d = document.getElementById('data').value;
            formData.set('anno', d.substring(0, 4));
        } else if (tipologia == "BOZZETTO") {
            var e = document.getElementById("categoria");
            var selection = e.options[e.selectedIndex].text;
            formData.set("categoria", selection);
	    
            var e = document.getElementById("tecnica");
            var selection = e.options[e.selectedIndex].text;
            formData.set("tecnica", selection);
        }  else if (tipologia == "BOZZETTO") {
            var e = document.getElementById("tecnica");
            var selection = e.options[e.selectedIndex].text;
            formData.set("tecnica", selection);
        }
	
        $('#result1').html("");
        $('#error1').html("");
        request = $.ajax({
            url: "../class/validate_new_ebook.php",
            type: "post",
            data: formData,
            contentType: false,
            cache: false,
            processData:false//,
//            beforeSend: function(){$("#overlay").show();}
        });
	
       	request.done(function (response) {
	    console.log(response);
	    var dict = JSON.parse(response);
            if(dict.hasOwnProperty('error')){
		setInterval(function() {$("#overlay").hide(); }, 500);
    		$('#error1').html(dict['error']);
		return false;
            } else {
		setInterval(function() {$("#overlay").hide(); }, 500);
		$('#result1').html(dict['result']);
		setTimeout(function(){
           	    location.reload();
      		}, 2000);
                return true;
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
		    ext != 'tiff' && ext != 'tif') {// &&
			//ext != 'png') {
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
	    var data = JSON.parse(response);
            $("#update_form").html("");
            $("#update_form").dform(data);
            return false;
        });
	return true;
    });
    
    $("#update_form").submit(function() {
	var formData = new FormData(document.getElementById("update_form"));
        var tipologia = document.getElementById('tipologia_upd').value;
        
        if (request) {
            request.abort();
        }
	
        if (tipologia == "SONETTO") {
            var e = document.getElementById("ricorrenza_upd");
            var selection = e.options[e.selectedIndex].text;
            formData.set("ricorrenza_upd", selection);

            //var d = document.getElementById('data').value;
            //formData.set('anno', d.substring(0, 4));
        } else if (tipologia == "BOZZETTO") {
            var e = document.getElementById("categoria_upd");
            var selection = e.options[e.selectedIndex].text;
            formData.set("categoria_upd", selection);
	    
            var e = document.getElementById("tecnica_upd");
            var selection = e.options[e.selectedIndex].text;
            formData.set("tecnica_upd", selection);
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
	    console.debug(response);
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
