<?php
require_once dirname(__FILE__)."/../class/Member.php";

$displayName = $_SESSION["name"];
$role = $_SESSION["role"];

$m = new Member();
$new_notes = $m->getNNotes($displayName, $role);

echo "<div align=\"center\">";
echo "<img src=\"/img/titolo.png\"/>";
echo "<p style=\"text-align:left;\">";
echo "<a href=\"/view/dashboard.php\" class=\"button\"><img src=\"/view/icons/home.png\" hspace=\"20\"></a>";

if ($new_notes != 0) {
   echo "<a href=\"/view/view_notes.php\" class=\"button\"><img src=\"/view/icons/new_message.png\"></a>";
} else {
   echo "<a href=\"/view/view_notes.php\" class=\"button\"><img src=\"/view/icons/message.png\"></a>";
}


echo "<span style=\"float:right;\">";
echo "<img src=\"/view/icons/username.png\" hspace=\"20\">".$displayName;
if ($role == "admin") {
   echo "<a href=\"/view/management.php\" class=\"logout-button\"><img src=\"/view/icons/site_admin.png\" hspace=\"20\"></a>";
}
echo "<a href=\"/logout.php\" class=\"logout-button\"><img src=\"/view/icons/logout.png\" hspace=\"20\"></a>";
echo "</span></p>";
echo "<hr>";//<br><br>";
echo "</div>";

?>