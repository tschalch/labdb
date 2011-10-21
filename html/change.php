<?php
//header('Location: http://localhost/lab_database/list.php?list=listPlasmids');
//include("header.php");
//include("accesscontrol.php");
include_once("functions.php");
include("extractData.php");
$relationTable = $_POST["relations"];
$relationsIDField = $_POST["relationsIDField"];
$id = $_GET['id'];
#print_r($data); 
foreach ($data as $table => $datasets){
	if ($table == 'none') continue;
	if ($table == 'connections'){
		$cnxs = getConnections($id);
		foreach ($datasets as $dataset){
			//print_r($dataset);
			if($dataset['connID'] > -1){
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
		updateRecord($id, $dataset, $userid, $groups);
		#keepTrack($dataset['id'], $_POST["project"], $_POST["subProject"], $userid);
	}
	$title = "Data changed sucessfully.";
}
echo "<h2>$title</h2>";
//$backURI = getLastView();
//print "<a href=\"$backURI\">Back to previous View</a>.";
//include("footer.php");
?>