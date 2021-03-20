var request;

$(document).ready(function() {
    $('.delete_user').click(function() {
	var name = $(this).closest('tr').find('.display_name').text();
	var r = confirm("Sicuro di voler rimuovere l'utente " + name + " ?");
  	if (r == true) {
	    var tr = $(this).closest('tr'),
            del_id = $(this).attr('id');
            $.ajax({
               url: "../class/remove_user.php?delete_id="+del_id,
               cache: false,
               success:function(result){
	          tr.fadeOut(1000, function(){
	              $(this).remove();
	          });
	       }
	    });
       } else {
          return false;
       }    
    });

    $(document).on('click', '[id^="mybutton-"]', function() {
        var dataForm = new FormData(document.getElementById("new_tagl2"));
	if (request) {
            request.abort();
        }

        request = $.ajax({
                url: "../class/validate_new_category.php",
                type: "post",
                data: dataForm,
                contentType: false,
                cache: false,
                processData:false                       
        });

        request.done(function (response){
//			   console.log(response);
                if (response != 1) {
		    $('#registered').html(response);
		    return false;
                } else {
                    window.location.href = "../view/management.php";
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

function relocate_home()
{
     location.href = "new_registration.php";
}

function relocate_home2(type, id)
{
    location.href = "new_category.php?type=" + type;
}

function relocate_home3(type, name, id)
{
    location.href = "validate_new_category.php?type=" + type + "&id=" + id + "&name=" + name;
}
