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
        
    if (pageName == "Insert") {
        request = $.ajax({
            url: "../class/solr_utilities.php",
            type: "POST",
            data: {'type':'VESTIZIONE', 'callback':'newitem'},
        });
        
        request.done(function (response) {
            console.debug(response);
            var data = JSON.parse(response);
            //console.debug(data);
            $("#insert_form").dform(data);
            return false;
        });
    }
}

$(document).ready(function() {
    
    //$('#aggiorna').prop('disabled', true);
    setInterval(checkForWarning, 1000 * 60)
    $('html, body').animate({
        //scrollTop: $('#scroll').offset().top
        scrollTop: $('#scroll').scrollTop(0)
    }, 'slow');

    $("#insert_form").submit(function() {
	var formData = new FormData(document.getElementById("new_vestizione"));
        if (request) {
            request.abort();
        }
	
        d = document.getElementById('data').value;
        if (d == "") {
            alert ("Hai dimenticato la data della vestizione.");
            return false;
        }
	
        if (document.getElementById('evento').value == 0) {
            alert ("Hai dimenticato la ricorrenza.");
            return false;
        }
	
        if(document.getElementById("comparsa").files.length == 0) {
            if (document.getElementById('ruolo').value == "null") {
                alert ("Hai dimenticato il ruolo.");
                return false;
            }
	    
            if (document.getElementById('nome_cognome').value == "") {
	        alert ("Nome e cognome ?");
	        return false;
	    }
        }
        
        formData.set('tipologia', 'MONTURATO');
        //formData.set('anno', d.substring(0, 4));
        request = $.ajax({            
            url: "../class/validate_new_ebook.php",
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
		$('#error1').html("");
		$('#result1').html("");
    		$('#error1').html(dict['error']);
		$('#result1').html(dict['result']);
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

    $("#lets_search_for_delete").bind('submit',function() {
        var value = $('#str_for_delete').val();
        $.post('/class/codice_archivio_selection.php',{'category':'vestizione_categories', value:value, type:"delete"}, function(data) {
            $("#search_results_for_delete").html(data);
        });
        return false;
    });
    
    $('#submit_delete').click(function() {
        var r = confirm("Sicuro di voler rimuovere i monturati selezionati ?");
        if (r == true) {
    	    var formData = new FormData(document.getElementById("delete_vestizione"));
            
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
                console.debug(response);
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

    $("#lets_search_for_update").bind('submit', function() {
        var value = $('#str').val();
        $.post('/class/codice_archivio_selection.php',{category:"vestizione_categories", value:value, type:"update"}, function(data){
            //console.debug(data);
	        var data_decoded = JSON.parse(data);
	        $('#vestizione').empty();
            $.each(data_decoded, function(key, codice_archivio) {
                addOption(document.form_sel_vestizione.vestizione, codice_archivio, codice_archivio);
            });
        });
        return false;
    });
    
    $("#form_sel_vestizione").change(function() {
	    var sel = document.getElementById("vestizione").value;
        
	    request = $.ajax({
            url: "../class/solr_utilities.php",
            type: "POST",
            data: {'codice_archivio':sel, 'callback':'finditem'},
        });
        
        request.done(function (response) {
            console.debug(response);
            var data = JSON.parse(response);
            //console.debug(data);
            document.getElementById("update_vestizione").innerHTML = "";
            $("#update_vestizione").dform(data);
            return false;
        });
        return false;
    });

    $("#update_vestizione").submit(function() {
        var formData = new FormData(document.getElementById("update_vestizione"));
        
        if (request) {
            request.abort();
        }

        var e = document.getElementById("evento_upd");
        var selection = e.options[e.selectedIndex].text;
        formData.set("evento_upd", selection);
        var e = document.getElementById("ruolo_upd");
        var selection = e.options[e.selectedIndex].text;
        formData.set("ruolo_upd", selection);

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
        
        //request.fail(function (response){                           
        //    console.log(
        //        "The following error occurred: " + response
        //    );
        //});
        return false;
    });    
    
});
