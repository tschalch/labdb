<?php
$title = "List of Storage Boxes";
$listItem = "project";  #used form javascript message box.
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "projects";
$columns = ['projects.name', 'projects.description', 'tracker.trackID', 'tracker.owner', 'tracker.permOwner'];
# optional join expressions to connect to more data
$join = "";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="projects.name";
#End SQL parameters

#array of query field => table heading
$fields = ['Name' => 'Name', 'Description' => 'Description'];

#toggle Project combobox on and off
$noProjectFilter = False;
#toggle user/group filters on and off
$noUserFilter = False;
#Set Menu items
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
	$edit = 0;
	$permissions = [$row['permOwner'], $row['permGroup'], $row['permOthers']];
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1) $edit = 1;
	echo "<tr class=\"lists data-row\" data-record_id=\"$id\">";
	echo listActions($id);
	echo "<td class=\"lists\" width=\"30%\">
		<a href=\"editEntry.php?id=$id&amp;mode=display\">${row['name']}</a></td>";
	echo "<td class=\"lists\" width=\"50%\">${row['description']}</td>";
	echo "</tr>";
	echo "<tr class=\"menu\" id=\"menu_$id\"></tr>";
}
print "</table>";
\PHP?>
