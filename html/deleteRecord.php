<?php
$id = $_GET['id'];
include("functions.php");
include_once("accesscontrol.php");
deleteRecord($id, $userid);
?>