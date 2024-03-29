<?php
$title = "List of Vials";

$listItem = "vial";  #used form javascript message box.
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "vials";
$columns = ['vials.name', 'vials.description', 'vials.position', 'vials.sID', 'vials.boxID', 'vials.exists', 'vials.date', 'trackboxes.boxName', 'tracker.trackID', 'tracker.owner', 'tracker.permOwner'];
# optional join expressions to connect to more data
$join = "LEFT JOIN trackboxes ON vials.boxID=trackboxes.tID ";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="IF(vials.position REGEXP '^[A-Z]', CONCAT( LEFT(vials.position, 1), LPAD(SUBSTRING(vials.position, 2), 20, '0')), CONCAT( '@', LPAD(vials.position, 20, '0')))";
#End SQL parameters

#array of query field => table heading
$fields = ['vials.name' => 'Label', 'position' => 'Pos', 'vials.date' => 'Date', 'description' => 'Description', 'sID' => 'Content', 'boxID' => 'Box', 'exists' => 'On Stock'];

#toggle Project combobox on and off
$noProjectFilter = True;
#toggle user/group filters on and off
$noUserFilter = True;

#filter by box

if (!isset($where)) $where = "";
if (array_key_exists('box', $_GET)) {
	$box = $_GET['box'];
	$where .= " boxID=$box ";
}
if (array_key_exists('ref', $_GET)) {
	$refid = $_GET['ref'];
	$where .= " ($table.sID='$refid')";
}
#Set Menu items
?>
<script type="text/javascript" >
    var menu_items = ["new","edit","delete"];
</script>

<?php

include("listhead.php");

foreach ($rows as $row) {
	#print "row: ";print_r($row); print "<br/>";
	$id = $row['trackID'];
	$edit = 0;
	$edit = 0;
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1) $edit = 1;
	echo "<tr class=\"lists data-row\" data-record_id=\"$id\">";
	echo listActions($id, null);
	($row['name'])? $vname = $row['name'] : $vname = 'no name';
	echo "<td class=\"lists\" width=\"15%\"><a href=\"editEntry.php?id=$id&mode=display\">$vname</a></td>";
	echo "<td class=\"lists\" width=\3%\">${row['position']}</td>";
	echo "<td class=\"lists\" width=\10%\">${row['date']}</td>";
	echo "<td class=\"lists\" width=\"25%\">${row['description']}</td>";
	echo "<td class=\"lists\" width=\"15%\">";
	if ($row['sID']){
		$sample = getRecord($row['sID'], $userid);
  		print "<a href=\"editEntry.php?id=${row['sID']}&mode=display\"> ${sample['name']}</a>";
		}
	echo "</td>";
	echo "<td class=\"lists\" width=\15%\">\n";
	echo "<a href=\"editEntry.php?id=${row['boxID']}&amp;mode=display\"> ${row['boxName']}</a>\n";
	echo "</td>\n";
	echo "<td class=\"lists\" width=\5%\">${row['exists']}</td>";
	echo "</tr>";
	echo "<tr class=\"menu\" id=\"menu_$id\"></tr>";
}
listProcessor([2, 3, 7]);
?>

</table>


