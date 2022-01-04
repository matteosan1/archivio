<?php
require_once dirname(__FILE__)."/../class/Member.php";

$displayName = $_SESSION["name"];
$role = $_SESSION["role"];

$m = new Member();
$new_notes = $m->getNNotes($displayName, $role);

echo "<div align=\"center\">";
echo "<table width=100%>";
echo "<column width=20%>";
echo "<column width=60%>";
echo "<column width=20%>";
echo "<tr>";
echo "<td valign=\"bottom\">";
echo "<p style=\"text-valign:bottom; text-align:left;\">";
echo "<a href=\"/view/dashboard.php\" class=\"button\"><img src=\"/view/icons/home.png\" hspace=\"20\"></a>";

if ($new_notes != 0) {
    echo "<a href=\"/view/view_notes.php\" class=\"button\"><img src=\"/view/icons/new_message.png\"></a>";
} else {
    echo "<a href=\"/view/view_notes.php\" class=\"button\"><img src=\"/view/icons/message.png\"></a>";
}    
echo "</td><td align=\"right\">";           
echo "<img src=\"/img/titolo2.png\"/>";
echo "</td><td valign=\"bottom\">";           
echo "<span style=\"float:right;\">";
echo "<img src=\"/view/icons/username.png\" hspace=\"10\">".$displayName;
if ($role == "admin") {
    echo "<a href=\"/view/management.php\" class=\"logout-button\"><img src=\"/view/icons/site_admin.png\" hspace=\"10\"></a>";
}
echo "<a href=\"/logout.php\" class=\"logout-button\"><img src=\"/view/icons/logout.png\" hspace=\"10\"></a>";
echo "</span></p>";
echo "</td></tr></table>";
//echo "<hr>";//<br><br>";

echo "</div>";
?>
