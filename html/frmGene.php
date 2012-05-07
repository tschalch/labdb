<?php
$table = 'fragments';
$mode = $_GET['mode'];
$formParams = array('table'=>'fragments', 'mode'=>$mode);
$noUserFilter = False;
$submitFunction = "true";
#determine type of building block and set title
include("formhead.php");
$fields['type']? $type=$fields['type']:$type = $_GET['type'];
switch ($type){
	case 'gene':
		$titleName = "Gene Fragment";
	    break;
	case 'PCR' :
		$titleName = "PCR Product";
	    break;
	case 'backbone':
		$titleName = "Plasmid Backbone";
	    break;
}
# hidden field that contains type
print "<input type=\"hidden\" value=\"$type\" name=\"${table}_0_type\"/>";
#form fields
#print_r($fields);echo "<br/>";
printID($formParams);
printTextField('Name', 'name',$formParams);
printProjectFields($formParams);
if ($type == 'PCR') printTextField('Reaction', 'reaction',$formParams);
printTextArea('Description', 'description',$formParams);
if ($type == 'gene') printTextField('Organism','organism',$formParams);
if ($type == 'PCR') printPCRFields($formParams);
?>
<style>
    .ui-button { margin-left: -1px; }
    .ui-button-icon-only .ui-button-text { padding: 0.35em; } 
    .ui-autocomplete-input { margin: 0; padding: 0.48em 0 0.47em 0.45em;}
    .ui-autocomplete {
	    max-height: 20em;
	    overflow-y: auto;
	    /* prevent horizontal scrollbar */
	    overflow-x: hidden;
	    /* add padding to account for vertical scrollbar */
	    padding-right: 20px;
    }
</style>

<?php
if ($type == 'backbone') printTextField('Resistance Marker', 'resistance',$formParams);
if ($type == 'backbone') printTextField('Origin of Replication', 'origin',$formParams);
if ($type == 'backbone' or $type == 'gene') printLinkField('Link to more info', 'link',$formParams);
if ($type == 'backbone' or $type == 'gene') printAttachmentField('File Attachement', 'attachment',$formParams);
#if ($type == 'gene') printLinkField('Internet link', 'link',$formParams);
printSequenceField('DNA Sequence', 'DNA', 'DNASequence',$formParams, True, False);
if ($type == 'gene') printSequenceField('Protein Sequence', 'protein', 'proteinSequence',$formParams, true, false);
printSubmitButton($formParams,$button);
printCloseAddFragmentButton($formParams, $id);
?>
</form>

<?php
#functions
###########################################################3

function printPCRFields($formParams){
	global $userid;
	global $groups;
	$mode = $formParams['mode'];
	$fields = $formParams['fields'];
	$table = $formParams['table'];
	$titleName = "PCR";
	$tables = array('oligos','plasmids');
	$choices = array();
	# get choices for the comboboxes
	foreach ($tables as $t){
		$tcols = array('tracker.trackID',"$t.name");
		$rows = getRecords($t, $userid, $tcols);
		if (!$rows) continue;
		foreach ($rows as $row) {
			$choices[$t][$row['trackID']] = $row['name'];
		}
	}
	#oligo comboboxes
	$PCRbox = "<div class=\"formRow\"><div class=\"formLabel\">PCR:<br/>";
	$PCRbox .= "<a style=\"display: block;\" target=\"blank\" href=\"sequence_extractor/index.php\" onClick=\"RunPCR();\"> Run PCR </a>";
	$PCRbox .= "</div>\n";
	if($mode == "modify"){
		$PCRbox .= "<div class=\"pcrField\">";
	}
	if ($mode == "display"){
		$PCRbox .= "<div class=\"displayField\">";
	}
	$PCRbox .= "<div class=\"PCR\">";
	for ($i=1;$i<=2;$i++){
		$PCRbox .= "<label class=\"pcrLabel\">Oligo $i:</label>\n";
		if($mode == "modify"){
		    $olid = $fields["PCRoligo$i"];
		    $PCRbox .= "<input class=\"oligoBox\" id=\"oligo$i\" name=\"${table}_0_PCRoligo$i\" columns=\"30\" value=\"$olid\">";
?>
		    <script type="text/javascript">
			window.addEvent('domready', function() {
			    new Autocompleter.labdb("oligo<?php print $i; ?>", 'autocomplete.php', {
				'postData': {
				'field': 'name', // send additional POST data, check the PHP code
				'table': 'oligos',
				'extended': '1',
				},
			    });
			});
		    </script>
<?php

		}
		if ($mode == "display"){
			$olid = $fields["PCRoligo$i"];
			$PCRbox .= "<span class=\"pcrText\"><a href=\"editEntry.php?id=$olid&amp;mode=display\" >".$choices['oligos'][$olid]."</a></span>";
			$PCRbox .= "<input type=\"hidden\" value=\"$olid\" id=\"oligo$i\" />";
		}
	}
	$PCRbox .= "</div>";
	# template combobox
	$PCRbox .= "<div class=\"PCR\" ><label class=\"pcrLabel\">Plasmid:</label>\n";
	if($mode == "modify"){
		$PCRbox .= "<input class=\"oligoBox\" id=\"template\" name=\"{$table}_0_PCRtemplate\" value=\"${fields['PCRtemplate']}\" >";
?>
		    <script type="text/javascript">
			window.addEvent('domready', function() {
			    new Autocompleter.labdb("template", 'autocomplete.php', {
				'postData': {
				'field': 'name', // send additional POST data, check the PHP code
				'table': 'plasmids',
				'extended': '1',
				},
			    });
			});
		    </script>
<?php
	}
	if ($mode == "display"){
		$plid = $fields["PCRtemplate"];
		$PCRbox .= "<span class=\"pcrText\"><a href=\"editEntry.php?id=$plid&amp;mode=display\" >".$choices['plasmids'][$plid]."</a></span>";
		$PCRbox .= "<input type=\"hidden\" value=\"$plid\" id=\"template\" />";
	}
	$PCRbox .= "</div></div></div>";
	print $PCRbox;
}
?>
