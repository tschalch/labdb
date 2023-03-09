<?php

require_once('accesscontrol.php');
require_once('functions.php');

$str_json = file_get_contents('php://input');
$event = json_decode( $str_json, true, 512, JSON_THROW_ON_ERROR );

if (isset($_POST['delete']) && isset($_POST['id'])){

  $id = $_POST['id'];
  $vars = [':id' => $id];

  $sql = "SELECT user FROM events WHERE id=:id";
  $event = pdo_query($sql, [':id'=>$id])[0];

  if (isset($event['user']) && $userid === $event['user']){
    $sql = "DELETE FROM events WHERE id=:id";
  }

} elseif (isset($event['id']) || isset($_POST['id'])) {


  $fields = ["id", "start", "end", "color", "title", "description"];

  $id = $event['id'];
  $vars = [':id' => $id];
  foreach($fields as $field){
    if(isset($_POST[$field])){
      $value_fields[$field] = $_POST[$field];
    }
  }

  $numItems = is_countable($value_fields) ? count($value_fields) : 0;
  $i = 0;
  foreach($value_fields as $f => $value){
    $sqlupdate .= "$f=:$f ";
    $vars[":$f"] = $value;
    if(++$i < $numItems) {
      $sqlupdate .= ", ";
    }
  }


  $sql = "UPDATE events SET $sqlupdate WHERE id=:id";

}

pdo_query($sql, $vars);

header('Location: '.$_SERVER['HTTP_REFERER']);

?>
