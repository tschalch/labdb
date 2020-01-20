<?php

require_once('accesscontrol.php');
require_once('bdd.php');

$str_json = file_get_contents('php://input');
$event = json_decode( $str_json, true );

if (isset($_POST['delete']) && isset($_POST['id'])){

  $id = $_POST['id'];

  $sql = "SELECT user FROM events WHERE id = $id";

  $query = $bdd->prepare( $sql );
  if ($query == false) {
    print_r($bdd->errorInfo());
    die ('SQL preparation error');
  }
  $res = $query->execute();
  if ($res == false) {
    print_r($query->errorInfo());
    print($sql);
    die ('SQL execution error');
  }
  $user = $query->fetch();
  if ($userid === $user['user']){
    $sql = "DELETE FROM events WHERE id = $id";
  }

} elseif (isset($event['id']) || isset($_POST['id'])) {


$fields = array("id","start","end","color","title","description");

foreach($fields as $field){
  if(isset($_POST[$field])){
    $value  = $_POST[$field];
    $event[$field] = $value;
  }
}

$numItems = count($event);
$i = 0;
foreach($event as $f => $value){
  $sqlupdate .= "$f='$value'";
  if(++$i < $numItems) {
    $sqlupdate .= ", ";
  }
}

print_r($event);
print $sqlupdate;
$id = $event['id'];

$sql = "UPDATE events SET $sqlupdate WHERE id='$id'";

}

$query = $bdd->prepare( $sql );
if ($query == false) {
  print_r($bdd->errorInfo());
  die ('SQL preparation error');
}
$res = $query->execute();
if ($res == false) {
  print_r($query->errorInfo());
  print($sql);
  die ('SQL execution error');
}

header('Location: '.$_SERVER['HTTP_REFERER']);

?>
