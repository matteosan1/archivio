var request;
$(document).ready(function() {
    $('.btn-backup').click(function() {
	var formData = new FormData(document.getElementById("fm_backup"));
	
	if(document.getElementById("last_upload").value == '') {
	    alert ("Devi scegliere una data !");
	    return false;
	}
	
        if (request) {
            request.abort();
        }
	
        request = $.ajax({
            url: "../class/solr_curl.php",
            type: "post",
            data: formData,
            contentType: false,
            cache: false,
            processData:false                       
        });
	
        request.done(function (response){
            console.log(response);
            response = JSON.parse(response);
            if(response.hasOwnProperty('error')){
                alert (response['error']);
		return false;
            } else {
		$('#link').html(response['result']);
                return true;
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
