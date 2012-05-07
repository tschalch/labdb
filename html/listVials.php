<?php
$title = "List of Vials";

$listItem = "vial";  #used form javascript message box.
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "vials";
$columns = array('vials.name', 'vials.description', 'vials.position',
                 'vials.sID', 'vials.boxID', 'vials.exists', 'vials.date',
                 'trackBoxes.boxName',
		 'tracker.trackID', 'tracker.owner','tracker.permOwner'
		 );
# optional join expressions to connect to more data
$join = "LEFT JOIN trackBoxes ON vials.boxID=trackBoxes.tID ";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="vials.position";
#End SQL parameters

#array of query field => table heading
$fields = array('vials.name' => 'Label',
		'position' => 'Pos',
                'vials.date' => 'Date',
                'description' => 'Description',
                'sID' => 'Content',
                'boxID' => 'Box',
                'exists' => 'On Stock');

#toggle Project combobox on and off
$noProjectFilter = False;
#toggle user/group filters on and off
$noUserFilter = False;

#filter by box
$box = $_GET['box'];
$refid = $_GET['ref'];
if ($box) $where .= " boxID=$box ";
if ($refid) $where .= " ($table.sID='$refid')";

include("listhead.php");

foreach ($rows as $row) {
	#print "row: ";print_r($row); print "<br/>";
	$id = $row['trackID'];
	$edit = 0;
	$edit = 0;
	$permissions = array(
		$row['permOwner'],
		$row['permGroup'],
		$row['permOthers']);
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1) $edit = 1;
	echo listActions($id, array("new","edit", "delete"));
	($row['name'])? $vname = $row['name'] : $vname = 'no name';
	echo "<td class=\"lists\" width=\"15%\"><a href=\"editEntry.php?id=$id&mode=display\">$vname</a></td>";
	echo "<td class=\"lists\" width=\3%\">${row['position']}</td>";
	echo "<td class=\"lists\" width=\10%\">${row['date']}</td>";
	echo "<td class=\"lists\" width=\"25%\">${row['description']}</td>";
	echo "<td class=\"lists\" width=\"15%\">";
	if ($row['sID']){
		$sample = getRecord($row['sID'], $userid, $groups);
  		print "<a href=\"editEntry.php?id=${row['sID']}&mode=display\"> ${sample['name']}</a>";
		}
	echo "</td>";
	echo "<td class=\"lists\" width=\15%\">\n";
	echo "<a href=\"editEntry.php?id=${row['boxID']}&amp;mode=display\"> ${row['boxName']}</a>\n";
	echo "</td>\n";
	echo "<td class=\"lists\" width=\5%\">${row['exists']}</td>";
	echo "</tr>";
	$i++;
}
listProcessor(array(2,3));
?>

</table>


