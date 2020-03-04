<?php
require_once "../view/session.php";
require ("../class/Member.php");

$member = new Member();
$users = $member->getAllMembers();
?> 

<!DOCTYPE html>
<html>
	<head>
		<title>Amministrazione sito</title>
		<meta charset="utf-8"/>
	        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script>
		$(function(){
		  $("#header").load("/view/header.html"); 
		    //$("#footer").load("/view/footer.html"); 
		    });
		    </script>
	    	</head>

<script type="text/javascript">

var request;
$(document).ready(function() {
    $('.delete_class').click(function(){
        var tr = $(this).closest('tr'),
        del_id = $(this).attr('id');
	console.log(del_id);
        $.ajax({
            url: "../class/remove_user.php?delete_id="+del_id,
	    //type: 'post',
	    //contentType: 'application/json; charset=utf-8',
	    //data: {"id": del_id},
	    //dataType: 'json',
	//    data: dati,
           cache: false,
           success:function(result){
	      console.log(result);
	      tr.fadeOut(1000, function(){
	          $(this).remove();
	      });
	   }
	});
    });

//    $("#removeBtn9").click(function() {
//        if (confirm('Are you sure you want to delete this entry?')) {
//        var dati = $("#remove").serialize();
//        request = $.ajax({
//            url: "../class/remove_user.php",
//            type: 'POST',
//            data: dati
//	});
//
//	request.done(function(response) {
//	    //window.location.href = "management.php";
//	    return true;
//	});
//	};
//    });
});

function relocate_home()
{
     location.href = "new_registration.php";
}
</script>
         <style>
   	    table,th,tr,td
            {
                border: 1px solid black;
            }
	</style>
	<body>
	    <div id="header" align="center"></div>
	    <br>
	    <div align=center>
	    <form enctype="multipart/form-data" action method="POST" id="new_user">
	    <input type="button" class="btn btn-info" value="Nuovo Utente" onclick="relocate_home()">
	    </form>
	    <br><br>
	    <div id="result"></div>
	    <table>
		<col width="130">
  		<col width="250">
		<col width="130">
  		<col width="200">
		<tr>
		<th> USERNAME </th>
		<th> NOME </th>
		<th> EMAIL </th>
		<th> RUOLO </th>
		<th> COMANDO </th>
		</tr>
		<?php
		    foreach ($users as $user) {
		    	    echo "<tr>";
			    echo "<td>".$user['user_name']."</td>";
   			    echo "<td>".$user['display_name']."</td>";
    			    echo "<td>".$user['email']."</td>";
    			    echo "<td>".$user['role']."</td>";
			    //echo '<td><form enctype="multipart/form-data" action method="POST" id="remove">';
			    //echo '<input type="hidden" id="id" name="id" value="'.$user['id'].'">';
			    //echo '<button type="button" id="removeBtn"'.$user['id'].'" class="btn btn-success"> Rimuovi </button>';
			    echo "<td><button class=\"btn btn-sm btn-danger delete_class\" id=\"".$user['id']."\" >RIMUOVI</button></td>";
         		    //echo "</form></td>";
			    echo "</tr>";			    
		    }
		?>     
    	    </table>
	    </div>
	</body>
</html>
<!-- FIXME AGGIUNGERE GESTIONE CATEGORIE LIBRI -->