<?php
include("formhead.php");
$type = $_GET['type'];
if ($type == 'PCR'){
	$tables = array('oligos','plasmids');
	$choices = array();
	# get choices for the comboboxes
	foreach ($tables as $t){
		$query="SELECT * FROM `$t` ORDER BY name";
		#print "$query <br/>";
		$rows = pdo_query($query);
		foreach ($rows as $row) {
			$choices[$t][$row['id']] = $row['name'];
		}
	}
	#oligo comboboxes
	$PCRbox = "<tr><td>PCR:</td><td><table><tr>";
	$style = "display: inline";
	for ($i=1;$i<=2;$i++){
		$PCRbox .= "<td><div style=\"$style\">Oligo $i:\n";
		$PCRbox .= "<select style=\"max-width: 120px;\" id=\"oligo$i\" name=\"${table}_0_PCRoligo$i\" columns=\"30\">";
		$c = "<option value=\"NA\"></option>\n";
		foreach ($choices['oligos'] as $id => $oligo){
			$c .= "<option value=\"$id\"";
			if ($fragment["PCRoligo$i"] == $id) $c .= " selected=\"selected\"";
			$c .= ">$oligo</option>\n";
		}
		$PCRbox .= "$c </select> </div>";
	}
	# template combobox
	$style = "display:block";
	$PCRbox .= "</td></tr><tr><td><div style=\"$style\">Template:\n";
	$PCRbox .= "<select id=\"oligo1\" name=\"{$table}_0_PCRoligo1\">";
	$c = "<option value=\"NA\"></option>\n";
	foreach ($choices['plasmids'] as $id => $plasmid){
		$c .= "<option value=\"$id\"";
		if ($fragment['PCRtemplate'] == $id) $c .= " selected=\"selected\"";
		$c .= ">$plasmid</option>\n";
	}
	$PCRbox .= "$c </select></div></td></tr></table></td></tr>";
}
?>

<table>
<tr><td width="20%">Fragment Name:</td><td><input type="text" name="<?php echo $table?>_0_name" size="50" value="<?php echo $fields['name']?>"></td></tr>
<tr><td>Organism:</td><td><input type="text" name="<?php echo $table?>_0_organism" size="50" value="<?php echo $fields['organism']?>"></td></tr>
<tr><td>Description:</td><td><textarea class="form" name="<?php echo $table?>_0_description" cols="50"><?php echo $fields['description']?></textarea></td></tr>

<?php
echo $PCRbox;
?>
<tr><td>DNA Sequence:</td><td><textarea id="DNA" class="form" onblur = "FilterSequence(this,'DNA');" name="<?php echo $table?>_0_DNASequence" rows="10"><?php echo $fields['DNASequence']?></textarea><br/>
length: <input type="text" value="click to get length" onclick="this.value = GetSequenceLength(document.mainform.<?php echo $table?>_0_DNASequence);"/>
</td></tr>
<tr><td>Protein Sequence:</td><td><textarea id="protein" class="form" onblur="FilterSequence(this,'protein');" name="<?php echo $table?>_0_proteinSequence" rows="10"><?php echo $fields['proteinSequence']?></textarea></td></tr>
<tr>
<td colspan="2" height="40px"><center><input type="Submit" value="<?php echo $button?>"></center></td></tr>
</table>