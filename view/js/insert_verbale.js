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
    //if (pageName == 'Insert') {
    //	document.getElementById("tipologia").selectedIndex = 0;
    //	$('#insert_form').html("");
    //}
}

function sanityChecksVerbale(tipologia, formData) {
    console.debug(tipologia);
    
    if (tipologia == "VERBALE") {
        var filenames = document.getElementById('scan').files;
        if (filenames.length == 0) {
    	    alert ("Almeno un documento deve essere selezionato.");
    	    return false;
        }
    	
        if (document.getElementById('data').value == "") {
            alert ("La data e` necessaria.");
            return false;
        }
    
    	if (document.getElementById('tipo_verbale').value == 4 && role != 'admin') {
            alert ("Non hai i permessi per fare questo inserimento.");
    	    return false;
        }
    }

    if (tipologia == "DELIBERA") {
        if (document.getElementById('argomento_breve').value == "") {
            alert ("Hai dimenticato l'argomento della delibera.");
	        return false;
        }
        
        if (document.getElementById('testo').value == "") {
            alert ("Hai dimenticato il testo della delibera.");
	        return false;
        }
        
        if (document.getElementById('data').value == "") {
            alert ("Hai dimenticato la data della delibera.");
	        return false;
        }

        if (document.getElementById('tipo_delibera').value == 0) {
            alert ("Hai dimenticato l'Organo deliberante.");
	        return false;
        }

        if (document.getElementById('tipo_delibera').value == 4 && role != 'admin') {
            alert ("Non hai i permessi per fare questo inserimento.");
	        return false;
        }
    }
    
    return true;
}

function sanityChecksVerbaleUpdate(tipologia, formData) {
    if (tipologia == "DELIBERA") {
	var e = document.getElementById("tipo_delibera_upd");
        var selection = e.options[e.selectedIndex].text;
        formData.set("tipo_delibera_upd", selection);
    } else {
	var e = document.getElementById("tipo_verbale_upd");
        var selection = e.options[e.selectedIndex].text;
        formData.set("tipo_verbale_upd", selection);
    }
    return true;
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
    
    $("#insert_form").submit(function() {
	var formData = new FormData(document.getElementById("insert_form"));
        var tipologia = document.getElementById('tipologia').value;
        formData.set('tipologia', tipologia);

	if (tipologia == "DELIBERA") {
	    var e = document.getElementById("tipo_delibera");
            var selection = e.options[e.selectedIndex].text;
            formData.set("tipo_delibera", selection);
	} else {
	    var e = document.getElementById("tipo_verbale");
            var selection = e.options[e.selectedIndex].text;
            formData.set("tipo_verbale", selection);
	}
	
	if (request) {
            request.abort();
        }
	
	if (!sanityChecksVerbale(tipologia, formData))
	    return false;
	
        $('#result1').html("");
        $('#error1').html("");
        request = $.ajax({
            url: "../class/validate.php",
            type: "post",
            data: formData,
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){$("#overlay").show();}
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
    
    $("#lets_search_for_delete").bind('submit',function() {
        var value = $('#str_for_delete').val();
        $.post('/class/codice_archivio_selection.php',{value:value, type:"delete", category:"verbale_categories"}, function(data) {
            $("#search_results_for_delete").html(data);
        });
        return false;
    });
    
    $('#submit_delete').click(function() {
        var r = confirm("Sicuro di voler rimuovere gli elementi selezionati ?");
        if (r == true) {
    	    var formData = new FormData(document.getElementById("delete_verbale"));
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
        $.post('/class/codice_archivio_selection.php',{category:"verbale_categories", value:value, type:"update"}, function(data) {
            var data_decoded = JSON.parse(data);
            $('#sel_verbale').empty();
            $.each(data_decoded, function(key, codice_archivio) {
                addOption(document.form_sel_verbale.sel_verbale, codice_archivio, codice_archivio);
            });
        });
        return false;
    });
    
    $("#form_sel_verbale").change(function() {
        var sel = document.getElementById("sel_verbale").value;
    	
    	request = $.ajax({
            url: "../class/solr_utilities.php",
            type: "POST",
            data: {'codice_archivio':sel, 'callback':'finditem'},
        });
    	
    	request.done(function (response) {
    	    console.debug(response);
    	    var data = JSON.parse(response);
            $("#update_form").html("");
            $("#update_form").dform(data);
            return false;
        });
    	return false;
    });
    
    $("#update_form").submit(function() {
    	var formData = new FormData(document.getElementById("update_form"));
        var tipologia = document.getElementById('tipologia_upd').value;
        
        if (request) {
            request.abort();
        }
    
    	//if (!sanityChecksVerbaleUpdate(tipologia, formData))
    	//    return false;
    	
        request = $.ajax({
            url: "../class/validate_upd.php",
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
