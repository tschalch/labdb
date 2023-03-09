<?php
$title = "Lab Logbook";
$listItem = "logbook entry";  #used form javascript message box.
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "logbook";
$columns = ['logbook.date', 'logbook.instrumentID', 'logbook.columnID', 'logbook.sample', 'logbook.buffer', 'logbook.date', 'logbook.bypresbef', 'logbook.bypresaf', 'logbook.colpresbef', 'logbook.colpresaf', 'logbook.storage', 'logbook.remarks', 'logbook.user', 'user.userid', 'tracker.trackID'];

# optional join expressions to connect to more data
//$join = "LEFT JOIN inventory ON logbook.instrumentID=locations.id ";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="logbook.date DESC";

#End SQL parameters

#array of query field => table heading
$fields = ['date' => 'Date', 'instrumentID' => 'Instrument', 'columnID' => 'Column No', 'user' => 'User', 'bypresaf' => 'System Pressure', 'colpresaf' => 'Column Pressure', 'remarks' => 'Remarks'];

#toggle Project combobox on and off
$noProjectFilter = True;
#toggle user/group filters on and off
$noUserFilter = True;
#Set Menu items
?>
<script type="text/javascript" >
    var menu_items = ["new","edit","delete"];
</script>

<?php

include("listhead.php");

# get instrument names
$fcols = ['inventory.name', 'tracker.trackID'];
$instrs = getRecords("inventory", $userid, [], $fcols, ' inventory.type!=0 ');
$instruments = [];
if($instrs){
	foreach($instrs as $instrument){
		$instruments[$instrument['trackID']] = ['name' => "${instrument['name']} (${instrument['trackID']})"];
	}
}

foreach ($rows as $row) {
	#print "row: ";print_r($row); print "<br/>";
	$id = $row['trackID'];
	$edit = 0;
	$permissions = [9, 9, 9];
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1) $edit = 1;
	if($row['status']==4 and $status != 4){
		print "<tr class=\"lists data-row\" data-record_id=\"$id\" style=\"background-color: #DCDCDC;\">";
	} elseif ($row['status']==1) {
		print "<tr class=\"lists data-row\" data-record_id=\"$id\" style=\"background-color: #FFF0F5;\">";
	} else {
		print "<tr class=\"lists data-row\" data-record_id=\"$id\">";
	}
	echo listActions($id, null);
	echo "<td class=\"lists\" width=\5%\"><a href=\"editEntry.php?id=$id&mode=display\">${row['date']}</a></td>";
	$instrument = $instruments[$row['instrumentID']]['name'];
	echo "<td class=\"lists\" width=\"15%\">${instrument}</td>";
	$column = $instruments[$row['columnID']]['name'];
	echo "<td class=\"lists\" width=\15%\">${column}</td>";
	echo "<td class=\"lists\" width=\10%\">${row['user']}</td>";
	echo "<td class=\"lists\" width=\"10%\">${row['bypresaf']}</td>";
	echo "<td class=\"lists\" width=\"10%\">${row['colpresaf']}</td>";
	echo "<td class=\"lists\" width=\"15%\">${row['remarks']}</td>";
	echo "</tr>";
	echo "<tr class=\"menu\" id=\"menu_$id\"></tr>";
	$i++;
}

listProcessor([3]);

?>

</table>


