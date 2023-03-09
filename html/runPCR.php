<?php
$formParams = ['table'=>$table, 'mode'=>'modify'];
# Sample type combobox
$q = "SELECT * FROM sampleTypes";
$values = pdo_query($q);
$sampleTypes = pdo_query($q);
$script = "<SCRIPT LANGUAGE=\"JavaScript\">\n";
$script .= "<!--\n";
$script .= "var hiddenFields = new Array(";
foreach ($sampleTypes as $type){
	$script .= "\"cmb.${type['name']}\", ";
}
$script = substr($script, 0, -2);
$script .= ");\n";
$script .= "// -->\n";
$script .= "</SCRIPT>\n";
print $script;
printComboBox('Choose Template', 'sampleType', $formParams, $sampleTypes, $fields['sampleType'], " onChange=\"AddFragmentField(this.value)\"");
// sample comboboxes
foreach ($sampleTypes as $type){
	$q = "SELECT * FROM ${type['table']}";
	$choices = pdo_query($q);
	print "<div id=\"${type['id']}\" name=\"cmb.${type['name']}\" style=\"display:none;\">";
	printComboBox('Select '.$type['name'], 'sampleID', $formParams, $choices, $fields['sampleID']);
	print "</div>";
}
?>