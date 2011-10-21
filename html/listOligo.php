<?php
$title = 'List of Oligos';
$listItem = "oligo";  #used form javascript message box.
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "oligos";
$columns = array('oligos.name', 'oligos.description', 'oligos.sequence',
		 'oligos.supplier', 'oligos.scale', 'oligos.modifications',
		 'oligos.purity', 'oligos.bpPrice', 'oligos.orderDate',
		 'tracker.trackID', 'tracker.owner','tracker.permOwner',
		 'oligos.tm', 'oligos.Saltconc', 'oligos.PCRconc',
		 'oligos.targetmatch'
		 );
# optional join expressions to connect to more data
$join = "";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="oligos.name";
#End SQL parameters

#array of query field => table heading
$fields = array('trackID' => 'ID',
		'name' => 'Name',
		'description' => 'Description',
		'sequence' => 'Sequence',
		'length' => 'Length',
		'supplier' => 'Supplier',
		'scale' => 'Scale',
		'modifications' => 'Modif.',
		'purity' => 'Purity',
		'bpPrice' => 'Cost',
		'1' => 'Tm',
		'orderDate' => 'order Date');


#toggle Project combobox on and off
$noProjectFilter = False;
#toggle user/group filters on and off
$noUserFilter = False;


include("listhead.php");
$totalCost = 0;
foreach ($rows as $row) {
	$id = $row['trackID'];
	print "<tr>";
	$edit = 0;
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1) $edit = 1;
	echo listActions($id, $edit, false);
	print "<td class=\"lists\" width=\"1%\" align=\"RIGHT\">$id</td>";
	$name = $row['name'];
	if (strlen($name) > 20){
		$nameTitle = $name;
		$name = substr($name,0,20)."...";
	}
	$seq = $row['sequence'];
	$seqlen = strlen($seq);
	$dispseq = $seq;
	if ($seqlen > 35) $dispseq = "see details";
	echo "<td class=\"lists\" width=\"15%\">
	      <a title=\"$nameTitle\" href=\"editEntry.php?id=$id&amp;mode=display\">$name</a>
	      <input type=\"hidden\" name=\"name_$id\" value=\"${row['name']}\"/></td>\n";
	$desc = $row['description'];
	if (strlen($desc) > 25){
		$descTitle = $desc;
		$desc = substr($desc,0,25)."...";
	}
	echo "<td class=\"lists\" width=\"15%\"><span title=\"$descTitle\">$desc<span></td>\n";
	echo "<td class=\"lists\" width=\"15%\">
		<input type=\"hidden\" name=\"sequence_$id\" value=\"$seq\"/>\n
		<input type=\"hidden\" name=\"targetmatch_$id\" value=\"${row['targetmatch']}\"/>\n
		$dispseq</td>\n";
	echo "<td class=\"lists\" width=\"5%\" align=\"center\">$seqlen</td>\n";
	echo "<td class=\"lists\" width=\"10%\" align=\"center\">${row['supplier']}</td>\n";
	echo "<td class=\"lists\" width=\"5%\" align=\"center\">${row['scale']}</td>\n";
	echo "<td class=\"lists\" width=\"10%\" align=\"center\" >${row['modifications']}</td>\n";
	echo "<td class=\"lists\" width=\"5%\" align=\"center\">${row['purity']}</td>\n";
	$cost = strlen($row['sequence']) * $row['bpPrice'];
	$totalCost += $cost;
	echo "<td class=\"lists\" width=\"5%\" align=\"center\">$cost</td>\n";
	$tm = $row['tm'] ? $row['tm'] : Tm($row['targetmatch'],'bre',$row['Saltconc']*1E-3, $row['PCRconc']*1E-9);
	printf("<td class=\"lists\" width=\"5%%\" align=\"center\">%6.1f</td>\n", $tm);
	$orderDate = $row['orderDate'];
	if (strlen($orderDate) > 8) $orderDate = substr($orderDate,0,8)."...";
	echo "<td class=\"lists\" width=\"5%\" align=\"center\">$orderDate";
	print "<input type=\"hidden\" name=\"scale_$id\" value=\"${row['scale']}\"/>";
	print "<input type=\"hidden\" name=\"purity_$id\" value=\"${row['purity']}\"/></td>\n";
	echo "</tr>\n";
	$i++;
}
?>
<tr><td colspan = "100"><?php 
	$noOligos = count($rows);
	echo "Number of Oligos: $noOligos; ";
	echo "Total Cost = \$ $totalCost"; 
	
	?></td></tr>
<tr><td colspan="100">
<div id="oligoOptions" name="oligoOptions" style="display:none;" >

Select ouput format: <select name="output">
<option value="extractor">Sequence extractor</option>
<option value="amplifx">AmplifX</option>
<option value="sigma">Sigma Genosis</option>
<option value="vectornti">Vector NTI</option>
<option value="finnzymes">Finnzymes Tm calculation</option>
</select><br/>
Select template for Sequence Extractor: 
<?php
	$cols = array('tracker.trackID',"plasmids.name");
	$choices = getRecords("plasmids", $userid, $cols);
	//print_r($choices);print "<br/>";
	$current = $_SESSION['template'];
	print "<select name=\"template\">";
	print "<option value=\"NA\"></option>";
	foreach ($choices as $choice){
		$cmbBox .= "<option value=\"${choice['trackID']}\"";
		if ($choice['trackID'] == $current) $cmbBox .= " selected=\"selected\"";
		$cmbBox .= ">${choice['name']}</option>\n";
	}
	print $cmbBox;
?>
</select>
</div>
</td></tr>
<?php listProcessor(array(0,2,3));?>
</table>
</form>