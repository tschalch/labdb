<?php

require_once('accesscontrol.php');
require_once('functions.php');

$str_json = file_get_contents('php://input');
$event = json_decode( $str_json, true, 512, JSON_THROW_ON_ERROR );
#print_r($event);

$sql = "INSERT INTO events (title,color,start,end,user,resource) VALUES (:title, :color, :start, :end, :user, :resource)";
$vars = [':title' => $event['title'], ':color' => $event['color'], ':start' => $event['start'], ':end' => $event['end'], ':user'=>$event['user'], ':resource'=>$event['resource']];

print pdo_query($sql, $vars);
?>
