<?php
require_once "../view/session.php";
require ("../class/Member.php");

$member = new Member();
$users = $member->getAllMembers();
$categories = $member->getAllCategories();
$categories_ebook = $member->getAllCategories("ebook_categories");
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
		  $("#footer").load("/view/footer.html"); 
		});
		</script>
	    	</head>

<script type="text/javascript">

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
    
    $('.delete_category').click(function() {
        var name = $(this).closest('tr').find('.category').text();
	var r = confirm("Sicuro di voler eliminare la tipologia " + name + " ?");
  	if (r == true) {
            var tr = $(this).closest('tr'),
            del_id = $(this).attr('id');

            $.ajax({
               url: "../class/remove_category.php?table=book&delete_id="+del_id,
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

    $('.delete_ebook_category').click(function() {
        var name = $(this).closest('tr').find('.ebook_category').text();
	var r = confirm("Sicuro di voler eliminare la tipologia " + name + " ?");
  	if (r == true) {
            var tr = $(this).closest('tr'),
            del_id = $(this).attr('id');

            $.ajax({
               url: "../class/remove_category.php?table=ebook&delete_id="+del_id,
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
});

function relocate_home()
{
     location.href = "new_registration.php";
}

function relocate_home2(type)
{
    location.href = "new_category.php?type=" + type;
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
	    <input type="button" class="btn btn-info" value="Nuovo utente" onclick="relocate_home()">
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
   			    echo "<td class='display_name'>".$user['display_name']."</td>";
    			    echo "<td>".$user['email']."</td>";
    			    echo "<td>".$user['role']."</td>";
			    echo "<td><button class=\"btn btn-sm btn-danger delete_user\" id=\"".$user['id']."\" >RIMUOVI</button></td>";
			    echo "</tr>";			    
		    }
		?>     
    	    </table>
	    </div>
	    <hr width="80%">
	    <div align=center>
	    <form enctype="multipart/form-data" action method="POST" id="new_book">
	    <input type="button" class="btn btn-info" value="Nuova categoria libri" onclick="relocate_home2('book')">
	    <input type="hidden" id="book" name="book" value="">
	    </form>	    
	    <div id="result_libri"></div>
	    <br>
	    <table>
		<col width="130">
		<col align="center">
		<tr>
		<th> CATEGORIA </th>
		<th align="center"> COMANDO </th>
		</tr>
		<?php
		    foreach ($categories as $category) {
		    	    echo "<tr>";
			    echo "<td class='category'>".$category['category']."</td>";
			    echo "<td align=center><button class=\"btn btn-sm btn-danger delete_category\" id=\"".$category['id']."\" >RIMUOVI</button></td>";
			    echo "</tr>";			    
		    }
		?>     
    	    </table>
    	    <hr width="80%">	
	    <div align=center>
	    <form enctype="multipart/form-data" action method="POST" id="new_ebook">
	    <input type="button" class="btn btn-info" value="Nuova categoria eDoc" onclick="relocate_home2('ebook')">
       	    <input type="hidden" id="ebook" name="ebook" value="">	
	    </form>
	    
	    <div id="result_edoc"></div>
	    <br>
	    <table>
		<col width="130">
		<tr>
		<th> CATEGORIA </th>
		<th> COMANDO </th>
		</tr>
		<?php
		    foreach ($categories_ebook as $category) {
		    	    echo "<tr>";
			    echo "<td class='ebook_category'>".$category['category']."</td>";
			    echo "<td><button class=\"btn btn-sm btn-danger delete_ebook_category\" id=\"".$category['id']."\" >RIMUOVI</button></td>";
			    echo "</tr>";			    
		    }
		?>     
    	    </table>

<div id="footer" align="center"></div>

</body>
</html>
