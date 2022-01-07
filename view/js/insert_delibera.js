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
            data: {'type':'DELIBERA', 'callback':'newitem'},
        });
        
        request.done(function (response) {
            //console.debug(response);
            var data = JSON.parse(response);
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
	    var formData = new FormData(document.getElementById("new_delibera"));
        if (request) {
            request.abort();
        }

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

        if ($("#straordinaria").is(':checked')) {
            formData.set("straordinaria", 1);
        } else {
            formData.set("straordinaria", 0);
        }

        if ($("#unanimita").is(':checked')) {
            formData.set("unanimita", 1);
        } else {
            formData.set("unanimita", 0);
        }

	var e = document.getElementById("tipo_delibera");
        var selection = e.options[e.selectedIndex].text;
        formData.set("tipo_delibera", selection);
	formData.set("tipologia", "DELIBERA");

        request = $.ajax({            
            url: "../class/validate.php",
            type: "post",
            data: formData,
            contentType: false,
            cache: false,
            processData:false                       
        });
	    
	request.done(function (response) {
	    //console.debug(response);
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

    $("#lets_search_for_delete").bind('submit',function() {
        var value = $('#str_for_delete').val();
        $.post('/class/codice_archivio_selection.php',{'category':'delibera_categories', value:value, type:"delete"}, function(data) {
            $("#search_results_for_delete").html(data);
        });
        return false;
    });
    
    $('#submit_delete').click(function() {
        var r = confirm("Sicuro di voler rimuovere le delibere selezionate ?");
        if (r == true) {
    	    var formData = new FormData(document.getElementById("delete_delibera"));
            
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
        $.post('/class/codice_archivio_selection.php',{category:"delibera_categories", value:value, type:"update"}, function(data){
            //console.debug(data);
	        var data_decoded = JSON.parse(data);
	        $('#delibera').empty();
            $.each(data_decoded, function(key, codice_archivio) {
                addOption(document.form_sel_delibera.delibera, codice_archivio, codice_archivio);
            });
        });
        return false;
    });
    
    $("#form_sel_delibera").change(function() {
	    var sel = document.getElementById("delibera").value;
        
	    request = $.ajax({
            url: "../class/solr_utilities.php",
            type: "POST",
            data: {'codice_archivio':sel, 'callback':'finditem'},
        });
        
        request.done(function (response) {
            //console.debug(response);
            var data = JSON.parse(response);
            document.getElementById("update_form").innerHTML = "";
            $("#update_form").dform(data);
            return false;
        });
        return false;
    });

    $("#update_form").submit(function() {
        var formData = new FormData(document.getElementById("update_form"));
         
        if ($("#straordinaria_upd").is(':checked')) {
            formData.set("straordinaria_upd", 1);
        } else {
            formData.set("straordinaria_upd", 0);
        }

        if ($("#unanimita_upd").is(':checked')) {
            formData.set("unanimita_upd", 1);
        } else {
            formData.set("unanimita_upd", 0);
        }

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
            //console.log(response);
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
