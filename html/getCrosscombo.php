<?php
include_once("accesscontrol.php");
include_once("functions.php");

$fcounter = $_GET['fcounter'];
$mode = $_GET['mode'];
$table = $_GET['table'];
$type = $_GET['type'];
$start = $_GET['start'];
$end = $_GET['end'];
$dir = $_GET['dir'];
$id = $_GET['id'];
if ($id != ""){
    $connection = ['connID' => -1, 'record'=>$id, 'start'=>$start, 'end'=>$end, 'direction'=>$dir];
}

#getCrossCombobox($connection, $table, $type, $fcounter, $mode, $userid);
getCrossAutoselectField($connection, $table, $type, $fcounter, $mode, $userid);

?>
