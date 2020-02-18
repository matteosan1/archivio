<!doctype html>
<html>
 
 <body >
 
  <form method='post' action id="upload">

   <input type='text' name='name' placeholder='Enter your name' id='name'>
   <input type='submit' value='submit' name='submit'><br>
   <div id='response'></div>
  </form>

  <!-- Script -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script>
  $(document).ready(function(){
	  //$('#name').keyup(function(){	
     $('#upload').submit(function() {
     var name = $('#name').val();

     $.ajax({
      url: 'pippo.php',
      type: 'post',
      data: {ajax: 1,name: name},
      success: function(response){
       $('#response').text('name : ' + response);
      }
     });
     return false;
    });
  });
  </script>
 </body>
</html>
