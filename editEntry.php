<?php
header("Cache-Control: max-age=0, must-revalidate");
include_once("functions.php");
include_once("accesscontrol.php");
$id = $_GET['id'];
$edit = true;
$form = $_GET['form'];
if ($form){
	include($form.".php");
} else {
	// if no form is given, assume the id is a tracker ID and get the form through tracker
	$r = getRecord($id, $userid, $groups);
	#print "record: $r";
	if ($r){
		include($r['form'].".php");
	} else {
		print "<H1>Record not found !</h1>";
	}
}
#if ($mode == 'display') saveURI($_SERVER['REQUEST_URI']);
if ($mode == "display"){
	print "<div style=\"margin-bottom: 2em;\" class=\"formRow\"><div class=\"formLabel\">";
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1){
		print "	<a href=\"editEntry.php?id=$id&mode=modify\">
			<span class=\"button\">Edit record<span></a>";
	}
	print "</div>";
}
include("footer.php");
?>


