<?php

require_once('accesscontrol.php');
require_once('bdd.php');

$str_json = file_get_contents('php://input');
$event = json_decode( $str_json, true );
#print_r($event);

$sql = "INSERT INTO events (title,color,start,end,user,resource) VALUES ('${event['title']}', '${event['color']}', '${event['start']}', '${event['end']}', '${event['user']}', '${event['resource']}')";
#print $sql;

$req = $bdd->prepare($sql);
$req->execute();

print $bdd->lastInsertId();
?>
