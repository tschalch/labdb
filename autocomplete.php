<?php
include_once("functions.php");
include_once("accesscontrol.php");
$field = $_POST['field'];
$cue = $_POST["value"];
$query = "SELECT DISTINCT $field FROM inventory WHERE $field LIKE '%$cue%'";
#print $query;
$r = pdo_query($query);
#print_r($r);
//print "<ul>\n";
foreach ($r as $row){
	$entry = $row[$field];
	print "\t<li>$entry</li>\n";
}
//print "</ul>\n";
?>