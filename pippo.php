<?php   
// Handle AJAX request (start)
if( isset($_POST['ajax']) && isset($_POST['name']) ){
 echo $_POST['name'];
 exit;
}
// Handle AJAX request (end)
?>

