<?php
$title = "List of Plasmids";
$listItem = "plasmid";  #used form javascript message box.
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "plasmids";
$columns = array('plasmids.name', 'plasmids.description', 'plasmids.generation','plasmids.sequence',
		 'tracker.trackID', 'tracker.owner','tracker.permOwner'
		 );
# optional join expressions to connect to more data
$join = "";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="plasmids.name";
#End SQL parameters

#array of query field => table heading
$fields = array('trackID' => 'ID',
		'Name' => 'Name',
		'Description' => 'Description',
		'1' => 'Backbone',
		'2' => 'Genes',
		'sequence' => 'DNA Sequence',);

#toggle Project combobox on and off
$noProjectFilter = False;
#toggle user/group filters on and off
$noUserFilter = False;
#Set Menu items
?>
<script type="text/javascript" >
    var menu_items = ["new","edit", "fasta", "vial", "delete"];
</script>

<?php
include("listhead.php");
$fragments = array();
$fcols = array('fragments.name','fragments.type','tracker.trackID');
$frags = getRecords("fragments", $userid, $fcols);
if($frags){
	foreach($frags as $frag){
		$fragments[$frag['trackID']] = array('name' => $frag['name'],'type' => $frag['type']);
	}
}
#print_r($rows);
foreach ($rows as $row) {
	#print "row: ";print_r($row); print "<br/>";
	$id = $row['trackID'];
	$conxs = getConnections($id);
	$edit = 0;
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1){
		$edit = 1;
	}
	echo "<tr class=\"lists data-row\" data-record_id=\"$id\">";
	echo listActions($id );
	print "<td class=\"lists\" width=\"1%\" align=\"RIGHT\">$id</td>";
	echo "<td class=\"lists\" width=\"10%\">
		<a href=\"editEntry.php?id=$id&mode=display\">${row['name']}</a></td>";
	echo "<td class=\"lists\" width=\"30%\">${row['description']}</td>";
	echo "<td class=\"lists\" width=\"10%\">";
		printFragments('backbone', $conxs);
	echo "</td>";
	echo "<td class=\"lists\" width=\"20%\">";
		printFragments('gene',$conxs);
	echo "</td>";
	echo "<td class=\"lists seq\" width=\"10%\"><a href=\"sequence.php?table=plasmids&field=sequence&id=$id\">".CountATCG($row['sequence'])." bp</a></td>";
	echo "</tr>";
	echo "<tr class=\"menu\" id=\"menu_$id\"></tr>";
}

function printFragments($typ, $conxs){
	if (!$conxs) return;
	global $userid, $fragments;
	$typeCons = array();
	foreach ($conxs as $c){
		 $trackID = $c['record'];
		 if ($fragments[$trackID]['type'] == $typ) $typeCons[]=$c;
	}
	$c = 0;
	$out = "";
	foreach ($typeCons as $c2){
		 $trackID = $c2['record'];
		 $fname = $fragments[$trackID]['name'];
		 $out .= "<a href=\"editEntry.php?id=$trackID&mode=display\">$fname</a>";
		 $c++;
		 if($c < sizeof($typeCons)) $out .= ", ";
	}
	print $out;
}
listProcessor(array(1,2,3,7));
?>
</table>

