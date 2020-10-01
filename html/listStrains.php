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
$defaultOrder ="strains.id DESC";
#End SQL parameters

#array of query field => table heading
$fields = array('trackID' => 'dbID',
		'hexID' => 'Strain',
		'strains.name' => 'Name',
		'organism' => 'Organism',
		'strain' => 'Genotype',
		'strains.description' => 'Description',
		'1' => 'Building blocks');

#toggle Project combobox on and off
$noProjectFilter = False;
#toggle user/group filters on and off
$noUserFilter = False;
#Set Menu items
?>
<script type="text/javascript" >
    var menu_items = ["new","edit", "vial", "delete"];
</script>

<?php

include("listhead.php");

$plasmids = array();
$pcols = array('plasmids.name', 'tracker.trackID');
$plds = getRecords("plasmids", $userid, array(), $pcols);
if($plds){
	foreach($plds as $plasmid){
		$plasmids[$plasmid['trackID']] = $plasmid['name'];
	}
}
#print_r($rows);
foreach ($rows as $row) {
	#print "row: ";print_r($row); print "<br/>";
	$dbid = $row['trackID'];
	$strainid = $row['hexID'];
	$conxs = getConnections($dbid);
	$edit = 0;
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($dbid, $userid)>1) $edit = 1;
	echo "<tr class=\"lists data-row\" data-record_id=\"$dbid\">";
	echo listActions($dbid, $strainid);
	print "<td class=\"lists dbid\" width=\"1%\" align=\"RIGHT\">$dbid</td>";
	print "<td class=\"lists\" width=\"1%\" >$strainid</td>";
	print "<td class=\"lists\" width=\"10%\">
	      <a href=\"editEntry.php?id=$dbid&amp;mode=display\">${row['name']}</a></td>";
	echo "<td class=\"lists\" width=\"20%\">${row['organism']}</td>";
	echo "<td class=\"lists\" width=\"20%\">${row['strain']}</td>";
	echo "<td class=\"lists\" width=\"40%\">${row['description']}</td>";
	echo "<td class=\"lists\" width=\"10%\">";
	printPlasmids($dbid, $conxs);
	echo "</td>";
	echo "</tr>";
	echo "<tr class=\"menu\" id=\"menu_$dbid\"></tr>";
	$i++;
}
listProcessor(array(2,3,7));

function printPlasmids($sID, $conxs){
	if (!$conxs) return;
	global $userid, $plasmids;
	$n = sizeof($conxs);
	$i = 1;
	$out = "";
	foreach ($conxs as $c){
		if (array_key_exists('record', $c)){
			$trackID = $c['record'];
			$parent = getRecord($trackID, $userid);
			$fname = $parent['name'];
			$type = array_key_exists('type',$parent) ? $parent['type']: $parent['st_name'];
			$out .= "<a title=\"$type\" href=\"editEntry.php?id=$trackID&mode=display\">$fname</a>";
			if($i < $n) $out .= ", ";
			$i++;
		}
	}
	print $out;
}
?>
</table>

