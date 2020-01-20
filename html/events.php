<?php

require_once('accesscontrol.php');
require_once('bdd.php');

$resource = isset($_GET['resource']) ? $_GET['resource']: NULL;
$start = isset($_GET['start']) ? $_GET['start']: NULL;
$end = isset($_GET['end']) ? $_GET['end']: NULL;

$sql = "SELECT id, title, description, start, end, color FROM events WHERE resource='$resource';";

$req = $bdd->prepare($sql);
$req->execute();

$events = $req->fetchAll();

print json_encode($events);
?>
