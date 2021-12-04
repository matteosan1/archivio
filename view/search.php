<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!
ini_set('display_startup_errors', 1); // SET IT TO 0 ON A LIVE SERVER !!!

require_once "../view/session.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
        <meta charset="UTF-8" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <title>Ricerca</title>
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"> </script>
<style>
@import url("/view/css/main.css");
@import url("/view/css/search.css");
</style>
</head>
<script type="text/javascript" src="js/autologout.js"></script>
<script type="text/javascript" src="js/search.js"></script>
<script>
    $(function(){
            $("#footer").load("/view/footer.html"); 
    });
    window.addEventListener('load', () => {
        const button = document.querySelector('#clear');
        button.addEventListener('click', () => {
            document.querySelector('#enter').value = "";
        });
    }); 
</script>
<body>
<div id="scroll"></div>
<div id="header"><?php include "../view/header.php"; ?></div>
<div align=center id=result1 style="color:green"></div>
<div align=center id=error1 style="color:red"></div>
<br>
<div id="navigation">
<h3 align="center">Faceting</h3>
    <div id="facet-result" style="padding: 10px"></div>
</div>

<div id="content_search" align="center">
<table>
    <tr>
    <td>
    <form enctype="multipart/form-data" action method="POST" id="new_search" name="new_search" class="new_search">
    <input type="text" size="50" id="query" name="query">
    </td>
    <td>
    &nbsp;<button id="search" class="btn btn-sm btn-info btn-search-doc"><img src="/view/icons/plus.png">&nbsp;Cerca</button>
    </td>
    </tr>
    <tr>
    <td colspan=2>
        <br>
        <table width=100%>
        <tr>
        <td><input type="checkbox" id="search_libri" name="search_libri"><label for="search_libri">&nbsp;Libri</label></td>
        <td><input type="checkbox" id="search_foto" name="search_foto"><label for="search_foto">&nbsp;Fotografie</label></td>
        <td><input type="checkbox" id="search_video" name="search_video"><label for="search_video">&nbsp;Video</label></td>
        <td><input type="checkbox" id="search_edoc" name="search_edoc"><label for="search_edoc">&nbsp;eDoc</label></td>
        <td><input type="checkbox" id="search_mont" name="search_mont"><label for="search_mont">&nbsp;Monturati</label></td>
        <?php
    if ($_SESSION['role'] == 'admin') {
        echo '<td><input type="checkbox" id="search_delibera" name="search_delibera"><label for="search_delibera">&nbsp;Delibere</label></td>';
    }
    ?> 
        </tr>
        </table>
    </td>
    </tr>
    </table>
    </form>

    <div id="query-result"></div>
<br>
<div id="pagination-result"></div>
</div>
 <div id="footer" align="center"></div>
</body>
</html>
