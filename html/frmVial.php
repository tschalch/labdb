<?php
$titleName = "Vial";
$mode = $_GET['mode'];
$template = $_GET['template'];
$table = 'vials';
$submitFunction = "validate_form()";
$formParams = array('table'=>$table, 'mode'=>$mode);

# setup fields from template if one is given
if ($template){
    $template = getRecord($template,$userid);
    $fields['name'] = $template['name'].", id: ".$template['trackID'];
    $fields['project'] = $template['project'];
    $fields['sID'] = $template['trackID'];
}

include("formhead.php");


//print_r($fields);
if ($mode == 'modify'){
    ?>
    <script type="text/javascript">
    <!--
    
    function validate_form ( )
    {
	valid = true;
	field = $('name');
	if ( field.get('value') == "" ){
	    oldStyle = field.get("styles");
	    field.style.border  = "1px solid #FF6633";
	    valid = false;
	} else {
	    field.style.border  = "1px solid gray";
	}
	if (!checkDate("<?php print "${table}_0_date";?>")){
	    valid = false;
	}
	if (!valid) alert ( "Form contains errors. Please correct or complete where marked." );
	return valid;
    }
    
    window.addEvent('domready', function() {
	    var addcmbx = function (event){
		    //prevent the page from changing
		    event.stop();  
		    //make the ajax call, replace text
		    if ($('cmbo').getFirst('div') != null){
			$('cmbo').getFirst('div').destroy();
		    }
		    var el = new Element('div');
		    var type = $(event.target).get('value');
		    addXcmbx(el, type);
	    }
	    $('cmbType').addEvent('change', addcmbx);
    })
    
    addXcmbx = function (el, type) {  
	    el.inject($('cmbo'));
	    var req = new Request.HTML({  
		    method: 'get',  
		    url: "getCombo.php",
		    data: { 'sampletype' : type, 'mode': '<?php print $mode; ?>'},
		    update: el,
    //		onComplete: function () {
    //				vm.updateFragments($('xcmbx'));
    //				vm.drawVector();
    //				},
		    }).send();
    };
    
    
    //-->
    </script>
    <?php
}
printID($formParams);
printTextField('Vial Label', 'name', $formParams);
printProjectFields($formParams);
printDateField('Date', 'date', $formParams);
printTextField('Concentration', 'concentration', $formParams);
printTextArea('Description', 'description', $formParams);
printTextField('Created by', 'creator', $formParams);
# Sample comboboxes
	$q = "SELECT *, id AS trackID, `st_name` AS `name` FROM sampletypes WHERE isSample=TRUE";
	$sampleTypes = pdo_query($q);
#	$choices = array('plasmids'=>'Plasmid','glycerolstocks'=>'Glycerol Stock','oligos' => 'Oligo','proteins' => 'Protein');
	$script = "<script type=\"text/javascript\" charset=\"utf-8\">\n";
	$script .= "<!--\n";
	$script .= "var hiddenFields = new Array(";
	foreach ($sampleTypes as $type){
		$script .= "\"cmb.${type['id']}\", ";
	}
	$script = substr($script, 0, -2);
	$script .= ");\n";
	$script .= "// -->\n";
	$script .= "</script>\n";
	print $script;
	if (isset($fields['sID'])){
                $r1 = getSampleType($fields['sID']);
		$st = $r1[0];
                //print "stID".$st['id'];
	} else {
		$st = array();
	}
        print "<div id =\"typeChoice\" class=\"formRow\"><div class=\"formLabel\">Select Type:</div>";
        print getComboBox('Type', '', $mode, $sampleTypes, $st['id'], '', False);
        print "</div></div>\n";
#	if ($id){
#		//get sample table
#		$sTable = $sample['table'];
#		$q2 =  "SELECT tracker.trackID as id, $sTable.name 
#			FROM tracker INNER JOIN $sTable ON tracker.sampleID = $sTable.id 
#			WHERE tracker.sampleType = ${sample['id']} 
#			ORDER BY $sTable.name";
#		//print $q2;
#		$choices = pdo_query($q2);
#		//print_r($choices);
#		//print $fields['trackerID'];
#		print "<div id=\"cmb.${sample['id']}\" style=\"display:block;\">";
#		$siq = pdo_query("SELECT sampleID FROM tracker WHERE trackID = ${fields['trackerID']}");
#		printComboBox($fields['stName'], 'trackerID', $formParams, $choices, $fields['trackerID'], '','', True);
#		print "</div>";
#	} else {
		foreach ($sampleTypes as $type){
			$sTable = $type['table'];
			#$q =   "SELECT tracker.trackID AS id, $sTable.name 
			#	FROM tracker INNER JOIN $sTable ON tracker.sampleID=$sTable.id 
			#	WHERE tracker.sampleType=${type['id']} AND userid=$userid
			#	ORDER BY $sTable.name";
			//print "$q <br/>";
			#$choices = pdo_query($q);
			$cols = array('tracker.trackID',"CONCAT($sTable.name,' (id: ',tracker.trackID,')') AS name");
			$choices = getRecords($sTable, $userid, $cols);
			if ($type['id'] == $st['id']){
				$fstyle = "display:block;";
			} else {
				$fstyle = "display:none;";
			}
#		}
	}
        print "<div id=\"cmbo\"> \n";
        if(isset($fields['sID'])){
            include('getCombo.php');
        }
        print "</div>\n\n";
# box combobox
#$choices = pdo_query("SELECT tracker.trackID as id, boxes.name FROM tracker INNER JOIN boxes ON tracker.sampleID=boxes.id INNER JOIN sampletypes ON sampletypes.name='box' WHERE tracker.sampleType=sampletypes.id AND tracker.owner=$userid");
$cols = array('tracker.trackID','boxes.name');
$choices = getRecords('boxes', $userid, $cols);
#print_r($choices);
printComboBox("Storage Box",'boxID', $formParams, $choices, $fields['boxID'],'','frmBoxes');
printTextField('Position', 'position', $formParams);
printSubmitButton($formParams,$button);
print "</form>\n";
?>