<?php
$title = "List of Bookable Resources";
$listItem = "resource";  #used form javascript message box.
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "resources";
$columns = array('resources.name', 'resources.description', 'resources.location', 'resources.active',
		 'tracker.trackID', 'tracker.owner','tracker.permOwner'
		 );
# optional join expressions to connect to more data
$join = "";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="resources.name";
#End SQL parameters

#array of query field => table heading
$fields = array('Name' => 'Name',
'Location' => 'Location',
'Description' => 'Description',
'active' => 'active');

#toggle Project combobox on and off
$noProjectFilter = True;
#toggle user/group filters on and off
$noUserFilter = True;
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
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1) $edit = 1;
	echo "<tr class=\"lists data-row\" data-record_id=\"$id\">";
	echo listActions($id, Null);
	echo "<td class=\"lists\" >
		<a href=\"editEntry.php?id=$id&mode=display\">${row['name']}</a></td>";
	echo "<td class=\"lists\" >${row['location']}</td>";
	echo "<td class=\"lists\" >${row['description']}</td>";
  $checked = $row['active'] ? "checked" : "";
	echo "<td class=\"lists\" ><input type=\"checkbox\" $checked disabled /></td>";
	echo "</tr>";
	echo "<tr class=\"menu\" id=\"menu_$id\"></tr>";
}
listProcessor(array(2,3));
print "</table>";
?>
