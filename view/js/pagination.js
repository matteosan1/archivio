$(document).ready(function(){
	var totalPage = parseInt($('#totalPages').val());	
	console.log("==totalPage=="+totalPage);
	var pag = $('#pagination').simplePaginator({
		totalPages: totalPage,
		maxButtonsVisible: 5,
		currentPage: 1,
		nextLabel: 'Next',
		prevLabel: 'Prev',
		firstLabel: 'First',
		lastLabel: 'Last',
		clickCurrentPage: true,
		pageChange: function(page) {			
			$("#docs").html('<tr><td colspan="6"><strong>loading...</strong></td></tr>');
            $.ajax({
				url:"../class/solr_query.php",
				method:"POST",
				dataType: "json",		
		                data: {q:"*:*", currentPage:page},
				success:function(responseData){
					$('#docs').html(responseData.html);
				}
			});
		}
	});
});
