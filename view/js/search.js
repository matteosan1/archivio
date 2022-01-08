var request;
$(document).ready(function() {
    setInterval(checkForWarning, 1000 * 60)
    
    $(".btn-search-doc").click(function() {
        var query = document.getElementById("query").value;
        var libri = document.getElementById("search_libri");
        var foto = document.getElementById("search_foto");
        var video = document.getElementById("search_video");
        var edoc = document.getElementById("search_edoc");
        var mont = document.getElementById("search_mont");
        var delibera = document.getElementById("search_delibera");

        var subsearch = 0;
        if (libri.checked) 
            subsearch = subsearch + 1;
        if (foto.checked) 
            subsearch += 2;
        if (video.checked) 
            subsearch += 4;
        if (edoc.checked) 
            subsearch += 8;
        if (mont.checked) 
            subsearch += 16;
        if (delibera.checked)
            subsearch += 32;
	
        request = $.ajax({
	    url: "../class/search_result.php",
	    type: 'GET',
	    data: "q="+query+"&sub="+subsearch,
            //formData, 
	    contentType: false,
	    cache: false,
	    processData:false
	});
	
	request.done(function (response) {
   	    //console.debug(response);
            response = JSON.parse(response);
            $("#query-result").html(response['header']);
            $("#pagination-result").html(response['body']);
            $("#facet-result").html(response['faceting']);
            
	    
            //if(response.hasOwnProperty('error')){
    	    //    $('#error1').html(response['error']);
	    //    return false;
            //} else {
	    //    $('#error1').html("");
	    //    $('#result1').html(response['result']);
	    //   setTimeout(function(){
            //        location.reload();
      	    //    }, 2000);
            //}
        });
	
        request.fail(function (response){			    
            console.log(
                "The following error occurred: " + response
            );
        });
	return false;
    });
});    

function getresult(page) {
    var query = document.getElementById("query").value;
    var libri = document.getElementById("search_libri");
    var foto = document.getElementById("search_foto");
    var video = document.getElementById("search_video");
    var edoc = document.getElementById("search_edoc");
    var mont = document.getElementById("search_mont");
    var delibera = document.getElementById("search_delibera");
    
    var subsearch = 0;
    if (libri.checked) 
        subsearch += 1;
    if (foto.checked) 
        subsearch += 2;
    if (video.checked) 
        subsearch += 4;
    if (edoc.checked) 
        subsearch += 8;
    if (mont.checked) 
        subsearch += 16;
    if (delibera.checked) 
        subsearch += 32;
    
    request = $.ajax({
	url: "../class/search_result.php",
	type: 'GET',
	data: "q="+query+"&page="+page+"&sub="+subsearch,
	contentType: false,
	cache: false,
	processData:false
    });
    
    request.done(function (response) {
        response = JSON.parse(response);
        //console.debug(response);
	
        $("#query-result").html(response['header']);
        $("#pagination-result").html(response['body']);
        $('html, body, #content').animate({
            scrollTop: $('#scroll').offset().top
        }, 10);
	
	//response = JSON.parse(response);
        //if(response.hasOwnProperty('error')){
    	//    $('#error1').html(response['error']);
	//    return false;
        //} else {
	//    $('#error1').html("");
	//    $('#result1').html(response['result']);
	//   setTimeout(function(){
        //        location.reload();
      	//    }, 2000);
        //}
    });
}
