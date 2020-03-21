<?php
require_once dirname(__FILE__)."/../class/Member.php";

$m = new Member();
$new_notes = $m->getNNotes($displayName, $role);

echo "<div align=\"center\">";
echo "<img src=\"/img/titolo.png\"/>";
echo "</div>";
echo "<div align=\"center\" class=\"member-dashboard\">";
echo "<a href=\"/view/dashboard.php\" class=\"button\">Home</a> - <a href=\"/view/note.php\" class=\"button\">Note</a> - <a href=\"/logout.php\" class=\"logout-button\">Logout</a>";
if ($new_notes != 0) {
   echo "- <a href=\"/view/view_notes.php\" class=\"button\">Hai ".$new_notes." nuove note</a>";
   } else {
   echo "- <a href=\"/view/view_notes.php\" class=\"button\">Note</a>";
   }
echo "</div><br>";

echo "Benvenuto ".$displayName;
echo "<br><br>";
?>