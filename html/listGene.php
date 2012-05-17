<?php
$title = "List of Genes";
$listItem = "building block";  #used form javascript message box.
if (array_key_exists('category', $_GET)){
	$category = $_GET['category'];
} else {
	$category = 'gene';
}
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "fragments";
$columns = array('fragments.name', 'fragments.description', 'fragments.proteinSequence',
		 'fragments.resistance', 'fragments.origin', 'fragments.DNASequence',
		 'fragments.PCRoligo1', 'fragments.PCRoligo2', 'fragments.PCRtemplate',
		 'fragments.reaction', 'fragments.type',
		 'tracker.trackID', 'tracker.owner','tracker.permOwner'
		 );
# optional join expressions to connect to more data
$join = "";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="fragments.name";
#End SQL parameters

#array of query field => table heading
$fields = array('trackID' => 'ID',
		'name' => 'Name',
		'description' => 'Description',
		'1' => 'DNA Sequence',
		'2' => 'Protein'
);

if($category == 'backbone'){
	$fields = array('trackID' => 'ID',
			'name' => 'Name',
			'description' => 'Description',
			'resistance' => 'Resistance Marker',
			'origin' => 'Origin of Replication',
			'3' => 'DNA Sequence',
	);
}

if($category == 'PCR'){
	$fields = array('trackID' => 'ID',
			'name' => 'Name',
			'PCRoligo1' => 'Oligo 1',
			'PCRoligo2' => 'Oligo 2',
			'3' => "Tm1 / Tm2",
			'PCRtemplate' => 'Template',
			'4' => 'Length',
	);
	if (array_key_exists('ref', $_GET)){
		$refid = $_GET['ref'];
		$where .= " ($table.PCRoligo1 = $refid OR $table.PCRoligo2 = $refid) ";
	}
}

#toggle Project combobox on and off
$noProjectFilter = False;
#toggle user/group filters on and off
$noUserFilter = False;

#deal with different types of fragments
$categories = array('gene'=>'Genes', 'PCR' => 'PCR Products', 'backbone' => 'Plasmid Backbones');

include("listhead.php");
#
if($category == 'PCR'){
	//get oligo list for name display
	$oligos = array();
	$ocols = array('tracker.trackID','oligos.name','oligos.targetmatch',
		       'oligos.Saltconc','oligos.PCRconc', 'oligos.tm');
	$olis = getRecords('oligos',$userid, $ocols);
	if($olis){
		foreach ($olis as $o) {
			$tm = $o['tm'] ? $o['tm'] : Tm($o['targetmatch'],'bre',$o['Saltconc']*1E-3, $o['PCRconc']*1E-9);
			$oligos[$o['trackID']] = array('name' => $o['name'], 
				'tm' => $tm);
		}
	}
	// get plasmids into array
	$plasmids = array();
	$pcols = array('tracker.trackID','plasmids.name');
	$plasms = getRecords('plasmids',$userid, $pcols);
	if ($plasms){
		foreach ($plasms as $p) {
			$plasmids[$p['trackID']] = $p['name'];
		}
	}
}

foreach ($rows as $row) {
	$id = $row['trackID'];
	$type = $row['type'];
	$edit = 0;
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1) $edit = 1;
	echo listActions($id, array("new", "edit", "fasta", "delete") );
	print "<td class=\"lists\" width=\"1%\" align=\"RIGHT\">$id</td>";
	echo "<td class=\"lists\" width=\"10%\"><a href=\"editEntry.php?id=$id&amp;type=$type&amp;mode=display\">${row['name']}</a></td>";
if($category == 'backbone' or $category == 'gene'){
	echo "<td class=\"lists\" width=\"30%\">${row['description']}</td>";
}
if($category == 'backbone'){
	echo "<td class=\"lists\" width=\"10%\">${row['resistance']}</td>";
	echo "<td class=\"lists\" width=\"10%\">${row['origin']}</td>";
}

if($category == 'PCR'){
	for ($i=1;$i<=2;$i++){
		$o = $row["PCRoligo$i"];
		#$oligo = $oligos[$o];
		$oligo = getRecord($o, $userid, $groups);
		printf("<td class=\"lists\" style=\"text-align:center;\" width=\"5%%\"><a href=\"editEntry.php?id=%d&amp;mode=display\">%s (%d)</a></td>", $o, $oligo['name'], $o);
	}
	print "<td class=\"lists\" width=\"10%\">";
	for ($i=1;$i<=2;$i++){
		$oligo = $oligos[$row["PCRoligo$i"]];
		printf("%4.1f", $oligo['tm']);
		if ($i < 2) print " / ";
	}
	print "</td>";
	$template = getRecord($row["PCRtemplate"],$userid, $groups);
	print "<td class=\"lists seq\" width=\"10%\"><a href=\"editEntry.php?id=${row['PCRtemplate']}&amp;mode=display\">".$template['name']." (".$row['PCRtemplate'].")</a></td>";
}

	echo "<td class=\"lists seq\" width=\"10%\"><a href=\"sequence.php?field=DNASequence&amp;id=$id\">".CountATCG($row['DNASequence'])." bp</a></td>";
$proteinSequence = remove_non_coding_prot($row['proteinSequence']);
	if($category == 'gene'){
		echo "<td class=\"lists seq\" width=\"5%\"><a href=\"protein_properties.php?id=$id\">"
			.round(protein_molecular_weight($proteinSequence))." Da</a></td>";
	}
	echo "</tr>";
	$i++;
}
listProcessor(array(1,2,3,7));
?>
</table>
