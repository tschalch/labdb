<?php
include("functions.php");
include_once("accesscontrol.php");
$id = $_GET['id'];
$field = $_GET['field'];
$process = $_GET['process'];
$row = getRecord($id, $userid, $groups);
$sequence = $row[$field];
$title = "${row['name']} sequence";
switch($process){
	case 'ic':
		$title = "Inverse complement of ".$title;
		$revcomp = revcomp($sequence, 'DNA');
		$sequence = fastaseq($revcomp, "\n");
		break;
}

include("header.php");
echo "<h2>$title</h2>";
if ($process != 'ic'){
	print "<span style=\"display: block; clear: right\"><a 		
		href=\"sequence.php?field=$field
		&amp;id=$id&amp;process=ic\">Get Inverse Complement</a></span>";
}
print "<pre class=\"sequence\">$sequence </pre>";
include("footer.php");
?>
