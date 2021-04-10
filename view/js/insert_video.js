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
            data: {'type':'VIDEO', 'callback':'newitem'},
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
    setInterval(checkForWarning, 1000 * 60)
    $('html, body').animate({
        //scrollTop: $('#scroll').offset().top
        scrollTop: $('#scroll').scrollTop(0)
    }, 'slow');

    $('#insert_form').click(function() {
	    var formData = new FormData(document.getElementById("new_video"));
        
	    if (request) {
            request.abort();
        }

	    var filename = document.getElementById('videos').value;
        if (filename == "") {
	        alert ("Il file da indicizzare devono essere specificati.");
	        return false;
	    } else {
 	        request = $.ajax({
               	url: "../class/validate_new_video.php",
               	type: "post",
               	data: formData,
               	contentType: false,
           	    cache: false,
               	processData:false                   
        	});
            
            request.done(function(response) {
	   	        console.log(response);
                response = JSON.parse(response);
                if(response.hasOwnProperty('error')) {
		            $('#result1').html("");
		            $('#error1').html(response['error']);
		            return false;
                } else {
		            $('#error1').html("");
		            $('#result1').html(response['result']);
		            setTimeout(function(){
	                    location.reload();}, 2000);
                }
            });
        }
        return false;
    });

    $("#lets_search_for_delete").bind('submit',function() {
        var value = $('#str_for_delete').val();
        $.post('/class/codice_archivio_selection.php',{'category':'video_categories', value:value, type:"delete"}, function(data) {
            $("#search_results_for_delete").html(data);
        });
        return false;
    });
    
    $('#submit_delete').click(function() {
    	var formData = new FormData(document.getElementById("delete_video"));
        
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
		        $('#result3').html("");
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

    function addOption(selectbox, text, value) {
        var optn = document.createElement("OPTION");
        optn.text = text;
        optn.value = value;
        selectbox.options.add(optn);
    }
    
    $("#lets_search_for_update").bind('submit', function() {
        var value = $('#str').val();
        $.post('/class/codice_archivio_selection.php',{category:"video_categories", value:value, type:"update"}, function(data){
            //console.debug(data);
            //console.debug("pippo");
	        var data_decoded = JSON.parse(data);
	        $('#vsel_video').empty();
            $.each(data_decoded, function(key, codice_archivio) {
                addOption(document.form_sel_video.sel_video, codice_archivio, codice_archivio);
            });
        });
        return false;
    });
    
    $("#form_sel_volume").change(function() {
	    var sel = document.getElementById("sel_video").value;
	    request = $.ajax({
            url: "../class/solr_utilities.php",
            type: "POST",
            data: {'codice_archivio':sel, 'callback':'finditem'},
        });
        
	    request.done(function (response){
			console.log(response);
            var data = JSON.parse(response);
            //console.debug(data);
            $("#update_form").dform(data);
            return false;
        });
	    return true;        
    });
    
    $("#update_form").submit(function() {
        //console.debug(response);
        var formData = new FormData(document.getElementById("upd_video"));

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
	            $('#result2').html("");
		        $('#error2').html(response['error']);
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
