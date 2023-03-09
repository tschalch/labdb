<?php
$titleName = "Vial";
$mode = $_GET['mode'];
$table = 'vials';
$submitFunction = "validate_form()";
$formParams = ['table'=>$table, 'mode'=>$mode];


# setup fields from template if one is given
if (isset($_GET['template'])){
  $template = $_GET['template'];
  $template = getRecord($template,$userid);
  $fields['name'] = "${template['hexID']} ${template['name']}";
  $fields['project'] = $template['project'];
  $fields['sID'] = $template['trackID'];
}
include("formhead.php");
console_log("frmVial template: ".print_r($fields, true));
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
print "\"${table}_0_date\", ";
?>];
});
</script>
<?php


//print_r($fields);
if ($mode == 'modify'){
?>
  <script type="text/javascript">
  <!--

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
        data: { 'sampletype' : type, 'mode': '<?php print $mode; ?>', 'table': 'vials', 'field': 'sID'},
        update: el,
        //onComplete: function () {
        //vm.updateFragments($('xcmbx'));
        //vm.drawVector();
        //},
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
console_log("sampleTypes from $q: ".print_r($sampleTypes, true));

#$choices = array('plasmids'=>'Plasmid','glycerolstocks'=>'Glycerol Stock','oligos' => 'Oligo','proteins' => 'Protein');
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
if (key_exists('sID', $fields)){
  $r1 = getSampleType($fields['sID']);
  console_log("rl: ".print_r($r1, true));
  $st = $r1['id'];
} else {
  $st = '';
}
print "<div id =\"typeChoice\" class=\"formRow\"><div class=\"formLabel\">Select Type:</div>";
print getComboBox('Type', 'none', $mode, $sampleTypes, $st, '', False);
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
  $cols = ['tracker.trackID', "CONCAT($sTable.name,' (id: ',tracker.trackID,')') AS name"];
  $choices = getRecords($sTable, $userid, [], $cols);
  if (key_exists('id', $type) & $type['id'] == $st){
    $fstyle = "display:block;";
      } else {
        $fstyle = "display:none;";
      }
#		}
  }
        print "<div id=\"cmbo\"> \n";
        if(isset($fields['sID'])){
          $trackID = $fields['sID'];
          include('getCombo.php');
        }
        print "</div>\n\n";
        # box combobox
        #$choices = pdo_query("SELECT tracker.trackID as id, boxes.name FROM tracker INNER JOIN boxes ON tracker.sampleID=boxes.id INNER JOIN sampletypes ON sampletypes.name='box' WHERE tracker.sampleType=sampletypes.id AND tracker.owner=$userid");
        $cols = ['tracker.trackID', 'boxes.name'];
        $choices = getRecords('boxes', $userid, [], $cols);
        #print_r($choices);
        if (!key_exists('boxID', $fields)) $fields['boxID'] = '';
        printComboBox("Storage Box",'boxID', $formParams, $choices, $fields['boxID'],'','frmBoxes');
        printTextField('Position', 'position', $formParams);
        printSubmitButton($formParams,$button);
        print "</form>\n";
?>
