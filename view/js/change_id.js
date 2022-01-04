var request;

$(document).ready(function() {
    setInterval(checkForWarning, 1000 * 60)
    
    $('.btn-change-id').click(function() {
	var formData = new FormData(document.getElementById("change_id"));
        if (request) {
            request.abort();
        }
	
        if (document.getElementById('old_codice_archivio').value == "") {
	    alert ("Manca il codice archivio originale.");
	    return false;
	}
	
        if (document.getElementById('new_codice_archivio').value == "") {
	    alert ("Mance il nuovo codice archivo.");
	    return false;
	}
	
        request = $.ajax({
            url: "../class/change_id.php",
            type: "post",
            data: formData,
            contentType: false,
            cache: false,
            processData:false                       
        });
	
	request.done(function (response) {
	    var dict = JSON.parse(response);
            if(dict.hasOwnProperty('error')){
		$('#result1').html("");
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
});
