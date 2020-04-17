var request;
$(document).ready(function() {
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
            url: "../class/solr_curl.php",
            type: "post",
            data: formData,
            contentType: false,
            cache: false,
            processData:false                       
        });
	
        request.done(function (response){
            var dict = JSON.parse(response);
            if(dict.hasOwnProperty('error')){
                $('#error').html(dict['error']['msg']);
                return false;
            } else {
                $('#result').html("Catalogo inserito in " + dict['responseHeader']['QTime'] + " ms");
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
