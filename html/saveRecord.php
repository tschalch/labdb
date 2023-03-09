<?php
//include("header.php");
include("accesscontrol.php");
include_once("functions.php");
include("extractData.php");
include('config.php');

$id = $_POST['id'];
//print_r($data);
//print_r($FILES);

if (!isset($permissions)) $permissions = '';

foreach ($data as $table => $datasets){
    if ($table == 'none') continue;
    if ($table == 'connections'){
        $cnxs = getConnections($id);
        foreach ($datasets as $dataset){
            //print_r($dataset);
            if($dataset['connID']>-1){
                [$uq, $values] = getUpdateQuery($dataset, 'connections', $dataset['connID']);
                //print "$uq\n";
                pdo_query($uq, $values);
                $newCnxs = [];
                foreach($cnxs as $con){
                    if ($con['connID']!=$dataset['connID']) $newCnxs[] = ($con);
                }
                $cnxs = $newCnxs;
            } else {
                unset($dataset['connID']);
                $dataset['belongsTo'] = $id;
                [$query, $values] = getInsertQuery($dataset, 'connections', '');
                //print "$query\n";
                pdo_query($query, $values);
            }
        }
        foreach($cnxs as $con){
            $qd = "DELETE FROM `connections` WHERE `connID`=:connid";
            pdo_query($qd, [':connid'=>$con['connID']]);
        }
        continue;
    }
    foreach ($datasets as $dataset){
        if (sizeof($_FILES) > 0){
            foreach ($_FILES as $file){
                UploadFiles($file);
            }
        }
        //print "id:$id,";
        if ($id){
            updateRecord($id, $dataset, $userid, Null);
            if($table == "inventory" && $dataset['status'] == 1 && $useremail != $adminEmail){
                $message = "Hi!

Item on order has been been changed:

-> New item: ${dataset['name']} $labdbUrl/editEntry.php?id=$id&mode=modify
-> Items on order: $labdbUrl/list.php?list=listItems&status=1

labdb ";

                $sendmailparams = "-r $adminEmail";
                $headers = ['From' => "From: $adminEmail", 'Reply-To' => "Reply-To: $adminEmail", 'X-Mailer' => 'X-Mail: PHP/' . phpversion()];
                #mail("$adminEmail","Item on order has been changed",
                #    $message, implode("\n", $headers), $sendmailparams);
            }
        } else {
            $id = newRecord($table, $dataset, $userid);
            if($table == "inventory" && $dataset['status'] == 1){
                $message = "Hi!

A new item has been been put on order:

-> New item: ${dataset['name']} $labdbUrl/editEntry.php?id=$id&mode=modify
-> Items on order: $labdbUrl/list.php?list=listItems&status=1

labdb ";

                $sendmailparams = "-r $adminEmail";
                $headers = ['From' => "From: $adminEmail", 'Reply-To' => "Reply-To: $adminEmail", 'X-Mailer' => 'X-Mail: PHP/' . phpversion()];
                mail("$adminEmail","New item on order",
                    $message, implode("\n", $headers), $sendmailparams);
            }
        }
    }
}

if(isset($id)){
  $r = getRecord($id, $userid);
  console_log("id, userid for getRecord: $id, $userid: ". print_r($r, true));
  if ($r['hexID'] != ""){
    $hexid = $r['hexID'];
    print "{\"id\":$id, \"hexid\":\"$hexid\"}";
  } else {
    print "{\"id\":\"$id\"}"; 
  }
}
?>
