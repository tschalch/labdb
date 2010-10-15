<?php
include("functions.php");
include("accesscontrol.php");
$new = true;
$id = $_GET['id'];
if($id){
	$row = getRecord($id, $userid, $groups);
	$form1 = $row['form'];
	$duplicate = true;
} else {
	$form1 = $_GET['form'];
}
include($form1.".php");
include("footer.php");
?>


