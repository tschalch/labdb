<?php
$forward = $_GET['forward'];
$query = $_GET['query'];
include("functions.php");
print $query;
pdo_query($query);
?>