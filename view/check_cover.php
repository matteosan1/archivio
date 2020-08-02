<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
require_once "../view/config.php";
require_once "../class/Member.php";

exec('/Users/sani/opt/anaconda3/bin/python ../class/check_copertine.py', $output, $status);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <meta charset="UTF-8" />
        <title>Libri</title>
        <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="an
onymous">
        </script>       
        <script>
            $(function(){
                $("#footer").load("/view/footer.html");
            });
        </script>
<style>
@import url("/view/css/band_style.css");
</style>
    </head>
    <script type="text/javascript" src="js/autologout.js"></script>

<body>
<?php include "../view/header.php"; ?>
<h3>Libri senza copertina:</h3>
<?php
    $table = array_chunk(json_decode($output[1], true), 10);
    echo "<table border=1px>";
    for ($i=0; $i<count($table); $i++) {
    	echo "<tr>";
        for ($j=0; $j<count($table[$i]); $j++) {    
    	    echo "<td>&nbsp;".$table[$i][$j]."&nbsp;</td>";
	}
	echo "</tr>";
    }
    echo "</table>";
?>
<br>
<h3>Copertine senza libri:</h3>
<?php
    $table = array_chunk(json_decode($output[3], true), 10);
    echo "<table border=1px>";
    for ($i=0; $i<count($table); $i++) {
    	echo "<tr>";
        for ($j=0; $j<8; $j++) {    
    	    echo "<td>&nbsp;".$table[$i][$j]."&nbsp;</td>";
	}
	echo "</tr>";
    }
    echo "</table>";
?>

<div id="footer" align="center"></div>

<script type="text/javascript" src="js/tab_selection.js"></script>
</body>
</html>