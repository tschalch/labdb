<?php

require_once('accesscontrol.php');
require_once('functions.php');

$resource = $_GET['resource'] ?? NULL;
$start = $_GET['start'] ?? NULL;
$end = $_GET['end'] ?? NULL;

$sql = "SELECT id, title, description, start, end, color FROM events WHERE resource=:resource;";
$events = pdo_query($sql, [':resource' => $resource]);

print json_encode($events, JSON_THROW_ON_ERROR);
?>
