<?php
//include("header.php");
include("accesscontrol.php");
include_once("functions.php");
include("extractData.php");

$id = $_POST['id'];
//print_r($data);
//print_r($FILES);

foreach ($data as $table => $datasets){
	if ($table == 'none') continue;
	if ($table == 'connections'){
		$cnxs = getConnections($id);
		foreach ($datasets as $dataset){
			//print_r($dataset);
			if($dataset['connID']>-1){
				$uq = getUpdateQuery($dataset, 'connections', $dataset['connID']);
				pdo_query($uq);
				$newCnxs = array();
				foreach($cnxs as $con){
					if ($con['connID']!=$dataset['connID']) $newCnxs[] = ($con);
				}
				$cnxs = $newCnxs;
			} else {
				unset($dataset['connID']);
				$dataset['belongsTo'] = $id;
				$query = getInsertQuery($dataset, 'connections', '');
				//print "$query\n";
				pdo_query($query);
			}
		}
		foreach($cnxs as $con){
			$qd = "DELETE FROM `connections` WHERE connID=${con['connID']}";
			pdo_query($qd);
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
		} else {
			$id = newRecord($table, $dataset, $userid, $permissions);
		}
	}
}
print "$id";
?>
