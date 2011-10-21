<?php
include("header.php");
include("accesscontrol.php");
include_once("functions.php");
include("extractData.php");
#print_r($data);
#$relationTable = $_POST["relations"];
#$relationsIDField = $_POST["relationsIDField"];
#$maintable = $_POST['maintable'];
$ID=0;

foreach ($data as $table => $datasets){
	$permissions = array(2,0,0);
	switch ($table){
		case 'inventory':
		$permissions = array(2,2,1);
		break;
		case 'locations':
		$permissions = array(2,1,1);
		break;
	}
	if ($table == 'connections' and $trackID){
		foreach ($datasets as $dataset){
			$dataset['belongsTo'] = $trackID;
			$query = getInsertQuery($dataset, 'connections', '');
			#print $query;
			pdo_query($query);
		}
		continue;
	}
	foreach ($datasets as $dataset){
		if (sizeof($_FILES) > 0){
			foreach ($_FILES as $file){
				UploadFiles($file);
			}
		}
		$trackID = newRecord($table, $dataset, $userid, $permissions);
		$title = "Data entered successfully (New Record ID: $trackID)";
	}
}

echo "<h2>$title</h2>";
$backURI = getLastView();
print "<a href=\"$backURI\">Back to previous View</a>.";
include("footer.php");
?>
