<?php
$title = "List of Lab Item Locations";
$listItem = "location";  #used form javascript message box.
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "locations";
$columns = array('locations.name', 'locations.description',
		 'tracker.trackID', 'tracker.owner','tracker.permOwner'
		 );
# optional join expressions to connect to more data
$join = "";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="locations.name";
#End SQL parameters

#array of query field => table heading
$fields = array('Name' => 'Name',
		'Description' => 'Description');

#toggle Project combobox on and off
$noProjectFilter = True;
#toggle user/group filters on and off
$noUserFilter = True;

include("listhead.php");
#print_r($rows);
foreach ($rows as $row) {
	#print "row: ";print_r($row); print "<br/>";
	$id = $row['trackID'];
	$edit = 0;
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1) $edit = 1;
	echo listActions($id, $edit, False);
	echo "<td class=\"lists\" width=\"20%\">
		<a href=\"editEntry.php?id=$id&mode=display\">${row['name']}</a></td>";
	echo "<td class=\"lists\" width=\"70%\">${row['description']}</td>";
	echo "</tr>";
	$i++;
}
listProcessor(array(2,3));
print "</table>";
php?>
