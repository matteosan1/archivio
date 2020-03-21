<?php
require_once "../view/session.php";
require_once "../class/Member.php";

$member = new Member();
$notes = $member->getAllNotes($displayName, $role);

?> 

<!DOCTYPE html>
<html>
	<head>
		<title>Controlla Note</title>
		<meta charset="utf-8"/>
	        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script>
		$(function(){
		  $("#footer").load("/view/footer.html"); 
		});
		</script>
	    	</head>

<script type="text/javascript">

var request;
$(document).ready(function() {
    $('.delete_note').click(function() {
	var name = $(this).closest('tr').find('.display_name').text();
	var r = confirm("Sicuro di voler rimuovere la note ?");
  	if (r == true) {
	    var tr = $(this).closest('tr'),
            del_id = $(this).attr('id');
            $.ajax({
               url: "../class/remove_note.php?delete_id="+del_id,
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
	<?php
	include "header.php";
	?>
	    <br>
	    <div align=center>
	    <table>
		<col width="130">
  		<col width="250">
		<col width="130">
  		<col width="200">
		<tr>
		<th> Da </th>
		<th> Data </th>
		<th> Messaggio </th>
		<th> </th>
		</tr>
		<?php
		    foreach ($notes as $note) {
		    	    echo "<tr>";
			    echo "<td>".$note['sender']."</td>";
   			    echo "<td class='display_name'>".$note['date']."</td>";
    			    echo "<td>".$note['text']."</td>";
			    echo "<td><button class=\"btn btn-sm btn-danger delete_note\" id=\"".$note['id']."\" >RIMUOVI</button></td>";
			    echo "</tr>";			    
		    }
		?>     
    	    </table>
	    </div>
    	    </table>
<br>
<div id="footer" align="center"></div>

</body>
</html>
