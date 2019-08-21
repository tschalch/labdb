<?php
include_once("functions.php");
include_once("accesscontrol.php");
$field = $_POST['field'];
$table = $_POST['table'];
$cue = $_POST["value"];
#print_r($r);

$extended = false;
$hexIDSQL = getHexIDSQL($table);

#$r = getRecords($table, $userid, array("$field"), " $field LIKE '%$cue%' OR tracker.trackID='$cue' OR $hexIDSQL='$cue' ", "$table.$field");
$r = getRecords($table, $userid, array("$field"), " $field LIKE '%$cue%' ", "$table.$field", 0, '', 1);

if (isset($_POST['extended']) && $_POST['extended'])
{
    $extended = true;
    $r = getRecords($table, $userid, array('tracker.trackID', "$field"), " $field LIKE '%$cue%' OR tracker.trackID='$cue'  OR $hexIDSQL='$cue'  ", "$table.$field", 0, '');
} 

#print_r($r);
foreach ($r as $row)
{
	$entry = $row[$field];
	if ($extended) {
		//$results = print_r($r, true);
		echo "<li title=\"$results\"><span>$entry</span><span style=\"display:none\">${row['trackID']}</span></li>";
	}
	else {
	    print "<li>$entry</li>\n";
	}
}

?>
