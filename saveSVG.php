<?php
header('Content-Type: text/plain');
$filename = $_POST["title"];
header("Content-Disposition: attachment; filename=\"$filename.svg\"");
print stripcslashes($_POST["svg"]);
?>
