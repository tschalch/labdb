<?php
$title = "List of Strains";
$listItem = "strain";  #used form javascript message box.
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "strains";
$columns = array('strains.name', 'strains.description', 'strains.organism',
		 'strains.strain',
		 'tracker.trackID', 'tracker.owner','tracker.permOwner'
		 );
# optional join expressions to connect to more data
$join = "";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="strains.name";
#End SQL parameters

#array of query field => table heading
$fields = array('trackID' => 'ID',
		'strains.name' => 'Name',
		'organism' => 'Organism',
		'strain' => 'Strain',
		'strains.description' => 'Description',
		'1' => 'Plasmids');

#toggle Project combobox on and off
$noProjectFilter = False;
#toggle user/group filters on and off
$noUserFilter = False;

include("listhead.php");

$plasmids = array();
$pcols = array('plasmids.name', 'tracker.trackID');
$plds = getRecords("plasmids", $userid, $pcols);
if($plds){
	foreach($plds as $plasmid){
		$plasmids[$plasmid['trackID']] = $plasmid['name'];
	}
}
#print_r($rows);
foreach ($rows as $row) {
	#print "row: ";print_r($row); print "<br/>";
	$id = $row['trackID'];
	$conxs = getConnections($id);
	$edit = 0;
	$permissions = array(
		$row['permOwner'],
		$row['permGroup'],
		$row['permOthers']);
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1) $edit = 1;
	echo listActions($id, array("new","edit", "vial", "delete") );
	print "<td class=\"lists\" width=\"1%\" align=\"RIGHT\">$id</td>";
	print "<td class=\"lists\" width=\"10%\">
	      <a href=\"editEntry.php?id=$id&amp;mode=display\">${row['name']}</a></td>";
	echo "<td class=\"lists\" width=\"30%\">${row['organism']}</td>";
	echo "<td class=\"lists\" width=\"30%\">${row['strain']}</td>";
	echo "<td class=\"lists\" width=\"20%\">${row['description']}</td>";
	echo "<td class=\"lists\" width=\"10%\">";
	printPlasmids($id, $conxs);
	echo "</td>";
	echo "</tr>";
	$i++;
}
listProcessor(array(2,3));

function printPlasmids($sID, $conxs){
	if (!$conxs) return;
	global $userid, $plasmids;
	$n = sizeof($conxs);
	$i = 1;
	foreach ($conxs as $c){
		 $trackID = $c['record'];
		 $fname = $plasmids[$trackID];
		 $out .= "<a href=\"editEntry.php?id=$trackID&mode=display\">$fname</a>";
		 if($i < $n) $out .= ", ";
		 $i++;
	}
	print $out;
}
?>
</table>

