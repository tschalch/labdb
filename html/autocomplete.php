<?php
include_once("functions.php");
include_once("accesscontrol.php");
$field = $_POST['field'];
$table = $_POST['table'];
$cue = $_POST["value"];
#print_r($r);

$extended = false;
 
$r = getRecords($table, $userid, array("$field"), " $field LIKE '%$cue%' OR tracker.trackID='$cue' ", "$table.$field");

if (isset($_POST['extended']) && $_POST['extended'])
{
    $extended = true;
    $r = getRecords($table, $userid, array('tracker.trackID',"$field"), " $field LIKE '%$cue%' OR tracker.trackID='$cue' ", "$table.$field");
}
foreach ($r as $row)
{
	$entry = $row[$field];
	if ($extended) {
		echo "<li><span>$entry</span><span style=\"display:none\">${row['trackID']}</span></li>";
	}
	else {
	    print "<li>$entry</li>\n";
	}
}

?>
