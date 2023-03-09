<?php
$table = "plasmids";
$titleName = "Plasmid";
$submitFunction = "validate_form()";
$mode = $_GET['mode'];
if (!$mode) $mode = 'display';
$formParams = ['table'=>$table, 'mode'=>$mode];

include("formhead.php");
?>
<script type="text/javascript">
//<![CDATA[
window.addEvent('domready', function() {
     new Autocompleter.Request.HTML('resistance', 'autocomplete.php', {
		'postData': {
		    'field': 'resistance', // send additional POST data, check the PHP code
		    'table': 'plasmids',
		    'extended': 0,
		}
    });
});
//]]>
</script>
<?php
?>
<script type="text/javascript">
window.addEvent('domready', function() {
    window.fields = [
	<?php
	$fieldname = "document.mainform.${table}_0_";
	print "${fieldname}name, ";
    ?>];
    window.NoFields = [
	<?php
	print "";
	?>];
    window.DateFields = [
	<?php 
	    print "";
	?>];
});
</script>
<?php

# get choices for fragment comboboxes
$types = ['backbone'=>'fragments', 'gene'=>'fragments', 'PCR'=>'fragments'];
#print_r($choices);
#get the associated fragments
#if($formParams['fields']){
#	$frquery="SELECT $ltable.fragmentID fragments.type FROM `$ltable` WHERE plasmidID=$id";
#	$rows = pdo_query($tquery);
#}
/*menu*/
print "<div id=\"leftToMap\">\n";
printID($formParams);
printTypeID($formParams, "Plasmid ID");
printTextField('Name', 'name',$formParams);
printProjectFields($formParams);
printTextArea('Description', 'description',$formParams);
printTextField('Resistance', 'resistance',$formParams);
printTextArea('Generation of plasmid', 'generation',$formParams);
//restriction enzymes
$helpText = "This field requires a comma separated liste of restriction enzymes.\
	Alternatively, the enzymes available in our lab can be chosen by specifying \"lab\" (all enzymes in box \"Lab Restriction Enzymes\").\
	After the list of enzymes, arguments to the <a href=\\\"doc/emboss/restrict.html\\\">\\'restrict\\' command of the Emboss package</a> \
	can be added.<br/><br/>\
	Examples:<pre>\
		PstI,BamHI\\n\
		lab -max 2 -min 1</pre>";
printTextField('Enzymes', 'enzymes',$formParams, null, $helpText);
$fcounter = printCrossCombobxs($id, $types, 0, $formParams);
printReferenceLink('Freezer', 'Freezer locations', $id, 'vial', $formParams);
print "</div>\n";
print "<div id=\"mapBox\">\n";
?>
<div style="background-color: gray; height: 1em; padding: 0px 0px 20px 0px"><ul id="nav">
	<li><a href="#">Graph</a>
		<ul>
			<li><a class="nav"
			       onclick="vm.updateFragments($('xcmbxFrags'));
					vm.drawVector();
					return false;"
			       href="#">
				Refresh
			</a></li>
		</ul>
	</li>
	<li><a href="#">Tools</a>
		<ul>
			<li><a class="nav" onclick="translate(this); return false"
			       target="newWindow" href="newEntry.php?form=frmGene&amp;type=gene&amp;mode=modify">
				Translate
			</a></li>
			<li><a class="nav" onclick="SaveSVG(); return false"
			       href="#">
				Save SVG map
			</a></li>
		</ul>
	</li>
</ul></div>
<script src="js/MenuMatic_0.68.3.js" type="text/javascript"></script>
<script type="text/javascript">
function SaveSVG(){
	var svg = $('map').get('html');
	postwith("saveSVG.php", {"svg":svg, title:$('name').get('value')})
}
window.addEvent('domready', function() {
	var myMenu = new MenuMatic();
})
</script>
<?php
print "<div id=\"map\"></div>
	       <div id=\"orfControl\">
			<div id=\"orfSize\" class=\"orfSize\">100</div>
			<div id=\"orfSizeSlider\">
				<div class=\"knob\"></div>
			</div>
			<div class=\"orfSize\">Minimum ORF size:</div>
		</div>
	</div>\n";
printSubmitButton($formParams,$button);
printSequenceField("Sequence", 'DNA', 'sequence', $formParams, true, true);
$sites = getRestrictionSites($fields['enzymes'], $fields['sequence']);
print "</form>\n";
if($mode == "modify"){
	$seqField = "sequence";
}else{
	$seqField = "disp_sequence";
}
?>

<script src="js/raphael_4.29.6.min.js" type="text/javascript" charset="utf-8"></script>
<script src="lib/sms/sms_common.js" type="text/javascript"></script>
<script src="lib/sms/sms_genetic_codes.js" type="text/javascript"></script>
<script src="lib/sms/orf_find.js" type="text/javascript"></script>
<script src="lib/sms/translate.js" type="text/javascript"></script>
<script src="js/vectorMap.js" type="text/javascript"></script>
<!--[if lt IE 7]>
	<link rel="stylesheet" href="css/MenuMatic-ie6.css" type="text/css" media="screen" charset="utf-8" />
<![endif]-->


<script type="text/javascript" charset="utf-8">
/*ajax code*/ 

window.addEvent('domready', function() {
	fcounter = <?php print $fcounter ?>;
	mode = '<?php print $mode; ?>';
	table = 'fragments';
	vm = new VectorMap({}, $("map"));		
	vm.updateFragments($('xcmbxFrags'));
	vm.sites = <?php print $sites; ?>;
	vm.drawVector(); 
<?php foreach($types as $name=>$table){
	print "\$('$name').addEvent('click', addcmbx);";
	}
?>
	sequence = "";
	
	var slideEl = $('orfSizeSlider');
	
	// Create the new slider instance
	new Slider(slideEl, slideEl.getElement('.knob'), {
		steps: 20,	// There are 40 steps
		range: [20,220],	// Minimum value is 8
		initialStep: 100,
		onChange: function(value){
			// Everytime the value changes, we change the font of an element
			vm.options.orfLength = value;
			vm.updateFragments($('xcmbxFrags'));
			vm.drawVector();
			$('orfSize').set('html', value);
		}
	});
})

</script>


