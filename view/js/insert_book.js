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
            data: {'type':'LIBRO', 'callback':'newitem'},
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
        $.post('/class/codice_archivio_selection.php',{'category':'book_categories', value:value, type:"delete"}, function(data) {
            $("#search_results_for_delete").html(data);
        });
        return false;
    });
    
    $('#submit_delete').click(function() {
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
        $.post('/class/codice_archivio_selection.php',{category:"book_categories", value:value, type:"update"}, function(data){
            //console.debug(data);
            //console.debug("pippo");
	        var data_decoded = JSON.parse(data);
	        $('#volume').empty();
            $.each(data_decoded, function(key, codice_archivio) {
                addOption(document.form_sel_volume.volume, codice_archivio, codice_archivio);
            });
        });
        return false;
    });
    
    $("#form_sel_volume").change(function() {
	    var sel = document.getElementById("volume").value;
        
	    request = $.ajax({
            url: "../class/solr_utilities.php",
            type: "POST",
            data: {'codice_archivio':sel, 'callback':'finditem'},
        });
        
        request.done(function (response) {
            //console.debug(response);
            var data = JSON.parse(response);
            //console.debug(data);
            $("#update_form").dform(data);
            return false;
        });
        return false;
    });

    $("#update_form").submit(function() {
        //console.debug(response);
        //var formData = new FormData(document.getElementById("upd_book"));
        var formData = new FormData(document.getElementById("upd_book"));
        //console.debug(formData.val())
        
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
        
        //request.fail(function (response){                           
        //    console.log(
        //        "The following error occurred: " + response
        //    );
        //});
        return false;
    });    
    
    function search_cdd() {
	    var title = document.getElementById("titolo").value;
	    var author = document.getElementById("prima_responsabilita").value;
        
	    if (title == "") {
	        $('#search_cdd_error').html("Manca il titolo per cercare il CDD")
	        return false;
	    }
        
	    if (author == "") {
	        $('#search_cdd_error').html("Manca l'autore per cercare il CDD")
	        return false;
	    }
        
        if (request) {
            request.abort();
        }
		
	    $.post('/class/search_cdd.php', {author:author, title:title}, function(data) {
	        data = JSON.parse(data);
            console.debug(data);
	        if (data.hasOwnProperty('error')) {
		        document.getElementById("cdd").value = "";
		        $("#search_cdd_error").html(data['error']);
	            return false;
	        } else {
		        $("#search_cdd_error").html("");
		        document.getElementById("cdd").value = data['cdd'][0];
		        return false;
	        }
        });
    }

    $(document).on('change','#titolo', function(){
	    search_cdd();
    });

    $(document).on('change','#prima_responsabilita', function(){
	    search_cdd();
    });

    $('.btn-backup').click(function() {
	    var formData = new FormData(document.getElementById("fm_backup"));
	    
	    if(document.getElementById("last_upload").value == '') {
	        alert ("Devi scegliere una data !");
	        return false;
	    }
	    
        if (request) {
            request.abort();
        }
        
        var mydate = document.getElementById("last_upload").value;
        
        request = $.ajax({
            url: "../class/solr_utilities.php",
            type: "POST",
            data: {'last_upload':mydate, 'callback':'backup'},
            beforeSend: function(){$("#overlay").show();}
        });
	    
        request.done(function (response){
            //console.log(response);
            response = JSON.parse(response);
            if(response.hasOwnProperty('error')){
                alert (response['error']);
                setInterval(function() {$("#overlay").hide(); }, 500);
		        return false;
            } else {
		        $('#link').html(response['result']);
                setInterval(function() {$("#overlay").hide(); }, 500);
                return true;
            }
        });
        
        return false;
    });

    $('.btn-restore').click(function() {
        var formData = new FormData(document.getElementById("new_catalogue"));
	    
        if (request) {
            request.abort();
        }
	    
	    var file1 = document.getElementById('filecsv').value;
	    var file2 = document.getElementById('filezip').value;
	    if (file1 == "" && file2 == "") {
	        alert("Devi specificare almeno un file (CSV o ZIP).");
	        return false;
	    }
        
        request = $.ajax({
            url: "../class/solr_utilities.php",
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){$("#overlay").show();}
        });
	    
        request.done(function (response){
            console.debug(response);
            var dict = JSON.parse(response);
            if(dict.hasOwnProperty('error')){
                $('#error5').html(dict['error']);
                setInterval(function() {$("#overlay").hide(); }, 500);
                return false;
            } else {
		$('#error').html("");
                $('#result5').html(dict['result']);
                setInterval(function() {$("#overlay").hide(); }, 500);
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
