<?php
$title = "List of Storage Boxes";
$listItem = "box";  #used form javascript message box.
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "boxes";
$columns = ['boxes.name', 'boxes.description', 'boxes.location', 'tracker.trackID', 'tracker.owner', 'tracker.permOwner'];
# optional join expressions to connect to more data
$join = "";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="boxes.name";
#End SQL parameters

#array of query field => table heading
$fields = ['Name' => 'Name', 'Location' => 'Location', 'Description' => 'Description'];

#toggle Project combobox on and off
$noProjectFilter = True;
#toggle user/group filters on and off
$noUserFilter = True;
#Set Menu items

if (!isset($where)) $where = "";
if (array_key_exists('location', $_GET)) {
	$location = $_GET['location'];
	$where .= " `$table`.location=$location ";
}

?>
<script type="text/javascript" >
    var menu_items = ["new","edit","delete"];
</script>

<?php
include("listhead.php");
#print_r($rows);
foreach ($rows as $row) {
	#print "row: ";print_r($row); print "<br/>";
	$id = $row['trackID'];
	echo "<tr class=\"lists data-row\" data-record_id=\"$id\">";
	echo listActions($id, null );
	#echo "<td class=\"lists\" width=\"20%\">
	#	<a href=\"list.php?list=listVials&box=$id\">${row['name']}</a></td>";
	echo "<td class=\"lists\" width=\"20%\">
		<a href=\"editEntry.php?id=$id&amp;mode=display\">${row['name']}</a></td>";
	$locID = $row['location'];
	$location = getRecord($locID, $userid);
	echo "<td class=\"lists\" width=\"20%\"><a href=\"editEntry.php?id=$locID&mode=display\">${location['name']}</a></td>";
	echo "<td class=\"lists\" width=\"30%\">${row['description']}</td>";
	echo "</tr>";
	echo "<tr class=\"menu\" id=\"menu_$id\"></tr>";
}
listProcessor([1, 2, 3, 7]);
print "</table>";
?>
