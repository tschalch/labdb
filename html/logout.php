<?php 
session_start();
include("header.php");

unset($_SESSION['uid']);
unset($_SESSION['pwd']);

print "<h3>Logout complete</h3>";

include("footer.php");
?>